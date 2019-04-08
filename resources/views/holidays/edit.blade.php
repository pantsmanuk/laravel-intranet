@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="table-responsive">
            <h4>Edit holiday request</h4>
            <hr>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul id="error">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ Form::open(['action' => ['HolidayController@update']]) }}
                <div class="form-group">
                    @csrf
                    @method('PATCH')
                    {{ Form::label('start', 'Start:') }}
                    <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                        {{ Form::text('start', NULL, ['class' => 'form-control datetimepicker-input', 'data-target' => '#startpicker', 'data-toggle' => 'datetimepicker', 'placeholder' => 'Start date', 'aria-label' => 'Start date', 'aria-describedby' => 'start-addon', 'value' => '$request->start']) }}
                        <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                            <span class="input-group-text" id="start-addon"><span class="fas fa-calendar-alt"></span></span>
                        </div>
                    </div>
                    <div class="form-check form-check-inline">
                        {{ Form::radio('startType', '1', NULL, ['class' => 'form-check-input ml-1', 'id' => 'startType1', 'aria-label' => 'Start on half day (AM)']) }}
                        {{ Form::label('startType', 'Half Day (AM)', ['class' => 'form-check-label', 'for' => 'startType1']) }}
                        {{ Form::radio('startType', '2', NULL, ['class' => 'form-check-input ml-2', 'id' => 'startType2', 'aria-label' => 'Start on half day (PM)']) }}
                        {{ Form::label('startType', 'Half Day (PM)', ['class' => 'form-check-label', 'for' => 'startType2']) }}
                        {{ Form::radio('startType', '3', true, ['class' => 'form-check-input ml-2', 'id' => 'startType3', 'aria-label' => 'Start on full day']) }}
                        {{ Form::label('startType', 'Full Day', ['class' => 'form-check-label', 'for' => 'startType3']) }}
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
                        {{ Form::radio('endType', '1', NULL, ['class' => 'form-check-input ml-1', 'id' => 'endType1', 'aria-label' => 'End on half day (AM)']) }}
                        {{ Form::label('endType', 'Half Day (AM)', ['class' => 'form-check-label', 'for' => 'endType1']) }}
                        {{ Form::radio('endType', '2', true, ['class' => 'form-check-input ml-2', 'id' => 'endType2', 'aria-label' => 'End on full day']) }}
                        {{ Form::label('endType', 'Full Day', ['class' => 'form-check-label', 'for' => 'endType2']) }}
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
@endsection
