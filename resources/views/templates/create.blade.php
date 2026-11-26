<x-layout title="Ny kontrolskabelon">
    <form action="{{ route('templates.store') }}" method="POST" class="form">
        @csrf
        <h2 class="mt-0">Ny kontrolskabelon</h2>

        <label for="name">Navn:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required>

        <label for="description">Beskrivelse:</label>
        <textarea id="description" name="description" rows="2">{{ old('description') }}</textarea>

        <h3>Kontrolpunkter</h3>
        <p class="muted">Tomme rækker ignoreres.</p>
        @for ($i = 0; $i < 8; $i++)
            <div class="grid grid-cols-3 gap-2">
                <input class="col-span-2" type="text" name="items[{{ $i }}][text]"
                       value="{{ old("items.$i.text") }}" placeholder="Kontrolpunkt {{ $i + 1 }}">
                <select name="items[{{ $i }}][category_id]">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old("items.$i.category_id") == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        @endfor

        <button type="submit" class="btn btn-primary">Opret skabelon</button>

        <x-errors />
    </form>
</x-layout>
