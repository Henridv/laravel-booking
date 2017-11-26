<div class="row justify-content">
  <div class="col-12">
  <fieldset>
    <div class="form-group">
      <label for="firstnameInput">Voornaam</label>
      <input class="form-control" name="firstname" id="firstnameInput" placeholder="Voornaam" autocomplete="off" type="text" required>
    </div>
    <div class="form-group">
      <label for="lastnameInput">Familienaam</label>
      <input class="form-control" name="lastname" id="lastnameInput" placeholder="Familienaam" autocomplete="off" type="text" required>
    </div>
    <div class="form-group">
      <label for="emailInput">Email</label>
      <input class="form-control" name="email" id="emailInput" placeholder="Email" autocomplete="off" type="email" required>
    </div>
    <div class="form-group">
      <label for="phoneInput">GSM nummer</label>
      <input class="form-control" name="phone" id="phoneInput" placeholder="GSM nummer" autocomplete="off" type="text" required>
    </div>
    <div class="form-group">
      <label for="country">Land</label>
      {{-- <input class="form-control" name="country" id="country" placeholder="Land" autocomplete="off" type="text" required> --}}
      <select class="form-control" name="country" id="country" required>
        @if(isset($countries))
          @foreach($countries as $code => $name)
            <option value="{{ $code }}">{{ $name }}</option>
          @endforeach
        @endif
      </select>
    </div>
</div>

