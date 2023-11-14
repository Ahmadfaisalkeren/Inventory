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
                                <div class="btn-group">
                                    <a href="{{ route('sales.export.excel') }}" class="btn btn-sm btn-success"><i class="far fa-file-excel"></i> Export Excel</a>
                                    <a href="{{ route('sales.export.csv') }}" class="btn btn-sm btn-success ml-1"><i class="fas fa-file-csv"></i> Export CSV</a>
                                    <a href="{{ route('sales.export.pdf') }}" target="_blank" class="btn btn-sm btn-danger ml-1"><i class="far fa-file-pdf"></i> Export PDF</a>
                                </div>
                                @if (auth()->user()->role != 'manager')
                                    <a title="Add Sales" href="{{ route('form-dodolan') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></a>
                                @endif
                            </div>
                            <div class="bg-info bg-opacity-10 border-info border rounded p-2 mt-2">
                                <small class='text-white'>
                                    Tip: The edit and delete button will be disabled after 24 hours
                                </small>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Number</th>
                                        <th>Date</th>
                                        <th>Total Price</th>
                                        <th>User</th>
                                        @if (auth()->user()->role != 'manager')
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('sales.form')

    @push('scripts')
        <script type="text/javascript">

            let table, table2;

            $(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                table = $('.data-table').DataTable({
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
                            data: 'total_price',
                            name: 'total_price'
                        },
                        {
                            data: 'user_id',
                            name: 'user_id'
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

                if (table.column(5) && "{{ auth()->user()->role }}" === "manager") {
                    table.column(5).visible(false);
                }

            });

            function addSales() {
                $('#addSalesModal').modal('show')
            }

            function deleteData(url) {
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
