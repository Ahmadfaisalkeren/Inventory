@extends('template.index')

@section('title', 'Edit Sales')

@section('content')
    <div class="content mt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <a title="Back To Sales" href="{{ route('sales.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i></a>
                                <h3 class="card-title">
                                    Edit Sales
                                </h3>
                            </div>
                            <div class="bg-info bg-opacity-10 border-info border rounded p-2 mt-2">
                                <small class='text-white'>
                                    Tip: You can only update the quantity data; the price will automatically adjust based on the quantity. The rest of the data cannot be updated.
                                </small>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-sales-details2">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>Qty</th>
                                        <th>Price Per Product</th>
                                        <th>Total Price Per Product</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('sales.editSalesDetails')
@endsection

@push('scripts')
    <script type="text/javascript">

        let table;
        let totalPrice = {{ $totalPrice }};

        $(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var salesId = {{ $sales_id }}

            table = $('.table-sales-details2').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('edit-sales', ':sales_id') }}".replace(':sales_id', salesId),
                },
                columns: [{
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
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'price_per_product',
                        name: 'price_per_product'
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

            $('body').on('click', '.editSalesDetailsModal', function () {
                var salesDetailId = $(this).data('id');

                var data = salesDetailId.split('-');
                var salesDetailId = data[0];
                var currentQty = data[1];

                $('#salesDetailId').val(salesDetailId);
                $('#newQuantity').val(currentQty);
                $('#editSalesDetailsModal').modal('show');

                $('#newQuantity').on('input', function() {
                    var newQuantity = $(this).val();
                    if ($.isNumeric(newQuantity)) {
                        var pricePerUnit = parseFloat($('#pricePerUnit').val());
                        var newPrice = pricePerUnit * newQuantity;
                        $('#price').text(newPrice);
                    }
                });

            });

            function updateTotalPrice() {
                let total = 0;
                $('.table-sales-details2 tbody tr').each(function () {
                    const row = $(this);
                    const qty = parseInt(row.find('.qty').text());
                    const rowPrice = parseFloat(row.find('.price').text().replace(/[^0-9.-]+/g, ''));
                    total += qty * rowPrice;
                });
                $('#totalPriceValue').text(total);
            }

            $('#saveNewQuantity').click(function(e) {
                e.preventDefault();

                var salesDetailId = $('#salesDetailId').val();
                var url = "{{ route('update-sales-details', ':id') }}".replace(':id', salesDetailId);
                var method = 'PUT';

                var newQuantity = $('#newQuantity').val();

                var formData = new FormData($('#updateQuantityForm')[0]);
                formData.append('_method', method);
                formData.append('qty', newQuantity);

                $.ajax({
                    data: formData,
                    url: url,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        $('#updateQuantityForm').trigger("reset");
                        $('#editSalesDetailsModal').modal('hide');

                        table.draw();

                        var total = 0;
                        $('.table-sales-details2 tbody tr').each(function () {
                            var row = $(this);
                            var qty = parseInt(row.find('.qty').text());
                            var rowPrice = parseFloat(row.find('.price').text().replace(/[^0-9.-]+/g, ''));
                            total += qty * rowPrice;
                            updateTotalPrice();
                        });
                        $('#totalPrice').text('Total Price: ' + total);

                        Swal.fire({
                            title: 'Success',
                            text: 'Sales Data Updated Successfully',
                            icon: 'success',
                        });

                        setTimeout(function () {
                            window.location.href = "{{ route('sales.index') }}";
                        }, 2000);
                    },
                    error: function (error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'The quantity should not be greater than the stock',
                            icon: 'error',
                            confirmButtonColor: '#dc3545',
                            confirmButtonText: 'Back'
                        });
                    }
                });
            });
        });

    </script>
@endpush

