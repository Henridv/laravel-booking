@extends('layout.base')

@section('title', 'Planning - ' . $dates[0]['date']->format('d-m-Y'))

@php $planning_table = true; @endphp

@section('content')
<h1>Planning</h1>

<div class="row">
  <div class="col-sm d-flex justify-content-between">
    <div>
      <div class="btn-group mb-2 btns__week" role="group">
        <a href="{{ route('print', ['date' => $dates[0]['date']->copy()->subWeek()->toDateString()]) }}" class="btn btn-primary" id="btn__prev"><i class="fa fa-chevron-left"></i></a>
        <a href="{{ route('print', ['date' => $dates[0]['date']->copy()->addWeek()->toDateString()]) }}" class="btn btn-primary" id="btn__next"><i class="fa fa-chevron-right"></i></a>
      </div>

      <a href="{{ route('print') }}" class="btn btn-secondary mb-2">Vandaag</a>
    </div>

    <div>
      <a href="#" id="printBtn" class="btn btn-secondary mb-2 js-print">Print <i class="fas fa-print fa-sm"></i></a>
    </div>
  </div>
</div>

{{-- notes --}}
@isset($note)
  <div class="row mb-1 print-notes">
    <div class="col-sm">
      <div>
        <p class="p-3">{{ $note->notes }}</p>
      </div>
    </div>
  </div>
@endisset

{{-- actual planning --}}
<div id="planning__container">
  <table class="table table-bordered table-print" id="planning__data">
    <thead>
      <tr class="text-center">
        <th>kamer</th>
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
        <td class="text-center"><span class="roomname">{{ $room->name }}</span></td>
        @for($d=0; $d<7; $d++)
          @php $date = $dates[$d]; @endphp
          @if(isset($room->bookings) && ($bookings = $room->hasBooking($date['date'])))
            <td>
              @foreach($bookings as $booking)
                @php $b = $booking->first(); @endphp
                <div class="booking-card">
                  <div><strong>{{ $b->customer->firstname }}</strong></div>
                  <ul class="fa-ul">
                    @if ($b->arrival->isSameDay($date['date']))
                      <li><span class="fa-li"><i class="fas fa-clock"></i></span>{{ $b->arrival->format('H:i') }}</li>
                    @endif
                    <li><span class="fa-li"><i class="fas fa-bed"></i></span>{{ $b->guests }}</li>
                    @isset($b->composition)
                      <li><span class="fa-li"><i class="fas fa-users"></i></span>{{ $b->composition }}</li>
                    @endisset
                    @isset($b->comments)
                      <li><span class="fa-li"><i class="fas fa-comments"></i></span>{{ $b->comments }}</li>
                    @endisset
                    @if($b->properties->options['asWhole'])
                      <li><span class="fa-li"><i class="far fa-check-square"></i></span>Volledig geboekt</li>
                    @endif
                  </ul>
                </div>
                <hr />
              @endforeach
            </td>
          @else
            <td></td>
          @endif
        @endfor
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

@endsection
