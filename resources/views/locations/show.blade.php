<x-layout :title="$location->name">
    <h2>{{ $location->name }}</h2>
    <div class="panel">
        <p><strong>Adresse:</strong> {{ $location->address ?? '–' }}</p>
        <p>{{ $location->description }}</p>
    </div>

    <h3>Fund på lokationen ({{ $location->findings->count() }})</h3>
    <table class="table">
        <tr><th>Dato</th><th>Kategori</th><th>Alvorlighed</th><th>Kommentar</th><th>Status</th></tr>
        @forelse ($location->findings as $finding)
            <tr>
                <td>{{ $finding->created_at->format('d.m.Y') }}</td>
                <td>{{ $finding->category->name }}</td>
                <td><x-severity :finding="$finding" /></td>
                <td><a href="{{ route('findings.show', $finding) }}">{{ Str::limit($finding->comment, 60) }}</a></td>
                <td>
                    @if ($finding->isResolved())
                        <span class="badge badge-resolved">Løst</span>
                    @elseif ($finding->actions->isEmpty())
                        <span class="badge badge-open">Ingen handling</span>
                    @else
                        <span class="badge badge-progress">Under behandling</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Ingen fund.</td></tr>
        @endforelse
    </table>
</x-layout>
