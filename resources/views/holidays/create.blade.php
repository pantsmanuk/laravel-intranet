@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="card">
            <div class="card-header">
                Request a holiday
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
                {{ Form::open(['action' => ['HolidayController@store']]) }}
                <div class="form-group">
                    {{ Form::label('start', 'Start:') }}
                    <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                        {{ Form::text('start', NULL, ['class' => 'form-control datetimepicker-input', 'data-target' => '#startpicker', 'data-toggle' => 'datetimepicker', 'placeholder' => 'Start date', 'aria-label' => 'Start date', 'aria-describedby' => 'start-addon']) }}
                        <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                            <span class="input-group-text" id="start-addon"><span class="fas fa-calendar-alt"></span></span>
                        </div>
                    </div>
                    <div class="form-check form-check-inline">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                {{ Form::radio('startType', '1', NULL, ['id' => 'startType1', 'aria-label' => 'Start on half day (AM)']) }} Half Day (AM)
                            </label>
                            <label class="btn btn-secondary">
                                {{ Form::radio('startType', '2', NULL, ['id' => 'startType2', 'aria-label' => 'Start on half day (PM)']) }} Half Day (PM)
                            </label>
                            <label class="btn btn-secondary active">
                                {{ Form::radio('startType', '3', true, ['id' => 'startType3', 'aria-label' => 'Start on full day']) }} Full Day
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {{ Form::label('end', 'End:') }}
                    <div class="input-group date mb-3" id="endpicker" data-target-input="nearest">
                        {{ Form::text('end', NULL, ['class' => 'form-control datetimepicker-input', 'data-target' => '#endpicker', 'data-toggle' => 'datetimepicker', 'placeholder' => 'End date', 'aria-label' => 'End date', 'aria-describedby' => 'end-addon']) }}
                        <div class="input-group-append" data-target="#endpicker" data-toggle="datetimepicker">
                            <span class="input-group-text" id="end-addon"><span class="fas fa-calendar-alt"></span></span>
                        </div>
                    </div>
                    <div class="form-check form-check-inline">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-secondary">
                                {{ Form::radio('endType', '1', NULL, ['id' => 'endType1', 'aria-label' => 'End on half day (AM)']) }} Half Day (AM)
                            </label>
                            <label class="btn btn-secondary active">
                                {{ Form::radio('endType', '2', NULL, ['id' => 'endType2', 'aria-label' => 'End on full day']) }} Full Day
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
                            useCurrent: false
                        });
                        $('#endpicker').datetimepicker({
                            locale: 'en-gb',
                            format: 'L',
                            daysOfWeekDisabled: [0,6],
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
                    {{ Form::label('note', 'Note:') }}
                    {{ Form::text('note', NULL, ['class' => 'form-control', 'placeholder' => 'Explanatory note', 'aria-label' => 'Explanatory note']) }}
                </div>
                {{ Form::submit('Submit request', ['class' => 'btn btn-primary']) }}
                {{ Form::button('Cancel', ['class' => 'btn btn-secondary', 'type' => 'reset']) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection