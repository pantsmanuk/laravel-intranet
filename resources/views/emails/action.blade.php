@extends('layouts.mail')
@section('content')
            <!-- action.blade.php -->
            <div class="container container-fluid">
                <div class="row">
                    <p><strong>{{$user_name}}'s</strong> requested <strong>{{$holiday_type}}</strong> holiday on <strong>{{$holiday_dates}}</strong> has been <strong>{{strtolower($holiday_action)}}</strong>.</p>
                </div>
                <div class="row">
                    <p><small><em>This is an automated email. Please do not reply.</em></small></p>
                </div>
            </div>
@endsection