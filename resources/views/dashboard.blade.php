@php use App\Enums\ActionStatus; @endphp
<x-layout title="Overblik">
    <div class="flex items-center justify-between">
        <h2>Overblik</h2>
        @can('inspect')
            <a href="{{ route('inspections.create') }}" class="btn btn-primary">+ Ny inspektion</a>
        @endcan
    </div>

    {{-- Status på handlinger --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @foreach (ActionStatus::cases() as $status)
            <a href="{{ route('actions.index', ['tab' => 'all']) }}" class="panel stat no-underline">
                <div class="value">{{ $statusCounts[$status->value] ?? 0 }}</div>
                <div class="label">{{ $status->label() }}</div>
            </a>
        @endforeach
        <a href="{{ route('actions.index', ['tab' => 'overdue']) }}" class="panel stat no-underline">
            <div class="value text-red-600">{{ $overdue->count() }}</div>
            <div class="label">Overskredne frister</div>
        </a>
    </div>

    {{-- Pilot-målinger --}}
    <div class="panel mt-4 text-sm flex flex-wrap gap-x-8 gap-y-1">
        <span><strong>Løste handlinger:</strong> {{ $metrics['resolved'] }}</span>
        <span><strong>Gns. løsningstid:</strong> {{ $metrics['avg_hours'] !== null ? $metrics['avg_hours'].' t' : '–' }}</span>
        <span><strong>Median løsningstid:</strong> {{ $metrics['median_hours'] !== null ? $metrics['median_hours'].' t' : '–' }}</span>
        <span><strong>Løst inden frist:</strong> {{ $metrics['on_time_pct'] !== null ? $metrics['on_time_pct'].' %' : '–' }}</span>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mt-2">
        <section>
            <h3>Mine åbne handlinger</h3>
            @forelse ($myActions as $action)
                <x-card href="{{ route('actions.show', $action) }}" :highlight="$action->isOverdue()" link="Åbn">
                    <div class="font-semibold">{{ $action->description }}</div>
                    <div class="muted">{{ $action->finding->location->name }} · frist {{ $action->due_date->format('d.m.Y') }}</div>
                    <x-status :action="$action" />
                </x-card>
            @empty
                <p class="muted">Du har ingen åbne handlinger.</p>
            @endforelse

            <h3>Overskredne frister</h3>
            @forelse ($overdue as $action)
                <x-card href="{{ route('actions.show', $action) }}" :highlight="true" link="Åbn">
                    <div class="font-semibold">{{ $action->description }}</div>
                    <div class="muted">
                        {{ $action->finding->location->name }} · {{ $action->finding->category->name }} ·
                        {{ $action->responsible->name }} · frist {{ $action->due_date->format('d.m.Y') }}
                        ({{ $action->due_date->diffForHumans() }})
                    </div>
                </x-card>
            @empty
                <p class="muted">Ingen overskredne frister.</p>
            @endforelse
        </section>

        <section>
            <h3>Gentagne fund <span class="muted font-normal">(seneste {{ \App\Models\Finding::RECURRING_WINDOW_DAYS }} dage)</span></h3>
            @if ($recurring->isEmpty())
                <p class="muted">Ingen gentagne fund.</p>
            @else
                <table class="table">
                    <tr><th>Lokation</th><th>Kategori</th><th>Antal</th><th>Seneste</th></tr>
                    @foreach ($recurring as $row)
                        <tr>
                            <td><a href="{{ route('locations.show', $row->location_id) }}">{{ $row->location->name }}</a></td>
                            <td>
                                <a href="{{ route('findings.index', ['location_id' => $row->location_id, 'category_id' => $row->category_id]) }}">
                                    {{ $row->category->name }}
                                </a>
                            </td>
                            <td><span class="badge badge-recurring">{{ $row->total }}×</span></td>
                            <td>{{ \Illuminate\Support\Carbon::parse($row->last_seen)->format('d.m.Y') }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif

            <h3>Seneste ændringer</h3>
            <ul class="panel text-sm space-y-2">
                @forelse ($recentLogs as $log)
                    <li>
                        <span class="muted">{{ $log->created_at->format('d.m H:i') }}</span>
                        <strong>{{ $log->user->name }}</strong>: {{ $log->event }}
                        – <a href="{{ route('actions.show', $log->corrective_action_id) }}">{{ $log->action->finding->location->name }}</a>
                    </li>
                @empty
                    <li class="muted">Ingen aktivitet endnu.</li>
                @endforelse
            </ul>
        </section>
    </div>
</x-layout>
