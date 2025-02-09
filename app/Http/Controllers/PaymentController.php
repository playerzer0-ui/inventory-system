<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Moving;
use App\Models\Order_Product;
use App\Models\Payment;
use App\Service\OrderProductService as ServiceOrderProductService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $orderProductService;

    public function __construct(ServiceOrderProductService $orderProductService)
    {
        $this->orderProductService = $orderProductService;
    }

    public function payment(Request $req)
    {
        $state = $req->state;
        $title = "PAYMENT " . $state;
        return view("payment", ["title" => $title, "state" => $state]);
    }

    public function create_payment(Request $req)
    {
        $no_sj = $req->no_sj;
        $no_moving = $req->no_moving;
        $payment_date = $req->payment_date;
        $payment_amount = $req->payment_amount;

        Payment::create([
            "nomor_surat_jalan" => $no_sj ?? "-",
            "no_moving" => $no_moving ?? "-",
            "payment_date" => $payment_date,
            "payment_amount" => $payment_amount
        ]);

        session()->flash('msg', 'payment created: ' . ($no_sj ?? $no_moving));

        return redirect()->route("dashboard");
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
    

    public function remove_payment(Request $req)
    {
        
    }

    public function amend_payment(Request $req)
    {
        $state = $req->state;
        $no_sj = $req->no_sj;
        $no_moving = $req->no_moving;
        $payment_id = $req->payment_id;

        if($no_moving){
            $result = Moving::where("no_moving", $no_moving)->first();
            $products = $this->orderProductService->getOrderProducts($no_moving, "moving");
            
        }
        else{
            $result = $this->orderProductService->getOrderByNoSJ($no_sj);
            $products = $this->orderProductService->getOrderProducts($no_sj, "in");
        }

        $invoice = $this->orderProductService->getInvoiceDetails($no_sj, $no_moving);
        $payment = Payment::where("payment_id", $payment_id)->first();

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

        $title = "AMEND PAYMENT " . $state;
        return view("amends.amend_payment", ["title" => $title, "state" => $state, "result" => $result, "invoice" => $invoice, "payment" => $payment, "products" => $products]);
    }
}
