<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Purchase_Order;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TruckController extends Controller
{
    public function show_truck_login()
    {
        $title = "truck login";
        return view("trucks.truck_login", ["title" => $title]);
    }

    public function truck_dashboard()
    {
        $title = "truck dashboard";
        $orders = Order::where("no_truk_out", session("no_truk"))->where("delivered", 0)->get();
        
        return view("trucks.truck_dashboard", ["title" => $title, "orders" => $orders]);
    }

    public function truck_login(Request $req)
    {
        $email = $req->input("email");
        $password = $req->input("password");
        $user = Truck::where("truckEmail", $email)->first();

        if($user && Hash::check($password, $user->truckPassword)){
            $req->session()->put("no_truk", $user->no_truk);
            $req->session()->put("email", $user->truckEmail);
            $req->session()->put("userType", 3);
            return redirect()->route("truck_dashboard");
        }
        else{
            return redirect()->route("show_truck_login");
        }
    }

    public function deliver(Request $req)
    {
        $no_sj = $req->no_sj;

        Order::where("nomor_surat_jalan", $no_sj)->update(["delivered" => 1]);
        Truck::where("no_truk", session('no_truk'))->update(["mode" => 1]);
        
        $purchaseOrder = Order::where("nomor_surat_jalan", $no_sj)->pluck("purchase_order")->first();

        // Query to get the total and accepted products
        $result = Order_Product::where('PO_no_PO', $purchaseOrder)
        ->selectRaw('COUNT(*) as total_products, SUM(CASE WHEN product_status = "purchase_approved" THEN 1 ELSE 0 END) as accepted_products')
        ->first();

        if ($result->total_products == $result->accepted_products) {
            Purchase_Order::where("no_PO", $purchaseOrder)->update(["status_mode" => 3]);
        }


        session()->flash('msg', 'delivery: COMPLETE: ' . $no_sj);
        return redirect()->route("truck_dashboard");
    }
}
