@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Absence Types
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul id="error">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br/>
                    @endif
                    <form method="post" action="{{route('absencetypes.update', $absenceLookup->id)}}">
                        <div class="form-group">
                            @csrf
                            @method('PATCH')
                            <label for="name">Name:</label>
                            <input class="form-control" aria-label="Absence type name" id="name" name="name" type="text"
                                   value="{{$absenceLookup->name}}"/>
                        </div>
                        <button type="submit" class="btn btn-primary">Update absence type</button>
                        <button type="reset" class="btn btn-secondary">Reset form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection