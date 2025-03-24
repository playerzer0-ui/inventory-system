<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Moving;
use App\Models\Order;
use App\Service\PDFService;
use App\Models\Order_Product;
use App\Service\OrderProductService as ServiceOrderProductService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $orderProductService;
    protected $pdf;

    public function __construct(ServiceOrderProductService $orderProductService,PDFService $pdf)
    {
        $this->orderProductService = $orderProductService;
        $this->pdf = $pdf;
    }

    public function invoice(Request $req)
    {
        $state = $req->state;
        $title = "INVOICE " . $state;
        if($state == "moving"){
            $orders = Moving::pluck('no_moving');
        }
        else{
            switch($state){
                case "in":
                    $orders = Order::where('status_mode', 1)->pluck('nomor_surat_jalan');
                    break;
                case "out":
                    $orders = Order::leftJoin('invoices', 'orders.nomor_surat_jalan', '=', 'invoices.nomor_surat_jalan')
                    ->where('orders.status_mode', 2)
                    ->whereNull('invoices.nomor_surat_jalan')
                    ->pluck('orders.nomor_surat_jalan');
                    break;
                case "out_tax":
                    $orders = Order::leftJoin('invoices', 'orders.nomor_surat_jalan', '=', 'invoices.nomor_surat_jalan')
                    ->where('orders.status_mode', 3)
                    ->whereNull('invoices.nomor_surat_jalan')
                    ->pluck('orders.nomor_surat_jalan');
                    break;
            }
        }
        return view("invoice", ["title" => $title, "state" => $state, "orders" => $orders]);
    }

    public function getInvoiceDetails(Request $req)
    {
        $no_sj = $req->no_sj;
        $no_moving = $req->no_moving;

        return $this->orderProductService->getInvoiceDetails($no_sj, $no_moving);
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

        return $this->pdf->createPDF($req);
        //return redirect()->route("payment", ["state" => $pageState]);
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
