@extends('layout.base')

@section('content')
<h1>Kamers en bedden</h1>

<p><a href="{{ route('room.add') }}" type="button" class="btn btn-primary">Kamer toevoegen</a></p>

<table class="table table-striped table-hover table-bordered">
  <thead class="thead-dark">
    <tr>
      <th>Naam</th>
      <th># bedden</th>
      <th width="5%"></th>
    </tr>
  </thead>
  <tbody>
    @foreach($rooms as $room)
      <tr>
        <td>{{ $room->name }}</td>
        <td>{{ $room->beds }}</td>
        <td>
          <div class="btn-group" role="group">
            <a href="{{ route('room.edit', $room->id) }}" class="btn btn-success btn-sm">edit</a>
            <a href="{{ route('room.del', $room->id) }}" class="btn btn-success btn-sm btn-danger"><i class="fa fa-times"></i></a>
          </div>
        </td>
      </tr>
    @endforeach
  </tbody>
</table> 
@endsection
