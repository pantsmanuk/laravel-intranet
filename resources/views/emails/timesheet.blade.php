@extends('layouts.mail')
@section('content')
    <!-- timesheet.blade.php -->
    <div class="container container-fluid">
        <div class="row">
            <p>Please find attached <strong>{{$user_name}}</strong>'s time sheet for <strong>{{$timesheet_date}}</strong>.</p>
        </div>
    </div>
@endsection