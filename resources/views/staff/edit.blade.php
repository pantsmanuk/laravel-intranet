@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Staff
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
                    <form method="post" action="{{route('staff.update', $staff->id)}}">
                        <div class="form-group required">
                            @csrf
                            @method('PATCH')
                            <label for="name" class="control-label">Name:</label>
                            <input class="form-control" aria-label="Name" id="name" name="name" type="text"
                                   value="{{$staff->name}}" required/>
                        </div>
                        <div class="form-group required">
                            <label for="username" class="control-label">Username:</label>
                            <input class="form-control" aria-label="Username" id="username" name="username" type="text"
                                   value="{{$staff->username}}" required/>
                        </div>
                        <div class="form-group required">
                            <label for="start" class="control-label">Join date:</label>
                            <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#startpicker"
                                       data-toggle="datetimepicker" aria-label="Join date"
                                       aria-describedby="start-addon" id="start" name="start" type="text"
                                       value="{{$staff->started_at}}" required/>
                                <div class="input-group-append" data-target="#startpicker" data-toggle="datepicker">
                                    <span class="input-group-text" id="start-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                                        $('#startpicker').datetimepicker({
                                            locale: 'en-gb',
                                            daysOfWeekDisabled: [0, 6],
                                            format: 'YYYY-MM-DD',
                                            date: '{{$staff->started_at}}',
                                            useCurrent: false
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end" class="control-label">Leave date:</label>
                            <div class="input-group date mb-3" id="endpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#endpicker"
                                       data-toggle="datetimepicker" aria-label="Leave date" aria-describedby="end-addon"
                                       id="end" name="end" type="text" value="{{$staff->ended_at}}"/>
                                <div class="input-group-append" data-target="#endpicker" data-toggle="datepicker">
                                    <span class="input-group-text" id="end-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                                        $('#endpicker').datetimepicker({
                                            locale: 'en-gb',
                                            daysOfWeekDisabled: [0, 6],
                                            format: 'YYYY-MM-DD',
                                            date: 'moment',
                                            useCurrent: false
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label for="entitlement" class="control-label">Holiday entitlement:</label>
                            <input class="form-control" aria-label="Holiday Entitlement" id="entitlement"
                                   name="entitlement" type="text" value="{{$staff->holiday_entitlement}}" required/>
                        </div>
                        <div class="form-group required">
                            <label for="carried_forward" class="control-label">Holiday carried forward:</label>
                            <input class="form-control" aria-label="Holiday Carried Forward" id="carried_forward"
                                   name="carried_forward" type="text" value="{{$staff->holiday_carried_forward}}" required/>
                        </div>
                        <div class="form-group required">
                            <label for="days_per_week" class="control-label">Days per week:</label>
                            <input class="form-control" aria-label="Days Per Week" id="days_per_week"
                                   name="days_per_week" type="text" value="{{$staff->days_per_week}}" required/>
                        </div>
                        <div class="form-group required">
                            <label for="workstate_id" class="control-label">Default work state:</label>
                            <select class="form-control" id="workstate_id" name="workstate_id"
                                    aria-label="Default work state selection" required>
                                @foreach($workstates as $value)
                                    <option value="{{$value->id}}"
                                            aria-label="{{$value->workstate}}"<?php echo ($value->id == $staff->default_workstate_id) ? " selected" : "";?>>{{$value->workstate}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update staff member</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
