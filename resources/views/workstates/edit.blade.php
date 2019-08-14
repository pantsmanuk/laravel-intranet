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
                    <form method="post" action="{{route('workstates.update', $workstate->id)}}">
                        <div class="form-group">
                            @csrf
                            @method('PATCH')
                            <label for="workstate">Work state:</label>
                            <input class="form-control" aria-label="Work state name" id="workstate" name="workstate"
                                   type="text" value="{{$workstate->workstate}}"/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update work state</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection