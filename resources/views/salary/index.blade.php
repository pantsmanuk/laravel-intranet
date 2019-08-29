@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Salary Run</h4>
                <hr>
                @if(session()->get('success'))
                    <div class="alert alert-success">
                        {{session()->get('success')}}
                    </div>
                @endif
                @if(session()->get('errors'))
                    <div class="alert alert-danger">
                        {{session()->get('errors')}}
                    </div>
                @endif
                <p>This page otherwise intentionally left blank.</p>
            </div>
        </div>
    </div>
@endsection
