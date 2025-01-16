<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Storage;
use App\Models\Vendor;
use Illuminate\Http\Request;

class SlipController extends Controller
{
    public function slip(Request $req)
    {
        $state = $req->state;
        $title = "SLIP " . $state;
        $storages = Storage::all();
        $vendors = Vendor::all();
        $customers = Customer::all();
        return view("slip", ["title" => $title, "state" => $state, "vendors" => $vendors, "storages" => $storages, "customers" => $customers]);
    }

    public function create_slip(Request $req)
    {
        
    }

    public function amend_slip(Request $req)
    {
        
    }
}
