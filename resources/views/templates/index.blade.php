<x-layout title="Kontrolskabeloner">
    <div class="flex items-center justify-between">
        <h2>Kontrolskabeloner</h2>
        @can('manage')
            <a href="{{ route('templates.create') }}" class="btn btn-primary">+ Ny skabelon</a>
        @endcan
    </div>

    @foreach ($templates as $template)
        <x-card href="{{ route('templates.show', $template) }}">
            <div class="font-semibold">{{ $template->name }}</div>
            <div class="muted">{{ $template->items_count }} kontrolpunkter</div>
        </x-card>
    @endforeach
</x-layout>
