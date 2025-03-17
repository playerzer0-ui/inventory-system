<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Purchase_Order;
use App\Models\Storage;
use App\Models\Truck;
use App\Models\Vendor;
use App\Service\AzureEmailService;
use App\Service\OrderProductService as ServiceOrderProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlipController extends Controller
{
    protected $orderProductService;
    protected $azure;

    public function __construct(ServiceOrderProductService $orderProductService, AzureEmailService $azure)
    {
        $this->orderProductService = $orderProductService;
        $this->azure = $azure;
    }

    public function slip(Request $req)
    {
        $state = $req->state;
        $title = "SLIP " . $state;
        $storages = Storage::all();
        $vendors = Vendor::all();
        $customers = Customer::all();
        $orders = Purchase_Order::whereIn('no_PO', function ($query) {
            $query->select('PO_no_PO')
                  ->from('order_products')
                  ->whereNotNull('PO_no_PO') // Exclude NULL values
                  ->where('PO_no_PO', '!=', '-') // Exclude dashes
                  ->groupBy('PO_no_PO')
                  ->havingRaw("SUM(CASE WHEN product_status != 'purchase_approved' THEN 1 ELSE 0 END) > 0");
        })->pluck('no_PO');
        return view("slip", ["title" => $title, "state" => $state, "vendors" => $vendors, "storages" => $storages, "customers" => $customers, "orders" => $orders]);
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
            'delivered' => 0
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
                $this->azure->alertAdmins($pageState);
            }
            else{
                for($i = 0; $i < count($productCodes); $i++){
                    if($purchase_status[$i] == "approve"){
                        $price = Order_Product::where("PO_no_PO", $purchase_order)
                        ->where("productCode", $productCodes[$i])
                        ->pluck('price_per_UOM')
                        ->first();

                        Order_Product::create([
                            "nomor_surat_jalan" => $no_sj, 
                            "repack_no_repack" => "-",
                            "moving_no_moving" => "-",
                            "PO_no_PO" => "-",
                            "productCode" => $productCodes[$i], 
                            "qty" => $qtys[$i], 
                            "UOM" => $uoms[$i], 
                            "price_per_UOM" => $price, 
                            "note" => $notes[$i],
                            "product_status" => $pageState
                        ]);

                        Order_Product::where("PO_no_PO", $purchase_order)->where("productCode", $productCodes[$i])->update([
                            "product_status" => "purchase_approved"
                        ]);
                    }
                }

                DB::table('purchase_orders')
                ->where('no_PO', $purchase_order)
                ->update(['status_mode' => 2]);

                DB::table('trucks')
                ->where('no_truk', $no_truk)
                ->update(['mode' => 2]);

                $truckEmail = Truck::where("no_truk", $no_truk)->pluck("truckEmail")->first();
                $this->azure->alertAdmins($pageState);
                $this->azure->sendEmail($truckEmail, "Delivery outstanding: $purchase_order", "an order requires sending and it has been assigned to you");
            }
        }

        session()->flash('msg', 'no_SJ: ' . $no_sj);

        if(session("userType") == 1){
            return redirect()->route("invoice", ["state" => $pageState]);
        }
        else{
            return redirect()->route("dashboard");
        }
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
