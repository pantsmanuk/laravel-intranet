@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Door Event
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
                    <form method="post" action="{{route('doorevents.store')}}">
                        <div class="form-group required">
                            @csrf
                            <label for="user_id" class="control-label">Staff member:</label>
                            <select class="form-control" id="user_id" name="user_id"
                                    aria-label="Staff member selection" required>
                                @foreach($staff as $value)
                                    <option value="{{$value->id}}"
                                            aria-label="{{$value->name}}">{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group required">
                            <label for="created_at" class="control-label">Date/time:</label>
                            <div class="input-group date mb-3" id="startpicker" data-target-input="nearest">
                                <input class="form-control datetimepicker-input" data-target="#startpicker"
                                       data-toggle="datetimepicker" placeholder="Date/time"
                                       aria-label="Date and time" aria-describedby="start-addon" id="created_at"
                                       name="created_at" type="text" required/>
                                <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                                    <span class="input-group-text" id="start-addon"><span
                                                class="fas fa-calendar-alt"></span></span>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                                $('#startpicker').datetimepicker({
                                    locale: 'en-gb',
                                    format: 'YYYY-MM-DD HH:mm:ss',
                                });
                            });
                        </script>
                        <div class="form-group">
                            <input id="event" name="event" type="checkbox" data-toggle="toggle" data-on="Out"
                                   data-off="In" data-onstyle="danger" data-offstyle="success"/> <label for="event" class="control-label">In/out?</label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create door event</button>
                            <button type="reset" class="btn btn-secondary">Reset form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.5.0/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.5.0/js/bootstrap4-toggle.min.js"></script>
@endsection