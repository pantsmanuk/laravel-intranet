@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Download Groups
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
                    <form method="post" action="{{route('downloads.store')}}">
                        <div class="form-group required">
                            @csrf
                            <label for="name" class="control-label">Name:</label>
                            <input class="form-control" aria-label="Download group name"
                                   placeholder="Download group name" id="name" name="name" type="text" required/>
                        </div>
                        <div class="form-group required">
                            <label for="files" class="control-label">Files:</label>
                            <input class="form-control" aria-label="Files" placeholder="Files" id="files" name="files"
                                   type="text" required/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create download group</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection