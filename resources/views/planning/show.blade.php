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
        <td>Aankomst</td>
        <td>{{ $booking->arrival->formatLocalized('%a, %e %b %Y') }}</td>
      </tr>
      <tr>
        <td>Vertrek</td>
        <td>{{ $booking->departure->formatLocalized('%a, %e %b %Y') }}</td>
      </tr>

      <tr>
        <td># gasten</td>
        <td>{{ $booking->guests }}</td>
      </tr>

      <tr>
        <td>Kamer</td>
        <td>{{ $booking->rooms[0]->name }}</td>
      </tr>

      <tr>
        <td>Basis prijs</td>
        <td>&euro;&nbsp;{{ $booking->basePrice }}</td>
      </tr>

      <tr>
        <td>Korting</td>
        <td>{{ $booking->discount }}&nbsp;%</td>
      </tr>

      <tr>
        <td>Voorschot</td>
        <td>&euro;&nbsp;{{ $booking->deposit }}</td>
      </tr>

      <tr>
        <td>Opmerkingen</td>
        <td>{!! nl2br($booking->comments) !!}</td>
      </tr>
    </table>
    <a href="{{ route('booking.delete', $booking) }}" type="submit" class="btn btn-danger">Verwijder boeking</a>
    <a href="{{ route('booking.edit', $booking) }}" type="submit" class="btn btn-primary float-right">Aanpassen</a>
  </div>
  <div class="col-sm">
    <h3>Boeker</h3>
    <table class="table table-hover mt-2">
      <tr>
        <td>Naam</td>
        <td>{{ $booking->customer->name }}</td>
      </tr>
      <tr>
        <td>E-mail</td>
        <td>{{ $booking->customer->email }}</td>
      </tr>
      <tr>
        <td>GSM</td>
        <td>{{ $booking->customer->phone }}</td>
      </tr>
      <tr>
        <td>Land</td>
        <td>{{ $booking->customer->country_str }}</td>
      </tr>
    </table>
    {{-- <a href="{{ route('booking.delete', $booking) }}" type="submit" class="btn btn-danger">Verwijder boeking</a> --}}
    <a href="{{ route('guest.edit', [$booking, $booking->customer]) }}" type="submit" class="btn btn-primary float-right">Aanpassen</a>
  </div>
</div>
@endsection
