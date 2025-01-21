<?php

namespace App\Http\Controllers;

use App\Models\Order_Product;
use App\Models\Repack;
use Illuminate\Http\Request;
use App\Models\Storage;

class RepackController extends Controller
{
    public function repack(Request $req)
    {
        $title = "REPACK";
        $storages = Storage::all();
        return view("repack", ["title" => $title, "storages" => $storages, "pageState" => "repack"]);
    }

    public function create_repack(Request $req)
    {
        $storageCode = $req->storageCode;
        $no_repack = $req->no_repack;
        $repack_date = $req->repack_date;

        $kd_awal = $req->input("kd_awal");
        $qty_awal = $req->input("qty_awal");
        $uom_awal = $req->input("uom_awal");
        $note_awal = $req->input("note_awal");

        $kd_akhir = $req->input("kd_akhir");
        $qty_akhir = $req->input("qty_akhir");
        $uom_akhir = $req->input("uom_akhir");
        $note_akhir =$req->input("note_akhir");

        Repack::create([
            "no_repack" => $no_repack,
            "repack_date" => $repack_date,
            "storageCode" => $storageCode
        ]);

        for($i = 0; $i < count($kd_awal); $i++){
            Order_Product::create([
                "nomor_surat_jalan" => "-", 
                "repack_no_repack" => $no_repack,
                "moving_no_moving" => "-",
                "productCode" => $kd_awal[$i], 
                "qty" => $qty_awal[$i], 
                "UOM" => $uom_awal[$i], 
                "price_per_UOM" => 0, 
                "note" => $note_awal[$i],
                "product_status" => "repack_awal"
            ]);
        }
        for($i = 0; $i < count($kd_akhir); $i++){
            Order_Product::create([
                "nomor_surat_jalan" => "-", 
                "repack_no_repack" => $no_repack,
                "moving_no_moving" => "-",
                "productCode" => $kd_akhir[$i], 
                "qty" => $qty_akhir[$i], 
                "UOM" => $uom_akhir[$i], 
                "price_per_UOM" => 0, 
                "note" => $note_akhir[$i],
                "product_status" => "repack_akhir"
            ]);
        }

        session()->flash('msg', 'repack created: ' . $no_repack);

        return redirect()->route("dashboard");
    }

    public function remove_repack(Request $req)
    {
        
    }

    public function amend_repack(Request $req)
    {
        
    }
}
