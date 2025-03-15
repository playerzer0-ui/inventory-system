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
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;

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
        $customerCode = session('customerCode');
        $purchaseDate = date("Y/m/d");

        // Retrieve data from session
        $purchaseData = session('purchase_data');

        if (!$purchaseData) {
            return redirect()->route("customer_dashboard")->with('error', 'Session expired. Please try again.');
        }

        $no_PO = $purchaseData['no_PO'];
        $productCodes = $purchaseData['productCodes'];
        $qtys = $purchaseData['qtys'];
        $price_per_uom = $purchaseData['price_per_uom'];

        // Save the purchase order
        Purchase_Order::create([
            "no_PO" => $no_PO,
            "purchaseDate" => $purchaseDate,
            "customerCode" => $customerCode,
            "status_mode" => 1
        ]);

        // Save order products
        foreach ($productCodes as $i => $code) {
            Order_Product::create([
                "nomor_surat_jalan" => "-", 
                "repack_no_repack" => "-",
                "moving_no_moving" => "-",
                "PO_no_PO" => $no_PO,
                "productCode" => $code, 
                "qty" => $qtys[$i], 
                "UOM" => "tray", 
                "price_per_UOM" => $price_per_uom[$i], 
                "note" => "",
                "product_status" => "purchase_order"
            ]);
        }

        // Clear session after use
        session()->forget('purchase_data');

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

    public function checkOutPurchase(Request $req)
    {
        $no_PO = "PO-" . substr(Str::uuid()->toString(), 0, 8);

        $productCodes = $req->input('kd');
        $qtys = $req->input('qty');
        $price_per_uom = $req->input("price_per_uom");
        
        $total = $req->grand_total;

        // Convert to cents (Stripe requires the amount in cents)
        $unit_amount = (int) round($total * 100);

        // Store purchase data in session
        session([
            'purchase_data' => [
                'no_PO' => $no_PO,
                'productCodes' => $productCodes,
                'qtys' => $qtys,
                'price_per_uom' => $price_per_uom
            ]
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => $no_PO],
                    'unit_amount' => $unit_amount, // Convert to cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('create_purchase'), // No need to pass parameters
            'cancel_url' => url('purchase_order'),
        ]);

        return redirect()->away($session->url);
    }
}
