<?php
namespace App\Services;

use App\Models\{Brand, Product, Tag, CatalogueRevision, CatalogueRevisionEvent};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CatalogueWorkflow
{
    public const FIELDS = ['name','description','brand_id','category_id','subcategory_id','price','stock','moq','weight_kg','markup_percent','discount_percent','custom_attributes','custom_attribute_groups','installation_options','description_blocks'];

    public function snapshot(Product $product): array
    {
        $attributes = $product->fresh()->getRawOriginal(); ksort($attributes);
        $snapshot = ['attributes'=>$attributes];
        foreach (['tags','sizes','colors','locations'] as $relation) {
            $snapshot[$relation] = $product->$relation()->orderBy($relation.'.id')->pluck($relation.'.id')->map(fn($id)=>(int)$id)->all();
        }
        $snapshot['pickup_points']=$product->pickupPoints()->orderBy('pickup_points.id')->pluck('pickup_points.id')->map(fn($id)=>(int)$id)->all();
        $snapshot['images'] = $product->images()->orderBy('id')->get(['id','image_url'])->toArray();
        return $snapshot;
    }

    public function fingerprint(array $snapshot): string { return hash('sha256', json_encode($snapshot, JSON_THROW_ON_ERROR)); }

    public function productPayload(Product $product): array
    {
        $data = $product->only(self::FIELDS);
        $data['requires_truck_set'] = $product->getRawOriginal('requires_truck') === null ? null : '1';
        $data['requires_truck'] = (bool)$product->getRawOriginal('requires_truck');
        foreach (['tags','sizes','colors','locations'] as $relation) $data[$relation] = $product->$relation()->pluck($relation.'.id')->map(fn($id)=>(string)$id)->all();
        $data['pickup_points']=$product->pickupPoints()->pluck('pickup_points.id')->all();
        $data['attr_groups'] = $product->custom_attribute_groups ?: [];
        $data['options'] = [];
        foreach ($product->custom_attributes ?? [] as $key=>$value) {
            if ($key !== 'tag_options' && is_scalar($value)) $data['options'][] = ['key'=>$key, 'value'=>(string)$value];
        }
        $data['new_tag_options_data'] = [];
        foreach (($product->custom_attributes ?? [])['tag_options'] ?? [] as $name=>$options) $data['new_tag_options_data'][] = json_encode(compact('name','options'));
        $data['publish_after_approval'] = true;
        return $data;
    }

    public function record(CatalogueRevision $revision, string $action, ?string $notes=null): void
    {
        CatalogueRevisionEvent::create(['revision_id'=>$revision->id,'actor_id'=>auth()->id(),'actor_name'=>auth()->user()?->name ?? 'System',
            'action'=>$action,'notes'=>$notes,'payload'=>$revision->payload]);
    }

    public function assertVersion(CatalogueRevision $revision, int $version): void
    {
        abort_unless($revision->version === $version, 409, 'This submission changed. Reload before continuing.');
    }

    public function review(int $id, int $version, string $decision, string $notes): CatalogueRevision
    {
        return DB::transaction(function () use ($id,$version,$decision,$notes) {
            $revision = CatalogueRevision::lockForUpdate()->findOrFail($id);
            $this->assertVersion($revision,$version);
            abort_unless($revision->status === 'pending',409,'Only pending submissions can be reviewed.');
            abort_if($revision->editor_id === auth()->id(),403,'You cannot approve your own submission.');
            if ($decision === 'approve') {
                $editor = $revision->editor;
                abort_unless($editor && in_array($editor->role,['catalogue_editor','manager']) && $editor->status !== 'banned',409,'The submitting staff account is no longer eligible.');
                $data = $revision->payload;
                $brand = Brand::lockForUpdate()->find($data['brand_id']);
                if (!$brand) throw ValidationException::withMessages(['brand_id'=>'The brand no longer exists. Return this submission for correction.']);
                if ($editor->role === 'manager') abort_unless($brand->manager_id === $editor->id,409,'Brand assignment changed. Return for correction.');
                $product = $revision->product_id ? Product::lockForUpdate()->find($revision->product_id) : new Product();
                abort_if(!$product,409,'The original product no longer exists.');
                if ($product->exists) {
                    abort_unless(hash_equals($revision->base_fingerprint ?? '',$this->fingerprint($this->snapshot($product))),409,'The product changed after this draft began. Return it for correction; the editor must refresh from the current product.');
                    if ($editor->role === 'manager') abort_unless($product->manager_id === $editor->id,409,'Product ownership changed.');
                }
                // Revalidate all references and values at approval time, not just when drafted.
                $validation = $data;
                $validation['description_blocks'] = isset($data['description_blocks']) ? json_encode($data['description_blocks']) : null;
                $validation += ['version'=>$revision->version,'intent'=>'submit'];
                $data = array_merge($data, app(CatalogueProductData::class)->validate(new \Illuminate\Http\Request($validation)));
                $product->fill(array_intersect_key($data,array_flip(self::FIELDS)));
                $product->brand = $brand->name; $product->manager_id = $brand->manager_id;
                $product->requires_truck = !empty($data['requires_truck_set']) ? (bool)($data['requires_truck'] ?? false) : null;
                $product->is_active = (bool)$data['publish_after_approval'];
                $product->save();
                $tags = [];
                foreach ($data['tags'] ?? [] as $input) {
                    $tag = ctype_digit((string)$input) ? Tag::findOrFail($input) : Tag::firstOrCreate(['slug'=>\Illuminate\Support\Str::slug(strip_tags($input))],['name'=>trim(strip_tags($input))]);
                    $tags[] = $tag->id;
                }
                $product->tags()->sync($tags);
                foreach (['sizes','colors'] as $relation) $product->$relation()->sync($data[$relation] ?? []);
                if(array_key_exists('locations',$data))$product->locations()->sync($data['locations']);
                $product->pickupPoints()->sync(\App\Services\ShoppingLocation::validateAssignments($data['locations']??[],$data['pickup_points']??[],(bool)$data['publish_after_approval']));
                foreach ($data['remove_images'] ?? [] as $imageId) $product->images()->whereKey($imageId)->delete();
                foreach ($revision->uploads()->where('kind','gallery')->whereNotIn('id',$data['remove_uploads'] ?? [])->get() as $upload) $product->images()->create(['image_url'=>$upload->path]);
                $revision->product_id = $product->id;
                $revision->status = 'approved';
            } else $revision->status = 'changes_requested';
            $revision->reviewer_id = auth()->id(); $revision->review_notes = $notes;
            $revision->reviewed_at = now(); $revision->version++; $revision->save();
            $this->record($revision,$revision->status,$notes);
            DB::afterCommit(fn()=>\App\Http\Controllers\UserDashboardController::clearHomepageCache());
            return $revision;
        });
    }
}
