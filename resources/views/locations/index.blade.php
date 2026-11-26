<x-layout title="Lokationer">
    <div class="flex items-center justify-between">
        <h2>Lokationer</h2>
        @can('manage')
            <a href="{{ route('locations.create') }}" class="btn btn-primary">+ Ny lokation</a>
        @endcan
    </div>

    @foreach ($locations as $location)
        <x-card href="{{ route('locations.show', $location) }}">
            <div class="font-semibold">{{ $location->name }}</div>
            <div class="muted">{{ $location->address }} · {{ $location->findings_count }} fund</div>
        </x-card>
    @endforeach

    {{ $locations->links() }}
</x-layout>
