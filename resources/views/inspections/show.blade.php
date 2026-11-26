<x-layout title="Inspektion">
    <h2>Inspektion: {{ $inspection->location->name }}</h2>
    <p class="muted">
        {{ $inspection->created_at->format('d.m.Y H:i') }} · {{ $inspection->inspector->name }}
        · {{ $inspection->template?->name ?? 'Uden skabelon' }}
    </p>
    @if ($inspection->notes)
        <p>{{ $inspection->notes }}</p>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <section>
            <h3>Kontrolpunkter</h3>
            @if ($inspection->template)
                @foreach ($inspection->template->items as $item)
                    <div class="card">
                        <div class="flex-1">
                            <div>{{ $item->text }}</div>
                            <div class="muted">{{ $item->category->name }}</div>
                        </div>
                        @can('inspect')
                            <a class="btn btn-danger"
                               href="{{ route('findings.create', ['inspection' => $inspection, 'category_id' => $item->category_id, 'comment' => $item->text.': ']) }}">
                                Registrér fund
                            </a>
                        @endcan
                    </div>
                @endforeach
            @else
                <p class="muted">Inspektionen bruger ingen skabelon.</p>
            @endif

            @can('inspect')
                <a href="{{ route('findings.create', $inspection) }}" class="btn mt-3">+ Andet fund</a>
            @endcan
        </section>

        <section>
            <h3>Registrerede fund ({{ $inspection->findings->count() }})</h3>
            @forelse ($inspection->findings as $finding)
                <x-card href="{{ route('findings.show', $finding) }}" :highlight="$finding->actions->isEmpty()">
                    <div><x-severity :finding="$finding" /> <strong>{{ $finding->category->name }}</strong></div>
                    <div class="muted">{{ Str::limit($finding->comment, 80) }}</div>
                    @if ($finding->actions->isEmpty())
                        <div class="text-xs text-red-600">Mangler korrigerende handling</div>
                    @endif
                </x-card>
            @empty
                <p class="muted">Ingen fund. Alle kontrolpunkter er OK.</p>
            @endforelse
        </section>
    </div>
</x-layout>
