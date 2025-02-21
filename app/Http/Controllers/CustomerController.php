<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order_Product;
use App\Models\Product;
use App\Models\Purchase_Order;
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
            $req->session()->put("customerCode", $user->customerCode);
            $req->session()->put("email", $user->customerEmail);
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
        return view("customers.purchase_order", ["title" => "purchase order"]);
    }

    public function create_purchase(Request $req)
    {
        $customerCode = $req->customerCode;
        $purchase_date = $req->purchase_date;
        $no_PO = "";

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $uoms = $req->input('uom');
        $notes = $req->input('note');

        Purchase_Order::create([
            "no_PO" => "sa",
            "purchaseDate" => $purchase_date,
            "customerCode" => $customerCode
        ]);
        
        if($productCodes){
            for($i = 0; $i < count($productCodes); $i++){
                Order_Product::create([
                    "nomor_surat_jalan" => "-", 
                    "repack_no_repack" => "-",
                    "moving_no_moving" => "-",
                    "PO_no_PO" => "-",
                    "productCode" => $productCodes[$i], 
                    "qty" => $qtys[$i], 
                    "UOM" => $uoms[$i], 
                    "price_per_UOM" => 0, 
                    "note" => $notes[$i],
                    "product_status" => "purchase_order"
                ]);
            }
        }

        session()->flash('msg', 'no_SJ: ' . $no_PO);

        return redirect()->route("customer_dashbaord");

    }
}
