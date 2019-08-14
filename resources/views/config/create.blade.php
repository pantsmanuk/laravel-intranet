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
                    <form method="post" action="{{route('config.store')}}">
                        <div class="form-group">
                            @csrf
                            <label for="name">Key:</label>
                            <input class="form-control" aria-label="Configuration key" placeholder="Key" id="name"
                                   name="name" type="text"/>
                        </div>
                        <div class="form-group">
                            <label for="value">Value:</label>
                            <input class="form-control" aria-label="Configuration value" placeholder="Value" id="value"
                                   name="value" type="text"/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create configuration key/value pair</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection