<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Sales;
use App\Models\Inventory;
use App\Exports\SalesExport;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Excel;
use Illuminate\Support\Facades\Auth;
use PDF;


class SalessController extends Controller
{
    public function passDataSalesDetail(Request $request)
    {
        if ($request->ajax()) {
            $data = SalesDetails::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('inventory_id', function($data) {
                    return $data->inventory->name;
                })
                ->addColumn('price', function($data) {
                    return format_uang($data->price);
                })
                ->addColumn('qty', function($data) {
                    return '<input type="number" class="form-control quantity-input" value="1" min="1">';
                })
                ->addColumn('action', function ($data) {
                    $addInventory = '<button onclick="setSelectedProduct(' . $data->id . ' , \'' . $data->name . '\', '. $data->price .')" class="btn btn-sm mt-1 btn-danger"><i class="fas fa-trash"></i></button>';

                    return $addInventory;
                })
                ->rawColumns(['action', 'number'])
                ->make(true);
        }

        return view('sales.create');
    }

    public function passDataInventory(Request $request)
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

        return view('sales.create');
    }

    public function formDodolan()
    {
        $sales = Sales::latest()->first() ?? new Sales();

        $number = 'S-'. tambah_nol_didepan((int)$sales->id + 1, 5);
        $date = tanggal_indonesia(Carbon::now()->format('Y-m-d'));
        $user_id = auth()->user()->name;
        $salesId = Sales::get();

        return view('sales.create', compact('number', 'date', 'user_id', 'salesId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required',
            'date' => 'required',
            'user_id' => 'required',
            'total_price' => 'required',
            'salesDetails' => 'required|array',
            'salesDetails.*.id' => 'required',
            'salesDetails.*.qty' => 'required',
            'salesDetails.*.price' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $totalPrice = $request->input('total_price');

            $sales = Sales::latest()->first() ?? new Sales();
            $request['number'] = 'S-' . tambah_nol_didepan((int)$sales->id + 1, 5);
            $request['date'] = Carbon::now()->format('Y-m-d');
            $request['user_id'] = auth()->user()->id;
            $request['total_price'] = $totalPrice;
            $sales = Sales::create($request->all());

            $quantitiesValid = true;

            foreach ($request->input('salesDetails') as $salesDetail) {
                $product = Inventory::find($salesDetail['id']);
                if (!$product || $salesDetail['qty'] > $product->stock) {
                    $quantitiesValid = false;
                    break;
                }

                $calculatedPrice = $salesDetail['qty'] * $salesDetail['price'];

                SalesDetails::create([
                    'sales_id' => $sales->id,
                    'inventory_id' => $salesDetail['id'],
                    'qty' => $salesDetail['qty'],
                    'price' => $calculatedPrice,
                ]);

                $product->stock -= $salesDetail['qty'];
                $product->save();
            }

            if ($quantitiesValid) {
                DB::commit();
                return response()->json(['success' => 'Sales and Sales Details have been added successfully.']);
            } else {
                DB::rollback();
                return response()->json(['error' => 'Invalid quantity or product not found.'], 400);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getSalesDetailsData($salesId)
    {
        $salesDetails = SalesDetails::with('inventory')->where('sales_id', $salesId)->get();

        return response()->json(['salesDetails' => $salesDetails]);
    }

    public function getTotalSalesPrice($salesId)
    {
        $totalPrice = SalesDetails::where('sales_id', $salesId)->sum('price');

        return $totalPrice;
    }

    public function editSalesDetails(Request $request, $sales_id)
    {
        $sales = Sales::find($sales_id);

        $totalPrice = $this->getTotalSalesPrice($sales_id);

        if ($request->ajax()) {
            $data = SalesDetails::with('inventory')->where('sales_id', $sales_id)->get();

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
                    $addInventory = '<button title="Edit Quantity" class="btn btn-sm mt-1 btn-info editSalesDetailsModal" data-id="' . $data->id . '-' . $data->qty . '"><i class="fas fa-edit"></i></button>';

                    return $addInventory;
                })
                ->rawColumns(['action', 'code', 'name', 'price'])
                ->make(true);
        }

        return view('sales.editForm', compact('sales_id', 'totalPrice'));
    }

    public function update(Request $request, $salesId)
    {
        $salesDetail = SalesDetails::find($salesId);

        if (!$salesDetail) {
            return response()->json(['message' => 'SalesDetail not found'], 404);
        }

        $request->validate([
            'qty' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $inventory = $salesDetail->inventory;

        if (!$inventory) {
            return response()->json(['error' => 'Inventory item not found'], 404);
        }

        $oldQty = $salesDetail->qty; // Get the original quantity
        $newQty = $request->input('qty');

        // Calculate the difference between the old and new quantities
        $qtyDifference = $newQty - $oldQty;

        // Check if the new quantity exceeds the available stock
        if ($qtyDifference > $inventory->stock) {
            return response()->json(['error' => 'Quantity exceeds stock'], 400);
        }

        // Update the quantity and price
        $salesDetail->qty = $newQty;
        $pricePerUnit = $inventory->price;
        $newPrice = $pricePerUnit * $newQty;
        $salesDetail->price = $newPrice;

        // Update the inventory stock based on the quantity difference
        $inventory->stock -= $qtyDifference;

        // Save changes
        $salesDetail->save();
        $inventory->save();

        // Retrieve the Sales record with eager loading
        $sales = Sales::with('salesDetails')->find($salesDetail->sales_id);

        if ($sales) {
            $sales->total_price = $sales->salesDetails->sum('price');
            $sales->save();
        }

        return response()->json(['message' => 'SalesDetail updated successfully']);
    }

    public function getStock($id)
    {
        // Replace 'Inventory' with the actual model name of your product inventory
        $product = Inventory::find($id);

        if (!$product) {
            // Product not found
            return response()->json([
                'success' => false,
                'stock' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'stock' => $product->stock
        ]);
    }

    public function deleteSales($id)
    {
        $sales = Sales::findorFail($id);

        $salesDetails = $sales->salesDetails;

        $totalQty = $salesDetails->sum('qty');

        $sales->salesDetails()->delete();
        $sales->delete();

        foreach ($salesDetails as $salesDetail) {
            $inventory = $salesDetail->inventory;
            $inventory->stock += $salesDetail->qty;
            $inventory->save();
        }

        return response()->json([
            'message' => 'Sales And Sales Details Deleted'
        ]);
    }

    public function exportPDF(Request $request)
    {
        $user = auth()->user();

        if ($user->role == "manager" || $user->role == "superadmin") {
            $sales = Sales::with('salesDetails')->get();
        } else {
            $user = Auth::user();
            $sales = Sales::with('SalesDetails')->where('user_id', $user->id)->orderBy('id', 'asc')->get();
        }

        $no = 1;
        $pdf = PDF::loadview('sales.exportpdf', compact('sales', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->stream('exportPDF.pdf');
    }

    public function exportExcel()
    {

        if (auth()->user()->role == "sales") {
            $sales =  Sales::with('salesDetails')->where('user_id', '=', '2')->get();
        } elseif(auth()->user()->role == "superadmin" || auth()->user()->role == "manager") {
            $sales = Sales::with('salesDetails')->get();
        }

        return Excel::download(new SalesExport($sales), 'sales.xlsx');
    }

    public function exportCSV()
    {
        if (auth()->user()->role == "sales") {
            $sales =  Sales::with('salesDetails')->where('user_id', '=', '2')->get();
        } elseif(auth()->user()->role == "superadmin" || auth()->user()->role == "manager") {
            $sales = Sales::with('salesDetails')->get();
        }

        return Excel::download(new SalesExport($sales), 'sales.csv');
    }

}
