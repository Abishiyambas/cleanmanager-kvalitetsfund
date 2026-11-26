<x-layout title="Fund">
    <h2>
        Fund: {{ $finding->category->name }} · {{ $finding->location->name }}
        @if ($previous->isNotEmpty())
            <span class="badge badge-recurring align-middle">Gentaget fund ({{ $previous->count() + 1 }}×)</span>
        @endif
    </h2>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="panel md:col-span-2">
            <p><x-severity :finding="$finding" /></p>
            <p>{{ $finding->comment }}</p>
            <p class="muted">
                Registreret {{ $finding->created_at->format('d.m.Y H:i') }} af {{ $finding->creator->name }}
                under <a href="{{ route('inspections.show', $finding->inspection_id) }}">inspektion #{{ $finding->inspection_id }}</a>
            </p>
            @if ($finding->photo_path)
                <img src="{{ Storage::url($finding->photo_path) }}" alt="Foto af fund" class="mt-3 max-h-72 rounded border">
            @endif
        </div>

        <div>
            @if ($previous->isNotEmpty())
                <div class="panel">
                    <h3 class="mt-0">Tidligere fund samme sted</h3>
                    <ul class="text-sm space-y-1">
                        @foreach ($previous as $p)
                            <li><a href="{{ route('findings.show', $p) }}">{{ $p->created_at->format('d.m.Y') }}</a> – {{ Str::limit($p->comment, 40) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h3>Korrigerende handlinger</h3>
        @can('assign')
            <a href="{{ route('actions.create', $finding) }}" class="btn btn-primary">+ Tildel handling</a>
        @endcan
    </div>

    @forelse ($finding->actions as $action)
        <x-card href="{{ route('actions.show', $action) }}" :highlight="$action->isOverdue()" link="Åbn">
            <div class="font-semibold">{{ $action->description }}</div>
            <div class="muted">{{ $action->responsible->name }} · frist {{ $action->due_date->format('d.m.Y') }}</div>
            <x-status :action="$action" />
        </x-card>
    @empty
        <p class="muted">Ingen handlinger tildelt endnu.</p>
    @endforelse
</x-layout>
