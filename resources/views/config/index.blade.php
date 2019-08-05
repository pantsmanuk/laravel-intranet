@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Configuration</h4>
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
                        <th>Value</th>
                        <th colspan="2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count=0; ?>
                    @foreach ($configs as $value)
                        <?php $t_count=$loop->count;?>
                        <tr>
                            <td>{{$value->id}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->value}}</td>
                            <td><a href="{{route('config.edit', $value->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit configuration pair"><span class="fas fa-pencil-alt"></span></a></td>
                            <td>
                                <form action="{{ route('config.destroy', $value->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete configuration pair"><span class="fas fa-trash-alt"></span></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="5"><strong>Total:</strong> {{$t_count}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row justify-content-center">
            <a href="{{ route('config.create') }}" class="btn btn-info">New configuration key/value pair</a>
        </div>
    </div>
@endsection
