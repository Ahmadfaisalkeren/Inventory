@extends('template.index')

@section('title', 'Product')

@section('content')
    <div class="content mt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <div class="btn-group">
                                    <a href="{{ route('export.excel') }}" class="btn btn-success btn-sm"><i class="far fa-file-excel"></i> Export Excel</a>
                                    <a href="{{ route('export.pdf') }}" target="_blank" class="btn btn-danger btn-sm ml-1"><i class="far fa-file-pdf"></i> Export PDF</a>
                                    <a href="{{ route('export.csv') }}" class="btn btn-success btn-sm ml-1"><i class="fas fa-file-csv"></i> Export CSV</a>
                                </div>
                                <button class="btn btn-primary btn-sm" id="addInventory"><i class="fas fa-plus"></i> Add Product</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Product Code</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
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

    @include('inventory.form')

    @push('scripts')
        <script type="text/javascript">
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('inventories.index') }}",
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
                            data: 'stock',
                            name: 'stock'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });

                $('#addInventory').click(function() {
                    $('#saveInventory').show();
                    $('#updateInventory').hide();
                    $('#saveInventory').val("create-inventories");
                    $('#inventory_id').val('');
                    $('#inventoryForm').trigger("reset");
                    $('#modelHeading').html("Create New Inventory");
                    $('#formModal .modal-dialog');
                    $('#formModal').modal('show');
                });

                $('#saveInventory').click(function(e) {
                    e.preventDefault();

                    var formData = new FormData($("#inventoryForm")[0]);

                    var url = "{{ route('inventories.store') }}";
                    var method = 'POST';

                    $.ajax({
                        data: formData,
                        processData: false,
                        contentType: false,
                        url: url,
                        type: method,
                        dataType: 'json',
                        success: function(data) {
                            $('#inventoryForm').trigger("reset");
                            $('#formModal').modal('hide');
                            table.draw();
                            Swal.fire({
                                title: "Success!",
                                text: "The Inventory has been added successfully.",
                                icon: "success",
                                timer: 3000
                            });
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            $('#saveInventory').html('Save Changes');
                        }
                    });
                });

                $('body').on('click', '.editInventory', function() {
                    var inventory_id = $(this).data('id');
                    $.get("{{ route('inventories.index') }}" + '/' + inventory_id + '/edit', function(data) {
                        $('#modelHeading').html("Edit Inventory");
                        $('#saveInventory').hide();
                        $('#updateInventory').show();
                        $('#updateInventory').val("edit-inventories");
                        $('#formModal .modal-dialog');
                        $('#formModal').modal('show');
                        $('#inventory_id').val(data.id);
                        $('#name').val(data.name);
                        $('#price').val(data.price);
                        $('#stock').val(data.stock);
                    });
                });

                $('#updateInventory').click(function(e) {
                    e.preventDefault();

                    var inventory_id = $('#inventory_id').val();
                    var url = "{{ route('inventories.update', ':id') }}".replace(':id', inventory_id);
                    var method = 'PUT';

                    var formData = new FormData($('#inventoryForm')[0]);
                    formData.append('_method', method);

                    $.ajax({
                        data: formData,
                        url: url,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            $('#inventoryForm').trigger("reset");
                            $('#formModal').modal('hide');
                            table.draw();
                            Swal.fire({
                                title: "Success!",
                                text: "The inventories has been updated successfully.",
                                icon: "success",
                                timer: 3000
                            });
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            $('#updateInventory').html('Save Changes');
                        }
                    });
                });
            });

            function deleteInventory(url) {
                Swal.fire({
                    title: 'Yakin ingin menghapus data terpilih?',
                    text: 'Anda tidak dapat mengembalikan data yang telah dihapus!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                                '_token': $('[name=csrf-token]').attr('content'),
                                '_method': 'delete'
                            })
                            .done((response) => {
                                const dataTable = $('.data-table').DataTable();
                                dataTable.row(`[data-id="${response.id}"]`).remove().draw();
                                Swal.fire({
                                    title: 'Data berhasil dihapus!',
                                    icon: 'success',
                                });
                            })
                            .fail((errors) => {
                                alert('Tidak dapat menghapus data');
                                return;
                            });
                    }
                });
            }
        </script>
    @endpush

@endsection
