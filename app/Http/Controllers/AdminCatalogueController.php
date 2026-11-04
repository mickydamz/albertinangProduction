<?php
namespace App\Http\Controllers;
use App\Models\CatalogueRevision;
use App\Services\CatalogueWorkflow;
use Illuminate\Http\Request;
class AdminCatalogueController extends Controller {
    public function __construct() { $this->middleware(['auth','role:admin']); }
    public function index(Request $request) {
        $request->validate(['status'=>'nullable|in:pending,approved,changes_requested,draft,archived','search'=>'nullable|string|max:255']);
        $revisions=CatalogueRevision::with(['editor','reviewer'])->where('status',$request->input('status','pending'))
            ->when($request->search,fn($q,$s)=>$q->where('payload->name','like','%'.$s.'%'))->latest('updated_at')->paginate(20)->withQueryString();
        return view('catalogue.review-index',compact('revisions'));
    }
    public function show(CatalogueRevision $revision) { $revision->load(['editor','reviewer','events','uploads','product']); return view('catalogue.review',compact('revision')); }
    public function review(Request $request,CatalogueRevision $revision,CatalogueWorkflow $workflow) {
        $data=$request->validate(['version'=>'required|integer|min:0','decision'=>'required|in:approve,return','notes'=>'required|string|min:3|max:2000']);
        $workflow->review($revision->id,(int)$data['version'],$data['decision'],$data['notes']);
        return redirect()->route('admin.catalogue.show',$revision)->with('success',$data['decision']==='approve' ? 'Submission approved and product updated.' : 'Returned to the staff member for correction.');
    }
}
