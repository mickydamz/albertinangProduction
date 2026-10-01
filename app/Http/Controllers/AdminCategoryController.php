<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminCategoryController extends Controller
{
    // Display a listing of categories and their subcategories
    public function index()
    {
        $categories = Category::with('subcategories')->get();
        return view('admin.categories.index', compact('categories'));
    }

    // Display a listing of subcategories
    public function SubcategoryIndex()
    {
        $subcategories = Subcategory::with('category')->get();
        return view('admin.subcategories.index', compact('subcategories'));
    }

    // Show the form for creating a new category
    public function create()
    {
        return view('admin.categories.create');
    }

    // Store a newly created category in storage
    public function store(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'description'         => 'nullable|string',
            'is_active'           => 'nullable|boolean',
            'requires_truck'      => 'nullable|boolean',
            'estimated_weight_kg' => 'nullable|numeric|min:0|max:99999',
        ]);

        $data = $request->only('name', 'description');
        $data['is_active']           = $request->has('is_active');
        $data['requires_truck']      = $request->has('requires_truck');
        $data['estimated_weight_kg'] = $request->filled('estimated_weight_kg') ? $request->estimated_weight_kg : null;

        Category::create($data);
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    // Show the form for editing a category
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    // Update the specified category in storage
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'description'         => 'nullable|string',
            'is_active'           => 'nullable|boolean',
            'requires_truck'      => 'nullable|boolean',
            'estimated_weight_kg' => 'nullable|numeric|min:0|max:99999',
        ]);

        $data = $request->only('name', 'description');
        $data['is_active']           = $request->has('is_active');
        $data['requires_truck']      = $request->has('requires_truck');
        $data['estimated_weight_kg'] = $request->filled('estimated_weight_kg') ? $request->estimated_weight_kg : null;

        $category->update($data);
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    // Remove the specified category from storage
    public function destroy(Category $category)
    {
        $category->delete();
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

    // Show the form for creating a new Subcategory
    public function createSubcategory()
    {
        $categories = Category::all();
        return view('admin.subcategories.create', compact('categories'));
    }

    // Store a newly created Subcategory in storage
    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'         => 'required|exists:categories,id',
            'description'         => 'nullable|string',
            'is_active'           => 'nullable|boolean',
            'requires_truck'      => 'nullable|boolean',
            'estimated_weight_kg' => 'nullable|numeric|min:0|max:99999',
        ]);

        $data = $request->only('name', 'category_id', 'description');
        $data['is_active']           = $request->has('is_active');
        $data['requires_truck']      = $request->has('requires_truck');
        $data['estimated_weight_kg'] = $request->filled('estimated_weight_kg') ? $request->estimated_weight_kg : null;

        Subcategory::create($data);
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Subcategory created successfully.');
    }
    // Show the form for editing a Subcategory
    public function editSubcategory(Subcategory $Subcategory)
    {
        $categories = Category::all();
        return view('admin.subcategories.edit', compact('Subcategory', 'categories'));
    }

    // Update the specified Subcategory in storage
    // public function updateSubcategory(Request $request, Subcategory $Subcategory)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'category_id' => 'required|exists:categories,id',
    //         'description' => 'nullable|string',
    //         'is_active' => 'required|boolean',
    //     ]);

    //     $Subcategory->update($request->only('name', 'category_id', 'description', 'is_active'));

    //     return redirect()->route('admin.categories.index')->with('success', 'Subcategory updated successfully.');
    // }


    public function updateSubcategory(Request $request, Subcategory $Subcategory)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'category_id'         => 'required|exists:categories,id',
            'description'         => 'nullable|string',
            'is_active'           => 'nullable|boolean',
            'requires_truck'      => 'nullable|boolean',
            'estimated_weight_kg' => 'nullable|numeric|min:0|max:99999',
        ]);

        $data = $request->only('name', 'category_id', 'description');
        $data['is_active']           = $request->has('is_active');
        $data['requires_truck']      = $request->has('requires_truck');
        $data['estimated_weight_kg'] = $request->filled('estimated_weight_kg') ? $request->estimated_weight_kg : null;

        $Subcategory->update($data);
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Subcategory updated successfully.');
    }

    // Remove the specified Subcategory from storage
    public function destroySubcategory(Subcategory $Subcategory)
    {
        $Subcategory->delete();
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')->with('success', 'Subcategory deleted successfully.');
    }

    public function toggleActive(Category $category)
{
    $category->update(['is_active' => !$category->is_active]);
    Cache::forget('nav_categories');

    return response()->json([
        'success'   => true,
        'is_active' => $category->is_active,
    ]);
}

public function toggleActiveSubcategory(Subcategory $Subcategory)
{
    $Subcategory->update(['is_active' => !$Subcategory->is_active]);
    Cache::forget('nav_categories');

    return response()->json([
        'success'   => true,
        'is_active' => $Subcategory->is_active,
    ]);
}
}