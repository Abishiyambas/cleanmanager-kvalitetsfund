<?php

namespace App\Models;

use App\Enums\ActionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Finding extends Model
{
    /** Et fund tæller som gentaget, hvis samme lokation + kategori har et fund inden for så mange dage. */
    public const RECURRING_WINDOW_DAYS = 90;

    public const SEVERITIES = [
        'low' => 'Lav',
        'medium' => 'Middel',
        'high' => 'Høj',
        'critical' => 'Kritisk',
    ];

    protected $fillable = [
        'inspection_id', 'location_id', 'category_id', 'created_by',
        'severity', 'comment', 'photo_path',
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actions()
    {
        return $this->hasMany(CorrectiveAction::class);
    }

    public function severityLabel(): string
    {
        return self::SEVERITIES[$this->severity] ?? $this->severity;
    }

    /** Løst = har mindst én handling, og alle handlinger er verificeret løst. */
    public function isResolved(): bool
    {
        return $this->actions->isNotEmpty()
            && $this->actions->every(fn ($a) => $a->status === ActionStatus::Resolved);
    }

    /** Tidligere fund med samme lokation og kategori inden for vinduet. */
    public function previousSimilar(): Collection
    {
        return Finding::query()
            ->where('location_id', $this->location_id)
            ->where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->where('created_at', '<=', $this->created_at)
            ->where('created_at', '>=', $this->created_at->copy()->subDays(self::RECURRING_WINDOW_DAYS))
            ->latest()
            ->get();
    }

    public function isRecurring(): bool
    {
        return $this->previousSimilar()->isNotEmpty();
    }

    /** Lokation + kategori med mindst 2 fund inden for vinduet. */
    public static function recurringGroups(): Collection
    {
        return Finding::query()
            ->selectRaw('location_id, category_id, COUNT(*) as total, MAX(created_at) as last_seen')
            ->where('created_at', '>=', now()->subDays(self::RECURRING_WINDOW_DAYS))
            ->groupBy('location_id', 'category_id')
            ->having('total', '>=', 2)
            ->orderByDesc('total')
            ->with(['location', 'category'])
            ->get();
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['location_id'] ?? null, fn ($q, $v) => $q->where('location_id', $v))
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['severity'] ?? null, fn ($q, $v) => $q->where('severity', $v));
    }
}
