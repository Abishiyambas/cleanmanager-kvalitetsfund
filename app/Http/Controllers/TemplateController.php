<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::withCount('items')->orderBy('name')->get();

        return view('templates.index', ['templates' => $templates]);
    }

    public function show(Template $template)
    {
        $template->load('items.category');

        return view('templates.show', ['template' => $template]);
    }

    public function create()
    {
        Gate::authorize('manage');

        return view('templates.create', ['categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        // Tomme rækker i formularen ignoreres
        $request->merge([
            'items' => collect($request->input('items', []))
                ->filter(fn ($item) => filled($item['text'] ?? null))
                ->values()
                ->all(),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.text' => 'required|string|max:255',
            'items.*.category_id' => 'required|exists:categories,id',
        ]);

        DB::transaction(function () use ($validated) {
            $template = Template::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);

            foreach ($validated['items'] as $i => $item) {
                $template->items()->create([...$item, 'position' => $i]);
            }
        });

        return redirect()->route('templates.index')->with('success', 'Kontrolskabelon oprettet!');
    }
}
