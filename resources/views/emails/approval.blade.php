@extends('layouts.mail')
@section('content')
            <!-- approval.blade.php -->
            <div class="container container-fluid">
                <div class="row">
                    <p><strong>{{$user_name}}</strong> has requested a <strong>{{$holiday_type}}</strong> holiday on <strong>{{$holiday_dates}}</strong>.</p>
                    @if(!empty($holiday_note))
                    <p>They included the following note with the request: <em>"{{$holiday_note}}"</em>.</p>
                    @endif
                </div>
                @if(is_array($holiday_overlaps))
                <div class="row">
                    <p>Please note, the requested holiday overlaps these existing holidays:</p>
                    <div class="table-responsive-sm">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                            <tr>
                                <td>Name</td>
                                <td>Start</td>
                                <td>End</td>
                                <td>Approved</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $t_approval = [0 => 'Not Approved', 1 => 'Approved'] ?>
                            @foreach($holiday_overlaps as $holiday)
                            <tr>
                                <td>{{$holiday->name}}</td>
                                <td>{{$holiday->start}}</td>
                                <td>{{$holiday->end}}</td>
                                <td>{{$t_approval[$holiday->approved]}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                <div class="row">
                    <p>To approve the holiday, please click on the following link:</p>
                    <a href="http://attendance.test:8080/holidays/{{$holiday_id}}/approve" class="btn btn-primary">Approve holiday request</a>
                    <p>To deny the holiday, please click on the following link:</p>
                    <a href="http://attendance.test:8080/holidays/{{$holiday_id}}/deny" class="btn btn-danger">Deny holiday request</a>
                </div>
                <div class="row">
                    <p><small><em>This is an automated email. Please do not reply.</em></small></p>
                </div>
            </div>
@endsection