@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Telephones</h4>
                <hr>
                @if(session()->get('success'))
                    <div class="alert alert-success">
                        {{session()->get('success')}}
                    </div>
                @endif
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th>User Name</th>
                        <th>Description</th>
                        <th>Number</th>
                        <th colspan="2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count=0; ?>
                    @foreach ($telephones as $value)
                        <?php $t_count=$loop->count;?>
                        <tr>
                            <td>{{$value->user_name}}</td><?php //Hide name on repeat? ?>
                            <td>{{$value->name}}</td>
                            <td>{{$value->number}}</td>
                            <td><a href="{{route('telephones.edit', $value->lookup_id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit telephone"><span class="fas fa-pencil-alt"></span></a></td>
                            <td>
                                <form action="{{ route('telephones.destroy', $value->lookup_id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete telephone"><span class="fas fa-trash-alt"></span></button>
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
            <a href="{{ route('telephones.create') }}" class="btn btn-info">New telephone</a>
        </div>
    </div>
@endsection
