<?php

namespace App\Http\Controllers;

use App\Enums\ActionStatus;
use App\Models\CorrectiveAction;
use App\Models\Finding;
use App\Models\User;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tab = $request->query('tab', 'mine');

        $query = CorrectiveAction::with(['finding.location', 'finding.category', 'responsible']);

        match ($tab) {
            'verify' => $query->where('status', ActionStatus::AwaitingVerification),
            'overdue' => $query->overdue(),
            'all' => null,
            default => $query->where('responsible_id', $user->id)->where('status', '!=', ActionStatus::Resolved),
        };

        return view('actions.index', [
            'actions' => $query->orderBy('due_date')->paginate(15)->withQueryString(),
            'tab' => $tab,
        ]);
    }

    public function show(CorrectiveAction $action)
    {
        $action->load(['finding.location', 'finding.category', 'responsible', 'assigner', 'verifier', 'evidence.user', 'logs.user']);

        return view('actions.show', ['action' => $action]);
    }

    public function create(Finding $finding)
    {
        Gate::authorize('assign');

        return view('actions.create', [
            'finding' => $finding->load(['location', 'category']),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Finding $finding)
    {
        Gate::authorize('assign');

        $validated = $request->validate([
            'responsible_id' => 'required|exists:users,id',
            'description' => 'required|string|max:1000',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $action = CorrectiveAction::assign($finding, $request->user(), $validated);

        return redirect()->route('actions.show', $action)->with('success', 'Handling tildelt!');
    }

    public function start(Request $request, CorrectiveAction $action)
    {
        return $this->run(fn () => $action->start($request->user()), $action, 'Handlingen er sat i gang.');
    }

    public function submitEvidence(Request $request, CorrectiveAction $action)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
            'photo' => 'nullable|image|max:5120',
        ]);

        return $this->run(function () use ($request, $action, $validated) {
            // Gem først filen, når vi ved, at brugeren må indsende
            if (! $action->isResponsible($request->user())) {
                throw new DomainException('Kun den ansvarlige kan indsende bevis.');
            }
            $path = $request->file('photo')?->store('evidence', 'public');
            $action->submitEvidence($request->user(), $validated['note'], $path);
        }, $action, 'Bevis indsendt. Handlingen afventer verificering.');
    }

    public function approve(Request $request, CorrectiveAction $action)
    {
        Gate::authorize('verify');

        $validated = $request->validate(['comment' => 'nullable|string|max:1000']);

        return $this->run(
            fn () => $action->approve($request->user(), $validated['comment'] ?? null),
            $action,
            'Løsningen er godkendt og handlingen er løst.',
        );
    }

    public function reject(Request $request, CorrectiveAction $action)
    {
        Gate::authorize('verify');

        $validated = $request->validate(['comment' => 'required|string|max:1000']);

        return $this->run(
            fn () => $action->reject($request->user(), $validated['comment']),
            $action,
            'Løsningen er afvist og sendt tilbage til den ansvarlige.',
            'successD',
        );
    }

    /** Kører en workflow-handling og oversætter regelbrud til en fejlbesked. */
    private function run(callable $step, CorrectiveAction $action, string $message, string $flash = 'success')
    {
        try {
            $step();
        } catch (DomainException $e) {
            return redirect()->route('actions.show', $action)->with('error', $e->getMessage());
        }

        return redirect()->route('actions.show', $action)->with($flash, $message);
    }
}
