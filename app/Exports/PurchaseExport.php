<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\RegistersCustomConcerns;

class PurchaseExport implements FromView
{
    use RegistersCustomConcerns;

    protected $purchase;

    public function __construct($purchase)
    {
        $this->purchase = $purchase;
    }

    public function view(): View
    {
        return view('purchase.exportExcel', ['purchase' => $this->purchase]);
    }
}
