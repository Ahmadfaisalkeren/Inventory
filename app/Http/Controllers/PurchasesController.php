<?php

namespace App\Http\Controllers;

use App\Models\Purchases;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Purchases::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $deleteBtn = '<button onclick="deleteInventory(`' . route('purchases.destroy', $data->id) . '`)" class="btn btn-sm mt-1 btn-danger">Delete</button>';

                    $editBtn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm mt-1 editInventory">Edit</a>';

                    return $editBtn . ' ' . $deleteBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('purchase.index');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchases $purchases)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchases $purchases)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchases $purchases)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchases $purchases)
    {
        //
    }
}
