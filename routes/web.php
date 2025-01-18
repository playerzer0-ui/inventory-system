<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
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

Route::get("/home", [HomeController::class, "index"])->name("home");
Route::post("/login", [HomeController::class, "login"])->name("login");
Route::get("/logout", [HomeController::class, "logout"])->name("logout");

Route::get("/dashboard", [ReportController::class, "dashboard"])->name("dashboard");
Route::get("/debt", [ReportController::class, "debt"])->name("debt");
Route::get("/receivables", [ReportController::class, "receivables"])->name("receivables");

Route::get("/slip", [SlipController::class, "slip"])->name("slip");

Route::get("/invoice", [InvoiceController::class, "invoice"])->name("invoice");

Route::get("/payment", [PaymentController::class, "payment"])->name("payment");