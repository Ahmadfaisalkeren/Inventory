<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Export Purchase</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
        }

        th {
            text-align: left;
        }
    </style>
</head>
<body>
    <h3>Purchase Lists</h3>
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Purchase Number</th>
                <th>Date</th>
                <th>User</th>
                <th>Product Name</th>
                <th>Qty</th>
                <th>Price Per Product</th>
                <th>Total Price Per Product</th>
                <th>Purchase Total</th>
            </tr>
        </thead>
        <tbody>
            @if ($purchase)
                @php
                    $rowspan = 0;
                @endphp
                @foreach ($purchase as $key => $item)
                    @php
                        $rowspan = count($item->purchaseDetails);
                    @endphp
                    @foreach ($item->purchaseDetails as $index => $detail)
                        <tr>
                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}">{{ $key + 1 }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $item->number }}</td>
                                <td rowspan="{{ $rowspan }}">{{ tanggal_indonesia($item->date) }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $item->name->name }}</td>
                                <td>{{ optional($detail->inventory)->name }}</td>
                                <td>{{ $detail->qty }}</td>
                                <td>{{ $detail->inventory->price }}</td>
                                <td>{{ $detail->price }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $item->total_price }}</td>
                            @else
                                <td>{{ optional($detail->inventory)->name }}</td>
                                <td>{{ $detail->qty }}</td>
                                <td>{{ $detail->inventory->price }}</td>
                                <td>{{ $detail->price }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            @else
                <tr>
                    <td colspan="9">This is data for a non-purchase role.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
