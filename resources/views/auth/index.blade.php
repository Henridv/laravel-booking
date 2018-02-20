@extends('layout.base')

@section('content')
<h1>Admin</h1>
@if (session('success'))
    <div class="alert alert-dismissable alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
@if ($errors->has('new-password'))
    <div class="alert alert-danger">
        <strong>{{ $errors->first('new-password') }}</strong>
    </div>
@endif
@if ($errors->has('current-password'))
    <div class="alert alert-danger">
        <strong>{{ $errors->first('current-password') }}</strong>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active show" data-toggle="tab" href="#profile">Profiel</a>
    </li>
    @can('edit.users')
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#users">Gebruikers</a>
    </li>
    @endcan
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#password">Wachtwoord veranderen</a>
    </li>
</ul>
<div class="row mt-4">
    <div class="col">
        <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade show active" id="profile">
            <a href="" class="btn btn-success">Profiel aanpassen</a>
            <table class="table table-hover mt-4">
                <tr>
                    <th>Naam</th>
                    <td>{{ $user->name }}</td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>

                <tr>
                    <th>Gebruikersnaam</th>
                    <td>{{ $user->username }}</td>
                </tr>
            </table>
        </div>
        @can('edit.users')
        <div class="tab-pane fade" id="users">
            <a href="{{ route('user.add') }}" class="btn btn-success">Gebruiker toevoegen</a>
            <table class="table table-hover mt-4">
                <thead>
                <tr>
                    <th>Gebruikersnaam</th>
                    <th>Naam</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th width="5%"></th>
                </tr>
                </thead>
                @foreach ($users as $u)
                <tbody>
                <tr>
                    <td>{{ $u->username }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->role->name }}</td>
                    <td>
                        @if(Auth::user()->id !== $u->id)
                        <div class="btn-group" role="group">
                            <a href=""
                                class="btn btn-success btn-sm">edit</a>
                            <a href=""
                                class="btn btn-success btn-sm btn-danger"><i class="fa fa-times"></i></a>
                        </div>
                        @endif
                    </td>
                </tr>
                </tbody>
                @endforeach
            </table>
        </div>
        @endcan
        <div class="tab-pane fade" id="password">
            <form class="form-horizontal" method="POST" action="{{ route('passwd') }}">
                {{ csrf_field() }}

                <div class="form-group row">
                    <label for="current-password" class="col-sm-3 offset-sm-2 col-form-label">Huidig wachtwoord</label>
                    <div class="col-sm-5">
                        <input id="current-password" type="password" class="form-control" name="current-password" required autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="new-password" class="col-sm-3 offset-sm-2 col-form-label">Nieuw wachtwoord</label>
                    <div class="col-sm-5">
                        <input id="new-password" type="password" class="form-control" name="new-password" required autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="new-password_confirmation" class="col-sm-3 offset-sm-2 col-form-label">Herhaal nieuw wachtwoord</label>
                    <div class="col-sm-5">
                        <input id="new-password_confirmation" type="password" class="form-control" name="new-password_confirmation" required autofocus>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-5 offset-md-5">
                        <button type="submit" class="btn btn-primary">
                            Aanpassen
                        </button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

<div class="modal" id="newGuestModal" tabindex="-1" role="dialog" aria-labelledby="newGuestModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Nieuwe gast</h5>
        <button class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
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
                <select class="form-control" name="country" id="country" required>
                    @if(isset($countries))
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}" @if(isset($guest) && $guest->country===$code) selected @endif>{{ $name }}</option>
                    @endforeach
                    @endif
                </select>
                </div>
            </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
        <button class="btn btn-primary" id="saveGuest">Opslaan</button>
      </div>
    </div>
  </div>
</div>
@endsection
