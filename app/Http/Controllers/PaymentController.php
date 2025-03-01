<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Moving;
use App\Models\Order_Product;
use App\Service\PDFService;
use App\Models\Payment;
use App\Service\OrderProductService as ServiceOrderProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $orderProductService;
    protected $pdf;

    public function __construct(ServiceOrderProductService $orderProductService, PDFService $pdf)
    {
        $this->orderProductService = $orderProductService;
        $this->pdf = $pdf;
    }

    public function payment(Request $req)
    {
        $state = $req->state;
        $title = "PAYMENT " . $state;
        if($state == "moving"){
            $orders = Invoice::pluck('no_moving');
        }
        else{
            switch($state){
                case "in":
                    $orders = Invoice::where('nomor_surat_jalan', 'NOT LIKE', '%SJK%')
                    ->where('nomor_surat_jalan', 'NOT LIKE', '%SJT%')
                    ->pluck('nomor_surat_jalan');
                    break;
                case "out":
                    $orders = Invoice::where('nomor_surat_jalan', 'LIKE', '%SJK%')->pluck('nomor_surat_jalan');
                    break;
                case "out_tax":
                    $orders = Invoice::where('nomor_surat_jalan', 'LIKE', '%SJT%')->pluck('nomor_surat_jalan');
                    break;
            }
        }
        return view("payment", ["title" => $title, "state" => $state, "orders" => $orders]);
    }

    public function create_payment(Request $req)
    {
        $no_sj = $req->no_sj;
        $no_moving = $req->no_moving;
        $payment_date = $req->payment_date;
        $payment_amount = $req->payment_amount;

        Payment::create([
            "payment_id" => substr(Str::uuid()->toString(), 0, 8),
            "nomor_surat_jalan" => $no_sj ?? "-",
            "no_moving" => $no_moving ?? "-",
            "payment_date" => $payment_date,
            "payment_amount" => $payment_amount
        ]);

        session()->flash('msg', 'payment created: ' . ($no_sj ?? $no_moving));

        return $this->pdf->create_paymentPDF($req);
        //return redirect()->route("dashboard");
    }

    function calculateDebt(Request $req) 
    {
        $no_sj = $req->no_sj;
        $payment_amount = $req->payment_amount;
        $tax = $req->tax;
        $remaining = 0;
    
        if ($no_sj !== null) {
            // Calculate total nominal
            if (strpos($no_sj, "SJP") === false) {
                $totalNominal = Order_Product::where('nomor_surat_jalan', $no_sj)
                    ->selectRaw('SUM(qty * price_per_UOM) as totalNominal')
                    ->pluck('totalNominal')
                    ->first();
            } else {
                $totalNominal = Order_Product::where('moving_no_moving', $no_sj)
                    ->selectRaw('SUM(qty * price_per_UOM) as totalNominal')
                    ->pluck('totalNominal')
                    ->first();
            }
    
            // Calculate total payment
            if (strpos($no_sj, "SJP") === false) {
                $totalPayment = Payment::where('nomor_surat_jalan', $no_sj)
                    ->selectRaw('SUM(payment_amount) as totalPayment')
                    ->pluck('totalPayment')
                    ->first();
            } else {
                $totalPayment = Payment::where('no_moving', $no_sj)
                    ->selectRaw('SUM(payment_amount) as totalPayment')
                    ->pluck('totalPayment')
                    ->first();
            }
    
            // Adjust total nominal for tax
            $totalNominal = $totalNominal + ($totalNominal * ((double)$tax / 100));
    
            // Calculate remaining payment
            if ($payment_amount !== null) {
                $remaining = $totalNominal - $totalPayment - $payment_amount;
            } else {
                $remaining = $totalNominal - $totalPayment;
            }
        }
    
        return $remaining;
    }

    public function amend_payment(Request $req)
    {
        $state = $req->state;
        $no_sj = $req->no_sj;
        $no_moving = $req->no_sj;
        $payment_id = $req->payment_id;

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

        $payment = Payment::where("payment_id", $payment_id)->first();

        $title = "AMEND PAYMENT " . $state;
        return view("amends.amend_payment", ["title" => $title, "state" => $state, "result" => $result, "invoice" => $invoice, "payment" => $payment, "products" => $products]);
    }
}
