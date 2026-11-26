<?php

namespace App\Http\Controllers;

use App\Enums\ActionStatus;
use App\Models\ActionLog;
use App\Models\CorrectiveAction;
use App\Models\Finding;

class DashboardController extends Controller
{
    public function index()
    {
        $statusCounts = CorrectiveAction::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overdue = CorrectiveAction::overdue()
            ->with(['finding.location', 'finding.category', 'responsible'])
            ->orderBy('due_date')
            ->get();

        $resolved = CorrectiveAction::where('status', ActionStatus::Resolved)->get();
        $hours = $resolved->map->resolutionHours()->sort()->values();

        $metrics = [
            'resolved' => $resolved->count(),
            'avg_hours' => $hours->isNotEmpty() ? round($hours->avg(), 1) : null,
            'median_hours' => $hours->isNotEmpty() ? $hours->median() : null,
            'on_time_pct' => $resolved->isNotEmpty()
                ? round(100 * $resolved->filter(fn ($a) => $a->resolved_at->lte($a->due_date->endOfDay()))->count() / $resolved->count())
                : null,
        ];

        return view('dashboard', [
            'statusCounts' => $statusCounts,
            'overdue' => $overdue,
            'recurring' => Finding::recurringGroups(),
            'metrics' => $metrics,
            'myActions' => auth()->user()->responsibleActions()
                ->where('status', '!=', ActionStatus::Resolved)
                ->with('finding.location')
                ->orderBy('due_date')
                ->get(),
            'recentLogs' => ActionLog::with(['user', 'action.finding.location'])->latest('id')->take(8)->get(),
        ]);
    }
}
