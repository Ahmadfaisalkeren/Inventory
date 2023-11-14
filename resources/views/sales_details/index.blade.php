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
                                <h3 class="card-title">Sales Detail Page</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Number</th>
                                        <th>Date</th>
                                        <th>User</th>
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

    {{-- @include('sales.form') --}}

    {{-- @push('scripts')
        <script type="text/javascript">
            $(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var table = $('.data-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('sales.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'number',
                            name: 'number'
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'user',
                            name: 'user'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });

                $('#addSales').click(function() {
                    $('#saveSales').show();
                    $('#updateSales').hide();
                    $('#saveSales').val("create-sales");
                    $('#sales_id').val('');
                    $('#salesForm').trigger("reset");
                    $('#modelHeading').html("Create New Sales");
                    $('#formModal .modal-dialog');
                    $('#formModal').modal('show');
                });

                $('#saveSales').click(function(e) {
                    e.preventDefault();

                    var formData = new FormData($("#salesForm")[0]);

                    var url = "{{ route('sales.store') }}";
                    var method = 'POST';

                    $.ajax({
                        data: formData,
                        processData: false,
                        contentType: false,
                        url: url,
                        type: method,
                        dataType: 'json',
                        success: function(data) {
                            $('#salesForm').trigger("reset");
                            $('#formModal').modal('hide');
                            table.draw();
                            Swal.fire({
                                title: "Success!",
                                text: "The Sales has been added successfully.",
                                icon: "success",
                                timer: 3000
                            });
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            $('#saveSales').html('Save Changes');
                        }
                    });
                });

                $('body').on('click', '.editSales', function() {
                    var sales_id = $(this).data('id');
                    $.get("{{ route('sales.index') }}" + '/' + sales_id + '/edit', function(data) {
                        $('#modelHeading').html("Edit Sales");
                        $('#saveSales').hide();
                        $('#updateSales').show();
                        $('#updateSales').val("edit-sales");
                        $('#formModal .modal-dialog');
                        $('#formModal').modal('show');
                        $('#sales_id').val(data.id);
                        $('#name').val(data.name);
                        $('#price').val(data.price);
                        $('#stock').val(data.stock);
                    });
                });

                $('#updateSales').click(function(e) {
                    e.preventDefault();

                    var sales_id = $('#sales_id').val();
                    var url = "{{ route('sales.update', ':id') }}".replace(':id', sales_id);
                    var method = 'PUT';

                    var formData = new FormData($('#salesForm')[0]);
                    formData.append('_method', method);

                    $.ajax({
                        data: formData,
                        url: url,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        success: function(data) {
                            $('#salesForm').trigger("reset");
                            $('#formModal').modal('hide');
                            table.draw();
                            Swal.fire({
                                title: "Success!",
                                text: "The sales has been updated successfully.",
                                icon: "success",
                                timer: 3000
                            });
                        },
                        error: function(data) {
                            console.log('Error:', data);
                            $('#updateSales').html('Save Changes');
                        }
                    });
                });
                function deleteSales(url) {
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
            });
        </script>
    @endpush --}}
@endsection
