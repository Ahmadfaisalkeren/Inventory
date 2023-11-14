<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Export Sales</title>
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
    <h3>Sales Lists</h3>
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Sales Number</th>
                <th>Date</th>
                <th>User</th>
                <th>Product Name</th>
                <th>Qty</th>
                <th>Price Per Product</th>
                <th>Total Price Per Product</th>
                <th>Sales Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $rowspan = 0;
            @endphp
            @foreach ($sales as $key => $item)
                @php
                    $rowspan = count($item->salesDetails);
                @endphp
                @foreach ($item->salesDetails as $index => $detail)
                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $rowspan }}">{{ $key + 1 }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $item->number }}</td>
                            <td rowspan="{{ $rowspan }}">{{ tanggal_indonesia($item->date) }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $item->name->name }}</td>
                            <td>{{ optional($detail->inventory)->name }}</td>
                            <td>{{ $detail->qty }}</td>
                            <td>{{ optional($detail->inventory)->price }}</td>
                            <td>{{ format_uang($detail->price) }}</td>
                            <td rowspan="{{ $rowspan }}">{{ format_uang($item->total_price) }}</td>
                        @else
                            <td>{{ optional($detail->inventory)->name }}</td>
                            <td>{{ $detail->qty }}</td>
                            <td>{{ optional($detail->inventory)->price }}</td>
                            <td>{{ format_uang($detail->price) }}</td>
                        @endif
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
