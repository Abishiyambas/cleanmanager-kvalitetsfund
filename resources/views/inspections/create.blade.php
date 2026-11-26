<x-layout title="Ny inspektion">
    <form action="{{ route('inspections.store') }}" method="POST" class="form">
        @csrf
        <h2 class="mt-0">Ny inspektion</h2>

        <label for="location_id">Lokation:</label>
        <select id="location_id" name="location_id" required>
            <option value="" disabled @selected(! old('location_id'))>Vælg lokation</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>{{ $location->name }}</option>
            @endforeach
        </select>

        <label for="template_id">Kontrolskabelon:</label>
        <select id="template_id" name="template_id">
            <option value="">Ingen skabelon (frit fund)</option>
            @foreach ($templates as $template)
                <option value="{{ $template->id }}" @selected(old('template_id') == $template->id)>{{ $template->name }}</option>
            @endforeach
        </select>

        <label for="notes">Noter:</label>
        <textarea id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>

        <button type="submit" class="btn btn-primary">Start inspektion</button>

        <x-errors />
    </form>
</x-layout>
