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
                    <form method="post" action="{{route('telephones.update', $lookup['id'])}}">
                        <div class="form-group">
                            @csrf
                            @method('PATCH')
                            <label for="user_id">Staff member:</label>
                            <select class="form-control" id="user_id" name="user_id"
                                    aria-label="Staff member selection">
                                @foreach($staff as $value)
                                    <option value="{{$value->id}}"
                                            aria-label="{{$value->name}}"<?php echo ($value->id == $lookup['user_id']) ? " selected" : "";?>>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Description:</label>
                            <input class="form-control" aria-label="Telephone description" id="name" name="name"
                                   type="text" value="{{$telephone->name}}"/>
                        </div>
                        <div class="form-group">
                            <label for="number">Number:</label>
                            <input class="form-control" aria-label="Telephone number" id="number" name="number"
                                   type="text" value="{{$telephone->number}}"/>
                        </div>
                        <button type="submit" class="btn btn-primary">Update telephone</button>
                        <button type="reset" class="btn btn-secondary">Reset form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection