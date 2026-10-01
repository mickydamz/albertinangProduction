<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class ProductList extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $ratingFilter = '';
    public $supplierFilter = '';
    public $yearFilter = '';
    public $brandFilter = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search', 'categoryFilter', 'minPrice', 'maxPrice', 'ratingFilter',
        'supplierFilter', 'yearFilter', 'brandFilter', 'sortBy', 'sortDirection'
    ];

    public function mount()
    {
        $this->search        = '';
        $this->categoryFilter= '';
        $this->minPrice      = '';
        $this->maxPrice      = '';
        $this->ratingFilter  = '';
        $this->supplierFilter= '';
        $this->yearFilter    = '';
        $this->sortBy        = 'name';
        $this->sortDirection = 'asc';
        // Pick up ?brands[]=Name from the initial page load
        $brandsParam = request()->input('brands', []);
        if (is_array($brandsParam) && count($brandsParam)) {
            $this->brandFilter = $brandsParam[0];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage(); 
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage(); 
    }

    public function updatingMinPrice()
    {
        $this->resetPage(); 
    }

    public function updatingMaxPrice()
    {
        $this->resetPage(); 
    }

    public function updatingRatingFilter()
    {
        $this->resetPage(); 
    }

    public function updatingSupplierFilter()
    {
        $this->resetPage(); 
    }

    public function updatingYearFilter()
    {
        $this->resetPage(); 
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage(); 
    }

    public function render()
    {
        $categories = Category::all();
        $suppliers = User::where('role', 'supplier')->get();
        
        // Generate a list of years from 1808 to the current year
        $years = collect(range(1808, Carbon::now()->year))->sort();  

        $query = Product::query()->withReviewStats();

        // Apply filters
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->minPrice) {
            $query->where('price', '>=', $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('price', '<=', $this->maxPrice);
        }

        if ($this->ratingFilter) {
            $query->where('rating', '>=', $this->ratingFilter);
        }

        if ($this->supplierFilter) {
            $query->where('supplier_id', $this->supplierFilter);
        }

        if ($this->yearFilter) {
            $query->whereYear('created_at', $this->yearFilter);
        }

        if ($this->brandFilter) {
            $query->where('brand', $this->brandFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $products = $query->paginate(10);

        return view('livewire.product-list', [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'years' => $years,  // Pass years to the view
        ]);
    }
}
