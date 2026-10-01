<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Location;
use Illuminate\Http\Request;

class AdminStateController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $states = State::withCount('locations')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.states.index', compact('states', 'search'));
    }

    public function create()
    {
        return view('admin.states.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:states,name',
        ]);

        State::create([
            'name'      => $request->name,
            'is_active' => true,
        ]);

        return redirect()->route('admin.states.index')
            ->with('success', 'State created successfully.');
    }

    public function edit(State $state)
    {
        $state->load('locations');
        return view('admin.states.edit', compact('state'));
    }

    public function update(Request $request, State $state)
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:states,name,' . $state->id,
            'is_active' => 'nullable|boolean',
        ]);

        $state->name      = $request->name;
        $state->is_active = $request->boolean('is_active');
        $state->save();

        return redirect()->route('admin.states.edit', $state->id)
            ->with('success', 'State updated successfully.');
    }

    public function destroy(State $state)
    {
        $state->delete();

        return redirect()->route('admin.states.index')
            ->with('success', 'State deleted successfully.');
    }

    public function toggleActive(State $state)
    {
        $state->is_active = !$state->is_active;
        $state->save();

        $status = $state->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.states.index')
            ->with('success', "State \"{$state->name}\" {$status} successfully.");
    }
}
