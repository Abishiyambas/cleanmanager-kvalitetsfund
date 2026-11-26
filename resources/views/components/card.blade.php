@props(['highlight' => false, 'link' => 'Se detaljer'])

<div @class(['highlight' => $highlight, 'card'])>
    <div class="flex-1">{{ $slot }}</div>
    @if ($attributes->has('href'))
        <a {{ $attributes }} class="btn">{{ $link }}</a>
    @endif
</div>
