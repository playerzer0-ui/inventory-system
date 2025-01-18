<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function dashboard()
    {
        $uuid = substr(Str::uuid()->toString(), 0, 8);
        return view("reports.dashboard", ["title" => "dashboard", "uuid" => $uuid]);
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
