<?php

namespace App\Http\Controllers;

use App\Models\Moving;
use App\Models\Order_Product;
use Illuminate\Http\Request;
use App\Models\Storage;
use App\Service\OrderProductService;

class MovingController extends Controller
{
    protected $orderProductService;

    public function __construct(OrderProductService $orderProductService){
        $this->orderProductService = $orderProductService;
    }

    public function moving(Request $req)
    {
        $title = "MOVING ";
        $storages = Storage::all();
        return view("moving", ["title" => $title, "storages" => $storages, "pageState" => "repack"]);
    }

    public function create_moving(Request $req)
    {
        $storageCodeSender = $req->storageCodeSender;
        $storageCodeReceiver = $req->storageCodeReceiver;
        $no_moving = $req->no_moving;
        $moving_date = $req->moving_date;

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $uoms = $req->input('uom');
        //$price_per_uom = $req->input("price_per_uom");

        Moving::create([
            'no_moving' => $no_moving,
            'moving_date' => $moving_date,
            'storageCodeSender' => $storageCodeSender,
            'storageCodeReceiver' => $storageCodeReceiver,
        ]);

        if($productCodes){
            for($i = 0; $i < count($productCodes); $i++){
                Order_Product::create([
                    "nomor_surat_jalan" => "-", 
                    "repack_no_repack" => "-",
                    "moving_no_moving" => $no_moving,
                    "PO_no_PO" => "-",
                    "productCode" => $productCodes[$i], 
                    "qty" => $qtys[$i], 
                    "UOM" => $uoms[$i], 
                    "price_per_UOM" => 0, 
                    "note" => "-",
                    "product_status" => "moving"
                ]);
            }
        }

        session()->flash('msg', 'moving created: ' . $no_moving);

        return redirect()->route("invoice", ["state" => "moving"]);
    }

    public function getMovingDetails(Request $req)
    {
        $no_moving = $req->no_moving;
        $moving = Moving::where("no_moving", $no_moving)->first();
        return $moving;
    }

    public function amend_moving(Request $req)
    {
        $no_moving = $req->no_moving;
        $title = "AMEND MOVING";
        $storages = Storage::all();

        $moving = Moving::where("no_moving", $no_moving)->first();
        $products = $this->orderProductService->getOrderProducts($no_moving, "moving");

        return view("amends.amend_moving", ["title" => $title, "storages" => $storages, "moving" => $moving, "products" => $products]);
    }
}
