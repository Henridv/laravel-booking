@extends('layout.base')

@section('content')
<h1>Planning</h1>

<div class="row">
  <div class="col-sm d-flex justify-content-between">
    <div>
      <div class="btn-group mb-2 btns__week" role="group">
        <a href="{{ route('planning', ['date' => $dates[0]['date']->copy()->subWeek()->toDateString()]) }}" class="btn btn-primary" id="btn__prev"><i class="fa fa-chevron-left"></i></a>
        <a href="{{ route('planning', ['date' => $dates[0]['date']->copy()->addWeek()->toDateString()]) }}" class="btn btn-primary" id="btn__next"><i class="fa fa-chevron-right"></i></a>
      </div>

      <a href="{{ route('planning') }}" class="btn btn-secondary mb-2">Vandaag</a>
    </div>
    <form action="{{ route('planning.change_date') }}" class="form-inline d-inline-flex  justify-content-center mb-2" method="POST">
      {{ csrf_field() }}
      <input class="form-control mr-2" name="goto_date"
        autocomplete="off" type="date" required
        value="{{ $dates[0]['date']->format('Y-m-d') }}">
      <button class="btn btn-success my-2 my-sm-0" type="submit">Ga</button>
    </form>

    <div>
      <a href="{{ route('export.email', ['date' => $dates[0]['date']->toDateString()]) }}" class="btn btn-secondary mb-2">Export emailadressen</a>
      <a href="#" id="printBtn" class="btn btn-secondary mb-2">Print <i class="fas fa-print fa-sm"></i></a>
      @can('add.booking')
      <a href="{{ route('booking.create') }}?date={{ $dates[0]['date']->toDateString() }}" class="btn btn-success mb-2">Nieuwe boeking</a>
      @endcan
    </div>
  </div>
</div>

<div class="row mb-2">
  <div class="col-sm d-flex justify-content-between">
    <textarea class="form-control" placeholder="Notities voor deze week..." id="weeklyNotes"
      data-date="{{ $dates[0]['date']->format('Y-m-d') }}"
      @isset($note) data-note-id="{{ $note->id }}" @endisset>@isset($note){{ $note->notes }}@endisset</textarea>
  </div>
</div>

<div id="planning__container" class="table-responsive">
<table class="table table-bordered" id="planning__data">
  <thead>
    <tr class="text-center">
      <th>kamer</th>
      <th>bed</th>
      @foreach($dates as $date)
        <th class="day @if(Carbon\Carbon::now()->isSameDay($date['date'])) day__now @endif">
          {{ $date['day'] }}<br />{{ $date['date_str'] }}
        </th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach($rooms as $room)
    <tr class="striped">
      <td class="text-center" rowspan="{{ $room->beds }}"><span class="roomname">{{ $room->name }}</span></td>
      @for($i=0; $i<$room->beds; $i++) {{-- loop through beds --}}
        @if ($i>0)
          <tr @if (in_array($i, $room->layout_splits)) class="layout-split" @endif>
        @endif
        <td><i class="fas fa-bed" title="bed {{ $i+1 }}"></i>&nbsp;</td>
        @for($d=0; $d<7; $d++) {{-- loop through days for this bed --}}
          @php $date = $dates[$d]; @endphp
          @if(!$room->bookings->isEmpty() && $booking = $room->hasBooking($date['date'], $i+1))
            <td
              data-toggle="tooltip"
              data-placement="left"
              data-html="true"
              title="{{ $booking->tooltip }}"
              colspan="{{ $booking->toShow($dates) }}"
              class="booked @if ($booking->color()['luma'] > 180.0) reversed @endif"
              style="background-color: {{ $booking->color()['color'] }}">
              <a href="{{ route('booking.show', $booking->id) }}">
                {{ $booking->getGuest($i)->name }}
                @if ($booking->isFirst($i)) &mdash; &euro;&nbsp;{{ $booking->deposit }} @endif</a>
            </td>
            @php $d += ($booking->toShow($dates)-1) @endphp
          @elseif (!$room->bookings->isEmpty() && $room->isBookedAsWhole($date['date']))
            <td class="non-bookable"></td>
          @else
            @can('add.booking')
            <td>
              <a href="{{ route('booking.create', ['date' => $date['date']->toDateString(), 'room' => $room->id]) }}" class="book__link">
                <i class="fas fa-plus"></i>
              </a>
            </td>
            @else
            <td></td>
            @endcan
          @endif
        @endfor {{-- days of the week --}}
        </tr>
      @endfor {{-- beds --}}
    @endforeach
  </tbody>
</table>
</div>

@endsection
