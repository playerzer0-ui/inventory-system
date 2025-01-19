<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
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

//reports
Route::get("/dashboard", [ReportController::class, "dashboard"])->name("dashboard");
Route::get("/debt", [ReportController::class, "debt"])->name("debt");
Route::get("/receivables", [ReportController::class, "receivables"])->name("receivables");

//slips
Route::get("/slip", [SlipController::class, "slip"])->name("slip");
Route::post("/create_slip", [SlipController::class, "create_slip"])->name("create_slip");

//invoices
Route::get("/invoice", [InvoiceController::class, "invoice"])->name("invoice");
Route::post("/create_invoice", [InvoiceController::class, "create_invoice"])->name("create_invoice");

//payments
Route::get("/payment", [PaymentController::class, "payment"])->name("payment");

//services
Route::get("/generate_LPB", [ServiceController::class, "generate_LPB"])->name("generate_LPB");
Route::get("/generate_SJ", [ServiceController::class, "generate_SJ"])->name("generate_SJ");
Route::get("/getProductSuggestions", [ServiceController::class, "getProductSuggestions"])->name("getProductSuggestions");
Route::get("/getProductDetails", [ServiceController::class, "getProductDetails"])->name("getProductDetails");
Route::get("/getOrderByNoSJ", [ServiceController::class, "getOrderByNoSJ"])->name("getOrderByNoSJ");
Route::get("/getOrderProducts", [ServiceController::class, "getOrderProducts"])->name("getOrderProducts");
Route::get("/generateNoInvoice", [ServiceController::class, "generateNoInvoice"])->name("generateNoInvoice");