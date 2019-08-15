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
                    <form method="post" action="{{route('fobs.update', $fob->id)}}">
                        <div class="form-group">
                            @csrf
                            @method('PATCH')
                            <label for="fob_id">Spare fob:</label>
                            <select class="form-control" id="fob_id" name="fob_id" aria-label="Spare fob selection">
                                @foreach($fobs as $value)
                                    <option value="{{$value->fob_id}}"
                                            aria-label="{{$value->name}}"<?php echo ($value->fob_id == $fob->fob_id) ? " selected" : "";?>>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_id">Staff member:</label>
                            <select class="form-control" id="user_id" name="user_id" aria-label="Staff member selection">
                                @foreach($staff as $value)
                                    <option value="{{$value->user_id}}"
                                            aria-label="{{$value->name}}"<?php echo ($value->user_id == $fob->user_id) ? " selected" : "";?>>{{$value->name}}</option>
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