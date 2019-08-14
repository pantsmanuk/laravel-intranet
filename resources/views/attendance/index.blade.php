@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>On-site attendance for {{$dtLocal->toDateString()}}</h4>
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
                    <?php $t_count = 0;
                    $t_eventtype = ['In', 'Out'];?>
                    @foreach ($employees as $value)
                        <?php $t_count = $loop->count;?>
                        <tr>
                            <?php if (empty($value->spare_name)) { ?>
                            <td>{{$value->name}}</td>
                            <?php } else { ?>
                            <td>{{$value->spare_name}} <em>({{$value->forenames}})</em></td>
                            <?php }?>
                            <td>{{$value->firstevent}}</td>
                            <td>{{$t_eventtype[$value->doorevent]}} <em>({{$value->dooreventtime}})</em></td>
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
                <h4>Off-site attendance for {{$dtLocal->toDateString()}}</h4>
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
                            <td>{{$value['name']}}</td>
                            <td>{{$value['doorevent']}} @if (!empty($value['note'])) <em>({{$value['note']}}
                                    )</em> @endif</td>
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