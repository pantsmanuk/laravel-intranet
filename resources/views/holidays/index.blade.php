@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="table-responsive">
            <h4>Holidays for {{$sYear}}</h4>
            <hr>
            @if(session()->get('success'))
            <div class="alert alert-success">
                {{session()->get('success')}}
            </div>
            @endif
            <table class="table table-bordered table-striped">
                <thead>
                <tr class="table-info">
                    <th rowspan="2">Start</th>
                    <th rowspan="2">End</th>
                    <th rowspan="2">Approved</th>
                    <th colspan="2">Days used</th>
                    <th colspan="2" rowspan="2">Actions</th>
                </tr>
				<?php $t_entitlementRemaining = $iEntitlement; $t_paid = 0; $t_unpaid = 0; $t_approval = [0 => 'Not Approved', 1 => 'Approved'];?>
                <tr class="table-info">
                    <th>Paid ({{$t_entitlementRemaining}})</th>
                    <th>Unpaid</th>
                </tr>
                </thead>
                <tbody>
                <?php $t_count=0; ?>
                @foreach ($allHolidays as $holiday)
                    <?php $t_count=$loop->count;?>
                    <tr>
                        <td>{{$holiday->start}}</td>
                        <td>{{$holiday->end}}</td>
                        <td>{{$t_approval[$holiday->approved]}}</td>
                        <td>{{$holiday->days_paid}} ({{$t_entitlementRemaining-=$holiday->days_paid}})</td>
                        <td>{{$holiday->days_unpaid}}</td>
                        <?php $t_paid += $holiday->days_paid; $t_unpaid += $holiday->days_unpaid; if ($holiday->enableTools) {?>
                        <td><a href="{{ route('holidays.edit',$holiday->holiday_id) }}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit holiday request"><span class="fas fa-pencil-alt"></span></a></td>
                        <td>
                            <form action="{{ route('holidays.destroy', $holiday->holiday_id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete holiday request"><span class="fas fa-trash-alt"></span></button>
                            </form>
                        </td>
                        <?php } else {?>
                        <td></td>
                        <td></td>
                        <?php }?>
                    </tr>
                @endforeach
                <tr class="table-info">
                    <td colspan="3"><strong>Totals ({{$t_count}}):</strong></td>
                    <td><strong>{{$t_paid}} ({{$t_entitlementRemaining}})</strong></td>
                    <td colspan="4"><strong>{{$t_unpaid}}</strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row justify-content-center">
        <!-- New holiday request -->
        <a href="{{ route('holidays.create') }}" class="btn btn-info">Request holiday</a>
    </div>
    <!-- Delete button modal (unused) -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete holiday request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>This will delete the holiday request!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Delete request</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endsection
