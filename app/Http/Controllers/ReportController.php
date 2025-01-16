<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function dashboard()
    {
        return view("reports.dashboard", ["title" => "dashboard"]);
    }

    public function debt()
    {
        return view("reports.debt", ["title" => "debt"]);
    }

    public function receivables()
    {
        return view("reports.receivables", ["title" => "receivables"]);
    }
}
