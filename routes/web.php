<?php

use App\Http\Controllers\AmendController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MovingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RepackController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SlipController;
use Illuminate\Support\Facades\Route;

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
Route::get("/logout", [HomeController::class, "logout"])->name("logout");
Route::get("/amends", [HomeController::class, "amends"])->name("amends");
Route::get("/amend_update", [HomeController::class, "amend_update"])->name("amend_update");
Route::get("/amend_delete", [HomeController::class, "amend_delete"])->name("amend_delete");

//reports
Route::get("/forecast", [ReportController::class, "forecast"])->name("forecast");
Route::get("/dashboard", [ReportController::class, "dashboard"])->name("dashboard");
Route::get("/debt", [ReportController::class, "debt"])->name("debt");
Route::get("/getDebtReport", [ReportController::class, "getDebtReport"])->name("getDebtReport");
Route::get("/receivables", [ReportController::class, "receivables"])->name("receivables");
Route::get("/getReceivablesReport", [ReportController::class, "getReceivablesReport"])->name("getReceivablesReport");
Route::get("/getReportStock", [ReportController::class, "getReportStock"])->name("getReportStock");
Route::get("/getProductData", [ReportController::class, "getProductData"])->name("getProductData");

//slips
Route::get("/slip", [SlipController::class, "slip"])->name("slip");
Route::post("/create_slip", [SlipController::class, "create_slip"])->name("create_slip");
Route::get("/amend_slip", [SlipController::class, "amend_slip"])->name("amend_slip");

//invoices
Route::get("/invoice", [InvoiceController::class, "invoice"])->name("invoice");
Route::post("/create_invoice", [InvoiceController::class, "create_invoice"])->name("create_invoice");
Route::get("/getInvoiceDetails", [InvoiceController::class, "getInvoiceDetails"])->name("getInvoiceDetails");
Route::get("/amend_invoice", [InvoiceController::class, "amend_invoice"])->name("amend_invoice");

//payments
Route::get("/payment", [PaymentController::class, "payment"])->name("payment");
Route::post("/create_payment", [PaymentController::class, "create_payment"])->name("create_payment");
Route::get("/calculateDebt", [PaymentController::class, "calculateDebt"])->name("calculateDebt");
Route::get("/amend_payment", [PaymentController::class, "amend_payment"])->name("amend_payment");

//repacks
Route::get("/repack", [RepackController::class, "repack"])->name("repack");
Route::post("/create_repack", [RepackController::class, "create_repack"])->name("create_repack");
Route::get("/amend_repack", [RepackController::class, "amend_repack"])->name("amend_repack");

//movings
Route::get("/moving", [MovingController::class, "moving"])->name("moving");
Route::post("/create_moving", [MovingController::class, "create_moving"])->name("create_moving");
Route::get("/getMovingDetails", [MovingController::class, "getMovingDetails"])->name("getMovingDetails");
Route::get("/amend_moving", [MovingController::class, "amend_moving"])->name("amend_moving");

//services
Route::get("/generate_LPB_SJK_INV", [ServiceController::class, "generate_LPB_SJK_INV"])->name("generate_LPB_SJK_INV");
Route::get("/getProductSuggestions", [ServiceController::class, "getProductSuggestions"])->name("getProductSuggestions");
Route::get("/getProductDetails", [ServiceController::class, "getProductDetails"])->name("getProductDetails");
Route::get("/getOrderByNoSJ", [ServiceController::class, "getOrderByNoSJ"])->name("getOrderByNoSJ");
Route::get("/getOrderProducts", [ServiceController::class, "getOrderProducts"])->name("getOrderProducts");
Route::get("/getHPP", [ReportController::class, "getHPP"])->name("getHPP");

//amends data
Route::post("/amend_slip_data", [AmendController::class, "amend_slip_data"])->name("amend_slip_data");
Route::post("/amend_invoice_data", [AmendController::class, "amend_invoice_data"])->name("amend_invoice_data");
Route::post("/amend_payment_data", [AmendController::class, "amend_payment_data"])->name("amend_payment_data");
Route::post("/amend_repack_data", [AmendController::class, "amend_repack_data"])->name("amend_repack_data");
Route::post("/amend_moving_data", [AmendController::class, "amend_moving_data"])->name("amend_moving_data");
Route::post("/amend_delete_data", [AmendController::class, "amend_delete_data"])->name("amend_delete_data");

//master
Route::get("/master_create", [MasterController::class, "create"])->name("master_create");
Route::post("/master_create_data", [MasterController::class, "create_data"])->name("master_create_data");
Route::get("/master_read", [MasterController::class, "read"])->name("master_read");
Route::get("/master_update", [MasterController::class, "update"])->name("master_update");
Route::post("/master_update_data", [MasterController::class, "update_data"])->name("master_update_data");
Route::get("/master_delete", [MasterController::class, "delete"])->name("master_delete");
Route::post("/master_delete_data", [MasterController::class, "delete_data"])->name("master_delete_data");

//testing
Route::get("/test1", [ReportController::class, "getreportStock"])->name("test1");


