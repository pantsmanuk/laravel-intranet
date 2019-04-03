@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="table-responsive">
            <h4>Attendance &raquo; Active Staff Whereabouts for {{$reportDate}}</h4>
            <hr>

            <table class="table table-bordered table-striped">
                <thead>
                <tr class="table-info">
                    <th>Name</th>
                    <th>Event Type</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($employees as $value)
					<?php $t_count = $loop->count;
					    $t_eventtype = ['In', 'Out'];?>
                    <tr>
                        <td>{{$value->name}}</td>
                        <td>{{$t_eventtype[$value->doorevent['doorevent']]}}</td>
                    </tr>
                @endforeach
                <tr class="table-info">
                    <td><strong>Total:</strong></td>
                    <td><strong>{{$t_count}}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="table-responsive">
            <h4>Attendance &raquo; Historical Events Report for {{$reportDate}}</h4>
            <hr>

            <table class="table table-bordered table-striped">
                <thead>
                <tr class="table-info">
                    <th>Time</th>
                    <th>Site</th>
                    <th>Door</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Event Type</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($rows as $value)
                <?php $t_count = $loop->count; ?>
                <tr>
                    <td>{{$value->eventtime}}</td>
                    <td>{{$value->site}}</td>
                    <td>{{$value->area}}</td>
                    <td>{{$value->empref}}</td>
                    <td>{{$value->name}}</td>
                    <td>{{$value->eventtype}}</td>
                </tr>
                @endforeach
                <tr class="table-info">
                    <td><strong>Total:</strong></td>
                    <td colspan="6"><strong>{{$t_count}}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<?php /**
 *     <div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
<div class="card-header">Dashboard</div>

<div class="card-body">
@if (session('status'))
<div class="alert alert-success" role="alert">
{{ session('status') }}
</div>
@endif

You are logged in! (Would be great if this &lt;div class="row"&gt; vanished after 5 seconds or so...)
</div>
</div>
</div>
</div>
 */ ?>
