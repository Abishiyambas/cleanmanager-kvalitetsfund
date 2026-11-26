<?php

namespace App\Models;

use App\Enums\ActionStatus;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CorrectiveAction extends Model
{
    protected $fillable = [
        'finding_id', 'responsible_id', 'assigned_by', 'description',
        'due_date', 'status', 'resolved_at', 'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => ActionStatus::class,
            'due_date' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    public function finding()
    {
        return $this->belongsTo(Finding::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function evidence()
    {
        return $this->hasMany(Evidence::class)->latest();
    }

    public function logs()
    {
        return $this->hasMany(ActionLog::class)->orderBy('created_at')->orderBy('id');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', '!=', ActionStatus::Resolved)
            ->whereDate('due_date', '<', today());
    }

    public function isOverdue(): bool
    {
        return $this->status !== ActionStatus::Resolved && $this->due_date->lt(today());
    }

    public function isResponsible(User $user): bool
    {
        return $this->responsible_id === $user->id;
    }

    /** Løsningstid i timer (fra handlingen blev oprettet til den blev verificeret løst). */
    public function resolutionHours(): ?float
    {
        return $this->resolved_at
            ? round($this->created_at->diffInMinutes($this->resolved_at) / 60, 1)
            : null;
    }

    // --- Workflow -----------------------------------------------------------

    public static function assign(Finding $finding, User $by, array $data): self
    {
        return DB::transaction(function () use ($finding, $by, $data) {
            $action = $finding->actions()->create([
                ...$data,
                'assigned_by' => $by->id,
                'status' => ActionStatus::Open,
            ]);

            $action->log($by, 'Handling oprettet og tildelt', null, ActionStatus::Open);

            return $action;
        });
    }

    public function start(User $by): void
    {
        $this->ensureResponsible($by);
        $this->moveTo(ActionStatus::InProgress, $by, 'Arbejdet er påbegyndt');
    }

    public function submitEvidence(User $by, string $note, ?string $photoPath): void
    {
        $this->ensureResponsible($by);

        DB::transaction(function () use ($by, $note, $photoPath) {
            $this->evidence()->create([
                'user_id' => $by->id,
                'note' => $note,
                'photo_path' => $photoPath,
            ]);
            $this->moveTo(ActionStatus::AwaitingVerification, $by, 'Bevis indsendt', $note);
        });
    }

    public function approve(User $by, ?string $comment = null): void
    {
        $this->ensureCanVerify($by);

        DB::transaction(function () use ($by, $comment) {
            $this->resolved_at = now();
            $this->verified_by = $by->id;
            $this->moveTo(ActionStatus::Resolved, $by, 'Løsning godkendt', $comment);
        });
    }

    public function reject(User $by, string $comment): void
    {
        $this->ensureCanVerify($by);
        $this->moveTo(ActionStatus::InProgress, $by, 'Løsning afvist', $comment);
    }

    private function moveTo(ActionStatus $to, User $by, string $event, ?string $comment = null): void
    {
        $from = $this->status;

        if (! $from->canTransitionTo($to)) {
            throw new DomainException("Handlingen kan ikke gå fra \"{$from->label()}\" til \"{$to->label()}\".");
        }

        $this->status = $to;
        $this->save();

        $this->log($by, $event, $from, $to, $comment);
    }

    private function log(User $by, string $event, ?ActionStatus $from, ?ActionStatus $to, ?string $comment = null): void
    {
        $this->logs()->create([
            'user_id' => $by->id,
            'event' => $event,
            'from_status' => $from,
            'to_status' => $to,
            'comment' => $comment,
        ]);
    }

    private function ensureResponsible(User $user): void
    {
        if (! $this->isResponsible($user)) {
            throw new DomainException('Kun den ansvarlige kan opdatere handlingen.');
        }
    }

    private function ensureCanVerify(User $user): void
    {
        if (! $user->isSupervisor()) {
            throw new DomainException('Kun en autoriseret verifier kan godkende eller afvise en løsning.');
        }
        if ($this->isResponsible($user)) {
            throw new DomainException('Du kan ikke verificere din egen løsning.');
        }
    }
}
