<?php

use App\Enums\ActionStatus;
use App\Models\Location;
use App\Models\User;

it('redirects guests to login', function () {
    $this->get('/dashboard')->assertRedirect(route('show.login'));
});

it('lets users log in and out', function () {
    $user = User::factory()->create(['email' => 'a@b.dk']);

    $this->post(route('login'), ['email' => 'a@b.dk', 'password' => 'password'])
        ->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    $this->post(route('logout'))->assertRedirect(route('show.login'));
    $this->assertGuest();
});

it('registers new users as employees, never as verifier', function () {
    $this->post(route('register'), [
        'name' => 'Ny', 'email' => 'ny@demo.dk',
        'password' => 'hemmelig123', 'password_confirmation' => 'hemmelig123',
        'role' => 'supervisor',
    ])->assertRedirect(route('dashboard'));

    expect(User::where('email', 'ny@demo.dk')->value('role'))->toBe('employee');
});

it('only lets inspectors and supervisors start inspections', function () {
    $location = Location::factory()->create();

    $this->actingAs(User::factory()->create())
        ->post(route('inspections.store'), ['location_id' => $location->id])
        ->assertForbidden();

    $this->actingAs(User::factory()->inspector()->create())
        ->post(route('inspections.store'), ['location_id' => $location->id])
        ->assertRedirect();
});

it('only lets the responsible person submit evidence', function () {
    $action = makeAction(User::factory()->create());

    $this->actingAs(User::factory()->create())
        ->post(route('actions.evidence', $action), ['note' => 'Hack'])
        ->assertSessionHas('error');

    expect($action->fresh()->status)->toBe(ActionStatus::Open)
        ->and($action->evidence()->count())->toBe(0);
});

it('only lets an authorized verifier mark an action as resolved', function () {
    $worker = User::factory()->create();
    $action = makeAction($worker);
    $action->submitEvidence($worker, 'Udført', null);

    $this->actingAs(User::factory()->inspector()->create())
        ->post(route('actions.approve', $action))->assertForbidden();
    $this->actingAs($worker)
        ->post(route('actions.approve', $action))->assertForbidden();

    expect($action->fresh()->status)->toBe(ActionStatus::AwaitingVerification);

    $this->actingAs(User::factory()->supervisor()->create())
        ->post(route('actions.approve', $action));

    expect($action->fresh()->status)->toBe(ActionStatus::Resolved);
});

it('prevents a supervisor from verifying their own solution', function () {
    $supervisor = User::factory()->supervisor()->create();
    $action = makeAction($supervisor);
    $action->submitEvidence($supervisor, 'Udført', null);

    $this->actingAs($supervisor)->post(route('actions.approve', $action))
        ->assertSessionHas('error', 'Du kan ikke verificere din egen løsning.');

    expect($action->fresh()->status)->toBe(ActionStatus::AwaitingVerification);
});

it('cannot resolve an action that has no evidence yet', function () {
    $action = makeAction(User::factory()->create());

    $this->actingAs(User::factory()->supervisor()->create())
        ->post(route('actions.approve', $action))
        ->assertSessionHas('error');

    expect($action->fresh()->status)->toBe(ActionStatus::Open);
});

it('requires a reason when rejecting a solution', function () {
    $worker = User::factory()->create();
    $action = makeAction($worker);
    $action->submitEvidence($worker, 'Udført', null);

    $this->actingAs(User::factory()->supervisor()->create())
        ->post(route('actions.reject', $action), ['comment' => ''])
        ->assertSessionHasErrors('comment');

    expect($action->fresh()->status)->toBe(ActionStatus::AwaitingVerification);
});

it('rejects a deadline in the past', function () {
    $action = makeAction(User::factory()->create());

    $this->actingAs(User::factory()->inspector()->create())
        ->post(route('actions.store', $action->finding), [
            'responsible_id' => $action->responsible_id,
            'description' => 'x',
            'due_date' => now()->subDay()->toDateString(),
        ])->assertSessionHasErrors('due_date');
});

it('shows overdue actions on the dashboard', function () {
    $action = makeAction(User::factory()->create());
    $action->update(['due_date' => now()->subDays(2)]);

    expect($action->fresh()->isOverdue())->toBeTrue();

    $this->actingAs(User::factory()->supervisor()->create())
        ->get(route('dashboard'))->assertOk()->assertSee('Ret fejlen');
});

it('only lets supervisors change roles', function () {
    $target = User::factory()->create();

    $this->actingAs(User::factory()->inspector()->create())
        ->patch(route('users.update', $target), ['role' => 'supervisor'])->assertForbidden();

    $this->actingAs(User::factory()->supervisor()->create())
        ->patch(route('users.update', $target), ['role' => 'inspector']);

    expect($target->fresh()->role)->toBe('inspector');
});
