@extends('layouts.adminlayout')

@section('content')
<!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">-->

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                        <h2 class="content-header-title mb-0">Edit Product</h2>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
                                <li class="breadcrumb-item active">Edit Product</li>
                            </ol>
            </div>
        </div>
        <div class="content-body">
            <section id="edit-product">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Edit Product: {{ $product->name }}</div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <form id="product-form" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-1">
                                        <label for="name" class="form-label">Product Name</label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="moq" class="form-label">Minimum Order Quantity (MOQ)</label>
                                        <input type="text" name="moq" id="moq" class="form-control @error('moq') is-invalid @enderror" value="{{ old('moq', $product->moq) }}">
                                        @error('moq')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" required rows="10">{{ old('description', $product->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="price" class="form-label">Price</label>
                                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('ublin, Irelandprice', $product->price) }}" step="0.01" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="stock" class="form-label">Stock</label>
                                        <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" required>
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                                            <option value="">Select a category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="subcategory_id" class="form-label">Subcategory</label>
                                        <select name="subcategory_id" id="subcategory_id" class="form-control @error('subcategory_id') is-invalid @enderror">
                                            <option value="">Select a Subcategory</option>
                                        </select>
                                        @error('subcategory_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-1">
                                        <label for="brand" class="form-label">Brand</label>
                                        <div class="brand-input-container form-control">
                                            <div id="selected-brands" class="d-flex flex-wrap align-items-center mb-1"></div>
                                            <input type="text" id="brand-input-field" placeholder="Search and select existing brands...">
                                        </div>
                                        <div id="brand-suggestions" class="tag-suggestions"></div>
                                        @error('brand')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!--<div class="mb-2">-->
                                    <!--    <label class="form-label">Available Brands</label>-->
                                    <!--    <div id="available-brands-list" class="d-flex flex-wrap gap-1"></div>-->
                                    <!--</div>-->

                                    <div class="mb-1">
                                        <label for="tag-input-field" class="form-label">Tags (e.g., Color:Red,Blue or Size)</label>
                                        <div class="tag-input-container form-control">
                                            <div id="selected-tags" class="d-flex flex-wrap align-items-center mb-1"></div>
                                            <input type="text" id="tag-input-field" placeholder="Search and select existing tags or type new ones...">
                                        </div>
                                        <div id="tag-suggestions" class="tag-suggestions"></div>
                                        @error('tags')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Available Tags</label>
                                        <div id="available-tags-list" class="d-flex flex-wrap gap-1"></div>
                                    </div>

                                    <div class="mb-1">
                                        <label for="size-input-field" class="form-label">Sizes</label>
                                        <div class="size-input-container form-control">
                                            <div id="selected-sizes" class="d-flex flex-wrap align-items-center mb-1"></div>
                                            <input type="text" id="size-input-field" placeholder="Search and select existing sizes...">
                                        </div>
                                        <div id="size-suggestions" class="tag-suggestions"></div>
                                        @error('sizes')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Available Sizes</label>
                                        <div id="available-sizes-list" class="d-flex flex-wrap gap-1"></div>
                                    </div>

                                    <div class="mb-1">
                                        <label for="color-input-field" class="form-label">Colors</label>
                                        <div class="color-input-container form-control">
                                            <div id="selected-colors" class="d-flex flex-wrap align-items-center mb-1"></div>
                                            <input type="text" id="color-input-field" placeholder="Search and select existing colors...">
                                        </div>
                                        <div id="color-suggestions" class="tag-suggestions"></div>
                                        @error('colors')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Available Colors</label>
                                        <div id="available-colors-list" class="d-flex flex-wrap gap-1"></div>
                                    </div>

                                    <div class="mb-1">
                                        <label for="location-input-field" class="form-label">Locations</label>
                                        <div class="location-input-container form-control">
                                            <div id="selected-locations" class="d-flex flex-wrap align-items-center mb-1"></div>
                                            <input type="text" id="location-input-field" placeholder="Search and select existing locations...">
                                        </div>
                                        <div id="location-suggestions" class="tag-suggestions"></div>
                                        @error('locations')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label">Available Locations</label>
                                        <div id="available-locations-list" class="d-flex flex-wrap gap-1"></div>
                                    </div>
                                    
                                    <div class="mb-1">
                                        <label for="rating" class="form-label">Rating</label>
                                        <div class="star-rating" id="rating">
                                            <span class="fa fa-star" data-value="1"></span>
                                            <span class="fa fa-star" data-value="2"></span>
                                            <span class="fa fa-star" data-value="3"></span>
                                            <span class="fa fa-star" data-value="4"></span>
                                            <span class="fa fa-star" data-value="5"></span>
                                        </div>
                                        <input type="hidden" name="rating" id="rating-value" value="{{ old('rating', $product->rating) }}" required>
                                        @error('rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-1">
                                        <label for="images" class="form-label">Upload Images</label>
                                        <input type="file" name="images[]" id="images" class="form-control @error('images') is-invalid @enderror" multiple accept="image/jpeg,image/png,image/jpg,image/gif">
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="row mt-2" id="image-previews">
                                            @foreach ($product->images as $image)
                                                <div class="col-md-3 col-sm-4 col-6 mb-2 preview-container position-relative" data-image-id="{{ $image->id }}">
                                                    <img src="{{ Storage::url($image->image_url) }}" class="img-fluid rounded small-image" alt="Product Image">
                                                    <button type="button" class="btn-remove-image" data-image-id="{{ $image->id }}">×</button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="removed_images" id="removed_images" value="">
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Custom Attributes</label>
                                        <div id="options-container">
                                            @php
                                                // Merge old input with existing custom attributes, removing duplicates
                                                $currentOptions = [];
                                                $oldOptions = old('options', []);
                                                $existingAttributes = $customAttributes ?? [];
                                                // Remove tag_options from existing attributes
                                                if (isset($existingAttributes['tag_options'])) {
                                                    unset($existingAttributes['tag_options']);
                                                }
                                                // Convert existing attributes to key-value array
                                                foreach ($existingAttributes as $key => $value) {
                                                    $currentOptions[$key] = ['key' => $key, 'value' => is_array($value) ? json_encode($value) : $value];
                                                }
                                                // Override with old input if present
                                                if (!empty($oldOptions)) {
                                                    foreach ($oldOptions as $index => $option) {
                                                        if (!empty($option['key'])) {
                                                            $currentOptions[$option['key']] = ['key' => $option['key'], 'value' => $option['value'] ?? ''];
                                                        }
                                                    }
                                                }
                                                // Convert to indexed array for iteration
                                                $currentOptions = array_values($currentOptions);
                                            @endphp
                                            @forelse ($currentOptions as $index => $option)
                                                <div class="row mb-1 option-item">
                                                    <div class="col-5 position-relative">
                                                        <input type="text" name="options[{{ $index }}][key]" class="form-control option-key-input" placeholder="Attribute Key" value="{{ $option['key'] }}" />
                                                        <div class="option-suggestions" data-for="key"></div>
                                                    </div>
                                                    <div class="col-5 position-relative">
                                                        <input type="text" name="options[{{ $index }}][value]" class="form-control option-value-input" placeholder="Attribute Value" value="{{ $option['value'] }}" />
                                                        <div class="option-suggestions" data-for="value"></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" class="btn btn-outline-danger remove-option-btn"><i class="fas fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="row mb-1 option-item">
                                                    <div class="col-5 position-relative">
                                                        <input type="text" name="options[0][key]" class="form-control option-key-input" placeholder="Attribute Key" />
                                                        <div class="option-suggestions" data-for="key"></div>
                                                    </div>
                                                    <div class="col-5 position-relative">
                                                        <input type="text" name="options[0][value]" class="form-control option-value-input" placeholder="Attribute Value" />
                                                        <div class="option-suggestions" data-for="value"></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" class="btn btn-outline-danger remove-option-btn"><i class="fas fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                        <button type="button" id="add-option-btn" class="btn btn-outline-primary btn-sm mt-1">
                                            <i class="fas fa-plus me-50"></i> Add Attribute
                                        </button>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Product</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade" id="editTagOptionsModal" tabindex="-1" aria-labelledby="editTagOptionsModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTagOptionsModalLabel">Edit Tag Options</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="modal-tag-name" class="form-label">Tag Name</label>
                                <input type="text" id="modal-tag-name" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="modal-tag-options" class="form-label">Options (comma-separated)</label>
                                <input type="text" id="modal-tag-options" class="form-control" placeholder="e.g., Red,Blue,Green">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="save-tag-options">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('#rating .fa-star');
        const ratingValueInput = document.getElementById('rating-value');
        const categorySelect = document.getElementById('category_id');
        const SubcategorySelect = document.getElementById('subcategory_id');
        const imageInput = document.getElementById('images');
        const imagePreviewsContainer = document.getElementById('image-previews');
        const removedImagesInput = document.getElementById('removed_images');
        let removedImageIds = [];
        const tagInputField = document.getElementById('tag-input-field');
        const selectedTagsContainer = document.getElementById('selected-tags');
        const tagSuggestionsContainer = document.getElementById('tag-suggestions');
        const availableTagsList = document.getElementById('available-tags-list');
        const sizeInputField = document.getElementById('size-input-field');
        const selectedSizesContainer = document.getElementById('selected-sizes');
        const sizeSuggestionsContainer = document.getElementById('size-suggestions');
        const availableSizesList = document.getElementById('available-sizes-list');
        const colorInputField = document.getElementById('color-input-field');
        const selectedColorsContainer = document.getElementById('selected-colors');
        const colorSuggestionsContainer = document.getElementById('color-suggestions');
        const availableColorsList = document.getElementById('available-colors-list');
        const locationInputField = document.getElementById('location-input-field');
        const selectedLocationsContainer = document.getElementById('selected-locations');
        const locationSuggestionsContainer = document.getElementById('location-suggestions');
        const availableLocationsList = document.getElementById('available-locations-list');
        const addOptionButton = document.getElementById('add-option-btn');
        const optionsContainer = document.getElementById('options-container');
        const productForm = document.getElementById('product-form');
        const brandInputField = document.getElementById('brand-input-field');
        const selectedBrandsContainer = document.getElementById('selected-brands');
        const brandSuggestionsContainer = document.getElementById('brand-suggestions');
        const availableBrandsList = document.getElementById('available-brands-list');

        const categories = @json($categories);
        const allTags = @json($tags);
        const allSizes = @json($sizes);
        const allColors = @json($colors);
        const allLocations = @json($locations);
        let selectedTags = @json($selectedTags).map(tag => ({
            id: tag.id,
            name: tag.name,
            isNew: tag.isNew,
            customOptions: tag.customOptions || [tag.name]
        }));
        let selectedSizes = new Set(@json($product->sizes->pluck('id')));
        let selectedColors = new Set(@json($product->colors->pluck('id')));
        let selectedLocations = new Set(@json($product->locations->pluck('id')));
        let selectedBrands = new Set([@json(old('brand', $product->brand))]);

       const allBrands = [
    { id: 1, name: 'Apple' },
    { id: 2, name: 'Samsung' },
    { id: 3, name: 'Sony' },
    { id: 4, name: 'LG' },
    { id: 5, name: 'Microsoft' },
    { id: 6, name: 'Dell' },
    { id: 7, name: 'HP' },
    { id: 8, name: 'Lenovo' },
    { id: 9, name: 'Asus' },
    { id: 10, name: 'Acer' },
    { id: 11, name: 'Toshiba' },
    { id: 12, name: 'Panasonic' },
    { id: 13, name: 'Philips' },
    { id: 14, name: 'Bose' },
    { id: 15, name: 'JBL' },
    { id: 16, name: 'TCL' },
    { id: 17, name: 'Binatone' },
    { id: 18, name: 'Intel' },
    { id: 19, name: 'AMD' },
    { id: 20, name: 'NVIDIA' },
    { id: 21, name: 'Qualcomm' },
    { id: 22, name: 'Canon' },
    { id: 23, name: 'Nikon' },
    { id: 24, name: 'Epson' },
    { id: 25, name: 'Brother' },
    { id: 26, name: 'Xiaomi' },
    { id: 27, name: 'Oppo' },
    { id: 28, name: 'Vivo' },
    { id: 29, name: 'OnePlus' },
    { id: 30, name: 'Huawei' },
    { id: 31, name: 'Razer' },
    { id: 32, name: 'Logitech' },
    { id: 33, name: 'Corsair' },
    { id: 34, name: 'SteelSeries' },
    { id: 35, name: 'HyperX' },
    { id: 36, name: 'GoPro' },
    { id: 37, name: 'Fitbit' },
    { id: 38, name: 'Garmin' },
    { id: 39, name: 'Dyson' },
    { id: 40, name: 'Shark' },
    { id: 41, name: 'Whirlpool' },
    { id: 42, name: 'Bosch' },
    { id: 43, name: 'Siemens' },
    { id: 44, name: 'Casio' },
    { id: 45, name: 'Seiko' },
    { id: 46, name: 'Hisense' },
    { id: 47, name: 'Vizio' },
    { id: 48, name: 'Sharp' },
    { id: 49, name: 'Pioneer' },
    { id: 50, name: 'Denon' },
    { id: 51, name: 'Marantz' },
    { id: 52, name: 'Bang & Olufsen' },
    { id: 53, name: 'Sennheiser' },
    { id: 54, name: 'Beyerdynamic' },
    { id: 55, name: 'Alpine' },
    { id: 56, name: 'Fujitsu' },
    { id: 57, name: 'Hitachi' },
    { id: 58, name: 'Kyocera' },
    { id: 59, name: 'Mitsubishi Electric' },
    { id: 60, name: 'NEC' },
    { id: 61, name: 'Oki' },
    { id: 62, name: 'Ricoh' },
    { id: 63, name: 'Sanyo' },
    { id: 64, name: 'JVC' },
    { id: 65, name: 'Kenwood' },
    { id: 66, name: 'Konica Minolta' },
    { id: 67, name: 'TDK' },
    { id: 68, name: 'Eizo' },
    { id: 69, name: 'Fujifilm' },
    { id: 70, name: 'Olympus' },
    { id: 71, name: 'Pentax' },
    { id: 72, name: 'Haier' },
    { id: 73, name: 'ZTE' },
    { id: 74, name: 'Meizu' },
    { id: 75, name: 'Realme' },
    { id: 76, name: 'Gionee' },
    { id: 77, name: 'Aigo' },
    { id: 78, name: 'Amoi' },
    { id: 79, name: 'BYD Electronic' },
    { id: 80, name: 'Changhong' },
    { id: 81, name: 'Hasee' },
    { id: 82, name: 'Konka Group' },
    { id: 83, name: 'Ningbo Bird' },
    { id: 84, name: 'Skyworth' },
    { id: 85, name: 'TP-Link' },
    { id: 86, name: 'Intex' },
    { id: 87, name: 'Walton' },
    { id: 88, name: 'Rangs' },
    { id: 89, name: 'Transcom' },
    { id: 90, name: 'BMTF' },
    { id: 91, name: 'Doel' },
    { id: 92, name: 'Jamuna' },
    { id: 93, name: 'Amkette' },
    { id: 94, name: 'Beetel' },
    { id: 95, name: 'Bharat Electronics' },
    { id: 96, name: 'BPL' },
    { id: 97, name: 'Celkon' },
    { id: 98, name: 'Godrej' },
    { id: 99, name: 'HCL' },
    { id: 100, name: 'Havells' },
    { id: 101, name: 'iBall' },
    { id: 102, name: 'Karbonn' },
    { id: 103, name: 'Micromax' },
    { id: 104, name: 'Moser Baer' },
    { id: 105, name: 'Notion Ink' },
    { id: 106, name: 'Onida' },
    { id: 107, name: 'Surya Roshni Limited' },
    { id: 108, name: 'Simmtronics' },
    { id: 109, name: 'Sterlite Technologies' },
    { id: 110, name: 'Videocon' },
    { id: 111, name: 'Videotex' },
    { id: 112, name: 'Wipro' },
    { id: 113, name: 'Advan' },
    { id: 114, name: 'Maspion' },
    { id: 115, name: 'Nexian' },
    { id: 116, name: 'Polytron' },
    { id: 117, name: 'Maadiran Group' },
    { id: 118, name: 'Snowa' },
    { id: 119, name: 'Allied Telesis' },
    { id: 120, name: 'Buffalo' },
    { id: 121, name: 'Clarion' },
    { id: 122, name: 'Funai' },
    { id: 123, name: 'Icom' },
    { id: 124, name: 'Iiyama' },
    { id: 125, name: 'Nintendo' },
    { id: 126, name: 'SII' },
    { id: 127, name: 'SNK Corporation' },
    { id: 128, name: 'Yaesu' },
    { id: 129, name: 'Cowon' },
    { id: 130, name: 'Daewoo Electronics' },
    { id: 131, name: 'Hansol' },
    { id: 132, name: 'Iriver' },
    { id: 133, name: 'Pantech' },
    { id: 134, name: 'SK Hynix' },
    { id: 135, name: 'Sindoh' },
    { id: 136, name: 'Humax' },
    { id: 137, name: 'Aftershock' },
    { id: 138, name: 'Creative' },
    { id: 139, name: 'PRISM+' },
    { id: 140, name: 'AOC' },
    { id: 141, name: 'Aopen' },
    { id: 142, name: 'BenQ' },
    { id: 143, name: 'D-Link' },
    { id: 144, name: 'ECS' },
    { id: 145, name: 'Elsa' },
    { id: 146, name: 'EPoX' },
    { id: 147, name: 'Foxconn' },
    { id: 148, name: 'Gigabyte' },
    { id: 149, name: 'HTC' },
    { id: 150, name: 'Lite-On' },
    { id: 151, name: 'MediaTek' },
    { id: 152, name: 'MSI' },
    { id: 153, name: 'Realtek' },
    { id: 154, name: 'Silicon Power' },
    { id: 155, name: 'Soyo' },
    { id: 156, name: 'Surya' },
    { id: 157, name: 'Transcend' },
    { id: 158, name: 'TSMC' },
    { id: 159, name: 'VIA Technologies' },
    { id: 160, name: 'Samart' },
    { id: 161, name: 'True' },
    { id: 162, name: 'Arçelik' },
    { id: 163, name: 'ASELSAN' },
    { id: 164, name: 'Beko' },
    { id: 165, name: 'Canovate' },
    { id: 166, name: 'Vestel' },
    { id: 167, name: 'Nokia' },
    { id: 168, name: 'Alcatel-Lucent' },
    { id: 169, name: 'Thomson Broadcast' },
    { id: 170, name: 'Blaupunkt' },
    { id: 171, name: 'Braun' },
    { id: 172, name: 'Gigaset' },
    { id: 173, name: 'Grundig' },
    { id: 174, name: 'Loewe' },
    { id: 175, name: 'Medion' },
    { id: 176, name: 'Miele' },
    { id: 177, name: 'Severin Elektro' },
    { id: 178, name: 'TechniSat' },
    { id: 179, name: 'Telefunken' },
    { id: 180, name: 'Teufel' },
    { id: 181, name: 'Wortmann' },
    { id: 182, name: 'KONČAR Group' },
    { id: 183, name: 'Orion' },
    { id: 184, name: 'Videoton' },
    { id: 185, name: 'Brionvega' },
    { id: 186, name: 'Brondi' },
    { id: 187, name: 'Cinemeccanica' },
    { id: 188, name: 'Eurotech' },
    { id: 189, name: 'Olivetti' },
    { id: 190, name: 'Radio Marconi' },
    { id: 191, name: 'Luxafor' },
    { id: 192, name: 'Kongsberg Gruppen' },
    { id: 193, name: 'Nordic Semiconductor' },
    { id: 194, name: 'Almaz-Antey' },
    { id: 195, name: 'Angstrem' },
    { id: 196, name: 'General Satellite' },
    { id: 197, name: 'MCST' },
    { id: 198, name: 'NPO Digital Television Systems' },
    { id: 199, name: 'Rovercomputers' },
    { id: 200, name: 'Sitronics' },
    { id: 201, name: 'Sozvezdie' },
    { id: 202, name: 'Yota' },
    { id: 203, name: 'Gorenje' },
    { id: 204, name: 'Electrolux' },
    { id: 205, name: 'Ericsson' },
    { id: 206, name: 'Husqvarna' },
    { id: 207, name: 'Revox' },
    { id: 208, name: 'EKTA' },
    { id: 209, name: 'Alfa' },
    { id: 210, name: 'Kyoto Electronics' },
    { id: 211, name: 'Lanix' },
    { id: 212, name: 'Mabe' },
    { id: 213, name: 'Meebox' },
    { id: 214, name: 'Satmex' },
    { id: 215, name: 'Zonda' },
    { id: 216, name: '3M' },
    { id: 217, name: 'Alienware' },
    { id: 218, name: 'Amazon' },
    { id: 219, name: 'Analog Devices' },
    { id: 220, name: 'Audiovox' },
    { id: 221, name: 'Avaya' },
    { id: 222, name: 'Averatec' },
    { id: 223, name: 'Cisco' },
    { id: 224, name: 'Crucial Technology' },
    { id: 225, name: 'eMachines' },
    { id: 226, name: 'Emerson Electric' },
    { id: 227, name: 'Emerson Radio' },
    { id: 228, name: 'Gateway' },
    { id: 229, name: 'Google' },
    { id: 230, name: 'Hewlett-Packard' },
    { id: 231, name: 'IBM' },
    { id: 232, name: 'Kingston' },
    { id: 233, name: 'Koss' },
    { id: 234, name: 'Magnavox' },
    { id: 235, name: 'Micron Technology' },
    { id: 236, name: 'Motorola Mobility' },
    { id: 237, name: 'Motorola Solutions' },
    { id: 238, name: 'Packard Bell' },
    { id: 239, name: 'Plantronics' },
    { id: 240, name: 'Polycom' },
    { id: 241, name: 'RCA' },
    { id: 242, name: 'Sandisk' },
    { id: 243, name: 'Seagate' },
    { id: 244, name: 'SGI' },
    { id: 245, name: 'Sonos' },
    { id: 246, name: 'Texas Instruments' },
    { id: 247, name: 'Unisonic Products Corporation' },
    { id: 248, name: 'Unisys' },
    { id: 249, name: 'Viewsonic' },
    { id: 250, name: 'Western Digital' },
    { id: 251, name: 'Westinghouse Electric Corporation' },
    { id: 252, name: 'Xerox' },
    { id: 253, name: 'Zenith' },
    { id: 254, name: 'A.G. Healing' },
    { id: 255, name: 'ADInstruments' },
    { id: 256, name: 'Amalgamated Wireless' },
    { id: 257, name: 'Blackmagic Design' },
    { id: 258, name: 'CEA Technologies' },
    { id: 259, name: 'Codan' },
    { id: 260, name: 'Dynalite' },
    { id: 261, name: 'Fairlight' },
    { id: 262, name: 'PowerLab' },
    { id: 263, name: 'Q-MAC Electronics' },
    { id: 264, name: 'Radio Rentals' },
    { id: 265, name: 'Redarc Electronics' },
    { id: 266, name: 'Røde Microphones' },
    { id: 267, name: 'Telectronics' },
    { id: 268, name: 'Vix Technology' },
    { id: 269, name: 'Winradio' },
    { id: 270, name: 'AeroDreams' },
    { id: 271, name: 'Cicaré' },
    { id: 272, name: 'CITEFA' },
    { id: 273, name: 'FAdeA' },
    { id: 274, name: 'INVAP' },
    { id: 275, name: 'Nostromo' },
    { id: 276, name: 'Avibras' },
    { id: 277, name: 'Embraer' },
    { id: 278, name: 'Gradiente' },
    { id: 279, name: 'Itautec' },
    { id: 280, name: 'Mectron' },
    { id: 281, name: 'Positivo Informatica' },
    { id: 282, name: 'WEG Industries' },
    { id: 283, name: 'Indumil' },
    { id: 284, name: 'Siragon' },
    { id: 285, name: 'VIT' }
];

        // Handle old input after validation errors for tags
        @if (old('tags'))
            selectedTags = @json(old('tags', [])).map(tagInput => {
                if (typeof tagInput === 'number') {
                    const tag = allTags.find(t => t.id === tagInput);
                    return tag ? { id: tag.id, name: tag.name, isNew: false, customOptions: [tag.name] } : null;
                } else if (typeof tagInput === 'string') {
                    const existingTag = allTags.find(tag => tag.name.toLowerCase() === tagInput.toLowerCase());
                    if (existingTag) {
                        const oldNewTagData = @json(old('new_tag_options_data', []));
                        const newTagOption = oldNewTagData.find(item => item.name === existingTag.name);
                        return {
                            id: existingTag.id,
                            name: existingTag.name,
                            isNew: false,
                            customOptions: newTagOption && Array.isArray(newTagOption.options) ? newTagOption.options : [existingTag.name]
                        };
                    } else {
                        const oldNewTagData = @json(old('new_tag_options_data', []));
                        const newTagOption = oldNewTagData.find(item => item.name === tagInput);
                        return {
                            name: tagInput,
                            isNew: true,
                            customOptions: newTagOption && Array.isArray(newTagOption.options) ? newTagOption.options : [tagInput]
                        };
                    }
                }
                return null;
            }).filter(Boolean);
        @endif

        // Star Rating
        const initialRating = ratingValueInput.value || 0;
        if (initialRating > 0) {
            stars.forEach(s => {
                if (parseFloat(s.getAttribute('data-value')) <= initialRating) {
                    s.classList.add('text-warning');
                    s.classList.remove('text-muted');
                } else {
                    s.classList.add('text-muted');
                    s.classList.remove('text-warning');
                }
            });
        } else {
            stars.forEach(s => {
                s.classList.add('text-muted');
                s.classList.remove('text-warning');
            });
        }

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const ratingValue = this.getAttribute('data-value');
                ratingValueInput.value = ratingValue;
                stars.forEach(s => {
                    if (parseFloat(s.getAttribute('data-value')) <= ratingValue) {
                        s.classList.add('text-warning');
                        s.classList.remove('text-muted');
                    } else {
                        s.classList.add('text-muted');
                        s.classList.remove('text-warning');
                    }
                });
            });
        });

        // Category and Subcategory
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            SubcategorySelect.innerHTML = '<option value="">Select a Subcategory</option>';

            if (categoryId) {
                const selectedCategory = categories.find(category => category.id == categoryId);
                if (selectedCategory && selectedCategory.subcategories) {
                    selectedCategory.subcategories.forEach(Subcategory => {
                        const option = document.createElement('option');
                        option.value = Subcategory.id;
                        option.textContent = Subcategory.name;
                        if (Subcategory.id == '{{ old('subcategory_id', $product->subcategory_id) }}') {
                            option.selected = true;
                        }
                        SubcategorySelect.appendChild(option);
                    });
                }
            }
        });

        if (categorySelect.value) {
            categorySelect.dispatchEvent(new Event('change'));
        }

        // Image Upload and Removal
        imageInput.addEventListener('change', function(event) {
            const files = event.target.files;
            imagePreviewsContainer.innerHTML = '';

            for (let file of files) {
                if (!file.type.startsWith('image/')) {
                    const messageBox = document.createElement('div');
                    messageBox.classList.add('alert', 'alert-danger', 'mt-2');
                    messageBox.textContent = `File ${file.name} is not a valid image.`;
                    imagePreviewsContainer.before(messageBox);
                    setTimeout(() => messageBox.remove(), 3000);
                    continue;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-fluid', 'rounded', 'small-image');
                    const div = document.createElement('div');
                    div.classList.add('col-md-3', 'col-sm-4', 'col-6', 'mb-2', 'preview-container', 'position-relative');
                    div.appendChild(img);
                    imagePreviewsContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            }

            // Re-render existing images
            @foreach ($product->images as $image)
                if (!removedImageIds.includes({{ $image->id }})) {
                    const div = document.createElement('div');
                    div.classList.add('col-md-3', 'col-sm-4', 'col-6', 'mb-2', 'preview-container', 'position-relative');
                    div.setAttribute('data-image-id', {{ $image->id }});
                    div.innerHTML = `
                        <img src="{{ Storage::url($image->image_url) }}" class="img-fluid rounded small-image" alt="Product Image">
                        <button type="button" class="btn-remove-image" data-image-id="{{ $image->id }}">×</button>
                    `;
                    imagePreviewsContainer.appendChild(div);
                }
            @endforeach

            // Attach event listeners to remove buttons
            imagePreviewsContainer.querySelectorAll('.btn-remove-image').forEach(button => {
                button.addEventListener('click', function() {
                    const imageId = this.getAttribute('data-image-id');
                    removedImageIds.push(parseInt(imageId));
                    removedImagesInput.value = removedImageIds.join(',');
                    const previewContainer = this.closest('.preview-container');
                    if (previewContainer) {
                        previewContainer.remove();
                    }
                });
            });
        });

        // Trigger initial image rendering
        imageInput.dispatchEvent(new Event('change'));

        // Brand Functionality
        function renderSelectedBrands() {
            selectedBrandsContainer.innerHTML = '';
            selectedBrandsContainer.querySelectorAll('input[name="brand"]').forEach(input => input.remove());

            selectedBrands.forEach(brandName => {
                if (brandName) {
                    const brandPill = document.createElement('span');
                    brandPill.classList.add('badge', 'bg-primary', 'me-1', 'mb-1', 'd-flex', 'align-items-center', 'tag-pill');
                    brandPill.innerHTML = `
                        ${brandName}
                        <button type="button" class="btn-close btn-close-white ms-1" aria-label="Remove" data-brand-name="${brandName}"></button>
                    `;
                    brandPill.querySelector('.btn-close').addEventListener('click', function() {
                        removeBrand(brandName);
                    });
                    selectedBrandsContainer.appendChild(brandPill);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'brand';
                    hiddenInput.value = brandName;
                    selectedBrandsContainer.appendChild(hiddenInput);
                }
            });
            renderAvailableBrands();
        }

        function renderAvailableBrands() {
            availableBrandsList.innerHTML = '';
            allBrands.forEach(brand => {
                if (!selectedBrands.has(brand.name)) {
                    const availableBrandPill = document.createElement('span');
                    availableBrandPill.classList.add('badge', 'bg-secondary', 'me-1', 'mb-1', 'available-tag-pill');
                    availableBrandPill.textContent = brand.name;
                    availableBrandPill.setAttribute('data-brand-id', brand.id);
                    availableBrandPill.setAttribute('data-brand-name', brand.name);
                    availableBrandPill.addEventListener('click', function() {
                        addBrand(brand.name);
                    });
                    availableBrandsList.appendChild(availableBrandPill);
                }
            });
        }

        function addBrand(brandName) {
            if (!selectedBrands.has(brandName)) {
                selectedBrands.clear(); // Ensure only one brand is selected
                selectedBrands.add(brandName);
                renderSelectedBrands();
            }
            brandInputField.value = '';
            brandSuggestionsContainer.innerHTML = '';
        }

        function removeBrand(brandName) {
            selectedBrands.delete(brandName);
            renderSelectedBrands();
        }

        brandInputField.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            brandSuggestionsContainer.innerHTML = '';

            if (query.length > 0) {
                const filteredBrands = allBrands.filter(brand => 
                    brand.name.toLowerCase().includes(query) && !selectedBrands.has(brand.name)
                );

                filteredBrands.forEach(brand => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.classList.add('tag-suggestion-item');
                    suggestionItem.textContent = brand.name;
                    suggestionItem.addEventListener('click', () => {
                        addBrand(brand.name);
                    });
                    brandSuggestionsContainer.appendChild(suggestionItem);
                });
            }
        });

        brandInputField.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const typedBrandName = this.value.trim().toLowerCase();
                if (typedBrandName.length > 0) {
                    const exactMatchBrand = allBrands.find(brand => brand.name.toLowerCase() === typedBrandName);
                    if (exactMatchBrand) {
                        addBrand(exactMatchBrand.name);
                    }
                }
            }
        });

        // Tag Functionality
        function addTag(tagObject) {
            const exists = selectedTags.some(selected =>
                (selected.id && selected.id === tagObject.id) ||
                (selected.name && selected.name.toLowerCase() === tagObject.name.toLowerCase())
            );

            if (!exists) {
                selectedTags.push(tagObject);
                renderSelectedTags();
            }
            tagInputField.value = '';
            tagSuggestionsContainer.innerHTML = '';
        }

        function removeTag(identifier, isNew) {
            selectedTags = selectedTags.filter(tag => {
                if (isNew === 'true') {
                    return tag.name.toLowerCase() !== identifier.toLowerCase();
                } else {
                    return tag.id !== parseInt(identifier);
                }
            });
            renderSelectedTags();
        }

        function renderSelectedTags() {
            selectedTagsContainer.innerHTML = '';
            document.querySelectorAll('input[name="tags[]"]').forEach(input => input.remove());
            document.querySelectorAll('input[name="new_tag_options_data[]"]').forEach(input => input.remove());

            selectedTags.forEach(tag => {
                const tagPill = document.createElement('span');
                tagPill.classList.add('badge', 'bg-primary', 'me-1', 'mb-1', 'd-flex', 'align-items-center', 'tag-pill');
                
                let pillText = tag.name;
                if (tag.customOptions && tag.customOptions.length > 0 && !(tag.customOptions.length === 1 && tag.customOptions[0].toLowerCase() === tag.name.toLowerCase())) {
                    pillText += `: ${tag.customOptions.join(', ')}`;
                }

                tagPill.innerHTML = `
                    ${pillText}
                    <button type="button" class="btn-close btn-close-white ms-1" aria-label="Remove" data-tag-identifier="${tag.isNew ? tag.name : tag.id}" data-is-new="${!!tag.isNew}"></button>
                    <button type="button" class="btn-edit-options ms-1" data-tag-name="${tag.name}" data-tag-options="${tag.customOptions ? tag.customOptions.join(',') : ''}">Edit Options</button>
                `;
                tagPill.querySelector('.btn-close').addEventListener('click', function() {
                    removeTag(this.dataset.tagIdentifier, this.dataset.isNew);
                });

                const editButton = tagPill.querySelector('.btn-edit-options');
                if (editButton) {
                    editButton.addEventListener('click', function() {
                        const tagName = this.dataset.tagName;
                        const tagOptions = this.dataset.tagOptions;
                        document.getElementById('modal-tag-name').value = tagName;
                        document.getElementById('modal-tag-options').value = tagOptions;

                        const modal = new bootstrap.Modal(document.getElementById('editTagOptionsModal'));
                        modal.show();

                        document.getElementById('save-tag-options').onclick = function() {
                            const newOptions = document.getElementById('modal-tag-options').value
                                .split(',')
                                .map(opt => opt.trim())
                                .filter(opt => opt.length > 0);
                            const tagIndex = selectedTags.findIndex(t => t.name === tagName);
                            if (tagIndex !== -1) {
                                selectedTags[tagIndex].customOptions = newOptions.length > 0 ? newOptions : [tagName];
                                renderSelectedTags();
                            }
                            modal.hide();
                        };
                    });
                }

                selectedTagsContainer.appendChild(tagPill);

                const hiddenTagInput = document.createElement('input');
                hiddenTagInput.type = 'hidden';
                hiddenTagInput.name = 'tags[]';
                hiddenTagInput.value = tag.isNew ? tag.name : tag.id;
                selectedTagsContainer.appendChild(hiddenTagInput);

                if (tag.customOptions && tag.customOptions.length > 0) {
                    const hiddenOptionsInput = document.createElement('input');
                    hiddenOptionsInput.type = 'hidden';
                    hiddenOptionsInput.name = 'new_tag_options_data[]';
                    hiddenOptionsInput.value = JSON.stringify({ name: tag.name, options: tag.customOptions });
                    selectedTagsContainer.appendChild(hiddenOptionsInput);
                }
            });
            renderAvailableTags();
        }

        tagInputField.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const typedInput = this.value.trim();
                if (typedInput.length === 0) return;

                let tagName;
                let customOptions = [];

                if (typedInput.includes(':')) {
                    const parts = typedInput.split(':', 2);
                    tagName = parts[0].trim();
                    const optionString = parts[1].trim();
                    customOptions = optionString.split(',').map(opt => opt.trim()).filter(opt => opt.length > 0);

                    if (tagName.length === 0) {
                        alert('Tag name cannot be empty when specifying options.');
                        return;
                    }
                    if (customOptions.length === 0) {
                        customOptions = [tagName];
                    }
                } else {
                    tagName = typedInput;
                    customOptions = [tagName];
                }

                const existingTag = allTags.find(tag => tag.name.toLowerCase() === tagName.toLowerCase());

                if (existingTag) {
                    addTag({ id: existingTag.id, name: existingTag.name, isNew: false, customOptions: customOptions });
                } else {
                    addTag({ name: tagName, isNew: true, customOptions: customOptions });
                }
            }
        });

        tagInputField.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            tagSuggestionsContainer.innerHTML = '';

            if (query.length > 0) {
                const filteredTags = allTags.filter(tag => 
                    tag.name.toLowerCase().includes(query) && 
                    !selectedTags.some(selected => selected.id === tag.id || selected.name.toLowerCase() === tag.name.toLowerCase())
                );

                filteredTags.forEach(tag => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.classList.add('tag-suggestion-item');
                    suggestionItem.textContent = tag.name;
                    suggestionItem.addEventListener('click', () => {
                        addTag({ id: tag.id, name: tag.name, isNew: false, customOptions: [tag.name] });
                    });
                    tagSuggestionsContainer.appendChild(suggestionItem);
                });
            }
        });

        function renderAvailableTags() {
            availableTagsList.innerHTML = '';
            allTags.forEach(tag => {
                const isSelected = selectedTags.some(selected =>
                    (selected.id && selected.id === tag.id) ||
                    (selected.name && selected.name.toLowerCase() === tag.name.toLowerCase())
                );

                if (!isSelected) {
                    const availableTagPill = document.createElement('span');
                    availableTagPill.classList.add('badge', 'bg-secondary', 'me-1', 'mb-1', 'available-tag-pill');
                    availableTagPill.textContent = tag.name;
                    availableTagPill.setAttribute('data-tag-id', tag.id);
                    availableTagPill.setAttribute('data-tag-name', tag.name);
                    availableTagPill.addEventListener('click', function() {
                        addTag({ id: tag.id, name: tag.name, isNew: false, customOptions: [tag.name] });
                    });
                    availableTagsList.appendChild(availableTagPill);
                }
            });
        }

        // Size Functionality
        function renderSelectedSizes() {
            selectedSizesContainer.innerHTML = '';
            selectedSizesContainer.querySelectorAll('input[name="sizes[]"]').forEach(input => input.remove());

            selectedSizes.forEach(sizeId => {
                const size = allSizes.find(s => s.id === sizeId);
                if (size) {
                    const sizePill = document.createElement('span');
                    sizePill.classList.add('badge', 'bg-primary', 'me-1', 'mb-1', 'd-flex', 'align-items-center', 'tag-pill');
                    sizePill.innerHTML = `
                        ${size.name}
                        <button type="button" class="btn-close btn-close-white ms-1" aria-label="Remove" data-size-id="${size.id}"></button>
                    `;
                    sizePill.querySelector('.btn-close').addEventListener('click', function() {
                        removeSize(size.id);
                    });
                    selectedSizesContainer.appendChild(sizePill);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'sizes[]';
                    hiddenInput.value = size.id;
                    selectedSizesContainer.appendChild(hiddenInput);
                }
            });
            renderAvailableSizes();
        }

        function renderAvailableSizes() {
            availableSizesList.innerHTML = '';
            allSizes.forEach(size => {
                if (!selectedSizes.has(size.id)) {
                    const availableSizePill = document.createElement('span');
                    availableSizePill.classList.add('badge', 'bg-secondary', 'me-1', 'mb-1', 'available-tag-pill');
                    availableSizePill.textContent = size.name;
                    availableSizePill.setAttribute('data-size-id', size.id);
                    availableSizePill.setAttribute('data-size-name', size.name);
                    availableSizePill.addEventListener('click', function() {
                        addSize(size.id);
                    });
                    availableSizesList.appendChild(availableSizePill);
                }
            });
        }

        function addSize(sizeId) {
            if (!selectedSizes.has(sizeId)) {
                selectedSizes.add(sizeId);
                renderSelectedSizes();
            }
            sizeInputField.value = '';
            sizeSuggestionsContainer.innerHTML = '';
        }

        function removeSize(sizeId) {
            selectedSizes.delete(sizeId);
            renderSelectedSizes();
        }

        sizeInputField.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            sizeSuggestionsContainer.innerHTML = '';

            if (query.length > 0) {
                const filteredSizes = allSizes.filter(size => 
                    size.name.toLowerCase().includes(query) && !selectedSizes.has(size.id)
                );

                filteredSizes.forEach(size => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.classList.add('tag-suggestion-item');
                    suggestionItem.textContent = size.name;
                    suggestionItem.addEventListener('click', () => {
                        addSize(size.id);
                    });
                    sizeSuggestionsContainer.appendChild(suggestionItem);
                });
            }
        });

        sizeInputField.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const typedSizeName = this.value.trim().toLowerCase();
                if (typedSizeName.length > 0) {
                    const exactMatchSize = allSizes.find(size => size.name.toLowerCase() === typedSizeName);
                    if (exactMatchSize) {
                        addSize(exactMatchSize.id);
                    }
                }
            }
        });

        // Color Functionality
        function renderSelectedColors() {
            selectedColorsContainer.innerHTML = '';
            selectedColorsContainer.querySelectorAll('input[name="colors[]"]').forEach(input => input.remove());

            selectedColors.forEach(colorId => {
                const color = allColors.find(c => c.id === colorId);
                if (color) {
                    const colorPill = document.createElement('span');
                    colorPill.classList.add('badge', 'bg-primary', 'me-1', 'mb-1', 'd-flex', 'align-items-center', 'tag-pill');
                    colorPill.innerHTML = `
                        ${color.name}
                        <button type="button" class="btn-close btn-close-white ms-1" aria-label="Remove" data-color-id="${color.id}"></button>
                    `;
                    colorPill.querySelector('.btn-close').addEventListener('click', function() {
                        removeColor(color.id);
                    });
                    selectedColorsContainer.appendChild(colorPill);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'colors[]';
                    hiddenInput.value = color.id;
                    selectedColorsContainer.appendChild(hiddenInput);
                }
            });
            renderAvailableColors();
        }

        function renderAvailableColors() {
            availableColorsList.innerHTML = '';
            allColors.forEach(color => {
                if (!selectedColors.has(color.id)) {
                    const availableColorPill = document.createElement('span');
                    availableColorPill.classList.add('badge', 'bg-secondary', 'me-1', 'mb-1', 'available-tag-pill');
                    availableColorPill.textContent = color.name;
                    availableColorPill.setAttribute('data-color-id', color.id);
                    availableColorPill.setAttribute('data-color-name', color.name);
                    availableColorPill.addEventListener('click', function() {
                        addColor(color.id);
                    });
                    availableColorsList.appendChild(availableColorPill);
                }
            });
        }

        function addColor(colorId) {
            if (!selectedColors.has(colorId)) {
                selectedColors.add(colorId);
                renderSelectedColors();
            }
            colorInputField.value = '';
            colorSuggestionsContainer.innerHTML = '';
        }

        function removeColor(colorId) {
            selectedColors.delete(colorId);
            renderSelectedColors();
        }

        colorInputField.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            colorSuggestionsContainer.innerHTML = '';

            if (query.length > 0) {
                const filteredColors = allColors.filter(color => 
                    color.name.toLowerCase().includes(query) && !selectedColors.has(color.id)
                );

                filteredColors.forEach(color => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.classList.add('tag-suggestion-item');
                    suggestionItem.textContent = color.name;
                    suggestionItem.addEventListener('click', () => {
                        addColor(color.id);
                    });
                    colorSuggestionsContainer.appendChild(suggestionItem);
                });
            }
        });

        colorInputField.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const typedColorName = this.value.trim().toLowerCase();
                if (typedColorName.length > 0) {
                    const exactMatchColor = allColors.find(color => color.name.toLowerCase() === typedColorName);
                    if (exactMatchColor) {
                        addColor(exactMatchColor.id);
                    }
                }
            }
        });

        // Location Functionality
        function renderSelectedLocations() {
            selectedLocationsContainer.innerHTML = '';
            selectedLocationsContainer.querySelectorAll('input[name="locations[]"]').forEach(input => input.remove());

            selectedLocations.forEach(locationId => {
                const location = allLocations.find(l => l.id === locationId);
                if (location) {
                    const locationPill = document.createElement('span');
                    locationPill.classList.add('badge', 'bg-primary', 'me-1', 'mb-1', 'd-flex', 'align-items-center', 'tag-pill');
                    locationPill.innerHTML = `
                        ${location.name}
                        <button type="button" class="btn-close btn-close-white ms-1" aria-label="Remove" data-location-id="${location.id}"></button>
                    `;
                    locationPill.querySelector('.btn-close').addEventListener('click', function() {
                        removeLocation(location.id);
                    });
                    selectedLocationsContainer.appendChild(locationPill);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'locations[]';
                    hiddenInput.value = location.id;
                    selectedLocationsContainer.appendChild(hiddenInput);
                }
            });
            renderAvailableLocations();
        }

        function renderAvailableLocations() {
            availableLocationsList.innerHTML = '';
            allLocations.forEach(location => {
                if (!selectedLocations.has(location.id)) {
                    const availableLocationPill = document.createElement('span');
                    availableLocationPill.classList.add('badge', 'bg-secondary', 'me-1', 'mb-1', 'available-tag-pill');
                    availableLocationPill.textContent = location.name;
                    availableLocationPill.setAttribute('data-location-id', location.id);
                    availableLocationPill.setAttribute('data-location-name', location.name);
                    availableLocationPill.addEventListener('click', function() {
                        addLocation(location.id);
                    });
                    availableLocationsList.appendChild(availableLocationPill);
                }
            });
        }

        function addLocation(locationId) {
            if (!selectedLocations.has(locationId)) {
                selectedLocations.add(locationId);
                renderSelectedLocations();
            }
            locationInputField.value = '';
            locationSuggestionsContainer.innerHTML = '';
        }

        function removeLocation(locationId) {
            selectedLocations.delete(locationId);
            renderSelectedLocations();
        }

        locationInputField.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            locationSuggestionsContainer.innerHTML = '';

            if (query.length > 0) {
                const filteredLocations = allLocations.filter(location => 
                    location.name.toLowerCase().includes(query) && !selectedLocations.has(location.id)
                );

                filteredLocations.forEach(location => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.classList.add('tag-suggestion-item');
                    suggestionItem.textContent = location.name;
                    suggestionItem.addEventListener('click', () => {
                        addLocation(location.id);
                    });
                    locationSuggestionsContainer.appendChild(suggestionItem);
                });
            }
        });

        locationInputField.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const typedLocationName = this.value.trim().toLowerCase();
                if (typedLocationName.length > 0) {
                    const exactMatchLocation = allLocations.find(location => location.name.toLowerCase() === typedLocationName);
                    if (exactMatchLocation) {
                        addLocation(exactMatchLocation.id);
                    }
                }
            }
        });

        document.addEventListener('click', function(event) {
            if (!tagInputField.contains(event.target) && !tagSuggestionsContainer.contains(event.target)) {
                tagSuggestionsContainer.innerHTML = '';
            }
            if (!sizeInputField.contains(event.target) && !sizeSuggestionsContainer.contains(event.target)) {
                sizeSuggestionsContainer.innerHTML = '';
            }
            if (!colorInputField.contains(event.target) && !colorSuggestionsContainer.contains(event.target)) {
                colorSuggestionsContainer.innerHTML = '';
            }
            if (!locationInputField.contains(event.target) && !locationSuggestionsContainer.contains(event.target)) {
                locationSuggestionsContainer.innerHTML = '';
            }
            if (!brandInputField.contains(event.target) && !brandSuggestionsContainer.contains(event.target)) {
                brandSuggestionsContainer.innerHTML = '';
            }
        });

        // Custom Attributes (Options)
        function addOptionRow(key = '', value = '') {
            const index = optionsContainer.querySelectorAll('.option-item').length;
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-1', 'option-item');
            newRow.innerHTML = `
                <div class="col-5 position-relative">
                    <input type="text" name="options[${index}][key]" class="form-control option-key-input" placeholder="Attribute Key" value="${key}" />
                    <div class="option-suggestions" data-for="key"></div>
                </div>
                <div class="col-5 position-relative">
                    <input type="text" name="options[${index}][value]" class="form-control option-value-input" placeholder="Attribute Value" value="${value}" />
                    <div class="option-suggestions" data-for="value"></div>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-outline-danger remove-option-btn"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
            optionsContainer.appendChild(newRow);

            // This listener is for newly added rows
            newRow.querySelector('.remove-option-btn').addEventListener('click', function() {
                console.log('Remove button clicked for new row!');
                newRow.remove();
                updateOptionIndices();
            });
        }

        function updateOptionIndices() {
            const optionItems = optionsContainer.querySelectorAll('.option-item');
            optionItems.forEach((item, index) => {
                const keyInput = item.querySelector('.option-key-input');
                const valueInput = item.querySelector('.option-value-input');
                keyInput.name = `options[${index}][key]`;
                valueInput.name = `options[${index}][value]`;
            });
        }

        addOptionButton.addEventListener('click', function() {
            // Check for existing keys to prevent duplicates
            const existingKeys = Array.from(optionsContainer.querySelectorAll('.option-key-input')).map(input => input.value.trim().toLowerCase());
            if (existingKeys.includes('')) {
                // Using a custom message box instead of alert()
                const messageBox = document.createElement('div');
                messageBox.classList.add('alert', 'alert-warning', 'mt-2');
                messageBox.textContent = 'Please fill in all existing attribute keys before adding a new one.';
                optionsContainer.before(messageBox);
                setTimeout(() => messageBox.remove(), 3000); // Remove message after 3 seconds
                return;
            }
            addOptionRow();
        });

        // Delegated event listener for existing and new custom attribute remove buttons
        optionsContainer.addEventListener('click', function(event) {
            console.log('Click event on optionsContainer. Target:', event.target);
            const removeButton = event.target.closest('.remove-option-btn');
            if (removeButton) {
                console.log('Remove button found:', removeButton);
                const optionItem = removeButton.closest('.option-item');
                if (optionItem) {
                    console.log('Option item found, removing:', optionItem);
                    optionItem.remove();
                    updateOptionIndices();
                } else {
                    console.error("Error: Could not find parent .option-item for the clicked remove button.");
                }
            }
        });

        // Prevent duplicate keys in real-time
        optionsContainer.addEventListener('input', function(event) {
            if (event.target.classList.contains('option-key-input')) {
                const currentKeyInput = event.target;
                const currentKey = currentKeyInput.value.trim().toLowerCase();
                const allKeyInputs = Array.from(optionsContainer.querySelectorAll('.option-key-input'));
                
                let isDuplicate = false;
                for (let i = 0; i < allKeyInputs.length; i++) {
                    if (allKeyInputs[i] !== currentKeyInput && allKeyInputs[i].value.trim().toLowerCase() === currentKey && currentKey !== '') {
                        isDuplicate = true;
                        break;
                    }
                }

                const feedbackDiv = currentKeyInput.nextElementSibling; // This is the .option-suggestions div
                let invalidFeedback = feedbackDiv.querySelector('.invalid-feedback');

                if (isDuplicate) {
                    currentKeyInput.classList.add('is-invalid');
                    if (!invalidFeedback) {
                        invalidFeedback = document.createElement('div');
                        invalidFeedback.classList.add('invalid-feedback');
                        feedbackDiv.appendChild(invalidFeedback);
                    }
                    invalidFeedback.textContent = 'This attribute key is already used.';
                    invalidFeedback.style.display = 'block'; // Ensure it's visible
                } else {
                    currentKeyInput.classList.add('is-valid');
                    if (invalidFeedback) {
                        invalidFeedback.remove();
                    }
                }
            }
        });

        productForm.addEventListener('submit', function(event) {
            const optionItems = optionsContainer.querySelectorAll('.option-item');
            let hasDuplicates = false;
            const seenKeys = new Set();
            optionItems.forEach(item => {
                const keyInput = item.querySelector('.option-key-input');
                const valueInput = item.querySelector('.option-value-input');
                const key = keyInput.value.trim();
                
                // If both key and value are empty, remove the row before submission
                if (key === '' && valueInput.value.trim() === '') {
                    item.remove();
                } else if (key !== '' && seenKeys.has(key.toLowerCase())) {
                    hasDuplicates = true;
                    keyInput.classList.add('is-invalid');
                    const feedbackDiv = keyInput.nextElementSibling;
                    let invalidFeedback = feedbackDiv.querySelector('.invalid-feedback');
                    if (!invalidFeedback) {
                        invalidFeedback = document.createElement('div');
                        invalidFeedback.classList.add('invalid-feedback');
                        feedbackDiv.appendChild(invalidFeedback);
                    }
                    invalidFeedback.textContent = 'Duplicate attribute key.';
                    invalidFeedback.style.display = 'block';
                } else if (key !== '') {
                    seenKeys.add(key.toLowerCase());
                }
            });
            if (hasDuplicates) {
                event.preventDefault();
                // Using a custom message box instead of alert()
                const messageBox = document.createElement('div');
                messageBox.classList.add('alert', 'alert-danger', 'mt-2');
                messageBox.textContent = 'Please remove or correct duplicate attribute keys before submitting.';
                productForm.before(messageBox); // Place message before the form
                setTimeout(() => messageBox.remove(), 5000); // Remove message after 5 seconds
            }
        });

        // Initial render
        renderSelectedTags();
        renderAvailableTags();
        renderSelectedSizes();
        renderAvailableSizes();
        renderSelectedColors();
        renderAvailableColors();
        renderSelectedLocations();
        renderAvailableLocations();
        renderSelectedBrands();
        renderAvailableBrands();
    });
</script>

<style>
    .star-rating .fa-star {
        cursor: pointer;
        color: #ccc;
    }
    .star-rating .fa-star.text-warning {
        color: #ffc107;
    }
    .preview-container {
        position: relative;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
        border: 1px solid #eee;
        border-radius: 8px;
        overflow: hidden;
    }
    .preview-container img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        display: block;
    }
    .btn-remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: rgba(220, 53, 69, 0.8);
        border: none;
        color: white;
        font-size: 0.75rem;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .btn-remove-image:hover {
        background-color: rgba(220, 53, 69, 1);
    }
    .tag-input-container, .size-input-container, .color-input-container, .location-input-container, .brand-input-container {
        border: 1px solid #d8d6de;
        border-radius: 0.357rem;
        padding: 0.571rem 1rem;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        min-height: 38px;
    }
    .tag-input-container:focus-within, .size-input-container:focus-within, .color-input-container:focus-within, .location-input-container:focus-within, .brand-input-container:focus-within {
        border-color: #7367f0;
        box-shadow: 0 3px 10px 0 rgba(115, 103, 240, 0.3);
    }
    #tag-input-field, #size-input-field, #color-input-field, #location-input-field, #brand-input-field {
        flex-grow: 1;
        border: none;
        outline: none;
        padding: 0;
        margin-top: 5px;
        min-width: 100px;
    }
    #selected-tags, #selected-sizes, #selected-colors, #selected-locations, #selected-brands {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        align-items: center;
        width: 100%;
    }
    .tag-pill {
        padding: 0.25em 0.6em;
        border-radius: 0.25rem;
        font-size: 0.875em;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        vertical-align: baseline;
        color: #fff;
        background-color: #6c757d;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    .tag-pill .btn-close {
        font-size: 0.7em;
        margin-left: 0.5rem;
        opacity: 0.8;
    }
    .tag-pill .btn-close:hover {
        opacity: 1;
    }
    .tag-suggestions {
        border: 1px solid #d8d6de;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        background-color: #fff;
        position: absolute;
        width: 100%;
        z-index: 1000;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border-radius: 0 0 0.357rem 0.357rem;
    }
    .tag-suggestion-item {
        padding: 8px 15px;
        cursor: pointer;
    }
    .tag-suggestion-item:hover {
        background-color: #f5f5f5;
    }
    .available-tag-pill {
        cursor: pointer;
        padding: 0.25em 0.6em;
        border-radius: 0.25rem;
        font-size: 0.875em;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
        vertical-align: baseline;
        color: #fff;
        background-color: #007bff;
        transition: background-color 0.2s ease;
    }
    .available-tag-pill:hover {
        background-color: #0056b3;
    }
    .option-item {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .option-item input {
        flex: 1;
    }
    .option-item .form-control {
        width: auto;
    }
    .btn-edit-options {
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 0.25rem;
        padding: 0.2rem 0.5rem;
        font-size: 0.7rem;
        margin-left: 0.5rem;
        cursor: pointer;
    }
    .btn-edit-options:hover {
        background-color: #218838;
    }
    .option-suggestions {
        position: absolute;
        z-index: 1000;
        background-color: #fff;
        border: 1px solid #d8d6de;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        width: 100%;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border-radius: 0 0 0.357rem 0.357rem;
    }
    .invalid-feedback {
        display: block;
    }
</style>
@endsection