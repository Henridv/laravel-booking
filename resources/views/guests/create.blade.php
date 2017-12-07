@extends('layout.base')

@section('content')
<h1>Gast</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(isset($guest))
  <form action="{{ route('guest.edit', [$booking->id, $guest->id]) }}" method="POST">
@else
  <form action="{{ route('guest.create') }}" method="POST">
@endif
  {{ csrf_field() }}
  <fieldset>
    @include('guests.create_form')
    @if(isset($guest))
      {{-- <a href="{{ route('guest.delete', $guest) }}" type="submit" class="btn btn-danger">Verwijder gast</a> --}}
      <button type="submit" class="btn btn-primary float-right">Opslaan!</button>
    @else
      <button type="submit" class="btn btn-primary float-right">Voeg toe!</button>
    @endif
  </fieldset>
</form>

@endsection
