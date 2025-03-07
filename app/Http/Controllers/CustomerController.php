<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order_Product;
use App\Models\Product;
use App\Models\Purchase_Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Service\OrderProductService as ServiceOrderProductService;

class CustomerController extends Controller
{
    protected $orderProductService;

    public function __construct(ServiceOrderProductService $orderProductService)
    {
        $this->orderProductService = $orderProductService;
    }

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
            return redirect()->route("customer_dashboard");
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
        $purchaseDate = $req->purchaseDate;
        $no_PO = "PO-" . substr(Str::uuid()->toString(), 0, 8);

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $uoms = $req->input('uom');
        $price_per_uom = $req->input("price_per_uom");
        $notes = $req->input('note');

        Purchase_Order::create([
            "no_PO" => $no_PO,
            "purchaseDate" => $purchaseDate,
            "customerCode" => $customerCode,
            "status_mode" => 1
        ]);
        
        if($productCodes){
            for($i = 0; $i < count($productCodes); $i++){
                Order_Product::create([
                    "nomor_surat_jalan" => "-", 
                    "repack_no_repack" => "-",
                    "moving_no_moving" => "-",
                    "PO_no_PO" => $no_PO,
                    "productCode" => $productCodes[$i], 
                    "qty" => $qtys[$i], 
                    "UOM" => $uoms[$i], 
                    "price_per_UOM" => $price_per_uom[$i], 
                    "note" => $notes[$i],
                    "product_status" => "purchase_order"
                ]);
            }
        }

        session()->flash('msg', 'no_PO: ' . $no_PO);

        return redirect()->route("customer_dashboard");
    }

    public function list_purchase()
    {
        $purchases = Purchase_Order::where("customerCode", session('customerCode'))->get();
        return view("customers.list_purchase", ["title" => "list purchases", "purchases" => $purchases]);
    }

    public function amend_purchase(Request $req)
    {
        $no_PO = $req->no_PO;
        $result = Purchase_Order::where("no_PO", $no_PO)->first();
        $products = $this->orderProductService->getOrderProducts($no_PO, "purchase");
        return view("amends.amend_purchase", ["title" => "amend purchase", "result" => $result, "products" => $products]);
    }

    public function getPurchaseOrderProducts(Request $request)
    {
        $no_PO = $request->input('no_PO'); // Get the purchase order number from the request

        // Fetch the purchase order
        $purchaseOrder = Purchase_Order::where('no_PO', $no_PO)->first();

        if (!$purchaseOrder) {
            return response()->json(['error' => 'Purchase order not found'], 404);
        }

        // Fetch the associated products
        $products = $this->orderProductService->getOrderProducts($no_PO, "purchase");

        return response()->json([
            'purchaseOrder' => $purchaseOrder,
            'products' => $products,
        ]);
    }
}
