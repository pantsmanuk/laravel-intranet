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
                        </div><br/>
                    @endif
                    <form method="post" action="{{route('holidays.store')}}">
                        <div class="form-group">
                            @csrf
                            <label for="started_at">Start date:</label>
                            <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#startpicker"
                                       data-toggle="datetimepicker" placeholder="Start date" aria-label="Start date"
                                       aria-describedby="start-addon" id="started_at" name="started_at" type="text">
                                <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="start-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary">
                                        <input id="start_type1" aria-label="Start on half day (AM)" name="start_type"
                                               type="radio" value="1"> Half Day (AM)
                                    </label>
                                    <label class="btn btn-secondary">
                                        <input id="start_type2" aria-label="Start on half day (PM)" name="start_type"
                                               type="radio" value="2"> Half Day (PM)
                                    </label>
                                    <label class="btn btn-secondary active">
                                        <input id="start_type3" aria-label="Start on full day" checked="checked"
                                               name="start_type" type="radio" value="3"> Full Day
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ended_at">End date:</label>
                            <div class="input-group date mb-3" id="endpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#endpicker"
                                       data-toggle="datetimepicker" placeholder="End date" aria-label="End date"
                                       aria-describedby="end-addon" id="ended_at" name="ended_at" type="text">
                                <div class="input-group-append" data-target="#endpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="end-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                            <div class="form-check form-check-inline">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-secondary">
                                        <input id="end_type1" aria-label="End on half day (AM)" name="end_type"
                                               type="radio" value="1"> Half Day (AM)
                                    </label>
                                    <label class="btn btn-secondary active">
                                        <input id="end_type2" aria-label="End on full day" checked="checked"
                                               name="end_type" type="radio" value="2"> Full Day
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
                                    useCurrent: false
                                });
                                $('#endpicker').datetimepicker({
                                    locale: 'en-gb',
                                    format: 'YYYY-MM-DD',
                                    daysOfWeekDisabled: [0, 6],
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
                            <label for="note">Note:</label>
                            <input class="form-control" placeholder="Explanatory note" aria-label="Explanatory note"
                                   id="note" name="note" type="text">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit holiday request</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection