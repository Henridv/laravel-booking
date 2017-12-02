@extends('layout.base')

@section('content')
<h1>Welkom!</h1>

<p>Overzicht voor de komende twee weken.</p>
<table class="table table-hover table-striped">
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
    <tr>
      <td>{{ $booking->arrival->format('d/m/Y') }}</td>
      <td>{{ $booking->departure->format('d/m/Y') }}</td>
      <td>{{ $booking->customer->name }}</td>
      <td>{{ $booking->customer->country_str }}</td>
      <td>{{ $booking->guests }}</td>
      <td>{{ $booking->rooms[0]->name }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection
