@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Download Log - {{$download_groups->where('id', '=', $id)->pluck('name')->first()}}</h4>
                <hr>

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th>Date</th>
                        <th>User</th>
                        <th>File</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count = 0; ?>
                    @foreach ($downloads as $value)
                        <?php $t_count = $loop->count; ?>
                        <tr>
                            <td>{{$value->date}}</td>
                            <td>{{$value->email}}</td>
                            <td>{{$value->filename}}</td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="3"><strong>Total: {{$t_count}}</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="card">
                <div class="card-header">
                    Select download group
                </div>
                <div class="card-body">
                    <form method="post" action="{{route('log.s', $id)}}">
                        <div class="form-group">
                            @csrf
                            <select class="form-control" id="id" name="id" aria-label="Download group selection" onchange="this.form.submit();">
                                @foreach($download_groups as $value)
                                    <option value="{{$value->id}}" aria-label="{{$value->name}}"<?php echo ($value->id == $id) ? " selected" : "";?>>{{$value->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection