@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Assign spare fob
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
                    <form method="post" action="{{route('fobs.store')}}">
                        <div class="form-group">
                            @csrf
                            <label for="FobID">Spare fob:</label>
                            <select class="form-control" id="FobID" name="FobID" aria-label="Spare fob selection">
                                @foreach($fobs as $value)
                                    <option value="{{$value->FobID}}"
                                            aria-label="{{$value->name}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="UserID">Staff member:</label>
                            <select class="form-control" id="UserID" name="UserID" aria-label="Staff member selection">
                                @foreach($staff as $value)
                                    <option value="{{$value->UserID}}"
                                            aria-label="{{$value->name}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Assign fob</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection