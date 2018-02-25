@extends('layout.base')

@section('content')

<a class="btn btn-primary mt-2 mb-4" href="{{ route('planning', ['date' => $booking->arrival->toDateString() ]) }}">
  Terug naar weekoverzicht</a>

<div class="row mt-2">
  <div class="col-sm">
    <h3>Boeking info
      @can('edit.booking')
      <a href="{{ route('booking.delete', $booking) }}" class="btn btn-danger float-right js-delete"><i class="far fa-trash-alt"> </i></a>
      <a href="{{ route('booking.edit', $booking) }}" class="btn btn-primary float-right">Wijzig boeking</a>
      @endcan
    </h3>
    <table class="table table-hover mt-2">
      <tr>
        <th>Aankomst</th>
        <td>{{ $booking->arrival->formatLocalized('%a, %e %b %Y') }} &mdash; {{ $booking->arrival->formatLocalized('%H:%M') }}</td>
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
        <th>Samenstelling</th>
        <td>{{ $booking->composition }}</td>
      </tr>

      <tr>
        <th>Kamer</th>
        <td>
          {{ $booking->rooms[0]->name }}
          @if ($booking->rooms[0]->properties->options['part'] != -1)
            &mdash; kamer {{ $booking->rooms[0]->properties->options['part']+1 }}
          @endif
        </td>
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
        <th>Te betalen</th>
        <td>&euro;&nbsp;{{ $booking->remaining }}</td>
      </tr>

      @can('edit.all')
      <tr>
        <th>Externe booking (booking.com,...)</th>
        <td>
          @if ($booking->ext_booking)
          ja
          @else
          nee
          @endif
        </td>
      </tr>
      @endcan

      <tr>
        <th>Opmerkingen</th>
        <td>{!! nl2br($booking->comments) !!}</td>
      </tr>
    </table>
  </div>
  <div class="col-sm">
    <h3>Boeker
      @can('edit.booking')
      <a href="{{ route('guest.edit', [$booking, $booking->customer]) }}" class="btn btn-primary float-right">Wijzig boeker</a>
      @endcan
    </h3>
    <table class="table table-hover mt-2">
      <tr>
        <th>Naam</th>
        <td
          class="booked {{ $booking->color()['luma'] > 180.0 ? 'reversed' : '' }}"
          style="background-color: {{ $booking->color()['color'] }}">
          {{ $booking->customer->name }}
        </td>
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
  </div>
</div>


<div class="modal" id="deleteBookingModal" tabindex="-1" role="dialog" aria-labelledby="deleteBookingLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Boeking verwijderen</h5>
        <button class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h4>Zeker dat je deze boeking wil verwijderen?</h4>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Nee</button>
        <button class="btn btn-primary" id="deleteBooking" data-id="{{ $booking->id }}">Ja</button>
      </div>
    </div>
  </div>
</div>
@endsection
