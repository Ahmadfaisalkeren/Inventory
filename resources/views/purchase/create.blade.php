@extends('template.index')

@section('title', 'Purchase')

@section('content')
    <div class="content mt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <a title="Back To Purchase" href="{{ route('get.purchase.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i></a>
                                <h3 class="card-title">Purchase</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="purchase" id="purchase">
                                <form class="form-horizontal mb-5" action="{{ route('purchase-store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="number" class="control-label">Number</label>
                                                <div>
                                                    <input type="text" class="form-control" id="number" name="number" value="{{ $number }}" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="date" class="control-label">Date</label>
                                                <div>
                                                    <input type="text" class="form-control" id="date" name="date" value="{{ $date }}" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label for="user_id" class="control-label">User</label>
                                                <div>
                                                    <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $user_id }}" required readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="total_price" id="total_price">
                                        <input type="hidden" value="{{ $purchaseId }}" name="purchase_id" id="purchase_id">
                                        <input type="hidden" name="inventory_id" id="inventory_id">
                                        <input type="hidden" name="qty" id="qty">
                                        <input type="hidden" name="price" id="price">
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm mb-2" onclick="addProduct()">Add Product</button>
                                    <table class="table table-striped table-purchase-details">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="show-price bg-primary"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-2 col-sm-10 p-0 mt-3">
                                            <button type="button" class="btn btn-primary btn-sm" id="createPurchaseBtn">Create Purchase</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('purchase.form')

    @push('scripts')
    <script type="text/javascript">
        let table, table1;
        let total = 0;

        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            table = $('.table-purchase-details').DataTable({
                searching: false,
                dom: 'Bfrtip',
                autoWidth: false,
            });

            table1 = $('.table-inventory').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('get-inventory') }}",
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
                autoWidth: false,
            });
        });

        function updateTotalPrice() {
            total_price = 0;
            for (const product of selectedProducts) {
                total_price += product.qty * product.price;
            }
            $('.show-price').text('Total Price: IDR. ' + total_price);
            $('#total_price').val(total_price);
            console.log(total_price);
        }

        function addProduct() {
            $('#addProductModal').modal('show')
        }

        let selectedProducts = [];
        let rowCounter = 1;

        $('.table-purchase-details').on('input', 'input.input-number', function () {
            const productId = $(this).data('id');
            const newQty = parseInt($(this).val());
            const product = selectedProducts.find(p => p.id === productId);
            if (product) {
                product.qty = newQty;
                updateTotalPrice();
            }
        });

        function setSelectedProduct(button) {
            const $button = $(button);
            const id = $button.data('id');
            const name = $button.data('name');
            const price = $button.data('price');
            const stock = $button.data('stock');

            const qty = 1;
            const existingProduct = selectedProducts.find(product => product.id === id);

            if (existingProduct) {
                const updatedQty = existingProduct.qty + qty;
                if (updatedQty > stock) {
                    alert('Quantity exceeds stock!');
                } else {
                    existingProduct.qty = updatedQty;

                    const $row = $('.table-purchase-details tbody tr').find(`[data-id="${id}"]`);
                    $row.val(updatedQty);

                    updateTotalPrice();
                }
            } else {
                selectedProducts.push({ id, name, qty, price, stock });
                var table = $('.table-purchase-details').DataTable();
                const rowNode = table.row.add(
                    [rowCounter,
                    name,
                    `<input type="number" class="form-control input-number" value="${qty}" min="1" max="${stock}" data-id="${id}" data-price="${price}">`,
                    price,
                    '<button class="btn btn-sm mt-1 btn-danger remove-product" onclick="removeProduct(' + id + ')"><i class="fas fa-trash"></i></button>']).draw();
                rowCounter++;
                updateTotalPrice();
            }
        }

        $(document).on('click', '.remove-product', function() {
            const id = $(this).data('id');
            removeProduct(id, $(this).closest('tr'));
        });

        function removeProduct(id, row) {
            const index = selectedProducts.findIndex(product => product.id === id);
            if (index !== -1) {
                selectedProducts.splice(index, 1);
            }

            var table = $('.table-purchase-details').DataTable();
            table.row(row).remove().draw();

            updateTotalPrice();
        }

        $('#createPurchaseBtn').on('click', function (e) {
            e.preventDefault();

            updateTotalPrice();

            var purchaseData = {
                number: $('#number').val(),
                date: $('#date').val(),
                user_id: $('#user_id').val(),
                total_price: $('#total_price').val(),
                purchaseDetails: selectedProducts
            };

            $.ajax({
                url: "{{ route('purchase-store') }}",
                type: "POST",
                data: JSON.stringify(purchaseData),
                contentType: 'application/json',
                dataType: "json",
                success: function (response) {
                    console.log('Success:', response);
                    if (response.success) {
                        Swal.fire({
                            title: "Success",
                            text: response.success,
                            icon: "success",
                            timer: 3000
                        }).then(function () {
                            window.location.href = "{{ route('get.purchase.index') }}"
                        });

                        selectedProducts = [];
                        updateTotalPrice();
                        var table = $('.table-purchase-details').DataTable();
                        table.clear().draw();
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.error,
                            icon: "error"
                        });
                    }
                },
                error: function (error) {
                    console.log('Error:', error);
                    Swal.fire({
                        title: "Error",
                        text: "An Error Occured, please try again",
                        icon: "error",
                        confirmButtonColor: "#FF0000",
                        confirmButtonText: "Back"
                    });
                }
            });
        });

    </script>

    @endpush
@endsection
