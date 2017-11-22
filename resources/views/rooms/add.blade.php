@extends('layout.base')

@section('content')
<h1>Kamer toevoegen</h1>

<div class="row justify-content">
  <div class="col-md-auto">

@if(isset($room))
  <form action="{{ route('room.edit', $room->id) }}" method="POST">
@else
  <form action="{{ route('room.add') }}" method="POST">
@endif
  {{ csrf_field() }}
  <fieldset>
    <div class="form-group">
      <label for="nameInput">Naam</label>
      <input class="form-control" name="name" id="nameInput" placeholder="Naam" autocomplete="off" type="text" required @if(isset($room)) value="{{ $room->name }}" @endif>
    </div>
    <div class="form-group">
      <label for="bedsSelect"># bedden</label>
      <select class="form-control" name="beds" id="bedsSelect">
        @for($i=0; $i<10; $i++)
          <option @if(isset($room) && $room->beds == ($i+1)) selected @endif>{{ $i+1 }}</option>
        @endfor
      </select>
    </div>
    @if(isset($room))
      <button type="submit" class="btn btn-primary">Opslaan!</button>
    @else
      <button type="submit" class="btn btn-primary">Voeg toe!</button>
    @endif
  </fieldset>
</form>
</div>
</div>
@endsection
