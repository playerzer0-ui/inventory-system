<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order_Product;
use App\Models\Product;
use App\Models\Purchase_Order;
use App\Service\AzureEmailService;
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
    protected $azure;

    public function __construct(ServiceOrderProductService $orderProductService, AzureEmailService $azure)
    {
        $this->orderProductService = $orderProductService;
        $this->azure = $azure;
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

    public function customer_receipt(Request $req)
    {
        $data = $this->getPurchaseOrderProducts($req)->getData(); // Extract data from JsonResponse
        $purchaseOrder = $data->purchaseOrder; // Access as object
        $noPO = $purchaseOrder->no_PO; // "PO-33f0ce44"
        $purchaseDate = $purchaseOrder->purchaseDate; // "2025-03-22"

        // Extract products array
        $products = $data->products;
        return view("customers.customer_receipt", ["title" => "receipt", "no_PO" => $noPO, "purchase_date" => $purchaseDate, "products" => $products]);
    }

    public function purchase_order()
    {
        return view("customers.purchase_order", ["title" => "purchase order"]);
    }

    public function create_purchase()
    {
        $customerCode = session('customerCode');
        $purchaseDate = date("Y/m/d");

        // Retrieve data from session
        $purchaseData = session('purchase_data');

        if (!$purchaseData) {
            return redirect()->route("customer_dashboard")->with('error', 'Session expired. Please try again.');
        }

        // Get the session_id from the URL
        $sessionId = session('session_id');

        if (!$sessionId) {
            return redirect()->route('purchase_order')->with('error', 'Session ID missing.');
        }

        // Retrieve the Stripe session
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = Session::retrieve($sessionId);

        // Get the payment intent ID
        $paymentIntentId = $session->payment_intent;
        session()->forget("session_id");

        $no_PO = $purchaseData['no_PO'];
        $productCodes = $purchaseData['productCodes'];
        $qtys = $purchaseData['qtys'];
        $price_per_uom = $purchaseData['price_per_uom'];

        // Save the purchase order
        Purchase_Order::create([
            "no_PO" => $no_PO,
            "purchaseDate" => $purchaseDate,
            "customerCode" => $customerCode,
            "status_mode" => 1,
            "payIntent" => $paymentIntentId
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

        $this->azure->alertSuppliers($no_PO);
        return redirect()->route("customer_receipt", ['no_PO' => $no_PO]);
    }

    public function list_purchase()
    {
        $purchases = Purchase_Order::where("customerCode", session('customerCode'))->get();
        return view("customers.list_purchase", ["title" => "list purchases", "purchases" => $purchases]);
    }

    public function amend_purchase(Request $req)
    {
        $no_PO = $req->no_PO;
        $mode = $req->mode;
        $result = Purchase_Order::where("no_PO", $no_PO)->first();
        if($mode == null){
            $products = $this->orderProductService->getOrderProducts($no_PO, "purchase");
        }
        else {
            $products = $this->orderProductService->getOrderProducts($no_PO, "purchase_otw");
        }
        return view("amends.amend_purchase", ["title" => "amend purchase", "result" => $result, "products" => $products, "mode" => $mode]);
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
                    'unit_amount' => $unit_amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('create_purchase'),
            'cancel_url' => url('purchase_order'),
        ]);

        session()->put("session_id", $session->id);
        return redirect()->away($session->url);
    }
}
