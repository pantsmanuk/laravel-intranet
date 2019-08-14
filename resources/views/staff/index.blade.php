@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Staff</h4>
                <hr>
                @if(session()->get('success'))
                    <div class="alert alert-success">
                        {{session()->get('success')}}
                    </div>
                @endif
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Joined</th>
                        <th>Left</th>
                        <th>Days per week</th>
                        <th>Holidays</th>
                        <th>Carried Over</th>
                        <th>Default Work State</th>
                        <th colspan="2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count = 0; ?>
                    @foreach ($staff as $user)
                        <?php $t_count = $loop->count;?>
                        <tr<?php if (!is_null($user->deleted_at)) echo ' class="table-warning"'?>>
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->started_at}}</td>
                            <td>{{$user->ended_at}}</td>
                            <td>{{$user->days_per_week}}</td>
                            <td>{{$user->holiday_entitlement}}</td>
                            <td>{{$user->holiday_carried_forward}}</td>
                            <td>{{$user->workstate}}</td>
                            <td><a href="{{route('staff.edit', $user->id)}}" class="btn btn-primary"
                                   data-toggle="tooltip" data-placement="top" title="Edit staff member"><span
                                            class="fas fa-pencil-alt"></span></a></td>
                            <td>
                                <form action="{{ route('staff.destroy', $user->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <?php if (is_null($user->deleted_at)) {
                                        echo '<button class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete staff member"><span class="fas fa-trash-alt"></span></button>';
                                    } ?>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="10"><strong>Total:</strong> {{$t_count}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row justify-content-center">
            <a href="{{ route('staff.create') }}" class="btn btn-info">New staff member</a>
        </div>
    </div>
@endsection
