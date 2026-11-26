<x-layout title="Ny lokation">
    <form action="{{ route('locations.store') }}" method="POST" class="form">
        @csrf
        <h2 class="mt-0">Ny lokation</h2>

        <label for="name">Navn:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Fx Hovedindgang" required>

        <label for="address">Adresse / placering:</label>
        <input type="text" id="address" name="address" value="{{ old('address') }}">

        <label for="description">Beskrivelse:</label>
        <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>

        <button type="submit" class="btn btn-primary">Opret lokation</button>

        <x-errors />
    </form>
</x-layout>
