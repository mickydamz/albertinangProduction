@extends('layouts.adminlayout')

@section('content')
{{-- Feather icons are typically loaded via a JS script in Vuexy, but including the CSS for general compatibility --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css">


<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Create Tag</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.tags.index') }}">Tags</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="tag-create-form">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">New Tag Information</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.tags.store') }}" method="POST">
                                    @csrf
                                    
                                    <div class="mb-1">
                                        <label class="form-label" for="name">Tag Identifier</label>
                                        <input type="text" id="slug" class="form-control @error('slug') is-invalid @enderror" name="slug" placeholder="Enter tag identifier" value="{{ old('slug') }}" required />
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-1">
                                        <label class="form-label" for="name">Tag Name</label>
                                        <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Enter tag name" value="{{ old('name') }}" required />
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label d-block" for="options-section">Tag Options (Key-Value Pairs)</label>
                                        @error('options')
                                            <div class="alert alert-danger p-1">{{ $message }}</div>
                                        @enderror
                                        <div id="options-container">
                                            {{-- Dynamically added option fields will go here --}}
                                            @php
                                                // Retrieve old options or an empty array
                                                $oldOptions = old('options', []);
                                            @endphp

                                            @forelse ($oldOptions as $index => $option)
                                                <div class="row mb-1 option-item">
                                                    <div class="col-5 position-relative">
                                                        <input type="text" name="options[{{ $index }}][key]" class="form-control option-key-input" placeholder="Option Key" value="{{ $option['key'] ?? '' }}" />
                                                        <div class="option-suggestions" data-for="key"></div>
                                                    </div>
                                                    <div class="col-5 position-relative">
                                                        <input type="text" name="options[{{ $index }}][value]" class="form-control option-value-input" placeholder="Option Value" value="{{ $option['value'] ?? '' }}" />
                                                        <div class="option-suggestions" data-for="value"></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" class="btn btn-outline-danger remove-option-btn"><i class="fas fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                            @empty
                                                {{-- Add an initial empty option row if none exist or old('options') is empty --}}
                                                <div class="row mb-1 option-item">
                                                    <div class="col-5 position-relative">
                                                        <input type="text" name="options[0][key]" class="form-control option-key-input" placeholder="Option Key" />
                                                        <div class="option-suggestions" data-for="key"></div>
                                                    </div>
                                                    <div class="col-5 position-relative">
                                                        <input type="text" name="options[0][value]" class="form-control option-value-input" placeholder="Option Value" />
                                                        <div class="option-suggestions" data-for="value"></div>
                                                    </div>
                                                    <div class="col-2">
                                                        <button type="button" class="btn btn-outline-danger remove-option-btn"><i class="fas fa-trash-alt"></i></button>
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                        <button type="button" id="add-option-btn" class="btn btn-outline-primary btn-sm mt-1">
                                            <i class="fas fa-plus me-50"></i> Add Option
                                        </button>
                                    </div>

                                    <button type="submit" class="btn btn-primary me-1">
                                        <i class="fas fa-save me-50"></i> Save Tag
                                    </button>
                                    <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const optionsContainer = document.getElementById('options-container');
        const addOptionBtn = document.getElementById('add-option-btn');
        let optionIndex = {{ count(old('options', [])) > 0 ? count(old('options')) : 1 }}; // Start index based on old data or 1 if no old data

        // Function to create a new option row
        function createOptionRow(key = '', value = '') {
            const row = document.createElement('div');
            row.classList.add('row', 'mb-1', 'option-item');
            row.innerHTML = `
                <div class="col-5 position-relative">
                    <input type="text" name="options[${optionIndex}][key]" class="form-control option-key-input" placeholder="Option Key" value="${key}" />
                    <div class="option-suggestions" data-for="key"></div>
                </div>
                <div class="col-5 position-relative">
                    <input type="text" name="options[${optionIndex}][value]" class="form-control option-value-input" placeholder="Option Value" value="${value}" />
                    <div class="option-suggestions" data-for="value"></div>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-outline-danger remove-option-btn"><i class="fas fa-trash-alt"></i></button>
                </div>
            `;
            optionsContainer.appendChild(row);
            
            // Attach listeners to the new row's elements
            attachRemoveListener(row);
            attachInputListeners(row);

            optionIndex++;
            // Focus on the new key input for continuous typing
            row.querySelector('.option-key-input').focus();
        }

        // Function to attach remove listener to a button
        function attachRemoveListener(row) {
            const removeButton = row.querySelector('.remove-option-btn');
            if (removeButton) {
                removeButton.addEventListener('click', function() {
                    // Prevent removing the last item if you want at least one
                    if (optionsContainer.children.length > 1) { // Allows removing if more than one item
                        row.remove();
                        reindexOptionInputs(); // Re-index after removal
                    } else if (optionsContainer.children.length === 1) { // If it's the last one, clear inputs instead
                        row.querySelector('input[name*="[key]"]').value = '';
                        row.querySelector('input[name*="[value]"]').value = '';
                    }
                });
            }
        }

        // Function to attach input and keypress listeners to key/value inputs
        function attachInputListeners(row) {
            const keyInput = row.querySelector('.option-key-input');
            const valueInput = row.querySelector('.option-value-input');
            const keySuggestions = row.querySelector('.option-suggestions[data-for="key"]');
            const valueSuggestions = row.querySelector('.option-suggestions[data-for="value"]');

            // Handle input for suggestions (currently empty as no predefined list)
            keyInput.addEventListener('input', function() {
                // In a real scenario, you'd fetch or filter suggestions here
                keySuggestions.innerHTML = ''; // Clear existing suggestions
                // Example: if you had a list of common keys like ['color', 'size', 'material']
                // const commonKeys = ['color', 'size', 'material'];
                // const query = this.value.toLowerCase();
                // const filtered = commonKeys.filter(k => k.includes(query));
                // filtered.forEach(item => {
                //     const div = document.createElement('div');
                //     div.classList.add('suggestion-item');
                //     div.textContent = item;
                //     div.addEventListener('click', () => {
                //         keyInput.value = item;
                //         keySuggestions.innerHTML = '';
                //     });
                //     keySuggestions.appendChild(div);
                // });
            });

            valueInput.addEventListener('input', function() {
                // Similar logic for value suggestions if applicable
                valueSuggestions.innerHTML = '';
            });

            // Handle Enter keypress for adding new rows
            keyInput.addEventListener('keypress', handleEnterKeyPress);
            valueInput.addEventListener('keypress', handleEnterKeyPress);

            // Close suggestions when clicking outside
            document.addEventListener('click', function(event) {
                if (!keyInput.contains(event.target) && !keySuggestions.contains(event.target)) {
                    keySuggestions.innerHTML = '';
                }
                if (!valueInput.contains(event.target) && !valueSuggestions.contains(event.target)) {
                    valueSuggestions.innerHTML = '';
                }
            });
        }

        function handleEnterKeyPress(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent form submission

                const currentInput = event.target;
                const currentRow = currentInput.closest('.option-item');
                const lastRow = optionsContainer.lastElementChild;

                // Check if this is the last row and inputs are not empty
                if (currentRow === lastRow) {
                    const keyInput = currentRow.querySelector('.option-key-input');
                    const valueInput = currentRow.querySelector('.option-value-input');

                    // Add a new row only if either key or value has content
                    if (keyInput.value.trim() !== '' || valueInput.value.trim() !== '') {
                        createOptionRow();
                    }
                }
            }
        }

        // Function to re-index input names after an item is removed
        function reindexOptionInputs() {
            optionsContainer.querySelectorAll('.option-item').forEach((row, index) => {
                row.querySelector('.option-key-input').name = `options[${index}][key]`;
                row.querySelector('.option-value-input').name = `options[${index}][value]`;
            });
            optionIndex = optionsContainer.children.length; // Reset optionIndex to the current count
        }

        // Attach listeners to existing rows (e.g., from old() data) on initial load
        optionsContainer.querySelectorAll('.option-item').forEach(row => {
            attachRemoveListener(row);
            attachInputListeners(row);
        });

        // Add Option button click handler
        addOptionBtn.addEventListener('click', function() {
            createOptionRow();
        });
    });
