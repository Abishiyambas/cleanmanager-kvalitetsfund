@php use App\Models\User; @endphp
<x-layout title="Brugere">
    <h2>Brugere og roller</h2>
    <p class="muted">Kun supervisorer kan verificere løsninger. Inspektører registrerer fund og tildeler handlinger.</p>

    <table class="table">
        <tr><th>Navn</th><th>E-mail</th><th>Rolle</th></tr>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <form action="{{ route('users.update', $user) }}" method="POST" class="flex gap-2 items-center">
                        @csrf
                        @method('PATCH')
                        <select name="role" class="!mb-0 !mt-0">
                            @foreach (User::ROLES as $key => $label)
                                <option value="{{ $key }}" @selected($user->role === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="btn">Gem</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</x-layout>
