<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('findings')->orderBy('name')->paginate(15);

        return view('locations.index', ['locations' => $locations]);
    }

    public function show(Location $location)
    {
        $location->load(['findings' => fn ($q) => $q->with(['category', 'actions'])->latest()]);

        return view('locations.show', ['location' => $location]);
    }

    public function create()
    {
        Gate::authorize('manage');

        return view('locations.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')->with('success', 'Lokation oprettet!');
    }
}
