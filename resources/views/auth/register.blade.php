<x-layout title="Opret bruger">
    <form action="{{ route('register') }}" method="POST" class="form mx-auto">
        @csrf

        <h2 class="mt-0">Opret bruger</h2>

        <label for="name">Navn:</label>
        <input type="text" id="name" name="name" required value="{{ old('name') }}">

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}">

        <label for="password">Adgangskode:</label>
        <input type="password" id="password" name="password" required>

        <label for="password_confirmation">Gentag adgangskode:</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required>

        <p class="muted">Nye brugere oprettes som medarbejder. En supervisor kan ændre rollen.</p>

        <button type="submit" class="btn btn-primary mt-2">Opret bruger</button>

        <x-errors />
    </form>
</x-layout>
