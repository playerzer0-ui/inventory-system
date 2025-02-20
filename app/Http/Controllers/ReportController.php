<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Product;
use App\Models\Storage;
use App\Service\StorageReport;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    protected $storageReport;

    public function __construct(StorageReport $storageReport)
    {
        $this->storageReport = $storageReport;
    }

    public function forecast()
    {
        $products = Product::all();
        return view("reports.forecast", ["title" => "forecast", "products" => $products]);
    }

    public function dashboard()
    {
        $storages = Storage::all();
        return view("reports.dashboard", ["title" => "dashboard", "storages" => $storages]);
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

    public function getreportStock(Request $req)
    {
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        $result = $this->storageReport->generateSaldo($storageCode, $month, $year);
        return $result;
        
    }

    public function getHPP(Request $req)
    {
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;
        $productCode = $req->productCode;
        
        $data = $this->storageReport->generateSaldo($storageCode, $month, $year);
        if(isset($data[$productCode]["ready_to_sell_items"]["price_per_qty"])){
            return $data[$productCode]["ready_to_sell_items"]["price_per_qty"];
        }
        else{
            return 0;
        }
    }

    public function getProductData(Request $req)
    {
        $productCode = $req->productCode;
        $results = Order_Product::select(
            'order_products.productCode',
            'orders.orderDate',
            DB::raw('SUM(order_products.qty) as total_qty'),
            'order_products.product_status'
        )
        ->join('orders', 'orders.nomor_surat_jalan', '=', 'order_products.nomor_surat_jalan')
        ->where('order_products.product_status', 'out')
        ->where('order_products.productCode', $productCode)
        ->groupBy('orders.orderDate', 'order_products.product_status', 'order_products.productCode')
        ->orderBy('orders.orderDate')
        ->get();
    
        return $results;
    }
}
