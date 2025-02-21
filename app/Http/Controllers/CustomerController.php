<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function show_customer_login()
    {
        return view("customers.customer_login", ["title" => "customer login"]);
    }

    public function customer_login(Request $req)
    {
        $email = $req->input("email");
        $password = $req->input("password");
        $user = Customer::where("customerEmail", $email)->first();

        if($user && Hash::check($password, $user->customerPassword)){
            $req->session()->put("email", $user->email);
            $req->session()->put("userType", 2);
            return redirect()->route("customer_dashboard", ["title" => "customer login"]);
        }
        else{
            return redirect()->route("show_customer_login");
        }
    }

    public function customer_dashboard()
    {
        $products = Product::all();
        return view("customers.customer_dashboard", ["title" => "customer dashboard", "products" => $products]);
    }

    public function purchase_order()
    {
        
    }
}
