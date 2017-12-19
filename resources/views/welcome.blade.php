@extends('layout.base')

@section('content')
<h1>Welkom!</h1>

<h3>Overzicht voor de komende twee weken</h3>
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
      <td>{{ $booking->rooms[0]->name }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<h3 class="mt-5">Vertrekken vandaag</h3>
<table class="table table-hover">
  <thead>
    <tr>
      <th>Van</th>
      <th>Tot</th>
      <th>Boeker</th>
      <th># gasten</th>
      <th>Kamer</th>
      <th>Prijs</th>
  </thead>
  <tbody>
    @foreach($leaving as $booking)
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
      <td>{{ $booking->guests }}</td>
      <td>{{ $booking->rooms[0]->name }}</td>
      <td>&euro;&nbsp;{{ $booking->basePrice * (1-$booking->discount) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
