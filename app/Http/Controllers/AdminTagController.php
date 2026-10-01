<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminTagController extends Controller
{
    /**
     * Display a listing of the tags.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $tags = Tag::when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%")
                                                       ->orWhere('slug', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tags.index', compact('tags', 'search'));
    }

    /**
     * Show the form for creating a new tag.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Return the view for creating a new tag
        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name', // Tag name is required, string, max 255 chars, and unique in the 'tags' table
            'options' => 'nullable|array', // Add validation for the options field
        ]);

        // Create a new Tag instance and fill it with validated data
        $tag = new Tag();
        $tag->name = $request->name;
       
        $tag->slug = $request->slug; 
        $tag->options = $request->input('options', []); // Assign options, default to empty array if not present
        $tag->save();
        Cache::forget('ref_tags');

        return redirect()->route('admin.tags.index')->with('success', 'Tag created successfully!');
    }

    /**
     * Show the form for editing the specified tag.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\View\View
     */
    public function edit(Tag $tag)
    {
        // Return the view for editing the tag, passing the tag instance
        return view('admin.tags.edit', compact('tag'));
    }


    public function show(Tag $tag)
    {
        // Return the view for editing the tag, passing the tag instance
        return view('admin.tags.show', compact('tag'));
    }

    /**
     * Update the specified tag in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tag $tag)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id, // Name must be unique, excluding the current tag's ID
            'options' => 'nullable|array', // Add validation for the options field
        ]);

        // Update the tag's name
        $tag->name = $request->name;
       
        $tag->slug = $request->slug;
        $tag->options = $request->input('options', []); // Update options, default to empty array if not present
        $tag->save();
        Cache::forget('ref_tags');

        return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully!');
    }

    /**
     * Remove the specified tag from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        Cache::forget('ref_tags');

        return redirect()->route('admin.tags.index')->with('success', 'Tag deleted successfully!');
    }
}
