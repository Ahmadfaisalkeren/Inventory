<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Sales;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salesDetails = SalesDetails::orderBy('id', 'asc')->get();

        return view('sales_details.create', compact('salesDetails'));
    }

    public function Su()
    {
        $Su = SalesDetails::all();

        return view('sales_details.jajal', compact('Su'));
    }

    // public function getInventories(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $data = SalesDetails::latest()->get();

    //         return Datatables::of($data)
    //             ->addIndexColumn()
    //             ->addColumn('action', function ($data) {
    //                 $addInventory = '<button onclick="setSelectedProduct(' . $data->id . ' , \'' . $data->name . '\', '. $data->price .')" class="btn btn-sm mt-1 btn-danger">Add</button>';

    //                 return $addInventory;
    //             })
    //             ->rawColumns(['action'])
    //             ->make(true);
    //     }

    //     return view('sales_details.create');
    // }

    public function passDataInventory(Request $request)
    {
        if ($request->ajax()) {
            $data = Inventory::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $addInventory = '<button onclick="setSelectedProduct(' . $data->id . ' , \'' . $data->name . '\', '. $data->price .')" class="btn btn-sm mt-1 btn-danger"><i class="fas fa-plus"></i></button>';

                    return $addInventory;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('sales.create');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Sales $sales)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sales_id' => 'required',
            'inventory_id' => 'required',
            'qty' => 'required',
            'price' => 'required'
        ]);

        $salesDetails = new SalesDetails([
            'sales_id' => $request->input('sales_id'),
            'inventory_id' => $request->input('inventory_id'),
            'qty' => $request->input('qty'),
            'price' => $request->input('price'),
        ]);

        $salesDetails->save();

        return response()->json([
            'success' => 'Data Stored Successfully'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesDetails $salesDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesDetails $salesDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesDetails $salesDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesDetails $salesDetails)
    {
        //
    }
}
