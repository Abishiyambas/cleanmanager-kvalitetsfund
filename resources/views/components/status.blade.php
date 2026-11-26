@props(['action'])

<span class="badge {{ $action->status->badgeClass() }}">{{ $action->status->label() }}</span>
@if ($action->isOverdue())
    <span class="badge badge-overdue">Overskredet</span>
@endif
