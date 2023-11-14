<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Select only the columns you want to include in the export
        return Inventory::select('id', 'code', 'name', 'price', 'stock')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Product Code',
            'Product Name',
            'Price',
            'Stock',
        ];
    }
}
