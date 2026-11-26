<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('manage');

        return view('users.index', ['users' => User::orderBy('name')->get()]);
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('manage');

        $validated = $request->validate([
            'role' => 'required|in:'.implode(',', array_keys(User::ROLES)),
        ]);

        if ($user->is($request->user()) && $validated['role'] !== 'supervisor') {
            return back()->with('error', 'Du kan ikke fjerne din egen supervisor-rolle.');
        }

        $user->update($validated);

        return back()->with('success', "Rollen for {$user->name} er opdateret.");
    }
}
