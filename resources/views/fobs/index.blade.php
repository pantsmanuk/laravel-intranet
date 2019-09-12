@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="table-responsive">
                <h4>Spare Fob Usage</h4>
                <hr>
                @if(session()->get('success'))
                    <div class="alert alert-success">
                        {{session()->get('success')}}
                    </div>
                @endif
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr class="table-info">
                        <th>Date</th>
                        <th>Spare Fob</th>
                        <th>Staff Member</th>
                        <th colspan="2">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $t_count = 0; ?>
                    @foreach ($fobs as $fob)
                        <?php $t_count = $loop->count;?>
                        <tr>
                            <td>{{ \Illuminate\Support\Facades\Date::parse($fob->created_at)->format('d/m/Y') }}</td>
                            <td>{{ $fob->fob_name }}</td>
                            <td>{{ $fob->staff_name }}</td>
                            <td><a href="{{ route('fobs.edit', $fob->id) }}" class="btn btn-primary" data-toggle="tooltip"
                                   data-placement="top" title="Edit fob assignment"><span
                                            class="fas fa-pencil-alt"></span></a></td>
                            <td>
                                <form action="{{ route('fobs.destroy', $fob->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                            title="Delete fob assignment"><span class="fas fa-trash-alt"></span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-info">
                        <td colspan="5"><strong>Total:</strong> {{ $t_count }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row justify-content-end">
            {{ $fobs->links('pagination::bootstrap-4') }}
        </div>
        <div class="row justify-content-center">
            <a href="{{ route('fobs.create') }}" class="btn btn-info">New fob assignment</a>
        </div>
    </div>
@endsection
