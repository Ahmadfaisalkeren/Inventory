<?php

namespace App\Http\Controllers;

use PDF;
use Excel;
use Exception;
use App\Models\Inventory;
use App\Models\Purchases;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\PurchaseExport;
use App\Models\PurchaseDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function getInventory(Request $request)
    {
        if ($request->ajax()) {
            $data = Inventory::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('code', function($data) {
                    return '<span class="bg-success p-1 pr-3 pl-3 rounded-pill">'. $data->code . '</span>';
                })
                ->addColumn('price', function ($data) {
                    return 'IDR. ' . format_uang($data->price);
                })
                ->addColumn('action', function ($data) {
                    if ($data->stock <= 0) {
                        $addInventory = '<button title="Out Of Stock" class="btn btn-sm mt-1 btn-secondary disabled"><i class="fas fa-plus"></i></button>';
                    } else {
                        $addInventory = '<button title="Add Product" data-id="' . $data->id . '" data-name="' . $data->name . '" data-price="' . $data->price . '" data-stock="' . $data->stock . '" onclick="setSelectedProduct(this)" class="btn btn-sm mt-1 btn-success"><i class="fas fa-plus"></i></button>';

                    }

                    return $addInventory;
                })
                ->rawColumns(['action', 'code'])
                ->make(true);
        }

        return view('purchase.create');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $user = auth()->user();

            if ($user->role == "manager" || $user->role == "superadmin") {
                $data = Purchases::orderBy('id', 'desc')->get();
            } else {
                $user = Auth::user();

                $data = Purchases::where('user_id', $user->id)
                        ->latest()
                        ->get();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('number', function ($data) {
                    return '<span class="bg-success p-1 pr-3 pl-3 rounded-pill">' . $data->number . '</span>';
                })
                ->addColumn('user_id', function ($data) {
                    return $data->name->name;
                })
                ->addColumn('date', function ($data) {
                    return tanggal_indonesia($data->date);
                })
                ->addColumn('total_price', function ($data) {
                    return 'IDR.' . format_uang($data->total_price);
                })
                ->addColumn('action', function ($data) {
                    $disableButton = strtotime($data->date) < strtotime('-24 hours');

                    $user = auth()->user();
                    $isSuperAdmin = $user && $user->role == 'superadmin';
                    $storedBySales = $data->user_id !== $user->id;

                    if ($disableButton || ($isSuperAdmin && $storedBySales)) {
                        $deleteBtn = '<button class="btn btn-sm btn-danger disabled ml-1"><i class="fas fa-trash"></button>';
                        $editBtn = '<a href="#" class="btn btn-primary btn-sm disabled" disabled><i class="fas fa-edit"></i></a>';
                    } else {
                        $deleteBtn = '<button onclick="deleteData(`' . route('deletePurchase', $data->id) . '`)" class="btn btn-sm ml-1 btn-danger' . (strtotime($data->date) < strtotime('-24 hours') ? ' disabled' : '') . '"><i class="fas fa-trash"></button>';
                        $editBtn = '<a href="' . route('edit-purchase', ['purchase_id' => $data->id]) . '" class="btn btn-primary btn-sm' . (strtotime($data->date) < strtotime('-24 hours') ? ' disabled' : '') . '"><i class="fas fa-edit"></i></a>';
                    }


                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['action', 'date', 'user_id', 'number'])
                ->make(true);
        }

        return view('purchase.index');
    }

    public function getPurchaseData()
    {
        $purchase = Purchases::latest()->first() ?? new Purchases();

        $number = 'P-'. tambah_nol_didepan((int)$purchase->id + 1, 5);
        $date = tanggal_indonesia(Carbon::now()->format('Y-m-d'));
        $user_id = auth()->user()->name;
        $purchaseId = Purchases::first() ? Purchases::first()->id : null;

        return view('purchase.create', compact('number', 'date', 'user_id', 'purchaseId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'date' => 'required',
            'user_id' => 'required',
            'total_price' => 'required',
            'purchaseDetails' => 'required|array',
            'purchaseDetails.*.id' => 'required',
            'purchaseDetails.*.qty' => 'required',
            'purchaseDetails.*.price' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $totalPrice = $request->input('total_price');

            $purchase = Purchases::latest()->first() ?? new Purchases();
            $request['number'] = 'P-' . tambah_nol_didepan((int)$purchase->id + 1, 5);
            $request['date'] = Carbon::now()->format('Y-m-d');
            $request['user_id'] = auth()->user()->id;
            $request['total_price'] = $totalPrice;

            $purchase = Purchases::create($request->all());

            $quantitiesValid = true;

            foreach ($request->input('purchaseDetails') as $purchaseDetail) {
                $product = Inventory::find($purchaseDetail['id']);

                $calculatedPrice = $purchaseDetail['qty'] * $purchaseDetail['price'];

                PurchaseDetails::create([
                    'purchase_id' => $purchase->id,
                    'inventory_id' => $purchaseDetail['id'],
                    'qty' => $purchaseDetail['qty'],
                    'price' => $calculatedPrice,
                ]);

                $product->stock += $purchaseDetail['qty'];
                $product->save();
            }

            if ($quantitiesValid) {
                DB::commit();
                return response()->json(['success' => 'Purchases and Purchases Details have been added successfully.']);
            } else {
                DB::rollback();
                return response()->json(['error' => 'Invalid quantity or product not found.'], 400);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e], 500);
        }
    }

    public function getTotalPurchasePrice($purchaseId)
    {
        $totalPrice = PurchaseDetails::where('purchase_id', $purchaseId)->sum('price');

        return $totalPrice;
    }

    public function edit_purchase(Request $request, $purchase_id)
    {
        $purchase = Purchases::find($purchase_id);

        $totalPrice = $this->getTotalPurchasePrice($purchase_id);

        if ($request->ajax()) {
            $data = PurchaseDetails::with('inventory')->where('purchase_id', $purchase_id)->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('code', function($data) {
                    return '<span class="bg-success p-1 pr-3 pl-3 rounded-pill">'. $data->inventory->code . '</span>';
                })
                ->addColumn('name', function($data) {
                    return $data->inventory->name;
                })
                ->addColumn('price_per_product', function($data) {
                    return 'IDR ' . format_uang($data->inventory->price);
                })
                ->addColumn('price', function($data) {
                    return 'IDR ' . format_uang($data->price);
                })
                ->addColumn('action', function ($data) {
                    $addInventory = '<button title="Edit Quantity" class="btn btn-sm mt-1 btn-info editPurchaseDetailsModal" data-id="' . $data->id . '-' . $data->qty . '"><i class="fas fa-edit"></i></button>';

                    return $addInventory;
                })
                ->rawColumns(['action', 'code', 'name', 'price'])
                ->make(true);
        }

        return view('purchase.editForm', compact('purchase_id', 'totalPrice'));
    }

    public function update(Request $request, $purchaseId)
    {
        $purchaseDetail = PurchaseDetails::find($purchaseId);

        if (!$purchaseDetail) {
            return response()->json(['message' => 'PurchaseDetail not found'], 404);
        }

        $request->validate([
            'qty' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $inventory = $purchaseDetail->inventory;

        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found'], 404);
        }

        $oldQty = $purchaseDetail->qty; // Get the original quantity
        $newQty = $request->input('qty');

        // Calculate the difference between the old and new quantities
        $qtyDifference = $newQty - $oldQty;

        // Update the quantity and price
        $purchaseDetail->qty = $newQty;
        $pricePerUnit = $inventory->price;
        $newPrice = $pricePerUnit * $newQty;
        $purchaseDetail->price = $newPrice;

        // Update the inventory stock based on the quantity difference
        $inventory->stock += $qtyDifference;

        // Save changes
        $purchaseDetail->save();
        $inventory->save();

        $purchase = Purchases::with('purchaseDetails')->find($purchaseDetail->purchase_id);

        if ($purchase) {
            // Calculate and update the total_price in the Purchase table
            $purchase->total_price = $purchase->purchaseDetails->sum('price');
            $purchase->save();
        }

        return response()->json(['message' => 'PurchaseDetail updated successfully']);
    }

    public function deletePurchase($id)
    {
        $purchase = Purchases::findorFail($id);

        $purchaseDetails = $purchase->purchaseDetails;

        $totalQty = $purchaseDetails->sum('qty');

        $purchase->purchaseDetails()->delete();
        $purchase->delete();

        foreach ($purchaseDetails as $purchaseDetail) {
            $inventory = $purchaseDetail->inventory;
            $inventory->stock -= $purchaseDetail->qty;
            $inventory->save();
        }

        return response()->json([
            'message' => 'Purchase And Purchase Details Deleted'
        ]);
    }

    public function exportPDF(Request $request)
    {
        $user = auth()->user();

        if ($user->role == "manager" || $user->role == "superadmin") {
            $purchase = Purchases::with('purchaseDetails')->get();
        } else {
            $user = Auth::user();
            $purchase = Purchases::with('purchaseDetails')->where('user_id', $user->id)->orderBy('id', 'asc')->get();
        }

        $no = 1;
        $pdf = PDF::loadview('purchase.exportpdf', compact('purchase', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('exportPDF.pdf');
    }

    public function exportExcel()
    {

        if (auth()->user()->role == "purchase") {
            $purchase =  Purchases::with('purchaseDetails')->where('user_id', '=', '3')->get();
        } elseif(auth()->user()->role == "superadmin" || auth()->user()->role == "manager") {
            $purchase = Purchases::with('purchaseDetails')->get();
        }

        return Excel::download(new PurchaseExport($purchase), 'purchase.xlsx');
    }

    public function exportCSV()
    {
        if (auth()->user()->role == "purchase") {
            $purchase =  Purchases::with('purchaseDetails')->where('user_id', '=', '2')->get();
        } elseif(auth()->user()->role == "superadmin" || auth()->user()->role == "manager") {
            $purchase = Purchases::with('purchaseDetails')->get();
        }

        return Excel::download(new PurchaseExport($purchase), 'purchase.csv');
    }

}
