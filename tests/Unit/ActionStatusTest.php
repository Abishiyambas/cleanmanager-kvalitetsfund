<?php

use App\Enums\ActionStatus as S;

it('allows only the defined workflow transitions', function (S $from, S $to, bool $allowed) {
    expect($from->canTransitionTo($to))->toBe($allowed);
})->with([
    [S::Open, S::InProgress, true],
    [S::Open, S::AwaitingVerification, true],
    [S::Open, S::Resolved, false],
    [S::InProgress, S::AwaitingVerification, true],
    [S::InProgress, S::Resolved, false],
    [S::AwaitingVerification, S::Resolved, true],
    [S::AwaitingVerification, S::InProgress, true],
    [S::Resolved, S::Open, false],
    [S::Resolved, S::InProgress, false],
]);
