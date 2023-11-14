<?php

namespace App\Http\Controllers;

use App\Exports\InventoryExport;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Excel;
use PDF;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Inventory::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('price', function ($data) {
                    return 'IDR' . ' ' . format_uang($data->price);
                })
                ->addColumn('code', function ($data) {
                    return '<span class="bg-success p-1 pr-3 pl-3 rounded-pill">'. $data->code .'</span>';
                })
                ->addColumn('stock', function ($data) {
                    return format_uang($data->stock);
                })
                ->addColumn('action', function ($data) {
                    $deleteBtn = '<button onclick="deleteInventory(`' . route('inventories.destroy', $data->id) . '`)" class="btn btn-sm mt-1 btn-danger"><i class="fas fa-trash"></i> Delete</button>';

                    $editBtn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm mt-1 editInventory"><i class="fas fa-pencil-alt"></i> Edit</a>';

                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action', 'price', 'code'])
                ->make(true);
        }

        return view('inventory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        Inventory::create([
            'id' => $request->inventory_id,
            'code' => 'PROD-' . (Inventory::count() + 1),
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return response()->json([
            'success' => "Product Created Successfully",
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $inventory = Inventory::find($id);
        return response()->json($inventory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'stock' => 'nullable|numeric',
        ]);

        $inventory->name = $request->input('name');
        $inventory->price = $request->input('price');
        $inventory->stock = $request->input('stock');

        $inventory->save();

        return response()->json([
            'success' => "Product Updated Successfully",
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return response()->json([
            'success' => 'Inventory Deleted Successfully'
        ]);
    }

    public function exportExcel()
    {
        return Excel::download(new InventoryExport, 'inventory.xlsx');
    }

    public function exportCSV()
    {
        return Excel::download(new InventoryExport, 'inventory.csv');
    }

    public function exportPDF(Request $request)
    {
        $inventory = Inventory::orderBy('code', 'asc')->get();

        $no = 1;
        $pdf = PDF::loadview('inventory.exportpdf', compact('inventory', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('exportPDF.pdf');
    }
}
