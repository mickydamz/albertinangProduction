<?php
namespace App\Http\Controllers;

use App\Models\{Brand, Product, Category, Tag, Size, Color, Location, CatalogueRevision, CatalogueUpload};
use App\Services\{CatalogueProductData, CatalogueWorkflow};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Storage};

class CatalogueController extends Controller
{
    public function __construct() { $this->middleware(['auth',\App\Http\Middleware\CatalogueEditor::class]); }

    public function index(Request $request) {
        $filters = $request->validate(['search'=>'nullable|string|max:255','brand'=>'nullable|integer','status'=>'nullable|in:draft,pending,changes_requested,approved,archived','view'=>'nullable|in:products,submissions,history']);
        $brands = Brand::orderBy('name')->get(['id','name']);
        $products = Product::with('images')->when($filters['search'] ?? null,fn($q,$s)=>$q->where('name','like','%'.$s.'%'))
            ->when($filters['brand'] ?? null,fn($q,$b)=>$q->where('brand_id',$b))->latest('id')->paginate(15)->withQueryString();
        $mine = CatalogueRevision::where('editor_id',auth()->id());
        $counts = (clone $mine)->selectRaw('status, COUNT(*) AS total')->groupBy('status')->pluck('total','status');
        $revisions = (clone $mine)->when($filters['status'] ?? null,fn($q,$s)=>$q->where('status',$s))
            ->when($filters['search'] ?? null,fn($q,$s)=>$q->where('payload->name','like','%'.$s.'%'))
            ->when($filters['brand'] ?? null,fn($q,$b)=>$q->where('payload->brand_id',(int)$b))->latest('updated_at')->paginate(15)->withQueryString();
        $events = \App\Models\CatalogueRevisionEvent::whereIn('revision_id',(clone $mine)->select('id'))->latest('id')->paginate(20)->withQueryString();
        return view('catalogue.index',compact('products','brands','counts','revisions','events','filters'));
    }

    public function create() { return $this->form(new CatalogueRevision(['payload'=>['publish_after_approval'=>true],'version'=>0])); }

    public function start(Product $product, CatalogueWorkflow $workflow) {
        $snapshot = $workflow->snapshot($product);
        $revision = DB::transaction(function () use ($product,$workflow,$snapshot) {
            // Serialise starts per staff account so repeated clicks reuse the open revision.
            \App\Models\User::whereKey(auth()->id())->lockForUpdate()->firstOrFail();
            $existing = CatalogueRevision::where('editor_id',auth()->id())->where('product_id',$product->id)->whereIn('status',['draft','pending','changes_requested'])->first();
            if ($existing) return $existing;
            $revision = CatalogueRevision::create(['editor_id'=>auth()->id(),'product_id'=>$product->id,'payload'=>$workflow->productPayload($product),
                'base_snapshot'=>$snapshot,'base_fingerprint'=>$workflow->fingerprint($snapshot)]);
            $workflow->record($revision,'draft_started'); return $revision;
        });
        return redirect()->route('catalogue.edit',$revision);
    }

    public function edit(CatalogueRevision $revision) {
        abort_unless($revision->editor_id === auth()->id(),404);
        return $this->form($revision);
    }

    private function form(CatalogueRevision $revision) {
        $defaults = $revision->payload;
        $defaults['requires_truck_option'] = empty($defaults['requires_truck_set']) ? 'inherit' : (!empty($defaults['requires_truck']) ? 'yes' : 'no');
        if (empty($defaults['attr_groups']) && !empty($defaults['options'])) $defaults['attr_groups'] = [['group'=>'Specifications','attrs'=>$defaults['options']]];
        $product = new Product($defaults);
        $brands = Brand::orderBy('name')->get(); $categories = Category::with('subcategories')->get();
        $tags = Tag::all(); $sizes = Size::all(); $colors = Color::all(); $locations = Location::all();
        $customAttributes = $defaults['custom_attributes'] ?? []; $selectedTags = [];
        $options = [];
        foreach (old('new_tag_options_data',$defaults['new_tag_options_data'] ?? []) as $json) {
            $entry = json_decode($json,true); if (is_array($entry) && isset($entry['name'])) $options[$entry['name']] = $entry['options'] ?? [];
        }
        foreach (old('tags',$defaults['tags'] ?? []) as $input) {
            $tag = ctype_digit((string)$input) ? $tags->find($input) : null;
            if (!$tag && ctype_digit((string)$input)) continue;
            $name = $tag?->name ?? strip_tags((string)$input);
            $selectedTags[] = ['id'=>$tag?->id,'name'=>$name,'isNew'=>!$tag,'customOptions'=>$options[$name] ?? [$name]];
        }
        $value = fn($key,$fallback=null)=>old($key,data_get($defaults,$key,$fallback));
        return view('catalogue.form',compact('revision','defaults','value','product','brands','categories','tags','sizes','colors','locations','customAttributes','selectedTags'));
    }

    public function store(Request $request, CatalogueProductData $validator, CatalogueWorkflow $workflow) {
        $request->validate(['creation_key'=>'nullable|uuid']);
        if ($request->filled('creation_key')) {
            $existing=CatalogueRevision::where('creation_key',$request->creation_key)->first();
            if ($existing) { abort_unless($existing->editor_id===auth()->id(),409); return redirect()->route('catalogue.edit',$existing); }
        }
        try { return $this->save($request,new CatalogueRevision(['version'=>0,'status'=>'draft','payload'=>[],'creation_key'=>$request->creation_key]),$validator,$workflow); }
        catch (\Illuminate\Database\QueryException $e) {
            $existing=$request->filled('creation_key') ? CatalogueRevision::where('creation_key',$request->creation_key)->where('editor_id',auth()->id())->first() : null;
            if ($existing) return redirect()->route('catalogue.edit',$existing);
            throw $e;
        }
    }
    public function update(Request $request, CatalogueRevision $revision, CatalogueProductData $validator, CatalogueWorkflow $workflow) {
        abort_unless($revision->editor_id === auth()->id(),404);
        return $this->save($request,$revision,$validator,$workflow);
    }

