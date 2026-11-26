<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('da');
        Paginator::useTailwind();

        // Brugeradgang (roller):
        // supervisor: alt, inkl. at verificere løsninger og administrere brugere/lokationer/skabeloner
        // inspector:  udfører inspektioner, registrerer fund og tildeler handlinger
        // employee:   arbejder på de handlinger, de er ansvarlige for
        Gate::define('inspect', fn (User $user) => in_array($user->role, ['supervisor', 'inspector']));
        Gate::define('assign', fn (User $user) => in_array($user->role, ['supervisor', 'inspector']));
        Gate::define('verify', fn (User $user) => $user->isSupervisor());
        Gate::define('manage', fn (User $user) => $user->isSupervisor());
    }
}
