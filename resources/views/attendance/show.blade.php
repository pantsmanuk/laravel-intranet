@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Attendance Events for {{$events['yesterday']}}</h4>
                <h5><?php echo auth()->user()->name ?></h5>
                <hr>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th>In Time</th>
                        <th>Out Time</th>
                        <th>Sub-total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count = 0; ?>
                    @foreach ($events['door_events'] as $value)
                        <?php $t_count = $loop->count; ?>
                        <tr>
                            <td>{{$value['in']}}</td>
                            <td>{{$value['out']}}</td>
                            <td>{{$value['sub_total']}}</td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="2"><strong>Time Working:</strong></td>
                        <td><strong>{{$events['time_worked']}}</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="">
            <form class="float-right" method="post" action="{{route('attendance.store')}}">
                <div class="form-group">
                    @csrf
                    <button type="submit" class="btn btn-primary">Email timesheet</button>
                </div>
            </form>
        </div>
    </div>
@endsection