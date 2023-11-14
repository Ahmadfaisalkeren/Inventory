<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sales;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $user = auth()->user();

            if ($user->role == 'manager' || $user->role == 'superadmin') {
                $data = Sales::orderBy('id', 'desc')->get();
            } else {
                $user = Auth::user();

                $data = Sales::where('user_id', $user->id)
                    ->latest()
                    ->get();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($data) {
                    return tanggal_indonesia($data->date);
                })
                ->addColumn('number', function ($data) {
                    return '<span class="bg-success p-1 pr-3 pl-3 rounded-pill">' . $data->number . '</span>';
                })
                ->addColumn('total_price', function ($data) {
                    return 'IDR ' . format_uang($data->total_price);
                })
                ->addColumn('user_id', function ($data) {
                    return $data->name->name;
                })
                ->addColumn('action', function ($data) {
                    $disableButton = strtotime($data->date) < strtotime('-24 hours');

                    $user = auth()->user();
                    $isSuperAdmin = $user && $user->role == 'superadmin';
                    $storedBySales = $data->user_id !== $user->id;

                    if ($disableButton || ($isSuperAdmin && $storedBySales)) {
                        $deleteBtn = '<button class="btn btn-sm btn-danger disabled ml-1" disabled><i class="fas fa-trash"></i></button>';
                        $editBtn = '<a href="#" class="btn btn-primary btn-sm disabled" disabled><i class="fas fa-edit"></i></a>';
                    } else {
                        $deleteBtn = '<button onclick="deleteData(`' . route('deleteSales', $data->id) . '`)" class="btn btn-sm ml-1 btn-danger' . (strtotime($data->date) < strtotime('-24 hours') ? ' disabled' : '') . '"><i class="fas fa-trash"></i></button>';
                        $editBtn = '<a href="' . route('edit-sales', ['sales_id' => $data->id]) . '" class="btn btn-primary btn-sm' . (strtotime($data->date) < strtotime('-24 hours') ? ' disabled' : '') . '"><i class="fas fa-edit"></i></a>';
                    }

                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['action', 'number'])
                ->make(true);
        }

        return view('sales.index');
    }

    // public function getSalesDetailsData(Request $request)
    // {
    //     if ($request->ajax()) {

    //         $salesId = $request->input('id');

    //         dd($salesId);

    //         $data = SalesDetails::where('sales_id', $salesId)->latest()->get();

    //         return Datatables::of($data)
    //             ->addIndexColumn()
    //             ->addColumn('code', function($data) {
    //                 return optional($data->inventory)->code;
    //             })
    //             ->addColumn('name', function($data) {
    //                 return optional($data->inventory)->name;
    //             })
    //             ->rawColumns(['name', 'code'])
    //             ->make(true);
    //     }

    //     return view('sales.index');
    // }

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
            'number' => 'required',
            'date' => 'required',
            'user_id' => 'required',
        ]);

        $number = 'S-' . tambah_nol_didepan(Sales::count() + 1, 5);
        $date = Carbon::now()->format('Y-m-d');
        $user_id = auth()->user()->id;

        $sales = Sales::create([
            'number' => $number,
            'date' => $date,
            'user_id' => $user_id,
        ]);

        $salesId = $sales->id;

        return view('sales_details.create')->with('sales', $sales);
    }

    public function getSalesDetailsData(Request $request)
    {
        $salesId = $request->input('id');
        $salesDetails = SalesDetails::where('sales_id', $salesId)->get();

        return view('sales.index', compact('salesDetails'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Sales $sales)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sales $sales)
    {
        //
    }
}
