<?php

namespace App\Enums;

enum ActionStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case AwaitingVerification = 'awaiting_verification';
    case Resolved = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Åben',
            self::InProgress => 'I gang',
            self::AwaitingVerification => 'Afventer verificering',
            self::Resolved => 'Løst',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Open => 'badge-open',
            self::InProgress => 'badge-progress',
            self::AwaitingVerification => 'badge-waiting',
            self::Resolved => 'badge-resolved',
        };
    }

    /**
     * Tilladte overgange. Kun en verifier kan gå fra "afventer verificering" til "løst";
     * det håndhæves i CorrectiveAction::approve().
     */
    public function canTransitionTo(self $to): bool
    {
        return in_array($to, match ($this) {
            self::Open => [self::InProgress, self::AwaitingVerification],
            self::InProgress => [self::AwaitingVerification],
            self::AwaitingVerification => [self::Resolved, self::InProgress],
            self::Resolved => [],
        }, true);
    }
}
