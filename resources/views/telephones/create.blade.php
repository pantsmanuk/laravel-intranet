@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Telephones
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
                    <form method="post" action="{{route('telephones.store')}}">
                        <div class="form-group required">
                            @csrf
                            <label for="user_id" class="control-label">Staff member:</label>
                            <select class="form-control" id="user_id" name="user_id"
                                    aria-label="Staff member selection">
                                @foreach($staff as $value)
                                    <option value="{{$value->id}}"
                                            aria-label="{{$value->name}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group required">
                            <label for="name" class="control-label">Description:</label>
                            <input class="form-control" aria-label="Telephone description"
                                   placeholder="Telephone description" id="name" name="name" type="text"/>
                        </div>
                        <div class="form-group required">
                            <label for="number" class="control-label">Number:</label>
                            <input class="form-control" aria-label="Telephone number" placeholder="Telephone number"
                                   id="number" name="number" type="text"/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create telephone</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection