<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Storage;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function dashboard()
    {
        $uuid = substr(Str::uuid()->toString(), 0, 8);
        return view("reports.dashboard", ["title" => "dashboard", "uuid" => $uuid]);
    }

    public function debt()
    {
        $storages = Storage::all();
        return view("reports.debt", ["title" => "debt", "storages" => $storages]);
    }

    function getProductsDebt($no_sj) {
        $results = Order_Product::where('nomor_surat_jalan', $no_sj)
            ->select([
                'productCode',
                'qty',
                'price_per_UOM',
                DB::raw('(qty * price_per_UOM) AS nominal')
            ])
            ->get();
    
        return $results;
    }

    public function getDebtReport(Request $req)
    {
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        $debtDetails = Order::query()
            ->select([
                'orders.nomor_surat_jalan',
                'invoices.invoice_date',
                'invoices.no_invoice',
                'invoices.tax',
                'vendors.vendorName',
                DB::raw('COALESCE(payments.payment_date, "-") AS payment_date'),
                DB::raw('COALESCE(payments.payment_amount, 0) AS payment_amount'),
            ])
            ->join('invoices', 'orders.nomor_surat_jalan', '=', 'invoices.nomor_surat_jalan')
            ->join('vendors', 'orders.vendorCode', '=', 'vendors.vendorCode')
            ->leftJoin('payments', 'orders.nomor_surat_jalan', '=', 'payments.nomor_surat_jalan')
            ->whereMonth('invoices.invoice_date', $month)
            ->whereYear('invoices.invoice_date', $year)
            ->where('orders.storageCode', $storageCode)
            ->where('orders.status_mode', 1)
            ->get();

        $groupData = [];
        foreach ($debtDetails as $details) {
            $hutangKey = $details["nomor_surat_jalan"];
            if (!isset($groupData[$hutangKey])) {
                $groupData[$hutangKey] = [
                    "invoice_date" => $details["invoice_date"],
                    "no_invoice" => $details["no_invoice"],
                    "tax" => $details["tax"],
                    "vendorName" => $details["vendorName"],
                    "payments" => [],
                    "products" => []
                ];

                $productsList = Order_Product::where('nomor_surat_jalan', $hutangKey)
                    ->select([
                        'productCode',
                        'qty',
                        'price_per_UOM',
                        DB::raw('(qty * price_per_UOM) AS nominal')
                    ])
                    ->get();

                foreach ($productsList as $product) {
                    array_push($groupData[$hutangKey]["products"], [
                        "productCode" => $product["productCode"],
                        "qty" => $product["qty"],
                        "price_per_UOM" => $product["price_per_UOM"],
                        "nominal" => $product["nominal"]
                    ]);
                }
            }

            array_push($groupData[$hutangKey]["payments"], [
                "payment_date" => $details["payment_date"],
                "payment_amount" => $details["payment_amount"]
            ]);
        }

        return array_values($groupData);
    }

    public function receivables()
    {
        return view("reports.receivables", ["title" => "receivables"]);
    }

    public function getReceivablesReport(Request $req)
    {
        $storageCode = "NON";
        $month = $req->month;
        $year = $req->year;

        $receivablesDetails = Order::query()
            ->select([
                'orders.nomor_surat_jalan',
                'invoices.invoice_date',
                'invoices.no_invoice',
                'invoices.tax',
                'customers.customerName',
                DB::raw('COALESCE(payments.payment_date, "-") AS payment_date'),
                DB::raw('COALESCE(payments.payment_amount, 0) AS payment_amount'),
            ])
            ->join('invoices', 'orders.nomor_surat_jalan', '=', 'invoices.nomor_surat_jalan')
            ->join('customers', 'orders.customerCode', '=', 'customers.customerCode')
            ->leftJoin('payments', 'orders.nomor_surat_jalan', '=', 'payments.nomor_surat_jalan')
            ->whereMonth('invoices.invoice_date', $month)
            ->whereYear('invoices.invoice_date', $year)
            ->where('orders.storageCode', $storageCode)
            ->where('orders.status_mode', 2)
            ->get();

        $groupData = [];
        foreach ($receivablesDetails as $details) {
            $receivablesKey = $details["nomor_surat_jalan"];
            if (!isset($groupData[$receivablesKey])) {
                $groupData[$receivablesKey] = [
                    "invoice_date" => $details["invoice_date"],
                    "no_invoice" => $details["no_invoice"],
                    "tax" => $details["tax"],
                    "customerName" => $details["customerName"],
                    "payments" => [],
                    "products" => []
                ];

                $productsList = Order_Product::where('nomor_surat_jalan', $receivablesKey)
                    ->select([
                        'productCode',
                        'qty',
                        'price_per_UOM',
                        DB::raw('(qty * price_per_UOM) AS nominal')
                    ])
                    ->get();

                foreach ($productsList as $product) {
                    array_push($groupData[$receivablesKey]["products"], [
                        "productCode" => $product["productCode"],
                        "qty" => $product["qty"],
                        "price_per_UOM" => $product["price_per_UOM"],
                        "nominal" => $product["nominal"]
                    ]);
                }
            }

            array_push($groupData[$receivablesKey]["payments"], [
                "payment_date" => $details["payment_date"],
                "payment_amount" => $details["payment_amount"]
            ]);
        }

        return array_values($groupData);
    }
}
