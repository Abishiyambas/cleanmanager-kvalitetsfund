@php use App\Enums\ActionStatus; @endphp
<x-layout title="Handling">
    <h2>Handling #{{ $action->id }} <x-status :action="$action" /></h2>

    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-4">
            <div class="panel">
                <p class="text-lg text-slate-900">{{ $action->description }}</p>
                <dl class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm mt-3">
                    <dt class="muted">Fund</dt>
                    <dd><a href="{{ route('findings.show', $action->finding) }}">{{ $action->finding->category->name }} · {{ $action->finding->location->name }}</a></dd>
                    <dt class="muted">Ansvarlig</dt><dd>{{ $action->responsible->name }}</dd>
                    <dt class="muted">Tildelt af</dt><dd>{{ $action->assigner->name }}</dd>
                    <dt class="muted">Frist</dt>
                    <dd @class(['text-red-600 font-semibold' => $action->isOverdue()])>{{ $action->due_date->format('d.m.Y') }}</dd>
                    @if ($action->resolved_at)
                        <dt class="muted">Verificeret løst</dt>
                        <dd>{{ $action->resolved_at->format('d.m.Y H:i') }} af {{ $action->verifier->name }} ({{ $action->resolutionHours() }} t)</dd>
                    @endif
                </dl>
            </div>

            {{-- Den ansvarlige --}}
            @if ($action->isResponsible(auth()->user()) && $action->status !== ActionStatus::Resolved)
                <div class="panel">
                    <h3 class="mt-0">Din opgave</h3>
                    @if ($action->status === ActionStatus::Open)
                        <form action="{{ route('actions.start', $action) }}" method="POST" class="inline">
                            @csrf
                            <button class="btn">Markér som i gang</button>
                        </form>
                    @endif

                    @if (in_array($action->status, [ActionStatus::Open, ActionStatus::InProgress]))
                        <form action="{{ route('actions.evidence', $action) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                            @csrf
                            <label for="note">Dokumentation – hvad er gjort?</label>
                            <textarea id="note" name="note" rows="3" required>{{ old('note') }}</textarea>
                            <label for="photo">Foto som bevis (valgfrit):</label>
                            <input type="file" id="photo" name="photo" accept="image/*" capture="environment">
                            <button class="btn btn-primary">Indsend bevis til verificering</button>
                        </form>
                    @else
                        <p class="muted">Dit bevis afventer verificering.</p>
                    @endif
                </div>
            @endif

            {{-- Verifier --}}
            @can('verify')
                @if ($action->status === ActionStatus::AwaitingVerification)
                    <div class="panel border-amber-300">
                        <h3 class="mt-0">Verificering</h3>
                        @if ($action->isResponsible(auth()->user()))
                            <p class="muted">Du er selv ansvarlig og kan ikke verificere din egen løsning.</p>
                        @else
                            <div class="grid md:grid-cols-2 gap-4">
                                <form action="{{ route('actions.approve', $action) }}" method="POST">
                                    @csrf
                                    <label for="approve_comment">Kommentar (valgfri)</label>
                                    <textarea id="approve_comment" name="comment" rows="2"></textarea>
                                    <button class="btn btn-primary">Godkend – markér som løst</button>
                                </form>
                                <form action="{{ route('actions.reject', $action) }}" method="POST">
                                    @csrf
                                    <label for="reject_comment">Begrundelse for afvisning</label>
                                    <textarea id="reject_comment" name="comment" rows="2" required></textarea>
                                    <button class="btn btn-danger">Afvis løsning</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
            @endcan

            <x-errors />

            <div>
                <h3>Indsendt dokumentation</h3>
                @forelse ($action->evidence as $evidence)
                    <div class="panel mb-2">
                        <p class="muted">{{ $evidence->created_at->format('d.m.Y H:i') }} · {{ $evidence->user->name }}</p>
                        <p>{{ $evidence->note }}</p>
                        @if ($evidence->photo_path)
                            <img src="{{ Storage::url($evidence->photo_path) }}" alt="Bevis" class="max-h-60 rounded border">
                        @endif
                    </div>
                @empty
                    <p class="muted">Intet bevis indsendt endnu.</p>
                @endforelse
            </div>
        </div>

        <aside>
            <h3 class="mt-0">Løsningshistorik</h3>
            <ol class="panel text-sm space-y-3">
                @foreach ($action->logs as $log)
                    <li class="border-l-2 border-violet-300 pl-3">
                        <div class="muted">{{ $log->created_at->format('d.m.Y H:i') }}</div>
                        <div><strong>{{ $log->user->name }}</strong>: {{ $log->event }}</div>
                        @if ($log->to_status)
                            <div class="text-xs">
                                {{ $log->from_status?->label() ?? '–' }} → {{ $log->to_status->label() }}
                            </div>
                        @endif
                        @if ($log->comment)
                            <div class="italic">"{{ $log->comment }}"</div>
                        @endif
                    </li>
                @endforeach
            </ol>
        </aside>
    </div>
</x-layout>
