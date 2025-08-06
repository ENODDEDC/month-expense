<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $expenses = Auth::user()->expenses()->orderBy('date', 'desc')->get();

        return view('dashboard', ['expenses' => $expenses]);
    }
}