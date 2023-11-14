@extends('template.index')

@section('content')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($Su as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->inventory_id }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->price }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
