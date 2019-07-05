@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Edit absence
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul id="error">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br />
                    @endif
                    <form method="post" action="{{route('absences.update', $absence->id)}}">
                        <div class="form-group">
                            @csrf
                            @method('PATCH')
                            <label for="staff_id">Staff member:</label>
                            <select class="form-control" id="user_id" name="user_id" aria-label="Staff member selection">
                                @foreach($staff as $value)
                                    <option value="{{$value->empref}}" aria-label="{{$value->name}}"<?php echo ($value->empref == $absence->user_id) ? " selected" : "";?>>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_at">Start date/time:</label>
                            <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#startpicker"
                                       data-toggle="datetimepicker" placeholder="{{$absence->start_at}}"
                                       aria-label="Start date time" aria-describedby="start-addon" id="start_at"
                                       name="start_at" type="text" value="{{$absence->start_at}}"/>
                                <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="start-addon"><span class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end_at">End date/time:</label>
                            <div class="input-group date mb-3" id="endpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#endpicker"
                                       data-toggle="datetimepicker" placeholder="{{$absence->end_at}}"
                                       aria-label="End datetime" aria-describedby="end-addon" id="end_at"
                                       name="end_at" type="text" value="{{$absence->end_at}}"/>
                                <div class="input-group-append" data-target="#endpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="end-addon"><span class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#startpicker').datetimepicker({
                                    locale: 'en-gb',
                                    daysOfWeekDisabled: [0,6],
                                    format: 'YYYY-MM-DD HH:mm:ss',
                                    useCurrent: false
                                });
                                $('#endpicker').datetimepicker({
                                    locale: 'en-gb',
                                    daysOfWeekDisabled: [0,6],
                                    format: 'YYYY-MM-DD HH:mm:ss',
                                    useCurrent: false
                                });
                                $('#startpicker').on('change.datetimepicker', function (e) {
                                    $('#endpicker').datetimepicker('minDate', e.date);
                                    $('#endpicker').datetimepicker('defaultDate', e.date);
                                });
                                $('#endpicker').on('change.datetimepicker', function (e) {
                                    $('#startpicker').datetimepicker('maxDate', e.date);
                                });
                            });
                        </script>
                        <div class="form-group">
                            <label for="absence_id">Absence type:</label>
                            <select class="form-control" id="absence_id" name="absence_id" aria-label="Absence type selection">
                                @foreach($absences as $value)
                                    <option value="{{$value->id}}" aria-label="{{$value->name}}"<?php echo ($value->id == $absence->absence_id) ? " selected" : "";?>>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="note">Note:</label>
                            <input class="form-control" aria-label="Absence request note" placeholder="Note" id="note" name="note" type="text" value="{{$absence->note}}" />
                        </div>
                        <div class="form-group">
                            <label for="days_paid">Days paid:</label>
                            <input class="form-control" aria-label="Days taken paid" placeholder="Days paid" id="days_paid" name="days_paid" type="text" value="{{$absence->days_paid}}" />
                        </div>
                        <div class="form-group">
                            <label for="days_unpaid">Days unpaid:</label>
                            <input class="form-control" aria-label="Days taken unpaid" placeholder="Days unpaid" id="days_unpaid" name="days_unpaid" type="text" value="{{$absence->days_unpaid}}" />
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" aria-label="Absence approved" id="approved" name="approved" type="checkbox"<?php echo ($absence->approved) ? " checked" : "";?> />
                            <label class="form-check-label" for="approved">Approved</label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update absence</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection