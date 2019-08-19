@extends('layouts.app')

@section('content')
    <div class="container">
        <?php $user = $remotes->where('id', auth()->id())->first();
        if (isset($user)) { ?>
        <div class="row justify-content-center d-print-none">
            <div class="embed-responsive">
                <h4 class="float-left">My Attendance</h4>
                <form class="float-right" method="post" action="{{route('home.store')}}">
                    <div class="form-group">
                        @csrf
                        <label for="event">I&#39;m currently </label>
                        <input id="event" name="event" type="checkbox" data-toggle="toggle" data-on="Out"
                               data-off="In" data-onstyle="danger" data-offstyle="success" onchange="this.form.submit()"
                               <?php echo ($user->door_event == 1) ? 'checked': '';?>/>
                    </div>
                </form>
                <span class="float-none">&nbsp;</span>
                <hr>
            </div>
        </div>
        <?php } ?>
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4 class="float-left">Telephone Directory</h4><h4 class="float-right">Switchboard: 020 8686 9887</h4>
                <span class="float-none">&nbsp;</span>
                <hr>

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th>Extn</th>
                        <th>Name</th>
                        <th>External(s)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($staff as $value)
                        <tr>
                            <td>{{$value->name}} <em>{{$value->work_state}}</em></td>
                            <td>{{$value->extn}}</td>
                            <td>@foreach ($value->telephones as $telephone){{$telephone->name}}: {{$telephone->number}}
                                <br/>@endforeach</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.5.0/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.5.0/js/bootstrap4-toggle.min.js"></script>
@endsection