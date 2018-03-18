@extends('layout.base')

@section('content')
<h1>Resultaten!</h1>

@if (count($bookings) == 0)
<div class="alert alert-warning">
  <h4 class="alert-heading">Oeps!</h4>
  <p class="mb-0">Geen boekingen gevonden.</p>
</div>
@else

<table class="table table-hover">
  <thead>
    <tr>
      <th>Van</th>
      <th>Tot</th>
      <th>Boeker</th>
      <th>Land</th>
      <th># gasten</th>
      <th>Kamer</th>
  </thead>
  <tbody>
    @foreach($bookings as $booking)
    <tr @if ($booking->isNow())
      class="booking__now @if ($booking->color()['luma'] > 180.0) reversed @endif"
      style="background-color: {{$booking->color()['color'] }}" @endif>
      <td>{{ $booking->arrival->format('d/m/Y') }}</td>
      <td>{{ $booking->departure->format('d/m/Y') }}</td>
      <td>
        <a href="{{ route('planning', ['date' => $booking->arrival->toDateString()]) }}">
          {{ $booking->customer->name }}
        </a>
      </td>
      <td>{{ $booking->customer->country_str }}</td>
      <td>{{ $booking->guests }}</td>
      <td>@isset($booking->rooms[0]){{ $booking->rooms[0]->name }}@endisset</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endif

@endsection
