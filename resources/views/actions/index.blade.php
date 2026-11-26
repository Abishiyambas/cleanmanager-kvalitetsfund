<x-layout title="Handlinger">
    <h2>Korrigerende handlinger</h2>

    @php
        $tabs = ['mine' => 'Mine åbne', 'overdue' => 'Overskredne', 'all' => 'Alle'];
        if (Gate::allows('verify')) {
            $tabs = ['mine' => 'Mine åbne', 'verify' => 'Afventer verificering', 'overdue' => 'Overskredne', 'all' => 'Alle'];
        }
    @endphp
    <div class="flex gap-2 mb-4">
        @foreach ($tabs as $key => $label)
            <a href="{{ route('actions.index', ['tab' => $key]) }}" @class(['btn', 'btn-primary' => $tab === $key])>{{ $label }}</a>
        @endforeach
    </div>

    <table class="table">
        <tr><th>Handling</th><th>Lokation · kategori</th><th>Ansvarlig</th><th>Frist</th><th>Status</th></tr>
        @forelse ($actions as $action)
            <tr>
                <td><a href="{{ route('actions.show', $action) }}">{{ Str::limit($action->description, 60) }}</a></td>
                <td>{{ $action->finding->location->name }} · {{ $action->finding->category->name }}</td>
                <td>{{ $action->responsible->name }}</td>
                <td>{{ $action->due_date->format('d.m.Y') }}</td>
                <td><x-status :action="$action" /></td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Ingen handlinger.</td></tr>
        @endforelse
    </table>

    <div class="mt-4">{{ $actions->links() }}</div>
</x-layout>
