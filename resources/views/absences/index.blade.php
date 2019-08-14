@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Absences for {{$sYear}}</h4>
                <hr>
                @if(session()->get('success'))
                    <div class="alert alert-success">
                        {{session()->get('success')}}
                    </div>
                @endif
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th rowspan="2">Name</th>
                        <th rowspan="2">Start</th>
                        <th rowspan="2">End</th>
                        <th rowspan="2">Type</th>
                        <th rowspan="2">Note</th>
                        <th rowspan="2">Approved</th>
                        <th colspan="2">Days used</th>
                        <th colspan="2" rowspan="2">Actions</th>
                    </tr>
                    <tr class="table-info">
                        <th>Paid</th>
                        <th>Unpaid</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count = 0; $t_approved = array('No', 'Yes');?>
                    @foreach ($absences as $value)
                        <?php $t_count = $loop->count;?>
                        <tr>
                            <td>{{$value->user_name}}</td>
                            <td>{{$value->start_at}}</td>
                            <td>{{$value->end_at}}</td>
                            <td>{{$value->absence_type}}</td>
                            <td>{{$value->note}}</td>
                            <td><?php echo ($value->absence_type == "Holiday") ? $t_approved[$value->approved] : "-"; ?></td>
                            <td>{{$value->days_paid}}</td>
                            <td>{{$value->days_unpaid}}</td>
                            <td><a href="{{ route('absences.edit',$value->id) }}" class="btn btn-primary"
                                   data-toggle="tooltip" data-placement="top" title="Edit absence"><span
                                            class="fas fa-pencil-alt"></span></a></td>
                            <td>
                                <form action="{{ route('absences.destroy', $value->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                            title="Delete absence"><span class="fas fa-trash-alt"></span></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="10"><strong>Totals ({{$t_count}}):</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row justify-content-center">
            <!-- New holiday request -->
            <a href="{{ route('absences.create') }}" class="btn btn-info">Create absence</a>
        </div>
    </div>
@endsection
