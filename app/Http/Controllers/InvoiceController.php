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

    public function getInvoiceDetails(Request $req)
    {
        $no_moving = $req->no_moving;
        $no_sj = $req->no_sj;

        if($no_sj){
            $result = Invoice::where("nomor_surat_jalan", $no_sj)->first();
        }
        else{
            $result = Invoice::where("no_moving", $no_moving)->first();
        }
        return $result;
    }

    public function remove_invoice(Request $req)
    {
        
    }

    public function amend_invoice(Request $req)
    {
        $state = $req->state;
        $no_sj = $req->no_sj;
        $no_moving = $req->no_moving;
        $title = "AMEND " . $state;

        if($no_moving){
            $result = Moving::where("no_moving", $no_moving)->first();
            $products = $this->orderProductService->getOrderProducts($no_moving, "moving");
        }
        else{
            $result = $this->orderProductService->getOrderByNoSJ($no_sj);
            $products = $this->orderProductService->getOrderProducts($no_sj, "in");
        }

        if($no_sj){
            $invoice = Invoice::where("nomor_surat_jalan", $no_sj)->first();
        }
        else{
            $invoice = Invoice::where("no_moving", $no_moving)->first();
        }

        return view("amends.amend_invoice", ["title" => $title, "state" => $state, "result" => $result, "invoice" => $invoice, "products" => $products]);
    }
}
