@extends('layouts.app')

@section('content')
    <div class="container">
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
                            <td>{{$value->name}} <em>{{$value->workstate}}</em></td>
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
@endsection