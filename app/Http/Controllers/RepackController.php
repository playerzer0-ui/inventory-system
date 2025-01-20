<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Storage;

class RepackController extends Controller
{
    public function repack(Request $req)
    {
        $title = "REPACK ";
        $storages = Storage::all();
        return view("repack", ["title" => $title, "storages" => $storages, "pageState" => "repack"]);
    }

    public function create_repack(Request $req)
    {

    }

    public function remove_repack(Request $req)
    {
        
    }

    public function amend_repack(Request $req)
    {
        
    }
}
