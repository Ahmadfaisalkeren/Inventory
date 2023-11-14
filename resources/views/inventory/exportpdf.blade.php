<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Export PDF</title>
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
    <h3>Inventory Lists</h3>
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventory as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>IDR. {{ format_uang($item->price) }}</td>
                    <td>{{ format_uang($item->stock) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
