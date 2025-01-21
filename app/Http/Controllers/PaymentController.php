<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
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

        session()->flash('msg', 'payment created: ' . $no_sj);

        return redirect()->route("dashboard");
    }

    public function remove_payment(Request $req)
    {
        
    }

    public function amend_payment(Request $req)
    {
        
    }
}
