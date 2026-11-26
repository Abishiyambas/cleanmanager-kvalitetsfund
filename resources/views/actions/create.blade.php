<x-layout title="Tildel handling">
    <form action="{{ route('actions.store', $finding) }}" method="POST" class="form">
        @csrf
        <h2 class="mt-0">Tildel korrigerende handling</h2>
        <p class="muted">Fund: {{ $finding->category->name }} · {{ $finding->location->name }} – {{ $finding->comment }}</p>

        <label for="description">Hvad skal gøres?</label>
        <textarea id="description" name="description" rows="3" required>{{ old('description') }}</textarea>

        <label for="responsible_id">Ansvarlig:</label>
        <select id="responsible_id" name="responsible_id" required>
            <option value="" disabled @selected(! old('responsible_id'))>Vælg ansvarlig</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @selected(old('responsible_id') == $user->id)>{{ $user->name }} ({{ $user->roleLabel() }})</option>
            @endforeach
        </select>

        <label for="due_date">Frist:</label>
        <input type="date" id="due_date" name="due_date" value="{{ old('due_date', now()->addDay()->toDateString()) }}"
               min="{{ now()->toDateString() }}" required>

        <button type="submit" class="btn btn-primary">Tildel handling</button>
        <a href="{{ route('findings.show', $finding) }}" class="btn">Annullér</a>

        <x-errors />
    </form>
</x-layout>
