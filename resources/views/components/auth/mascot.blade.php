@props(['pose', 'alt' => 'Maskot Owy'])
<span class="owy-mascot">
    <img src="{{ asset('maskot_owy/maskot_'.$pose.'.png') }}" alt="{{ $alt }}" draggable="false" {{ $attributes->class(['owy-mascot-image']) }}>
</span>
