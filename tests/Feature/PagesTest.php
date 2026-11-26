<?php

use App\Models\User;

it('renders every page for every role without errors', function (string $email) {
    $this->seed();
    $user = User::where('email', $email)->first();

    $pages = [
        route('dashboard'), route('inspections.index'), route('inspections.show', 1),
        route('findings.index'), route('findings.show', 1), route('actions.index'),
        route('actions.index', ['tab' => 'all']), route('actions.index', ['tab' => 'verify']),
        route('actions.index', ['tab' => 'overdue']), route('actions.show', 4),
        route('locations.index'), route('locations.show', 1),
        route('templates.index'), route('templates.show', 1),
    ];

    foreach ($pages as $url) {
        $this->actingAs($user)->get($url)->assertOk();
    }
})->with(['sara@demo.dk', 'ida@demo.dk', 'mads@demo.dk']);

it('renders the forms for supervisors', function () {
    $this->seed();
    $this->actingAs(User::where('email', 'sara@demo.dk')->first());

    foreach ([route('inspections.create'), route('findings.create', 1), route('actions.create', 1),
        route('locations.create'), route('templates.create'), route('users.index')] as $url) {
        $this->get($url)->assertOk();
    }
});
