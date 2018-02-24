@extends('layout.base')

@section('content')
<h1>Gebruiker</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(isset($user) && $update_me)
  <form action="{{ route('profile.update') }}" method="POST">
@elseif(isset($user) && !$update_me)
  <form action="{{ route('user.update', [$user->id]) }}" method="POST">
@else
  <form action="{{ route('user.create') }}" method="POST">
@endif
    {{ csrf_field() }}
    <fieldset>
        <div class="row">
        <div class="col-sm-8 offset-sm-2">
            <div class="form-group row">
                <label for="usernameInput" class="col-sm-4 col-form-label">Gebruikersnaam</label>
                <div class="col-sm-8">
                <input class="form-control" name="username" id="usernameInput"  autocomplete="off" type="text" required
                    @if(old('username')) value="{{ old('username') }}"
                    @elseif(isset($user)) value="{{ $user->username }}" @endif>
                    </div>
            </div>
            <div class="form-group row">
                <label for="nameInput" class="col-sm-4 col-form-label">Naam</label>
                <div class="col-sm-8">
                <input class="form-control" name="name" id="nameInput"  autocomplete="off" type="text" required
                    @if(old('name')) value="{{ old('name') }}"
                    @elseif(isset($user)) value="{{ $user->name }}" @endif>
                    </div>
            </div>
            <div class="form-group row">
                <label for="emailInput" class="col-sm-4 col-form-label">Email</label>
                <div class="col-sm-8">
                <input class="form-control" name="email" id="emailInput"  autocomplete="off" type="email" required
                    @if(old('email')) value="{{ old('email') }}"
                    @elseif(isset($user)) value="{{ $user->email }}" @endif>
                    </div>
            </div>
            @if(!isset($user))
            <div class="form-group row">
                <label for="password" class="col-sm-4 col-form-label">Wachtwoord</label>
                <div class="col-sm-8">
                <input id="password" type="password" class="form-control" name="password" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="password_confirmation" class="col-sm-4 col-form-label">Herhaal wachtwoord</label>
                <div class="col-sm-8">
                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                </div>
            </div>
            @endif
            @if(!isset($user) || (isset($user) && !$update_me))
            <div class="form-group row">
                <label for="role" class="col-sm-4 col-form-label">Rol</label>
                <div class="col-sm-8">
                <select class="form-control custom-select" name="role" id="role" required>
                    @if(isset($roles))
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                        @if(old('role') && (int)old('role') === $role->id) selected
                        @elseif(isset($user) && $user->role === $role) selected @endif>{{ $role->name }}</option>
                    @endforeach
                    @endif
                </select>
                </div>
            </div>
            @endif

            @if(isset($user))
                <button type="submit" class="btn btn-primary float-right">Opslaan!</button>
            @else
                <button type="submit" class="btn btn-primary float-right">Voeg toe!</button>
            @endif
        </div>
        </div>
    </fieldset>
</form>

@endsection
