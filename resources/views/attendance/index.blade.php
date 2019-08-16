@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>On-site attendance for {{\Illuminate\Support\Facades\Date::now('Europe/London')->format('j F')}}</h4>
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
                    <?php $t_count = 0; ?>
                    @foreach ($onSite as $value)
                        <?php $t_count = $loop->count; ?>
                        <tr>
                            <?php if (empty($value->spare_name)) { ?>
                            <td>{{$value->name}}</td>
                            <?php } else { ?>
                            <td>{{$value->spare_name}} <em>({{$value->forenames}})</em></td>
                            <?php }?>
                            <td>{{$value->first_event}}</td>
                            <td>{{$value->door_event}} <em>({{$value->door_event_time}})</em></td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="3"><strong>Total: {{$t_count}}</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Off-site attendance for {{\Illuminate\Support\Facades\Date::now('Europe/London')->format('j F')}}</h4>
                <hr>

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th>Name</th>
                        <th>Last Known Location</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count = 0; ?>
                    @foreach ($offSite as $value)
                        <?php $t_count = $loop->count;?>
                        <tr>
                            <td>{{$value->name}}</td>
                            <td>{{$value['door_event']}} @if (!empty($value['note'])) <em>({{$value['note']}})</em> @endif</td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="2"><strong>Total: {{$t_count}}</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection