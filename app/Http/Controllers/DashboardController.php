<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == "superadmin") {
            return view('dashboard.superadmin');
        } elseif (auth()->user()->role == "sales") {
            return view('dashboard.sales');
        } elseif (auth()->user()->role == "purchase") {
            return view('dashboard.purchase');
        } elseif (auth()->user()->role == "manager") {
            return view('dashboard.manager');
        }
    }
}
