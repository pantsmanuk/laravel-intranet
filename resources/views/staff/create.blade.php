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
                    <form method="post" action="{{route('staff.store')}}">
                        <div class="form-group required">
                            @csrf
                            <label for="name" class="control-label">Name:</label>
                            <input class="form-control" aria-label="Name" placeholder="Name" id="name" name="name"
                                   type="text" required/>
                        </div>
                        <div class="form-group required">
                            <label for="username" class="control-label">Username:</label>
                            <input class="form-control" aria-label="Username" placeholder="Username" id="username"
                                   name="username" type="text" required/>
                        </div>
                        <div class="form-group required">
                            <label for="start" class="control-label">Start:</label>
                            <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#startpicker"
                                       data-toggle="datetimepicker" aria-label="Start date"
                                       aria-describedby="start-addon" placeholder="Start date" id="start" name="start"
                                       type="text" required/>
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
                                            minDate: 'moment',
                                            useCurrent: false
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Add staff member</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection