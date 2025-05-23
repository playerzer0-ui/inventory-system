<?php

use App\Http\Controllers\AmendController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MovingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RepackController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SlipController;
use App\Http\Controllers\TruckController;
use App\Service\AzureEmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route("home");
});

//home
Route::get("/home", [HomeController::class, "index"])->name("home");
Route::post("/login", [HomeController::class, "login"])->name("login");
Route::get("/show_customer_login", [CustomerController::class, "show_customer_login"])->name("show_customer_login");
Route::post("/customer_login", [CustomerController::class, "customer_login"])->name("customer_login");
Route::get("/show_truck_login", [TruckController::class, "show_truck_login"])->name("show_truck_login");
Route::post("/truck_login", [TruckController::class, "truck_login"])->name("truck_login");
Route::get("/logout", [HomeController::class, "logout"])->name("logout");

Route::group(['middleware' => ['auth.user']], function () {
    Route::get("/amends", [HomeController::class, "amends"])->name("amends");
    Route::get("/amend_update", [HomeController::class, "amend_update"])->name("amend_update");
    Route::get("/amend_delete", [HomeController::class, "amend_delete"])->name("amend_delete");
    
    //services
    Route::get("/generate_LPB_SJK_INV", [ServiceController::class, "generate_LPB_SJK_INV"])->name("generate_LPB_SJK_INV");
    Route::get("/getProductSuggestions", [ServiceController::class, "getProductSuggestions"])->name("getProductSuggestions");
    Route::get("/getProductDetails", [ServiceController::class, "getProductDetails"])->name("getProductDetails");
    Route::get("/getOrderByNoSJ", [ServiceController::class, "getOrderByNoSJ"])->name("getOrderByNoSJ");
    Route::get("/getOrderProducts", [ServiceController::class, "getOrderProducts"])->name("getOrderProducts");
    Route::get("/getHPP", [ReportController::class, "getHPP"])->name("getHPP");
    Route::get("/getTruck", [ServiceController::class, "getTruck"])->name("getTruck");
    Route::get('/getPurchaseOrderProducts', [CustomerController::class, 'getPurchaseOrderProducts']);

    Route::post("/amend_delete_data", [AmendController::class, "amend_delete_data"])->name("amend_delete_data");
});

//customer
Route::group(['middleware' => ['check.user.type:2']], function () {
    Route::get("/customer_dashboard", [CustomerController::class, "customer_dashboard"])->name("customer_dashboard");
    Route::get("/customer_receipt", [CustomerController::class, "customer_receipt"])->name("customer_receipt");
    Route::get("/purchase_order", [CustomerController::class, "purchase_order"])->name("purchase_order");
    Route::post('/checkOutPurchase', [CustomerController::class, "checkOutPurchase"])->name("checkOutPurchase");
    Route::get("/create_purchase", [CustomerController::class, "create_purchase"])->name("create_purchase");
    Route::get("/list_purchase", [CustomerController::class, "list_purchase"])->name("list_purchase");
    Route::get("/amend_purchase", [CustomerController::class, "amend_purchase"])->name("amend_purchase");

    Route::get("/amend_purchase_data", [AmendController::class, "amend_purchase_data"])->name("amend_purchase_data");
    Route::post("/amendCheckoutPurchase", [AmendController::class, "amendCheckoutPurchase"])->name("amendCheckoutPurchase");
});

//trucks
Route::group(['middleware' => ['check.user.type:3']], function () {
    Route::get("/truck_dashboard", [TruckController::class, "truck_dashboard"])->name("truck_dashboard");
    Route::get("/deliver", [TruckController::class, "deliver"])->name("deliver");
});

//suppliers
Route::group(['middleware' => ['check.user.type:0,1']], function () {
    // Reports
    Route::get("/forecast", [ReportController::class, "forecast"])->name("forecast");
    Route::get("/getAllProductCodes", [ReportController::class, "getAllProductCodes"])->name("getAllProductCodes");
    Route::get("/dashboard", [ReportController::class, "dashboard"])->name("dashboard");
    Route::get("/getReportStock", [ReportController::class, "getReportStock"])->name("getReportStock");
    Route::get("/getProductData", [ReportController::class, "getProductData"])->name("getProductData");
    Route::get("/excel_stock", [ReportController::class, "excel_stock"])->name("excel_stock");

    // Slips
    Route::get("/slip", [SlipController::class, "slip"])->name("slip");
    Route::post("/create_slip", [SlipController::class, "create_slip"])->name("create_slip");
    Route::get("/amend_slip", [SlipController::class, "amend_slip"])->name("amend_slip");

    // Repacks
    Route::get("/repack", [RepackController::class, "repack"])->name("repack");
    Route::post("/create_repack", [RepackController::class, "create_repack"])->name("create_repack");
    Route::get("/amend_repack", [RepackController::class, "amend_repack"])->name("amend_repack");

    // Movings
    Route::get("/moving", [MovingController::class, "moving"])->name("moving");
    Route::post("/create_moving", [MovingController::class, "create_moving"])->name("create_moving");
    Route::get("/getMovingDetails", [MovingController::class, "getMovingDetails"])->name("getMovingDetails");
    Route::get("/amend_moving", [MovingController::class, "amend_moving"])->name("amend_moving");

    // Amends
    Route::post("/amend_slip_data", [AmendController::class, "amend_slip_data"])->name("amend_slip_data");
    Route::post("/amend_repack_data", [AmendController::class, "amend_repack_data"])->name("amend_repack_data");
    Route::post("/amend_moving_data", [AmendController::class, "amend_moving_data"])->name("amend_moving_data");
});

// Admin routes (userType = 1)
Route::group(['middleware' => ['check.user.type:1']], function () {
    // Additional reports for admin
    Route::get("/debt", [ReportController::class, "debt"])->name("debt");
    Route::get("/getDebtReport", [ReportController::class, "getDebtReport"])->name("getDebtReport");
    Route::get("/receivables", [ReportController::class, "receivables"])->name("receivables");
    Route::get("/getReceivablesReport", [ReportController::class, "getReceivablesReport"])->name("getReceivablesReport");
    Route::get("/excel_debt", [ReportController::class, "excel_debt"])->name("excel_debt");
    Route::get("/excel_receivable", [ReportController::class, "excel_receivable"])->name("excel_receivable");
    Route::get("/excel_logs", [ReportController::class, "excel_logs"])->name("excel_logs");

    // Invoices
    Route::get("/invoice", [InvoiceController::class, "invoice"])->name("invoice");
    Route::post("/create_invoice", [InvoiceController::class, "create_invoice"])->name("create_invoice");
    Route::get("/getInvoiceDetails", [InvoiceController::class, "getInvoiceDetails"])->name("getInvoiceDetails");
    Route::get("/amend_invoice", [InvoiceController::class, "amend_invoice"])->name("amend_invoice");

    // Payments
    Route::get("/payment", [PaymentController::class, "payment"])->name("payment");
    Route::post("/create_payment", [PaymentController::class, "create_payment"])->name("create_payment");
    Route::get("/calculateDebt", [PaymentController::class, "calculateDebt"])->name("calculateDebt");
    Route::get("/amend_payment", [PaymentController::class, "amend_payment"])->name("amend_payment");

    // Additional amends for admin
    Route::post("/amend_invoice_data", [AmendController::class, "amend_invoice_data"])->name("amend_invoice_data");
    Route::post("/amend_payment_data", [AmendController::class, "amend_payment_data"])->name("amend_payment_data");

    // Master data management
    Route::get("/master_create", [MasterController::class, "create"])->name("master_create");
    Route::post("/master_create_data", [MasterController::class, "create_data"])->name("master_create_data");
    Route::get("/master_read", [MasterController::class, "read"])->name("master_read");
    Route::get("/master_update", [MasterController::class, "update"])->name("master_update");
    Route::post("/master_update_data", [MasterController::class, "update_data"])->name("master_update_data");
    Route::get("/master_delete", [MasterController::class, "delete"])->name("master_delete");
    Route::post("/master_delete_data", [MasterController::class, "delete_data"])->name("master_delete_data");
});



//testing
Route::get("/test", [ReportController::class, "debug"])->name("test1");