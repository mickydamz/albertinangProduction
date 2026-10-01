<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class AdminFaqController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $faqs = Faq::ordered()
            ->when($search !== '', fn ($q) => $q->where('question', 'like', "%{$search}%")
                                               ->orWhere('answer', 'like', "%{$search}%")
                                               ->orWhere('category', 'like', "%{$search}%"))
            ->paginate(15)
            ->withQueryString();

        return view('admin.faqs.index', compact('faqs', 'search'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'category'   => 'required|string|max:60',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active');

        Faq::create($data);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ added successfully.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'category'   => 'required|string|max:60',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active');

        $faq->update($data);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'FAQ deleted.');
    }
}
