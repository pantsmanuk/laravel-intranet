@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="table-responsive">
            <h4>On-site attendance for {{$dtLondon->toDateString()}}</h4>
            <hr>

            <table class="table table-bordered table-striped">
                <thead>
                <tr class="table-info">
                    <th>Name</th>
                    <th>First Event Time</th>
                    <th>Last Known Location</th>
                </tr>
                </thead>
                <tbody>
                <?php $t_eventtype = ['In', 'Out'];?>
                @foreach ($employees as $value)
					<?php $t_count = $loop->count;?>
                    <tr>
                        <td>{{$value->name}}</td>
                        <td>{{$value->firstevent}}</td>
                        <td>{{$t_eventtype[$value->doorevent]}} ({{$value->dooreventtime}})</td>
                    </tr>
                @endforeach
                <tr class="table-info">
                    <td><strong>Total:</strong></td>
                    <td colspan="2"><strong>{{$t_count}}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="table-responsive">
            <h4>Off-site attendance for {{$dtLondon->toDateString()}}</h4>
            <hr>

            <table class="table table-bordered table-striped">
                <thead>
                <tr class="table-info">
                    <th>Name</th>
                    <th>Last Known Location</th>
                </tr>
                </thead>
                <tbody>
				<?php $t_eventtype = ['Remote Working', 'Holiday', 'Sickness', 'Sickness', 'Conference/Exhibition/Seminar', 'Delayed', 'Existing Customer Visit', 'New Business Visit', 'External Meeting', 'Approved Absence', 'Approved Absence']; // This should match absence_lookup? ?>
                @foreach ($offSite as $value)
					<?php $t_count = $loop->count;?>
                    <tr>
                        <td>{{$value['name']}}</td>
                        <td>{{$t_eventtype[$value['doorevent']]}}</td>
                    </tr>
                @endforeach
                <tr class="table-info">
                    <td><strong>Total:</strong></td>
                    <td colspan="2"><strong>{{$t_count}}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php /* ?>    <div class="row justify-content-center">
        <div class="table-responsive">
            <h4>Attendance &raquo; Historical Events Report for {{$dtLondon->subWeekdays(1)->toDateString()}}</h4>
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
                @foreach ($events as $value)
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
 <?php */ ?>
</div>
@endsection