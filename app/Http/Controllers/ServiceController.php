<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function getProductSuggestions(Request $req)
    {
        $code = $req->input("code");
        $products = Product::where('productCode', 'like', '%' . $code . '%')
        ->limit(10)
        ->pluck('productCode');

        return $products;
    }

    public function getProductDetails(Request $req)
    {
        $code = $req->input("code");
        $products = Product::where("productCode", $code)->first();
        
        return $products;
    }

    public function getOrderByNoSJ(Request $req)
    {
        $no_sj = $req->no_sj;

        $result = DB::table('orders as o')
        ->join('customers as c', 'o.customerCode', '=', 'c.customerCode')
        ->where('o.nomor_surat_jalan', $no_sj)
        ->select(
            'o.nomor_surat_jalan',
            'o.storageCode',
            'o.no_LPB',
            'o.no_truk',
            'o.vendorCode',
            'o.customerCode',
            'c.customerName',
            'c.customerAddress',
            'c.customerNPWP',
            'o.orderDate',
            'o.purchase_order',
            'o.status_mode'
        )
        ->first();

        return $result;
    }

    public function getOrderProducts(Request $req)
    {
        $no_sj = $req->no_sj;
        $status = $req->status;

        if($status == "in" || $status == "out"){
            $orderProducts = DB::table('order_products as op')
            ->join('products as p', 'op.productCode', '=', 'p.productCode')
            ->where('op.nomor_surat_jalan', $no_sj)
            ->select(
                'op.nomor_surat_jalan',
                'op.productCode',
                'p.productName',
                'op.qty',
                'op.uom',
                'op.price_per_UOM',
                'op.note',
                'op.product_status'
            )
            ->get();
        }
        else if($status == "repack"){
            $orderProducts = DB::table('order_products as op')
            ->join('products as p', 'op.productCode', '=', 'p.productCode')
            ->where('op.repack_no_repack', $no_sj)
            ->select(
                'op.repack_no_repack',
                'op.productCode',
                'p.productName',
                'op.qty',
                'op.uom',
                'op.price_per_UOM',
                'op.note',
                'op.product_status'
            )
            ->get();
        }
        else{
            $orderProducts = DB::table('order_products as op')
            ->join('products as p', 'op.productCode', '=', 'p.productCode')
            ->where('op.moving_no_moving', $no_sj)
            ->select(
                'op.moving_no_moving',
                'op.productCode',
                'p.productName',
                'op.qty',
                'op.uom',
                'op.price_per_UOM',
                'op.note',
                'op.product_status'
            )
            ->get();
        }

        return $orderProducts;
    }

    public function getInvoiceByNoSJ(Request $req)
    {
        $no_sj = $req->no_sj;

        $invoice = Invoice::where("nomor_surat_jalan", $no_sj)->first();
        return $invoice;
    }

    public function generate_LPB_SJK_INV(Request $req)
    {
        $state = $req->state;
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        $uuid = substr(Str::uuid()->toString(), 0, 8);
        $timestamp = now()->format('YmdHis');

        $format = "$timestamp-$uuid/$state/$storageCode/$month/$year";

        return $format;
    }
}
