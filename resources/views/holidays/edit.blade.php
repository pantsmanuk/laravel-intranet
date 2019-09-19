@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Holiday Request
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul id="error">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="post" action="{{route('holidays.update', $holiday->id)}}">
                        <div class="form-group required">
                            @csrf
                            @method('PATCH')
                            <label for="started_at" class="control-label">Start:</label>
                            <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" id="started_at"
                                       name="started_at" data-target="#startpicker" data-toggle="datetimepicker"
                                       aria-label="Start date" aria-describedby="start-addon"
                                       value="{{$request['started_at']}}" required/>
                                <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="start-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary<?php echo ($request['start_type'] == 1) ? ' active ' : ''; ?>">
                                        <input type="radio" id="start_type_1" name="start_type"
                                               <?php echo ($request['start_type'] == 1) ? 'checked="checked" ' : ''; ?>value="1"
                                               aria-label="Start on half day (AM)"/> Half Day (AM)
                                    </label>
                                    <label class="btn btn-secondary<?php echo ($request['start_type'] == 2) ? ' active ' : ''; ?>">
                                        <input type="radio" id="start_type_2" name="start_type"
                                               <?php echo ($request['start_type'] == 2) ? 'checked="checked" ' : ''; ?>value="2"
                                               aria-label="Start on half day (PM)"/> Half Day (PM)
                                    </label>
                                    <label class="btn btn-secondary<?php echo ($request['start_type'] == 3) ? ' active ' : ''; ?>">
                                        <input type="radio" id="start_type_3" name="start_type"
                                               <?php echo ($request['start_type'] == 3) ? 'checked="checked" ' : ''; ?>value="3"
                                               aria-label="Start on full day"/> Full Day
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ended_at" class="control-label">End:</label>
                            <div class="input-group date mb-3" id="endpicker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" id="ended_at" name="ended_at"
                                       data-target="#endpicker" data-toggle="datetimepicker" aria-label="End date"
                                       aria-describedby="end-addon" value="{{$request['ended_at']}}"/>
                                <div class="input-group-append" data-target="#endpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="end-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary<?php echo ($request['end_type'] == 1) ? ' active ' : ''; ?>">
                                        <input type="radio" id="end_type_1" name="end_type"
                                               <?php echo ($request['end_type'] == 1) ? 'checked="checked" ' : ''; ?>value="1"
                                               aria-label="End on half day (AM)"/> Half Day (AM)
                                    </label>
                                    <label class="btn btn-secondary<?php echo ($request['end_type'] == 2) ? ' active ' : ''; ?>">
                                        <input type="radio" id="end_type_2" name="end_type"
                                               <?php echo ($request['end_type'] == 2) ? 'checked="checked" ' : ''; ?>value="2"
                                               aria-label="End on full day"/> Full Day
                                    </label>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#startpicker').datetimepicker({
                                    locale: 'en-gb',
                                    format: 'YYYY-MM-DD',
                                    daysOfWeekDisabled: [0, 6],
                                    useCurrent: false,
                                    defaultDate: '{{$request['started_at']}}',
                                });
                                $('#endpicker').datetimepicker({
                                    locale: 'en-gb',
                                    format: 'YYYY-MM-DD',
                                    daysOfWeekDisabled: [0, 6],
                                    useCurrent: false,
                                    defaultDate: '{{$request['ended_at']}}',
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
                            <label for="note" class="control-label">Note:</label>
                            <input type="text" class="form-control" id="note" name="note" aria-label="Explanatory note"
                                   value="{{$holiday->note}}"/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update holiday request</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection