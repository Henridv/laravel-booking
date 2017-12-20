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
        value="{{ $dates[0]['date']->format('d-m-Y') }}">
      <button class="btn btn-success my-2 my-sm-0" type="submit">Ga</button>
    </form>

    <div>
      <a href="{{ route('booking.create') }}?date={{ $dates[0]['date']->toDateString() }}" class="btn btn-success mb-2">Nieuwe boeking</a>
      <a  href="#" id="printBtn" class="btn btn-secondary mb-2">Print <i class="fas fa-print fa-sm"></i></a>
    </div>
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
    @php $row=1 @endphp
    @foreach($rooms as $room)
    <tr class="striped">
      <td class="text-center" rowspan="{{ $room->beds }}"><span class="roomname">{{ $room->name }}</span></td>
      @for($i=0; $i<$room->beds; $i++)
        {!! ($i>0) ? '<tr>' : '' !!}
        <td><i class="fas fa-bed" title="bed {{ $i+1 }}"></i>&nbsp;</td>
        @for($d=0; $d<7; $d++)
          @php $date = $dates[$d]; @endphp
          @if(isset($room->bookings) && ($booking = $room->hasBooking($date['date'], $i+1)))
            <td
              data-toggle="tooltip"
              data-placement="left"
              title="{{ $booking->comments }}"
              colspan="{{ $booking->toShow($dates) }}"
              class="booked @if ($booking->color()['luma'] > 180.0) reversed @endif"
              style="background-color: {{ $booking->color()['color'] }}">
              <a href="{{ route('booking.show', $booking->id) }}">
                {{ $booking->customer->name }}</a>
            </td>
            @php $d += ($booking->toShow($dates)-1) @endphp
          @else
            <td @php echo ($i == 0) ? 'class="striped"' : '' @endphp>
              <a href="{{ route('booking.create', ['date' => $date['date']->toDateString(), 'room' => $room->id]) }}" class="book__link">
                <i class="fas fa-plus"></i>
              </a>
            </td>
          @endif
        @endfor
        </tr>
      @endfor
    @endforeach
  </tbody>
</table>
</div>

@endsection
