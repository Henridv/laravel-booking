@extends('layout.base')

@section('title', $booking->customer->name)

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

  {{-- booker info --}}
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


    @unless($booking->guests === 1)
    <h3>Extra gasten
      @if ($booking->extraGuests->count() < $booking->guests-1)
        <a href="" class="btn btn-primary float-right js-add-extra-guest">Extra gast toevoegen</a>
      @endif
    </h3>
    @endunless

    @unless($booking->extraGuests->isEmpty())
    <table class="table table-hover mt-2" id="extraGuestTable">
    @foreach($booking->extraGuests as $guest)
      <tr>
        <td>{{ $guest->name }}</td>
        <td class="text-right">
          {{-- <a href="" class="btn btn-primary js-edit-extra-guest" data-guest-id="{{ $guest-> id }}"><i class="fas fa-pencil-alt"> </i></a> --}}
          <a href="{{ route('booking.extra.delete', [$booking, $guest]) }}" class="btn btn-danger"><i class="far fa-trash-alt"> </i></a>
        </td>
      </tr>
    @endforeach
    </table>
    @endunless
  </div>
</div>

<h3>Extra's
  <a href="" class="btn btn-primary float-right js-add-extra">Extra toevoegen</a>
</h3>

@unless($booking->extras->isEmpty())
<table class="table table-hover mt-2" id="extrasTable">
  <thead class="thead-dark">
    <tr>
      <th>Aantal</th>
      <th>Naam</th>
      <th>Prijs</th>
      <th>Per</th>
      <th>&nbsp;</th>
    </tr>
  </thead>
@foreach($booking->extras as $extra)
  <tr>
    <td>{{ $extra->pivot->amount }}</td>
    <td>{{ $extra->name }}</td>
    <td>&euro; {{ $extra->price }}</td>
    <td>{{ $extra->per }}</td>
    <td class="text-right">
      <a href="{{ route('booking.extras.delete', [$booking, $extra]) }}" class="btn btn-danger"><i class="far fa-trash-alt"> </i></a>
    </td>
  </tr>
@endforeach
</table>
@else
<p class="alert alert-light">Geen extra's voor deze boeking.</p>
@endunless

@can('edit.booking')
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

<div class="modal" id="extraGuestModal" tabindex="-1" role="dialog" aria-labelledby="extraGuestModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Extra gast</h5>
        <button class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <select class="form-control custom-select mt-2 js-extra-guest-select" name="extra-guest" placeholder="Selecteer gast..." id="guestSelect">
          <option></option>
          <option value="new-guest">Nieuwe gast...</option>
          @foreach($guests as $guest)
            <option
              @if(isset($booking) && $booking->customer_id == $guest->id) selected @endif value="{{ $guest->id }}">{{ $guest->name }}</option>
          @endforeach
        </select>

        @php unset($guest) @endphp
        <div class="new-extra-guest" style="display: none" data-booking-id="{{ $booking->id }}">
          @include('guests.create_form')
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
        <button class="btn btn-primary" id="addExtraGuest">Opslaan</button>
      </div>
    </div>
  </div>
</div>

<div class="modal" id="extrasModal" tabindex="-1" role="dialog" aria-labelledby="extrasModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="extrasModalLabel">Extra toevoegen</h5>
        <button class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <select class="form-control custom-select my-2" name="extra" placeholder="Selecteer extra..." id="extraSelect">
          @foreach($extras as $extra)
            <option value="{{ $extra->id }}">{{ $extra->name }}</option>
          @endforeach
        </select>

        <input class="form-control" name="amount" placeholder="Aantal" type=number min=1 />
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
        <button class="btn btn-primary" id="addExtra">Voeg toe</button>
      </div>
    </div>
  </div>
</div>
@endcan

@endsection
