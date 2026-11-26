<x-layout title="Inspektioner">
    <div class="flex items-center justify-between">
        <h2>Inspektioner</h2>
        @can('inspect')
            <a href="{{ route('inspections.create') }}" class="btn btn-primary">+ Ny inspektion</a>
        @endcan
    </div>

    @forelse ($inspections as $inspection)
        <x-card href="{{ route('inspections.show', $inspection) }}" :highlight="$inspection->findings_count > 0">
            <div class="font-semibold">{{ $inspection->location->name }}</div>
            <div class="muted">
                {{ $inspection->created_at->format('d.m.Y H:i') }} · {{ $inspection->inspector->name }}
                · {{ $inspection->template?->name ?? 'Uden skabelon' }} · {{ $inspection->findings_count }} fund
            </div>
        </x-card>
    @empty
        <p class="muted">Ingen inspektioner endnu.</p>
    @endforelse

    {{ $inspections->links() }}
</x-layout>
