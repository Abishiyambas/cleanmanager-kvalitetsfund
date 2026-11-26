<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Finding;
use App\Models\Inspection;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FindingController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['location_id', 'category_id', 'severity']);

        $findings = Finding::with(['location', 'category', 'actions'])
            ->filter($filters)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('findings.index', [
            'findings' => $findings,
            'filters' => $filters,
            'locations' => Location::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function show(Finding $finding)
    {
        $finding->load(['inspection', 'location', 'category', 'creator', 'actions.responsible']);

        return view('findings.show', [
            'finding' => $finding,
            'previous' => $finding->previousSimilar(),
        ]);
    }

    public function create(Request $request, Inspection $inspection)
    {
        Gate::authorize('inspect');

        return view('findings.create', [
            'inspection' => $inspection->load('location'),
            'categories' => Category::orderBy('name')->get(),
            // Forudfyldt fra et kontrolpunkt i skabelonen
            'prefill' => $request->only(['category_id', 'comment']),
        ]);
    }

    public function store(Request $request, Inspection $inspection)
    {
        Gate::authorize('inspect');

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'severity' => 'required|in:'.implode(',', array_keys(Finding::SEVERITIES)),
            'comment' => 'required|string|max:1000',
            'photo' => 'nullable|image|max:5120',
        ]);

        $finding = $inspection->findings()->create([
            'location_id' => $inspection->location_id,
            'category_id' => $validated['category_id'],
            'severity' => $validated['severity'],
            'comment' => $validated['comment'],
            'photo_path' => $request->file('photo')?->store('findings', 'public'),
            'created_by' => $request->user()->id,
        ]);

        $message = $finding->isRecurring()
            ? 'Fund registreret. OBS: gentaget fund på samme lokation og kategori!'
            : 'Fund registreret!';

        return redirect()->route('findings.show', $finding)->with('success', $message);
    }
}
