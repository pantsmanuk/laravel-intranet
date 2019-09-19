@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Create absence
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul id="error">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div><br/>
                    @endif
                    <form method="post" action="{{route('absences.store')}}">
                        <div class="form-group required">
                            @csrf
                            <label for="user_id" class="control-label">Staff member:</label>
                            <select class="form-control" id="user_id" name="user_id"
                                    aria-label="Staff member selection">
                                @foreach($staff as $value)
                                    <option value="{{$value->empref}}"
                                            aria-label="{{$value->name}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group required">
                            <label for="started_at" class="control-label">Start date/time:</label>
                            <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#startpicker"
                                       data-toggle="datetimepicker" placeholder="Start date/time"
                                       aria-label="Start date time" aria-describedby="start-addon" id="started_at"
                                       name="started_at" type="text"/>
                                <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="start-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label for="ended_at" class="control-label">End date/time:</label>
                            <div class="input-group date mb-3" id="endpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#endpicker"
                                       data-toggle="datetimepicker" placeholder="End date/time"
                                       aria-label="End datetime" aria-describedby="end-addon" id="ended_at"
                                       name="ended_at" type="text"/>
                                <div class="input-group-append" data-target="#endpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="end-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#startpicker').datetimepicker({
                                    locale: 'en-gb',
                                    daysOfWeekDisabled: [0, 6],
                                    format: 'YYYY-MM-DD HH:mm:ss',
                                    useCurrent: false
                                });
                                $('#endpicker').datetimepicker({
                                    locale: 'en-gb',
                                    daysOfWeekDisabled: [0, 6],
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
                        <div class="form-group required">
                            <label for="absence_id" class="control-label">Absence type:</label>
                            <select class="form-control" id="absence_id" name="absence_id"
                                    aria-label="Absence type selection">
                                @foreach($absences as $value)
                                    <option value="{{$value->id}}"
                                            aria-label="{{$value->name}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="note" class="control-label">Note:</label>
                            <input class="form-control" aria-label="Absence request note" placeholder="Note" id="note"
                                   name="note" type="text"/>
                        </div>
                        <div class="form-group required">
                            <label for="days_paid" class="control-label">Days paid:</label>
                            <input class="form-control" aria-label="Days taken paid" placeholder="Days paid"
                                   id="days_paid" name="days_paid" type="text"/>
                        </div>
                        <div class="form-group required">
                            <label for="days_unpaid" class="control-label">Days unpaid:</label>
                            <input class="form-control" aria-label="Days taken unpaid" placeholder="Days unpaid"
                                   id="days_unpaid" name="days_unpaid" type="text"/>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" aria-label="Absence approved" id="approved" name="approved"
                                   type="checkbox"/>
                            <label class="form-check-label" for="approved">Approved</label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create absence</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection