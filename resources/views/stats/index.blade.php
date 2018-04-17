@extends('layout.base')

@section('content')
@php use App\Http\Controllers\StatsController; @endphp
<h1>Extra's</h1>

<form method="GET">
  <div class="form-row">
    <div class="form-group col-md-3">
      <div class="form-group">
        <label for="statType">Type</label>
          <select class="custom-select" name="type" id="statType">
            <option value="{{ StatsController::GUESTS_PER_COUNTRY }}"
                @isset($type) @if($type === StatsController::GUESTS_PER_COUNTRY) selected @endif @endisset>Gasten per land</option>
            <option value="{{ StatsController::NO_OF_NIGHTS }}"
                @isset($type) @if($type === StatsController::NO_OF_NIGHTS) selected @endif @endisset>Aantal overnachtingen</option>
          </select>
      </div>
    </div>
    <div class="form-group col-md-3">
      <div class="form-group">
        <label for="fromDateInput">Van</label>
        <div class="input-group date">
          <input type="date" class="form-control actual_range" name="from" id="fromDateInput" autocomplete="off" required
            @if(isset($from_date)) value="{{ $from_date->format('Y-m-d') }}"@endif>
        </div>
      </div>
    </div>
    <div class="form-group col-md-3">
      <div class="form-group">
        <label for="toDateInput">Tot</label>
        <div class="input-group date">
          <input type="date" class="form-control actual_range" name="to" id="toDateInput" autocomplete="off" required
            @if(isset($to_date)) value="{{ $to_date->format('Y-m-d') }}"@endif>
        </div>
      </div>
    </div>
    <div class="form-group col-md-3">
      <div class="form-group">
        <label>&nbsp;</label>
        <button type="submit" class="form-control btn btn-primary">Genereer</button>
      </div>
    </div>
  </div>
</form>

@if ($type === StatsController::GUESTS_PER_COUNTRY)
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Land</th>
            <th>Boekingen</th>
            <th>Gasten</th>
            <th>Gem. gasten per boeking</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($stats as $country)
            @if ($loop->last)
                @break
            @endif
            <tr>
                <td>{{ $country['country_name'] }}</td>
                <td>{{ $country['bookings'] }}</td>
                <td>{{ $country['guests'] }}</td>
                <td>{{ round($country['guests_per_booking'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>TOTAAL</th>
                <th>{{ $stats['totals']['bookings'] }}</th>
                <th>{{ $stats['totals']['guests'] }}</th>
                <th>{{ round($stats['totals']['guests_per_booking'], 2) }}</th>
            </tr>
        </tfoot>
    </table>
@elseif ($type === StatsController::NO_OF_NIGHTS)
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Aantal overnachtingen</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $stats['nights'] }} nachten</td>
            </tr>
        </tbody>
    </table>
@endif
@endsection