</script>
<style>
    /* Styles for the suggestion dropdowns */
    .option-suggestions {
        border: 1px solid #d8d6de;
        border-top: none;
        max-height: 150px;
        overflow-y: auto;
        background-color: #fff;
        position: absolute;
        width: calc(100% - 2px); /* Adjust for border */
        z-index: 1000;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border-radius: 0 0 0.357rem 0.357rem;
        left: 1px; /* Align with input border */
        top: 100%; /* Position below the input */
        display: none; /* Hidden by default, show when suggestions exist */
    }

    .option-suggestions.active {
        display: block;
    }

    .suggestion-item {
        padding: 8px 15px;
        cursor: pointer;
    }

    .suggestion-item:hover {
        background-color: #f5f5f5;
    }

    /* General styles from previous version */
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

    .tag-input-container, .size-input-container, .color-input-container, .location-input-container {
        border: 1px solid #d8d6de;
        border-radius: 0.357rem;
        padding: 0.571rem 1rem;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        min-height: 38px;
    }

    .tag-input-container:focus-within, .size-input-container:focus-within, .color-input-container:focus-within, .location-input-container:focus-within {
        border-color: #7367f0;
        box-shadow: 0 3px 10px 0 rgba(115, 103, 240, 0.3);
    }

    #tag-input-field, #size-input-field, #color-input-field, #location-input-field {
        flex-grow: 1;
        border: none;
        outline: none;
        padding: 0;
        margin-top: 5px;
        min-width: 100px;
    }

    #selected-tags, #selected-sizes, #selected-colors, #selected-locations {
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
</style>
@endpush
