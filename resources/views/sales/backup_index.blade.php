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
                                <h3 class="card-title">Sales Page</h3>
                                <a title="Add Sales" href="{{ route('form-dodolan') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped data-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Number</th>
                                        <th>Date</th>
                                        <th>User</th>
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

    @include('sales.form')
    @include('sales.see')

    @push('scripts')
        <script type="text/javascript">

            let table, table2;
            let salesId;

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

                $('.data-table2').on('click', 'button', function() {
                    salesId = $(this).data('id');
                    $('#salesId').text(salesId);

                    console.log('Clicked button with salesId:', salesId);

                    // Reload the DataTable with the new salesId
                    table.ajax.reload();
                    $('#editSalesDetails').modal('show');
                });

                table2 = $('.data-table2').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: `/sales-details-data/${salesId}`,
                        type: 'GET',
                        error: function (xhr, error, thrown) {
                            console.log('DataTables Error:', error, thrown);
                        }
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
            function addSales() {
                $('#addSalesModal').modal('show')
            }

        </script>
    @endpush
@endsection
