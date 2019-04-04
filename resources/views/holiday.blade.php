@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="table-responsive">
            <h4>Holidays for {{$sYear}}</h4>
            <hr>

            <table class="table table-bordered table-striped">
                <thead>
                <tr class="table-info">
                    <th rowspan="2">Start</th>
                    <th rowspan="2">End</th>
                    <th colspan="2">Days used</th>
                    <th colspan="2" rowspan="2">&lt;Tools&gt;</th>
                </tr>
				<?php $t_entitlementRemaining = $iEntitlement; $t_paid = 0; $t_unpaid = 0;?>
                <tr class="table-info">
                    <th>Paid ({{$t_entitlementRemaining}})</th>
                    <th>Unpaid</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($allHolidays as $holiday)
					<?php $t_count = $loop->count;?>
                    <tr>
                        <td>{{$holiday->start}}</td>
                        <td>{{$holiday->end}}</td>
                        <td>{{$holiday->days_paid}} ({{$t_entitlementRemaining-=$holiday->days_paid}})</td>
                        <td>{{$holiday->days_unpaid}}</td>
                        <?php $t_paid += $holiday->days_paid; $t_unpaid += $holiday->days_unpaid; if ($holiday->enableTools) {?>
                        <td>[Edit]</td>
                        <td>[Delete]</td>
                        <?php } else {?>
                        <td></td>
                        <td></td>
                        <?php }?>
                    </tr>
                @endforeach
                <tr class="table-info">
                    <td colspan="2"><strong>Totals ({{$t_count}}):</strong></td>
                    <td><strong>{{$t_paid}} ({{$t_entitlementRemaining}})</strong></td>
                    <td colspan="4"><strong>{{$t_unpaid}}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
