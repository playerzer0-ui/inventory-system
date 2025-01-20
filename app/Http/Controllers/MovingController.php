<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Storage;

class MovingController extends Controller
{
    public function moving(Request $req)
    {
        $title = "MOVING ";
        $storages = Storage::all();
        return view("moving", ["title" => $title, "storages" => $storages, "pageState" => "repack"]);
    }

    public function create_moving(Request $req)
    {

    }

    public function remove_moving(Request $req)
    {
        
    }

    public function amend_moving(Request $req)
    {
        
    }
}
