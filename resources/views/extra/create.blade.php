@extends('layout.base')

@section('content')
<h1>Extra</h1>

@isset($extra)
  <form action="{{ route('extra.edit', $extra->id) }}" method="POST">
@else
  <form action="{{ route('extra.create') }}" method="POST">
@endisset
  {{ csrf_field() }}
  <div class="form-row">
    <div class="form-group col-md-3">
      <label for="nameInput">Naam</label>
      <input class="form-control" name="name" id="nameInput" placeholder="Naam" autocomplete="off" type="text" required @isset($extra) value="{{ $extra->name }}" @endisset>
    </div>
    <div class="form-group col-md-3">
      <label for="priceInput">Prijs</label>
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">€</span>
        </div>
        <input class="form-control" name="price" id="priceInput" placeholder="Prijs" autocomplete="off" type=number min=0 step=0.01 required @isset($extra) value="{{ $extra->price }}" @endisset>
      </div>
    </div>

    <div class="form-group col-md-3">
      <label for="perInput">Per</label>
      <input class="form-control" name="per" id="perInput" placeholder="Per (dag, persoon)" autocomplete="off" type="text" required @isset($extra) value="{{ $extra->per }}" @endisset>
    </div>

    <div class="form-group col-md-3">
      <label class="block-label" for="iconInput">Icoon</label>
      <input type="text" name="icon" class="form-control" placeholder="Fontawesome icon" autocomplete="off"
        @isset($extra) value="{{ $extra->fa_icon }}" @endisset>
    </div>
  </div>
  @isset($extra)
    <button type="submit" class="btn btn-primary">Opslaan!</button>
  @else
    <button type="submit" class="btn btn-primary">Voeg toe!</button>
  @endisset
</form>
@endsection
