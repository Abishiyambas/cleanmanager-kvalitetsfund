@props(['title' => null])
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}Kvalitetsfund</title>

    @vite('resources/css/app.css')
</head>
<body>

@if (session('success'))
    <div id="flash" class="p-3 text-center bg-green-50 text-green-700 font-semibold">{{ session('success') }}</div>
@elseif (session('successD'))
    <div id="flash" class="p-3 text-center bg-amber-50 text-amber-800 font-semibold">{{ session('successD') }}</div>
@elseif (session('error'))
    <div id="flash" class="p-3 text-center bg-red-50 text-red-600 font-semibold">{{ session('error') }}</div>
@endif

<header>
    <nav>
        <h1><a href="{{ route('dashboard') }}" class="no-underline">Kvalitetsfund</a></h1>

        @guest
            <a href="{{ route('show.login') }}" class="btn ml-auto">Log ind</a>
            <a href="{{ route('show.register') }}" class="btn">Opret bruger</a>
        @endguest

        @auth
            <a href="{{ route('dashboard') }}" @class(['nav-link', 'active' => request()->routeIs('dashboard')])>Overblik</a>
            <a href="{{ route('inspections.index') }}" @class(['nav-link', 'active' => request()->routeIs('inspections.*')])>Inspektioner</a>
            <a href="{{ route('findings.index') }}" @class(['nav-link', 'active' => request()->routeIs('findings.*')])>Fund</a>
            <a href="{{ route('actions.index') }}" @class(['nav-link', 'active' => request()->routeIs('actions.*')])>Handlinger</a>
            <a href="{{ route('locations.index') }}" @class(['nav-link', 'active' => request()->routeIs('locations.*')])>Lokationer</a>
            <a href="{{ route('templates.index') }}" @class(['nav-link', 'active' => request()->routeIs('templates.*')])>Skabeloner</a>
            @can('manage')
                <a href="{{ route('users.index') }}" @class(['nav-link', 'active' => request()->routeIs('users.*')])>Brugere</a>
            @endcan

            <span class="ml-auto border-r-2 pr-3 text-slate-500">
                {{ Auth::user()->name }} · {{ Auth::user()->roleLabel() }}
            </span>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button class="btn">Log ud</button>
            </form>
        @endauth
    </nav>
</header>

<main class="container">
    {{ $slot }}
</main>

</body>
</html>
