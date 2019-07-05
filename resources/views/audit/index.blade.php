@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Intranet Audit Log</h4>
                <hr>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th>Date</th>
                        <th>Staff Member</th>
                        <th>IP Address</th>
                        <th>Event</th>
                        <th>Model</th>
                        <th>Row ID</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count=0; ?>
                    @foreach ($audit as $value)
                        <?php $t_count=$loop->count;?>
                        <tr>
                            <td>{{\Illuminate\Support\Facades\Date::parse($value->created_at)->format('d/m/Y H:i:s')}}</td>
                            <td>{{$value->user_name}}</td>
                            <td>{{$value->ip_address}}</td>
                            <td>{{$value->event}}</td>
                            <td>{{$value->auditable_type}}</td>
                            <td>{{$value->auditable_id}}</td>
                            <td>{{$value->old_values}}</td>
                            <td>{{$value->new_values}}</td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="5"><strong>Total:</strong> {{$t_count}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
