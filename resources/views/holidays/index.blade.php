@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h4>Your Absences for {{$sYear}}</h4>
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
            @if(!auth()->guest())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="table-info">
                            <th rowspan="2">Start</th>
                            <th rowspan="2">End</th>
                            <th colspan="2">Days used</th>
                            <th rowspan="2">Note</th>
                            <th rowspan="2">Approved</th>
                            <th colspan="2" rowspan="2">Actions</th>
                        </tr>
                        <?php $tEntitlement = \App\Employee::where('id', '=', auth()->id())->pluck('holiday_entitlement')->first();
                        $tCarried = \App\Employee::where('id', '=', auth()->id())->pluck('holiday_carried_forward')->first();
                        $t_entitlementRemaining = $tEntitlement + $tCarried;
                        $t_paid = 0;
                        $t_unpaid = 0;
                        $t_approval = [0 => 'Not Approved', 1 => 'Approved'];?>
                        <tr class="table-info">
                            <th>Paid ({{$t_entitlementRemaining}})</th>
                            <th>Unpaid</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $t_count = 0; ?>
                        @foreach ($absences as $holiday)
                            <?php $t_count = $loop->count;?>
                            <tr<?php if ($holiday->absence_id == 1 && $holiday->started_at >= now('Europe/London')) {
                                echo ($holiday->approved == 0) ? ' class="table-warning"' : ' class="table-success"';
                            }?>>
                                <td>{{$holiday->started_at}}</td>
                                <td>{{$holiday->ended_at}}</td>
                                <td>{{$holiday->days_paid}} ({{$t_entitlementRemaining -= $holiday->days_paid}})</td>
                                <td>{{$holiday->days_unpaid}}</td>
                                <td><em>({{$holiday->absence_type}})</em> {{$holiday->note}}</td>
                                <td><?php echo ($holiday->absence_id == 1) ? $t_approval[$holiday->approved] : '-'?></td>
                                <?php $t_paid += $holiday->days_paid; $t_unpaid += $holiday->days_unpaid;
                                if ($holiday->absence_id == 1 && $holiday->started_at >= now('Europe/London')) {?>
                                <td><a href="{{ route('holidays.edit', $holiday->id) }}" class="btn btn-primary"
                                       data-toggle="tooltip" data-placement="top" title="Edit holiday request"><span
                                                class="fas fa-pencil-alt"></span></a></td>
                                <td>
                                    <form action="{{ route('holidays.destroy', $holiday->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                                title="Delete holiday request"><span class="fas fa-trash-alt"></span>
                                        </button>
                                    </form>
                                </td>
                                <?php } else {?>
                                <td></td>
                                <td></td>
                                <?php }?>
                            </tr>
                        @endforeach
                        <tr class="table-info">
                            <td colspan="2"><strong>Totals ({{$t_count}}):</strong></td>
                            <td><strong>{{$t_paid}} ({{$t_entitlementRemaining}})</strong></td>
                            <td colspan="5"><strong>{{$t_unpaid}}</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row justify-content-center">
                    <!-- New holiday request -->
                    <a href="{{ route('holidays.create') }}" class="btn btn-info">Request holiday</a>
                </div>
            @endif
        </div>
    </div>
@endsection
