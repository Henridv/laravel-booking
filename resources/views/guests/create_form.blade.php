<div class="row">
  <div class="col-sm">
    <div class="form-group">
      <label for="firstnameInput">Voornaam</label>
      <input class="form-control" name="firstname" id="firstnameInput"  autocomplete="off" type="text" required
      @if(isset($guest)) value="{{ $guest->firstname }}" @endif>
    </div>
    <div class="form-group">
      <label for="lastnameInput">Familienaam</label>
      <input class="form-control" name="lastname" id="lastnameInput"  autocomplete="off" type="text" required
      @if(isset($guest)) value="{{ $guest->lastname }}" @endif>
    </div>
    <div class="form-group">
      <label for="emailInput">Email</label>
      <input class="form-control" name="email" id="emailInput"  autocomplete="off" type="email"
      @if(isset($guest)) value="{{ $guest->email }}" @endif>
    </div>
    <div class="form-group">
      <label for="phoneInput">GSM nummer</label>
      <input class="form-control" name="phone" id="phoneInput" autocomplete="off" type="text"
      @if(isset($guest)) value="{{ $guest->phone }}" @endif>
    </div>
    <div class="form-group">
      <label for="country">Land</label>
      @if(isset($countries))
        <select class="form-control" name="country" id="country" required>
          @foreach($countries as $code => $name)
            <option value="{{ $code }}" @if(isset($guest) && $guest->country===$code) selected @endif>{{ $name }}</option>
          @endforeach
        </select>
      @endif
    </div>
  </div>
</div>

