@extends('layout.base')

@section('content')
<h1>Extra's</h1>

<p><a href="#" type="button" class="btn btn-primary">Extra toevoegen</a></p>

<table class="table table-hover">
    <thead class="thead-dark">
    <tr>
        <th>Naam</th>
        <th>Prijs</th>
        <th>Per</th>
        <th width="5%"></th>
    </tr>
    </thead>
    <tbody>
    @foreach($extras as $extra)
        <tr>
            <td>{{ $extra->name }}</td>
            <td>{{ $extra->price }}</td>
            <td>{{ $extra->per }}</td>
            <td>
                <div class="btn-group" role="group">
                    <a href="{{ route('room.edit', $extra->id) }}"
                        class="btn btn-success btn-sm">Aanpassen</a>
                    <a href="{{ route('extra.delete', $extra->id) }}"
                        class="btn btn-success btn-sm btn-danger"><i class="fa fa-trash-alt"></i></a>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
