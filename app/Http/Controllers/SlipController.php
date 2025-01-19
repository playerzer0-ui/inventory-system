<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Order_Product;
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
        $no_sj = $req->no_sj;
        $storageCode = $req->storageCode;
        $no_LPB = $req->no_LPB;
        $no_truk = $req->no_truk;
        $vendorCode = $req->vendorCode;
        $customerCode = $req->customerCode;
        $orderDate = $req->order_date;
        $purchase_order = $req->purchase_order;
        $status_mode = $req->status_mode;

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $uoms = $req->input('uom');
        $notes = $req->input('note');
        $pageState = $req->pageState;

        Order::create([
            'nomor_surat_jalan' => $no_sj,
            'storageCode' => $storageCode,
            'no_LPB' => $no_LPB ?? "-",
            'no_truk' => $no_truk,
            'vendorCode' => $vendorCode ?? 'NON',
            'customerCode' => $customerCode ?? 'NON',
            'orderDate' => $orderDate,
            'purchase_order' => $purchase_order,
            'status_mode' => $status_mode,
        ]);

        for($i = 0; $i < count($productCodes); $i++){
            Order_Product::create([
                "nomor_surat_jalan" => $no_sj, 
                "repack_no_repack" => "-",
                "moving_no_moving" => "-",
                "productCode" => $productCodes[$i], 
                "qty" => $qtys[$i], 
                "UOM" => $uoms[$i], 
                "price_per_UOM" => 0, 
                "note" => $notes[$i],
                "product_status" => $pageState
            ]);
        }

        session()->flash('msg', 'no_SJ: ' . $no_sj);

        return redirect()->route("invoice", ["state" => $pageState]);
    }

    public function remove_slip(Request $req)
    {
        
    }

    public function amend_slip(Request $req)
    {
        
    }
}
