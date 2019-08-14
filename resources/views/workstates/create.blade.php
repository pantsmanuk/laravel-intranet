@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Work States
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
                    <form method="post" action="{{route('workstates.store')}}">
                        <div class="form-group">
                            @csrf
                            <label for="workstate">Work state:</label>
                            <input class="form-control" aria-label="Work state name" placeholder="Work state name"
                                   id="workstate" name="workstate" type="text"/>
                        </div>
                        <button type="submit" class="btn btn-primary">Create work state</button>
                        <button type="reset" class="btn btn-secondary">Reset form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection