<?php

namespace App\Exports;

use App\Models\Sales;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\RegistersCustomConcerns;

class SalesExport implements FromView
{
    use RegistersCustomConcerns;

    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function view(): View
    {
        return view('sales.exportExcel', ['sales' => $this->sales]);
    }
}
