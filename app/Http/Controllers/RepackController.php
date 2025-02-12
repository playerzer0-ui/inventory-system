<?php

namespace App\Http\Controllers;

use App\Models\Order_Product;
use App\Models\Repack;
use Illuminate\Http\Request;
use App\Models\Storage;
use App\Service\OrderProductService;

class RepackController extends Controller
{
    protected $orderProductService;

    public function __construct(OrderProductService $orderProductService)
    {
        $this->orderProductService = $orderProductService;
    }

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

        $kd_start = $req->input("kd_start");
        $qty_start = $req->input("qty_start");
        $uom_start = $req->input("uom_start");
        $note_start = $req->input("note_start");

        $kd_end = $req->input("kd_end");
        $qty_end = $req->input("qty_end");
        $uom_end = $req->input("uom_end");
        $note_end =$req->input("note_end");

        Repack::create([
            "no_repack" => $no_repack,
            "repack_date" => $repack_date,
            "storageCode" => $storageCode
        ]);

        for($i = 0; $i < count($kd_start); $i++){
            Order_Product::create([
                "nomor_surat_jalan" => "-", 
                "repack_no_repack" => $no_repack,
                "moving_no_moving" => "-",
                "productCode" => $kd_start[$i], 
                "qty" => $qty_start[$i], 
                "UOM" => $uom_start[$i], 
                "price_per_UOM" => 0, 
                "note" => $note_start[$i],
                "product_status" => "repack_start"
            ]);
        }
        for($i = 0; $i < count($kd_end); $i++){
            Order_Product::create([
                "nomor_surat_jalan" => "-", 
                "repack_no_repack" => $no_repack,
                "moving_no_moving" => "-",
                "productCode" => $kd_end[$i], 
                "qty" => $qty_end[$i], 
                "UOM" => $uom_end[$i], 
                "price_per_UOM" => 0, 
                "note" => $note_end[$i],
                "product_status" => "repack_end"
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
        $no_repack = $req->no_repack;
        $storages = Storage::all();
        $repack = Repack::where("no_repack", $no_repack)->first();
        $products = $this->orderProductService->getOrderProducts($no_repack, "repack");
        // dd($products);
        $title = "AMEND REPACK";
        return view("amends.amend_repack", ["title" => $title, "storages" => $storages, "repack" => $repack, "products" => $products]);
    }
}
