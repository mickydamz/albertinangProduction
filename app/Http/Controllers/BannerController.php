<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Admin list — shows ALL banners (active and inactive) so the
     * admin can manage them, in display order. Only the public site
     * should filter by status.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $banners = Banner::when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")
                                                            ->orWhere('type', 'like', "%{$search}%"))
            ->orderBy('sort_order')
            ->orderByDesc('created_at') // tie-breaker for equal sort_order
            ->paginate(15)
            ->withQueryString();

        return view('admin.banners.index', compact('banners', 'search'));
    }

    /**
     * Show a specific banner by type (banner1, banner2, popup).
     * Note: $banner may be null if no active banner of that type
     * exists — the view must handle that.
     */
    public function show($type)
    {
        $banner = Banner::where('type', $type)
                        ->where('status', true)
                        ->orderBy('sort_order')
                        ->first();

        return view('admin.banners.show', compact('banner'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'       => 'required|in:banner1,banner2,popup',
            'image'      => 'required|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
            'title'      => 'nullable|string|max:255',
            'link'       => 'nullable|string|max:2048',
            'status'     => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ]);

        // If no order was given, append the new banner to the end
        // instead of letting it default to 0 (which would jump it
        // to the front of the slider).
        $validated['sort_order'] = $validated['sort_order']
            ?? ((int) Banner::max('sort_order') + 1);

        $validated['image'] = $request->file('image')->store('banners', 'public');

        Banner::create($validated);

        return redirect()->route('admin.banners.index')
                         ->with('success', 'Banner created successfully.');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        // link is nullable here too — matches store() and the form's
        // "optional" help text.
        $validated = $request->validate([
            'type'       => 'required|in:banner1,banner2,popup',
            'image'      => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:2048',
            'title'      => 'nullable|string|max:255',
            'link'       => 'nullable|string|max:2048',
            'status'     => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ]);

        // If the field came through empty, keep the banner's current
        // position rather than resetting it.
        if (!isset($validated['sort_order'])) {
            unset($validated['sort_order']);
        }

        if ($request->hasFile('image')) {
            // Safe delete: guards against a null image path and lets
            // the Storage facade handle a missing file gracefully.
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        } else {
            unset($validated['image']); // keep the existing image
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
                         ->with('success', 'Banner updated successfully.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
                         ->with('success', 'Banner deleted successfully.');
    }
}