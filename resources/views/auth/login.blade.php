<x-layout title="Log ind">
    <form action="{{ route('login') }}" method="POST" class="form mx-auto">
        @csrf

        <h2 class="mt-0">Log ind</h2>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required value="{{ old('email') }}">

        <label for="password">Adgangskode:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn btn-primary mt-2">Log ind</button>

        <x-errors />

        @if (app()->environment('local'))
            <p class="muted mt-6">
                Demo-brugere (adgangskode <code>password</code>):<br>
                sara@demo.dk (supervisor) · soeren@demo.dk (supervisor)<br>
                ida@demo.dk (inspektør) · mads@demo.dk / lene@demo.dk (medarbejder)
            </p>
        @endif
    </form>
</x-layout>
