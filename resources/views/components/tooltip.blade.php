<ul class="fa-ul">
    <li><span class="fa-li"><i class="fas fa-clock"></i></span> {{ $tooltip->arrival->format('H:i') }}</li>

@isset($tooltip->composition)
    <li><span class="fa-li"><i class="fas fa-users"></i></span> {{ $tooltip->composition }}</li>
@endisset

@isset($tooltip->comments)
    <li><span class="fa-li"><i class="fas fa-comments"></i></span> {{ $tooltip->comments }}</li>
@endisset

@if($tooltip->properties->options['asWhole'])
    <li><span class="fa-li"><i class="far fa-check-square"></i></span> Volledig geboekt</li>
@endif
</ul>
