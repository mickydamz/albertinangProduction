<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSizeController extends Controller
{
    /**
     * Display a listing of the sizes.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $sizes = Size::when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.sizes.index', compact('sizes', 'search'));
    }

    /**
     * Show the form for creating a new Size.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Return the view for creating a new Size
        return view('admin.sizes.create');
    }

    /**
     * Store a newly created Size in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name', // Size name is required, string, max 255 chars, and unique in the 'sizes' table
        ]);

        // Create a new Size instance and fill it with validated data
        $size = new Size();
        $size->name = $request->name;
        $size->save();
        Cache::forget('ref_sizes');

        return redirect()->route('admin.sizes.index')->with('success', 'Size created successfully!');
    }

    /**
     * Show the form for editing the specified Size.
     *
     * @param  \App\Models\Size  $Size
     * @return \Illuminate\View\View
     */
    public function edit(Size $size)
    {
        // Return the view for editing the Size, passing the Size instance
        return view('admin.sizes.edit', compact('size'));
    }
    
     public function show(Size $size)
    {
        // Return the view for editing the Size, passing the Size instance
        return view('admin.sizes.show', compact('size'));
    }

    /**
     * Update the specified Size in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Size  $Size
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Size $size)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:sizes,name,' . $Size->id, // Name must be unique, excluding the current Size's ID
        ]);

        // Update the Size's name
        $size->name = $request->name;
      
        $size->save();
        Cache::forget('ref_sizes');

        return redirect()->route('admin.sizes.index')->with('success', 'Size updated successfully!');
    }

    /**
     * Remove the specified Size from storage.
     *
     * @param  \App\Models\Size  $Size
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Size $size)
    {
        $size->delete();
        Cache::forget('ref_sizes');

        return redirect()->route('admin.sizes.index')->with('success', 'Size deleted successfully!');
    }
}
