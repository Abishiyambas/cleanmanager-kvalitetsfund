@php use App\Models\Finding; @endphp
<x-layout title="Registrér fund">
    <form action="{{ route('findings.store', $inspection) }}" method="POST" enctype="multipart/form-data" class="form">
        @csrf
        <h2 class="mt-0">Registrér fund</h2>
        <p class="muted">Lokation: {{ $inspection->location->name }}</p>

        <label for="category_id">Kategori:</label>
        <select id="category_id" name="category_id" required>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('category_id', $prefill['category_id'] ?? null) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <label for="severity">Alvorlighed:</label>
        <select id="severity" name="severity" required>
            @foreach (Finding::SEVERITIES as $key => $label)
                <option value="{{ $key }}" @selected(old('severity', 'medium') === $key)>{{ $label }}</option>
            @endforeach
        </select>

        <label for="comment">Kommentar:</label>
        <textarea id="comment" name="comment" rows="3" required>{{ old('comment', $prefill['comment'] ?? '') }}</textarea>

        <label for="photo">Foto (valgfrit):</label>
        <input type="file" id="photo" name="photo" accept="image/*" capture="environment">

        <button type="submit" class="btn btn-primary">Gem fund</button>
        <a href="{{ route('inspections.show', $inspection) }}" class="btn">Annullér</a>

        <x-errors />
    </form>
</x-layout>
