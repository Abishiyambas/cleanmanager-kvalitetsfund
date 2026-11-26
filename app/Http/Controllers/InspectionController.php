<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Location;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InspectionController extends Controller
{
    public function index()
    {
        $inspections = Inspection::with(['location', 'template', 'inspector'])
            ->withCount('findings')
            ->latest()
            ->paginate(15);

        return view('inspections.index', ['inspections' => $inspections]);
    }

    public function show(Inspection $inspection)
    {
        $inspection->load(['location', 'inspector', 'template.items.category', 'findings.category', 'findings.actions']);

        return view('inspections.show', ['inspection' => $inspection]);
    }

    public function create()
    {
        Gate::authorize('inspect');

        return view('inspections.create', [
            'locations' => Location::orderBy('name')->get(),
            'templates' => Template::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('inspect');

        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'template_id' => 'nullable|exists:templates,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $inspection = Inspection::create([...$validated, 'inspector_id' => $request->user()->id]);

        return redirect()->route('inspections.show', $inspection)->with('success', 'Inspektion startet!');
    }
}
