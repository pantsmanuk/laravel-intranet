@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Configuration
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
                    <form method="post" action="{{route('config.update', $config->id)}}">
                        <div class="form-group required">
                            @csrf
                            @method('PATCH')
                            <label for="name" class="control-label">Key:</label>
                            <input class="form-control" aria-label="Configuration key name" id="name" name="name"
                                   type="text" value="{{$config->name}}" required/>
                        </div>
                        <div class="form-group required">
                            <label for="value" class="control-label">Value:</label>
                            <input class="form-control" aria-label="Configuration value" id="value" name="value"
                                   type="text" value="{{$config->value}}" required/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update configuration key/value pair</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection