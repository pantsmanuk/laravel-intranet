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
                        </div><br />
                    @endif
                    <form method="post" action="{{route('fobs.store')}}">
                        <div class="form-group">
                            @csrf
                            <label for="empref">Spare fob:</label>
                            <select class="form-control" id="empref" name="empref" aria-label="Spare fob selection">
                            @foreach($fobs as $value)
                                <option value="{{$value->empref}}" aria-label="{{$value->name}}">{{$value->name}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="staff_id">Staff member:</label>
                            <select class="form-control" id="staff_id" name="staff_id" aria-label="Staff member selection">
                            @foreach($staff as $value)
                                <option value="{{$value->staff_id}}" aria-label="{{$value->name}}">{{$value->name}}</option>
                            @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Assign fob</button>
                        <button type="reset" class="btn btn-secondary">Reset form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection