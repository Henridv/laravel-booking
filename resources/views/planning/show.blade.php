@extends('layout.base')

@section('content')
{{--<div class="row">
  <div class="col-sm">
    <h1>{{ $booking->customer->name }}</h1>
  </div>
</div>
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#boeking-info">Boeking</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#customer">Gast</a>
  </li>
</ul>
<div id="myTabContent" class="tab-content">
  <div class="tab-pane active show" id="boeking-info"> --}}
<div class="row mt-2">
  <div class="col-sm">
    <h3>Boeking info</h3>
    <table class="table table-hover mt-2">
      <tr>
        <th>Aankomst</th>
        <td>{{ $booking->arrival->formatLocalized('%a, %e %b %Y') }}</td>
      </tr>
      <tr>
        <th>Vertrek</th>
        <td>{{ $booking->departure->formatLocalized('%a, %e %b %Y') }}</td>
      </tr>

      <tr>
        <th># gasten</th>
        <td>{{ $booking->guests }}</td>
      </tr>

      <tr>
        <th>Kamer</th>
        <td>{{ $booking->rooms[0]->name }}</td>
      </tr>

      <tr>
        <th>Basis prijs</th>
        <td>&euro;&nbsp;{{ $booking->basePrice }}</td>
      </tr>

      <tr>
        <th>Korting</th>
        <td>{{ $booking->discount }}&nbsp;%</td>
      </tr>

      <tr>
        <th>Voorschot</th>
        <td>&euro;&nbsp;{{ $booking->deposit }}</td>
      </tr>

      <tr>
        <th>Opmerkingen</th>
        <td>{!! nl2br($booking->comments) !!}</td>
      </tr>
    </table>
    <a href="{{ route('booking.delete', $booking) }}" class="btn btn-danger">Verwijder boeking</a>
    <a href="{{ route('booking.edit', $booking) }}" class="btn btn-primary float-right">Aanpassen</a>
  </div>
  <div class="col-sm">
    <h3>Boeker</h3>
    <table class="table table-hover mt-2">
      <tr>
        <th>Naam</th>
        <td>{{ $booking->customer->name }}</td>
      </tr>
      <tr>
        <th>E-mail</th>
        <td><a href="mailto:{{ $booking->customer->email }}">{{ $booking->customer->email }}</a></td>
      </tr>
      <tr>
        <th>GSM</th>
        <td><a href="tel:{{ $booking->customer->phone }}">{{ $booking->customer->phone }}</a></td>
      </tr>
      <tr>
        <th>Land</th>
        <td>{{ $booking->customer->country_str }}</td>
      </tr>
    </table>
    {{-- <a href="{{ route('booking.delete', $booking) }}" type="submit" class="btn btn-danger">Verwijder boeking</a> --}}
    <a href="{{ route('guest.edit', [$booking, $booking->customer]) }}" class="btn btn-primary float-right">Aanpassen</a>
  </div>
</div>
@endsection