    private function save(Request $request, CatalogueRevision $revision, CatalogueProductData $validator, CatalogueWorkflow $workflow) {
        $data = $validator->validate($request); $paths = [];
        try {
            $revision = DB::transaction(function () use ($request,$revision,$data,$workflow,&$paths) {
                if ($revision->exists) $revision = CatalogueRevision::lockForUpdate()->findOrFail($revision->id);
                $workflow->assertVersion($revision,(int)$request->version);
                abort_unless(in_array($revision->status,['draft','changes_requested']),409,'This submission cannot be edited. Withdraw it first or start a new draft.');
                $baseIds = array_column($revision->base_snapshot['images'] ?? [],'id');
                abort_if(array_diff($request->input('remove_images',[]),$baseIds),422,'Invalid image selection.');
                $uploadIds = $revision->exists ? $revision->uploads()->where('kind','gallery')->pluck('id')->all() : [];
                abort_if(array_diff($request->input('remove_uploads',[]),$uploadIds),422,'Invalid uploaded image selection.');
                abort_if(count($uploadIds)-count($request->input('remove_uploads',[]))+count($request->file('images',[]))>10,422,'Use at most ten draft gallery images.');
                $data['remove_images'] = $request->input('remove_images',[]); $data['remove_uploads'] = $request->input('remove_uploads',[]);
                $revision->editor_id = auth()->id(); $revision->payload = $data;
                $revision->status = $request->intent === 'submit' ? 'pending' : 'draft';
                if ($revision->status === 'pending') $revision->submitted_at = now();
                $revision->version++; $revision->save();
                foreach ($request->file('images',[]) as $image) {
                    $path = $image->store('catalogue/gallery','public'); $paths[] = $path;
                    CatalogueUpload::create(['editor_id'=>auth()->id(),'revision_id'=>$revision->id,'path'=>$path,'kind'=>'gallery']);
                }
                $workflow->record($revision,$revision->status === 'pending' ? 'submitted' : 'draft_saved');
                return $revision;
            });
        } catch (\Throwable $e) { foreach ($paths as $path) Storage::disk('public')->delete($path); throw $e; }
        return redirect()->route('catalogue.edit',$revision)->with('success',$revision->status === 'pending' ? 'Sent to admin for approval. The shop has not changed.' : 'Draft saved.');
    }

    public function withdraw(Request $request, CatalogueRevision $revision, CatalogueWorkflow $workflow) {
        return $this->transition($request,$revision,$workflow,'draft',['pending'],'withdrawn');
    }
    public function archive(Request $request, CatalogueRevision $revision, CatalogueWorkflow $workflow) {
        return $this->transition($request,$revision,$workflow,'archived',['draft','changes_requested'],'archived');
    }
    private function transition(Request $request,CatalogueRevision $revision,CatalogueWorkflow $workflow,string $status,array $allowed,string $action) {
        abort_unless($revision->editor_id === auth()->id(),404); $request->validate(['version'=>'required|integer|min:0']);
        DB::transaction(function () use ($request,$revision,$workflow,$status,$allowed,$action) {
            $locked = CatalogueRevision::lockForUpdate()->findOrFail($revision->id); $workflow->assertVersion($locked,(int)$request->version);
            abort_unless(in_array($locked->status,$allowed),409); $locked->status=$status; $locked->version++; $locked->save(); $workflow->record($locked,$action);
        });
        return redirect()->route('catalogue.edit',$revision)->with('success','Submission updated.');
    }
    public function refreshBase(Request $request,CatalogueRevision $revision,CatalogueWorkflow $workflow) {
        abort_unless($revision->editor_id === auth()->id(),404); $request->validate(['version'=>'required|integer|min:0']);
        DB::transaction(function () use ($request,$revision,$workflow) {
            $locked=CatalogueRevision::lockForUpdate()->findOrFail($revision->id); $workflow->assertVersion($locked,(int)$request->version);
            abort_unless(in_array($locked->status,['draft','changes_requested']) && $locked->product_id,409);
            $product=Product::lockForUpdate()->findOrFail($locked->product_id); $snapshot=$workflow->snapshot($product);
            $locked->payload=$workflow->productPayload($product); $locked->base_snapshot=$snapshot; $locked->base_fingerprint=$workflow->fingerprint($snapshot);
            $locked->status='draft'; $locked->version++; $locked->save(); $workflow->record($locked,'refreshed_from_product','Previous proposed edits remain in change history.');
        });
        return redirect()->route('catalogue.edit',$revision)->with('success','Draft refreshed from current product. Previous proposed edits remain in history.');
    }
    public function upload(Request $request) {
        $request->validate(['image'=>'required|image|mimes:jpeg,png,jpg,webp|max:5120']);
        $path = $request->file('image')->store('catalogue/description','public');
        try { CatalogueUpload::create(['editor_id'=>auth()->id(),'path'=>$path,'kind'=>'description']); }
        catch (\Throwable $e) { Storage::disk('public')->delete($path); throw $e; }
        return response()->json(['url'=>Storage::disk('public')->url($path)]);
    }
}
