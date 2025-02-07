<?php

namespace App\Http\Controllers;

use App\Models\Moving;
use App\Models\Order_Product;
use App\Models\Payment;
use App\Services\OrderProductService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $orderProductService;

    public function __construct(OrderProductService $orderProductService)
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
        
    }
}
