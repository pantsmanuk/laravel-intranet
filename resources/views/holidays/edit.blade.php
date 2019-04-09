@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Edit holiday request
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
                    <form method="post" action="{{route('holidays.update', $holiday->holiday_id)}}">
                        <div class="form-group">
                            @csrf
                            @method('PATCH')
                            <label for="start">Start:</label>
                            <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" id="start" name="start" data-target="#startpicker" data-toggle="datetimepicker" aria-label="Start date" aria-describedby="start-addon" value="{{$request['start']}}" />
                                <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="start-addon"><span class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary<?php echo ($request['startType']==1) ? ' active ' : ''; ?>">
                                        <input type="radio" name="startType" <?php echo ($request['startType']==1) ? 'checked="checked" ' : ''; ?>value="1" aria-label="Start on half day (AM)" /> Half Day (AM)
                                    </label>
                                    <label class="btn btn-secondary<?php echo ($request['startType']==2) ? ' active ' : ''; ?>">
                                        <input type="radio" name="startType" <?php echo ($request['startType']==2) ? 'checked="checked" ' : ''; ?>value="2" aria-label="Start on half day (PM)" /> Half Day (PM)
                                    </label>
                                    <label class="btn btn-secondary<?php echo ($request['startType']==3) ? ' active ' : ''; ?>">
                                        <input type="radio" name="startType" <?php echo ($request['startType']==3) ? 'checked="checked" ' : ''; ?>value="3" aria-label="Start on full day" /> Full Day
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end">End:</label>
                            <div class="input-group date mb-3" id="endpicker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" id="end" name="end" data-target="#endpicker" data-toggle="datetimepicker" aria-label="End date" aria-describedby="end-addon" value="{{$request['end']}}" />
                                <div class="input-group-append" data-target="#endpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="end-addon"><span class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary<?php echo ($request['endType']==1) ? ' active ' : ''; ?>">
                                        <input type="radio" name="endType" <?php echo ($request['endType']==1) ? 'checked="checked" ' : ''; ?>value="1" aria-label="End on half day (AM)" /> Half Day (AM)
                                    </label>
                                    <label class="btn btn-secondary<?php echo ($request['endType']==2) ? ' active ' : ''; ?>">
                                        <input type="radio" name="endType" <?php echo ($request['endType']==2) ? 'checked="checked" ' : ''; ?>value="2" aria-label="End on full day" /> Full Day
                                    </label>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#startpicker').datetimepicker({
                                    locale: 'en-gb',
                                    format: 'L',
                                    daysOfWeekDisabled: [0,6],
                                    useCurrent: false,
                                    defaultDate: '{{$request['start']}}',
                                });
                                $('#endpicker').datetimepicker({
                                    locale: 'en-gb',
                                    format: 'L',
                                    daysOfWeekDisabled: [0,6],
                                    useCurrent: false,
                                    defaultDate: '{{$request['end']}}',
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
                            <label for="note">Note:</label>
                            <input type="text" class="form-control" id="note" name="note" aria-label="Explanatory note" value="{{$holiday->note}}" />
                        </div>
                        <button type="submit" class="btn btn-primary">Update request</button>
                        <button type="reset" class="btn btn-secondary">Reset form</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection