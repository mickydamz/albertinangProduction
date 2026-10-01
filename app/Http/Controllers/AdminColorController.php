<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminColorController extends Controller
{
    /**
     * Display a listing of the colors.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $colors = Color::when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.colors.index', compact('colors', 'search'));
    }

    /**
     * Show the form for creating a new color.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Return the view for creating a new color
        return view('admin.colors.create');
    }

    /**
     * Store a newly created color in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name', // color name is required, string, max 255 chars, and unique in the 'colors' table
        ]);
//  \Log::info('color request:', $request->all());
        // Create a new color instance and fill it with validated data
        $color = new Color();
        $color->name = $request->name;
        
        $color->save();
        Cache::forget('ref_colors');

        return redirect()->route('admin.colors.index')->with('success', 'color created successfully!');
    }

    /**
     * Show the form for editing the specified color.
     *
     * @param  \App\Models\color  $color
     * @return \Illuminate\View\View
     */
    public function edit(Color $color)
    {
        // Return the view for editing the color, passing the color instance
        return view('admin.colors.edit', compact('color'));
    }
    
     public function show(Color $color)
    {
        // Return the view for editing the color, passing the color instance
        return view('admin.colors.show', compact('color'));
    }

    /**
     * Update the specified color in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\color  $color
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Color $color)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name,' . $color->id, // Name must be unique, excluding the current color's ID
        ]);

        // Update the color's name
        $color->name = $request->name;
      
        $color->save();
        Cache::forget('ref_colors');

        return redirect()->route('admin.colors.index')->with('success', 'color updated successfully!');
    }

    /**
     * Remove the specified color from storage.
     *
     * @param  \App\Models\color  $color
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Color $color)
    {
        $color->delete();
        Cache::forget('ref_colors');

        return redirect()->route('admin.colors.index')->with('success', 'color deleted successfully!');
    }
}
