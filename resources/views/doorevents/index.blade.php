@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Door Events</h4>
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
                        <th>User</th>
                        <th>Date/Time</th>
                        <th>Event</th>
                        <th colspan="2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count = 0; $t_event = [0 => 'In', 1 => 'Out']?>
                    @foreach ($doorevents as $value)
                        <?php $t_count = $loop->count;?>
                        <tr>
                            <td>{{$value->id}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->created_at}}</td>
                            <td>{{$t_event[$value->event]}}</td>
                            <td><a href="{{route('doorevents.edit', $value->id)}}" class="btn btn-primary"
                                   data-toggle="tooltip" data-placement="top" title="Edit door event"><span
                                            class="fas fa-pencil-alt"></span></a></td>
                            <td>
                                <form action="{{ route('doorevents.destroy', $value->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                            title="Delete door event"><span class="fas fa-trash-alt"></span></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="6"><strong>Total:</strong> {{$t_count}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row justify-content-center">
            <a href="{{ route('doorevents.create') }}" class="btn btn-info">New door event</a>
        </div>
    </div>
@endsection
