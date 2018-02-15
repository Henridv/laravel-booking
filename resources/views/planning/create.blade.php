@extends('layout.base')

@section('content')
<h1>Boeking</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('error'))
  <div class="alert alert-danger">
    <ul>
      <li>{{ session('error') }}</li>
    </ul>
  </div>
@endif

@if(isset($booking))
  <form action="{{ route('booking.edit', $booking->id) }}" method="POST">
@else
  <form action="{{ route('booking.create') }}" method="POST">
@endif
  {{ csrf_field() }}
  <fieldset>
  <div class="row justify-content-md-center">
    <div class="col-6">
      <div class="form-group">
        <label for="arrivalInput">Aankomst</label>
        <div class="input-group date">
          <input type="date" class="form-control actual_range" name="arrival" id="arrivalInput" autocomplete="off" required
            @if(old('arrival')) value="{{ old('arrival') }}"
            @elseif(isset($booking)) value="{{ $booking->arrival->format('Y-m-d') }}"
            @elseif(isset($date)) value="{{ $date->format('Y-m-d') }}"
            @endif>
          <select class="custom-select" name="arrivalTime" id="arrivalTime">
            @for($h=0; $h<24; $h++)
              @for($m=0; $m<60; $m += 30)
                @php $time = str_pad($h, 2, '0', STR_PAD_LEFT).':'.str_pad($m, 2, '0', STR_PAD_LEFT) @endphp
                <option
                  @if(old('arrivalTime') === $time) selected
                  @elseif(isset($booking) && $booking->arrival->format('H:i') === $time) selected
                  @endif
                  value="{{ $time }}">{{ $time }}</option>
              @endfor
            @endfor
          </select>
          <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
        </div>
      </div>
      <div class="form-group">
        <label for="departureInput">Vertrek</label>
        <div class="input-group date">
          <input type="date" class="form-control actual_range" name="departure" id="departureInput" autocomplete="off" required
            @if(old('departure')) value="{{ old('departure') }}"
            @elseif(isset($booking)) value="{{ $booking->departure->format('Y-m-d') }}"
            @elseif(isset($date)) value="{{ $date->addWeek()->format('Y-m-d') }}"
            @endif>
          <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
        </div>
      </div>
      <div class="form-group">
        <label for="customerSelect">Hoofdboeker</label>
        <select class="form-control custom-select" name="customer" id="customerSelect" placeholder="Selecteer gast...">
          <option></option>
          <option value="new-guest">Nieuwe gast...</option>
          @foreach($guests as $guest)
            <option
              @if(old('customer') == $guest->id) selected
              @elseif(isset($booking) && $booking->customer_id == $guest->id) selected @endif value="{{ $guest->id }}">{{ $guest->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="guestsSelect"># gasten</label>
        <select class="form-control custom-select" name="guests" id="guestsSelect">
          @for($i=0; $i<$max_beds; $i++)
            <option
              @if(old('guests') == $i+1) selected
              @elseif(isset($booking) && $booking->guests == ($i+1)) selected
              @endif>{{ $i+1 }}</option>
          @endfor
        </select>
      </div>
      <div class="form-group">
        <label class="form-check-label">
          <input class="form-check-input" name="as_whole" type="checkbox" value="yes"
            @if(old('as_whole')) checked
            @elseif(isset($options) && $options['asWhole']) checked
            @endif>
          Kamer volledig boeken?
        </label>
      </div>
    </div>
    <div class="col-6">
      <div class="form-group">
        <label for="roomSelect">Kamer</label>
        <select class="form-control custom-select" name="room" id="roomSelect">
          @foreach($rooms as $room) {{-- all rooms --}}
            <option {{-- room as whole --}}
            @if((int)old('room') === $room->id) selected
            @elseif($part === -1 && isset($room_id) && $room_id == $room->id) selected @endif
            value="{{ $room->id }}">{{ $room->name }}</option>
            @if (count($room->layout) > 1)
              @foreach($room->layout as $l)
                <option {{-- "sub" rooms --}}
                @if(old('room') === $room->id.';'.$loop->index) selected
                @elseif(isset($room_id) && isset($part) && $room_id == $room->id && $part == $loop->index) selected @endif
                value="{{ $room->id }};{{ $loop->index }}">-- Kamer {{ $loop->iteration }}</option>
              @endforeach
            @endif
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label for="basePriceInput">Basis prijs</label>
        <div class="input-group">
          <span class="input-group-addon">€</span>
          <input class="form-control" name="basePrice" id="basePriceInput" autocomplete="off" type="number" required min="0"
          @if(isset($booking)) value="{{ $booking->basePrice }}"
          @else value="{{ old('basePrice', 0) }}"
          @endif >
        </div>
      </div>
      <div class="form-group">
        <label for="discountInput">Korting</label>
        <div class="input-group">
          <span class="input-group-addon">%</span>
          <input class="form-control" name="discount" id="discountInput" autocomplete="off" type="number" required min="0" max="100"
            @if(isset($booking)) value="{{ $booking->discount }}"
            @else value="{{ old('discount', 0) }}"
            @endif>
        </div>
      </div>
      <div class="form-group">
        <label for="depositInput">Voorschot</label>
        <div class="input-group">
          <span class="input-group-addon">€</span>
          <input class="form-control" name="deposit" id="depositInput" autocomplete="off" type="number" required min="0"
            @if(isset($booking)) value="{{ $booking->deposit }}"
            @else value="{{ old('deposit', 0) }}"
            @endif>
        </div>
      </div>
      <div class="form-group">
        <label class="form-check-label">
          <input class="form-check-input" name="ext_booking" type="checkbox" value="yes"
          @if(old('ext_booking')) checked
          @elseif(isset($booking) && $booking->ext_booking) checked @endif>
          Externe boeking? (booking.com,&hellip;)
        </label>
      </div>
    </div>
  </div>
  <div class="row justify-content-md-center">
    <div class="col-12">
      <div class="form-group">
        <label for="compositionTextArea">Samenstelling</label>
        <textarea class="form-control" name="composition" id="compositionTextArea" rows="5">@if(old('composition')) {{ old('composition') }}@elseif(isset($booking) && $booking->composition) {{ $booking->composition }}@endif</textarea>
      </div>
    </div>
  </div>
  <div class="row justify-content-md-center">
    <div class="col-12">
      <div class="form-group">
        <label for="commentTextArea">Opmerkingen / Extra info</label>
        <textarea class="form-control" name="comments" id="commentTextArea" rows="5">@if(old('comments')) {{ old('comments') }}@elseif(isset($booking) && $booking->comments) {{ $booking->comments }}@endif</textarea>
      </div>
    </div>
  </div>
    @if(isset($booking))
      <a href="{{ route('booking.delete', $booking) }}" class="btn btn-danger">Verwijder boeking</a>
      <button type="submit" class="btn btn-primary float-right">Opslaan!</button>
    @else
      <button type="submit" class="btn btn-primary float-right">Voeg toe!</button>
    @endif
  </fieldset>
</form>

<div class="modal" id="newGuestModal" tabindex="-1" role="dialog" aria-labelledby="newGuestModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nieuwe gast</h5>
        <button class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        @php unset($guest) @endphp
        @include('guests.create_form')
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
        <button class="btn btn-primary" id="saveGuest">Opslaan</button>
      </div>
    </div>
  </div>
</div>

@endsection
