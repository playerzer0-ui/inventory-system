<?php

namespace App\Http\Controllers;

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
        
    }

    public function remove_payment(Request $req)
    {
        
    }

    public function amend_payment(Request $req)
    {
        
    }
}
