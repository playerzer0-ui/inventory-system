<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

    public function getDebtReport(Request $req)
    {
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        $results = Order::query()
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

        return $results;
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

        $results = Order::query()
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

        return $results;
    }
}
