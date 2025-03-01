<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Storage;
use App\Models\Vendor;
use App\Service\OrderProductService as ServiceOrderProductService;
use Illuminate\Http\Request;

class SlipController extends Controller
{
    protected $orderProductService;

    public function __construct(ServiceOrderProductService $orderProductService)
    {
        $this->orderProductService = $orderProductService;
    }

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
        $purchase_status = $req->input("purchase_status");
        $pageState = $req->pageState;

        Order::create([
            'nomor_surat_jalan' => $no_sj,
            'storageCode' => $storageCode,
            'no_LPB' => $no_LPB ?? "-",
            'no_truk_in' => ($pageState == 'in') ? $no_truk : null,
            'no_truk_out' => ($pageState == 'out' || $pageState == 'out_tax') ? $no_truk : null,
            'vendorCode' => $vendorCode ?? 'NON',
            'customerCode' => $customerCode ?? 'NON',
            'orderDate' => $orderDate,
            'purchase_order' => $purchase_order,
            'status_mode' => $status_mode,
        ]);

        if($productCodes){
            if($pageState == "in" || $pageState == "out_tax"){
                for($i = 0; $i < count($productCodes); $i++){
                    Order_Product::create([
                        "nomor_surat_jalan" => $no_sj, 
                        "repack_no_repack" => "-",
                        "moving_no_moving" => "-",
                        "PO_no_PO" => "-",
                        "productCode" => $productCodes[$i], 
                        "qty" => $qtys[$i], 
                        "UOM" => $uoms[$i], 
                        "price_per_UOM" => 0, 
                        "note" => $notes[$i],
                        "product_status" => $pageState
                    ]);
                }
            }
            else{
                for($i = 0; $i < count($productCodes); $i++){
                    if($purchase_status[$i] == "approve"){
                        Order_Product::create([
                            "nomor_surat_jalan" => $no_sj, 
                            "repack_no_repack" => "-",
                            "moving_no_moving" => "-",
                            "PO_no_PO" => "-",
                            "productCode" => $productCodes[$i], 
                            "qty" => $qtys[$i], 
                            "UOM" => $uoms[$i], 
                            "price_per_UOM" => 0, 
                            "note" => $notes[$i],
                            "product_status" => $pageState
                        ]);

                        Order_Product::where("PO_no_PO", $purchase_order)->where("productCode", $productCodes[$i])->update([
                            "product_status" => "purchase_approved"
                        ]);
                    }
                }
            }
        }

        session()->flash('msg', 'no_SJ: ' . $no_sj);

        return redirect()->route("invoice", ["state" => $pageState]);
    }
    
    public function amend_slip(Request $req)
    {
        $no_sj = $req->no_sj;
        
        $storages = Storage::all();
        $vendors = Vendor::all();
        $customers = Customer::all();

        $result = $this->orderProductService->getOrderByNoSJ($no_sj);
        switch($result["status_mode"]){
            case 1:
                $state = "in";
                break;
            case 2:
                $state = "out";
                break;
            case 3:
                $state = "out_tax";
                break;
        }
        $title = "AMEND SLIP " . $state;

        $products = $this->orderProductService->getOrderProducts($no_sj, $state);
        
        return view("amends.amend_slip", ["title" => $title, "state" => $state, "vendors" => $vendors, "storages" => $storages, "customers" => $customers, "result" => $result, "products" => $products]);
    }
}
