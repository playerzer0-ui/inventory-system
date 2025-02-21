<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Moving;
use App\Models\Order_Product;
use App\Service\OrderProductService as ServiceOrderProductService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $orderProductService;

    public function __construct(ServiceOrderProductService $orderProductService)
    {
        $this->orderProductService = $orderProductService;
    }

    public function invoice(Request $req)
    {
        $state = $req->state;
        $title = "INVOICE " . $state;
        return view("invoice", ["title" => $title, "state" => $state]);
    }

    public function create_invoice(Request $req)
    {
        $no_sj = $req->no_sj;
        $no_moving = $req->no_moving;
        $invoice_date = $req->invoice_date;
        $no_invoice = $req->no_invoice;
        $no_faktur = $req->no_faktur;
        $tax = $req->tax;

        $productCodes = $req->input('kd');
        $price_per_uom = $req->input("price_per_uom");
        $pageState = $req->pageState;

        Invoice::create([
            "nomor_surat_jalan" => $no_sj ?? "-",
            "no_moving" => $no_moving ?? "-",
            "invoice_date" => $invoice_date,
            "no_invoice" => $no_invoice,
            "no_faktur" => $no_faktur,
            "tax" => $tax
        ]);

        if($productCodes){
            for($i = 0; $i < count($productCodes); $i++){
                $column = $no_sj ? 'nomor_surat_jalan' : 'moving_no_moving';
                $value = $no_sj ?? $no_moving;
            
                Order_Product::where($column, $value)
                    ->where('productCode', $productCodes[$i])
                    ->update(['price_per_UOM' => $price_per_uom[$i]]);
            }
        }
        
        session()->flash('msg', 'no_SJ: ' . ($no_sj ?? $no_moving));

        return redirect()->route("payment", ["state" => $pageState]);
    }

    public function amend_invoice(Request $req)
    {
        $state = $req->state;
        $no_sj = $req->no_sj;
        $no_moving = $req->no_sj;

        if(strpos($no_moving, "SJP")){
            $result = Moving::where("no_moving", $no_moving)->first();
            $products = $this->orderProductService->getOrderProducts($no_moving, "moving");
            $state = "moving";
            $invoice =  $this->orderProductService->getInvoiceDetails(null, $no_moving);
        }
        else{
            $result = $this->orderProductService->getOrderByNoSJ($no_sj);
            $products = $this->orderProductService->getOrderProducts($no_sj, "in");
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
            $invoice =  $this->orderProductService->getInvoiceDetails($no_sj, null);
        }

        

        $title = "AMEND INVOICE " . $state;

        return view("amends.amend_invoice", ["title" => $title, "state" => $state, "result" => $result, "invoice" => $invoice, "products" => $products]);
    }
}
