@extends('template.index')

@section('title', 'Sales')

@section('content')
    <div class="content mt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Sales Details Page</h3>
                                <button type="button" class="btn btn-info btn-sm" onclick="setSalesId({{ $sales->id }}); selectProduct();">Select Product</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Price Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($salesDetails as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->id_inventory }}</td>
                                            <td>{{ $item->qty }}</td>
                                            <td>{{ $item->price }}</td>
                                            <td>
                                                <button class="btn btn-danger btn-sm">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('sales_details.product')

    @push('scripts')
        <script type="text/javascript">
            let table, table2;
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                table = $('.table-sales-details').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('sales_details.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'id_inventory',
                            name: 'id_inventory'
                        },
                        {
                            data: 'qty',
                            name: 'qty'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    searching: false,
                    dom: 'Bfrtip',
                    "initComplete": function(settings, json) {
                        console.log(json); // Add this line to check the loaded data
                    }
                });

                table2 = $('.select-product-table').DataTable({
                    processing:true,
                    serverSide: true,
                    ajax: "{{ route('getInventories') }}",
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'code',
                            name: 'code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    autoWidth: false,
                });
            });

            var salesId;

            function setSalesId(id) {
                salesId = id;
            }

            function selectProduct() {
                $('#showProduct').modal('show')
            }

            function setSelectedProduct(productId, productName, productPrice) {
                var inventoryId = productId;
                var qty = 1;

                $.ajax({
                    type: "POST",
                    url: "{{ route('sales_details.store') }}",
                    data: {
                        sales_id: salesId,
                        inventory_id: inventoryId,
                        qty: qty,
                        price: productPrice,
                    },
                    dataType: "json",
                    success: function (data) {
                        console.log("Success: " + data.success);
                        table.ajax.reload();
                        Swal.fire({
                            title: "Success!",
                            text: data.success,
                            icon: "success",
                            timer: 3000,
                        });

                        $('#showProduct').modal('hide');
                    },
                    error: function (data) {
                        console.log("Error:", data);
                        alert("Failed to add the product to the sales details.");
                    },
                });
            }
        </script>
    @endpush
@endsection
