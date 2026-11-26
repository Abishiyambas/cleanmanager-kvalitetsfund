@php use App\Models\Finding; @endphp
<x-layout title="Fund">
    <h2>Fund</h2>

    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label for="location_id">Lokation</label>
            <select id="location_id" name="location_id">
                <option value="">Alle</option>
                @foreach ($locations as $l)
                    <option value="{{ $l->id }}" @selected(($filters['location_id'] ?? '') == $l->id)>{{ $l->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="category_id">Kategori</label>
            <select id="category_id" name="category_id">
                <option value="">Alle</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(($filters['category_id'] ?? '') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="severity">Alvorlighed</label>
            <select id="severity" name="severity">
                <option value="">Alle</option>
                @foreach (Finding::SEVERITIES as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['severity'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn mb-4">Filtrér</button>
    </form>

    <table class="table">
        <tr><th>Dato</th><th>Lokation</th><th>Kategori</th><th>Alvorlighed</th><th>Kommentar</th><th>Handlinger</th></tr>
        @forelse ($findings as $finding)
            <tr>
                <td>{{ $finding->created_at->format('d.m.Y') }}</td>
                <td>{{ $finding->location->name }}</td>
                <td>{{ $finding->category->name }}</td>
                <td><x-severity :finding="$finding" /></td>
                <td><a href="{{ route('findings.show', $finding) }}">{{ Str::limit($finding->comment, 60) }}</a></td>
                <td>
                    @if ($finding->isResolved())
                        <span class="badge badge-resolved">Løst</span>
                    @elseif ($finding->actions->isEmpty())
                        <span class="badge badge-overdue">Ingen</span>
                    @else
                        <span class="badge badge-progress">{{ $finding->actions->count() }} åben(e)</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">Ingen fund matcher filteret.</td></tr>
        @endforelse
    </table>

    <div class="mt-4">{{ $findings->links() }}</div>
</x-layout>
