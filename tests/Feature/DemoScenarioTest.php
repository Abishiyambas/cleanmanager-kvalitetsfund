<?php

use App\Enums\ActionStatus;
use App\Models\Category;
use App\Models\CorrectiveAction;
use App\Models\Finding;
use App\Models\Inspection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
 * Demoen fra projektforslaget:
 * registrér snavset indgangsglas og tom sæbedispenser → tildel handlinger →
 * indsend bevis → afvis én løsning → verificér → identificér et gentaget fund.
 */
it('runs the full demo scenario from the project proposal', function () {
    Storage::fake('public');
    $this->seed();

    $ida = User::where('email', 'ida@demo.dk')->first();      // inspektør
    $mads = User::where('email', 'mads@demo.dk')->first();    // medarbejder
    $lene = User::where('email', 'lene@demo.dk')->first();    // medarbejder
    $sara = User::where('email', 'sara@demo.dk')->first();    // supervisor / verifier

    $glass = Category::where('name', 'Glas og vinduer')->first();
    $sanitary = Category::where('name', 'Sanitet og forbrugsvarer')->first();

    // 1. Inspektion og to fund
    $this->actingAs($ida)->post(route('inspections.store'), [
        'location_id' => 1, // Hovedindgang
        'template_id' => 1,
    ])->assertRedirect();
    $inspection = Inspection::latest('id')->first();

    $this->post(route('findings.store', $inspection), [
        'category_id' => $glass->id,
        'severity' => 'medium',
        'comment' => 'Snavset indgangsglas – fingeraftryk på hele døren.',
        'photo' => UploadedFile::fake()->image('glas.jpg'),
    ])->assertSessionHas('success', fn ($msg) => str_contains($msg, 'gentaget'));
    $glassFinding = Finding::latest('id')->first();

    $this->post(route('findings.store', $inspection), [
        'category_id' => $sanitary->id,
        'severity' => 'high',
        'comment' => 'Tom sæbedispenser ved indgangstoilet.',
    ])->assertRedirect();
    $soapFinding = Finding::latest('id')->first();

    Storage::disk('public')->assertExists($glassFinding->photo_path);

    // 2. Tildel handlinger
    $this->post(route('actions.store', $glassFinding), [
        'responsible_id' => $mads->id,
        'description' => 'Pudse indgangsglas.',
        'due_date' => now()->toDateString(),
    ])->assertRedirect();
    $glassAction = CorrectiveAction::latest('id')->first();

    $this->post(route('actions.store', $soapFinding), [
        'responsible_id' => $lene->id,
        'description' => 'Fyld sæbedispenser.',
        'due_date' => now()->addDay()->toDateString(),
    ])->assertRedirect();
    $soapAction = CorrectiveAction::latest('id')->first();

    // 3. Indsend bevis
    $this->actingAs($mads)->post(route('actions.start', $glassAction));
    $this->post(route('actions.evidence', $glassAction), [
        'note' => 'Glasset er pudset.',
        'photo' => UploadedFile::fake()->image('efter.jpg'),
    ]);
    $this->actingAs($lene)->post(route('actions.evidence', $soapAction), ['note' => 'Dispenser fyldt.']);

    expect($glassAction->fresh()->status)->toBe(ActionStatus::AwaitingVerification)
        ->and($soapAction->fresh()->status)->toBe(ActionStatus::AwaitingVerification);

    // 4. Afvis én løsning
    $this->actingAs($sara)->post(route('actions.reject', $glassAction), [
        'comment' => 'Der er stadig striber i nederste hjørne.',
    ]);
    expect($glassAction->fresh()->status)->toBe(ActionStatus::InProgress);

    // 5. Verificér
    $this->actingAs($mads)->post(route('actions.evidence', $glassAction), ['note' => 'Pudset igen, også hjørnet.']);
    $this->actingAs($sara)->post(route('actions.approve', $glassAction));
    $this->post(route('actions.approve', $soapAction));

    $glassAction->refresh();
    expect($glassAction->status)->toBe(ActionStatus::Resolved)
        ->and($glassAction->verified_by)->toBe($sara->id)
        ->and($glassAction->resolved_at)->not->toBeNull()
        ->and($glassAction->evidence)->toHaveCount(2)
        ->and($glassAction->logs->pluck('event')->all())->toBe([
            'Handling oprettet og tildelt',
            'Arbejdet er påbegyndt',
            'Bevis indsendt',
            'Løsning afvist',
            'Bevis indsendt',
            'Løsning godkendt',
        ]);

    // 6. Gentaget fund
    expect($glassFinding->isRecurring())->toBeTrue();
    $this->get(route('dashboard'))->assertOk()->assertSee('Gentagne fund')->assertSee('Hovedindgang');
    $this->get(route('findings.show', $glassFinding))->assertSee('Gentaget fund');
});
