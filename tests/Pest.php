<?php

use App\Models\Category;
use App\Models\CorrectiveAction;
use App\Models\Finding;
use App\Models\Inspection;
use App\Models\Location;
use App\Models\User;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/** Opretter et fund med en tildelt handling (status: åben). */
function makeAction(User $responsible, ?User $assigner = null, array $overrides = []): CorrectiveAction
{
    $assigner ??= User::factory()->inspector()->create();
    $location = Location::factory()->create();
    $inspection = Inspection::create(['location_id' => $location->id, 'inspector_id' => $assigner->id]);

    $finding = Finding::create([
        'inspection_id' => $inspection->id,
        'location_id' => $location->id,
        'category_id' => Category::factory()->create()->id,
        'created_by' => $assigner->id,
        'severity' => 'medium',
        'comment' => 'Testfund',
    ]);

    return CorrectiveAction::assign($finding, $assigner, [
        'responsible_id' => $responsible->id,
        'description' => 'Ret fejlen',
        'due_date' => now()->addDay()->toDateString(),
        ...$overrides,
    ]);
}
