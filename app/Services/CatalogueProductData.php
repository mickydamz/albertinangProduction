<?php
namespace App\Services;
use Illuminate\Http\Request;
class CatalogueProductData {
    public function validate(Request $request): array {
        $data = $request->validate([
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'name' => 'required|string|max:255', 'description' => 'nullable|string|max:10000',
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => [\Illuminate\Validation\Rule::requiredIf(fn () => \App\Models\Subcategory::where('category_id', $request->input('category_id'))->exists()), 'nullable', 'integer', \Illuminate\Validation\Rule::exists('subcategories', 'id')->where('category_id', $request->input('category_id'))],
            'price' => 'required|numeric|min:0.01|max:999999999',
            'stock' => 'required|integer|min:0|max:1000000', 'moq' => 'nullable|integer|min:1|max:1000000',
            'weight_kg' => 'nullable|numeric|min:0|max:99999',
            'images' => 'nullable|array|max:10', 'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'manager_id' => 'prohibited', 'supplier_id' => 'prohibited', 'brand' => 'prohibited',
                        'is_active' => 'prohibited', 'publish_after_approval' => 'required|boolean', 'version' => 'required|integer|min:0', 'intent' => 'required|in:draft,submit', 'remove_images' => 'nullable|array|max:100', 'remove_images.*' => 'integer', 'remove_uploads' => 'nullable|array|max:100', 'remove_uploads.*' => 'integer',
            'requires_truck_set' => 'nullable|in:1', 'requires_truck' => 'nullable|boolean',
            'markup_percent' => 'nullable|numeric|min:0|max:1000', 'discount_percent' => 'nullable|numeric|min:0|max:100',
            'tags' => 'nullable|array|max:50', 'tags.*' => 'required|string|max:255',
            'new_tag_options_data' => 'nullable|array|max:50', 'new_tag_options_data.*' => 'required|json|max:10000',
            'sizes' => 'nullable|array|max:100', 'sizes.*' => 'integer|exists:sizes,id',
            'colors' => 'nullable|array|max:100', 'colors.*' => 'integer|exists:colors,id',
            'pickup_points' => 'nullable|array|max:100', 'pickup_points.*' => 'integer|exists:pickup_points,id',
            'locations' => 'nullable|array|max:100', 'locations.*' => 'integer|exists:locations,id',
            'options' => 'nullable|array|max:200', 'options.*.key' => 'required|string|max:255', 'options.*.value' => 'nullable|string|max:2000',
            'attr_groups' => 'nullable|array|max:50', 'attr_groups.*.group' => 'required|string|max:255',
            'attr_groups.*.attrs' => 'nullable|array|max:200', 'attr_groups.*.attrs.*.key' => 'nullable|string|max:255',
            'attr_groups.*.attrs.*.value' => 'nullable|string|max:2000',
            'installation_options' => 'nullable|array|max:30', 'installation_options.*.label' => 'nullable|string|max:255',
            'installation_options.*.price' => 'nullable|numeric|min:0|max:999999999', 'installation_options.*.description' => 'nullable|string|max:500',
            'description_blocks' => 'nullable|json|max:200000',
        ]);
                $data['pickup_points'] = \App\Services\ShoppingLocation::validateAssignments($data['locations']??[], $data['pickup_points']??[], $request->input('intent')==='submit' && $request->boolean('publish_after_approval'));
                $flat = []; $groups = [];
                foreach ($data['options'] ?? [] as $option) {
                    $key = trim($option['key']); if ($key !== '') $flat[$key] = trim($option['value'] ?? '');
                }
                foreach ($data['attr_groups'] ?? [] as $group) {
                    $attributes = [];
                    foreach ($group['attrs'] ?? [] as $attr) {
                        $key = trim($attr['key'] ?? ''); if ($key === '') continue;
                        $value = trim($attr['value'] ?? ''); $attributes[] = ['key'=>$key,'value'=>$value]; $flat[$key] = $value;
                    }
                    $groups[] = ['group'=>trim($group['group']),'attrs'=>$attributes];
                }
                $tagNames = [];
                foreach ($data['tags'] ?? [] as $input) {
                    if (ctype_digit((string)$input)) $tag = \App\Models\Tag::findOrFail($input);
                    else {
                        $name = trim(strip_tags($input)); $slug = \Illuminate\Support\Str::slug($name);
                        if ($slug === '') throw \Illuminate\Validation\ValidationException::withMessages(['tags'=>'Enter a valid tag name.']);
                        $tag = new \App\Models\Tag(['name'=>$name,'slug'=>$slug]);
                    }
                    $tagNames[$tag->name] = true;
                }
                $tagOptions = [];
                foreach ($data['new_tag_options_data'] ?? [] as $json) {
                    $entry = json_decode($json, true);
                    if (!is_array($entry) || !isset($tagNames[$entry['name'] ?? '']) || !is_array($entry['options'] ?? null))
                        throw \Illuminate\Validation\ValidationException::withMessages(['tags'=>'Tag options must belong to a selected tag.']);
                    foreach ($entry['options'] as $value) if (!is_string($value) || strlen($value)>255)
                        throw \Illuminate\Validation\ValidationException::withMessages(['tags'=>'Invalid tag option.']);
                    $tagOptions[$entry['name']] = array_values($entry['options']);
                }
                if ($tagOptions) $flat['tag_options'] = $tagOptions;
                $installation = [];
                foreach ($data['installation_options'] ?? [] as $option) {
                    if (trim($option['label'] ?? '') === '') continue;
                    $installation[] = ['label'=>$option['label'],'price'=>(float)($option['price'] ?? 0),'description'=>$option['description'] ?? ''];
                }
                $blocks = empty($data['description_blocks']) ? null : json_decode($data['description_blocks'], true);
                if ($blocks !== null && (!is_array($blocks) || !array_is_list($blocks)))
                    throw \Illuminate\Validation\ValidationException::withMessages(['description_blocks'=>'Description blocks must be a list.']);
                $this->validateDescriptionBlocks($blocks ?? []);
                $data['moq'] = $data['moq'] ?? 1;
                $data['description'] = strip_tags($data['description'] ?? '');
                $data['custom_attributes']=$flat; $data['custom_attribute_groups']=$groups; $data['installation_options']=$installation; $data['description_blocks']=$blocks;
                unset($data['images'],$data['version'],$data['intent'],$data['remove_images'],$data['remove_uploads']);
                return $data;

    }
    private function validateDescriptionBlocks(array $nodes, int $depth = 0): void
    {
        if ($depth > 20) throw \Illuminate\Validation\ValidationException::withMessages(['description_blocks'=>'Description nesting is too deep.']);
        foreach ($nodes as $key => $value) {
            if (is_array($value)) $this->validateDescriptionBlocks($value, $depth+1);
            elseif ($key === 'url' && is_string($value) && $value !== '' && !preg_match('~^(https?://|/(?!/)|\#)~i', $value))
                throw \Illuminate\Validation\ValidationException::withMessages(['description_blocks'=>'Use an HTTP(S) or local URL for description links and images.']);
        }
    }

}
