<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function generate_LPB(Request $req)
    {
        $state = "LPB";
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        $uuid = substr(Str::uuid()->toString(), 0, 8);
        $timestamp = now()->format('YmdHis');

        $format = "$timestamp-$uuid/$state/$storageCode/$month/$year";

        return $format;
    }

    public function generate_SJ(Request $req)
    {
        $state = "SJK";
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        $uuid = substr(Str::uuid()->toString(), 0, 8);
        $timestamp = now()->format('YmdHis');

        $format = "$timestamp-$uuid/$state/$storageCode/$month/$year";

        return $format;
    }

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

        $order = Order::where("no_sj", $no_sj)->first();
        return $order;
    }

    public function getOrderProducts(Request $req)
    {
        $no_sj = $req->no_sj;
        $status = $req->status;

        if($status == "in" || $status == "out"){
            $order_products = Order_Product::where("nomor_surat_jalan", $no_sj);
        }
        else if($status == "repack"){
            $order_products = Order_Product::where("repack_no_repack", $no_sj);
        }
        else{
            $order_products = Order_Product::where("moving_no_moving", $no_sj);
        }

        return $order_products;
    }

    public function generateNoInvoice(Request $req)
    {
        $state = "INV";
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        $uuid = substr(Str::uuid()->toString(), 0, 8);
        $timestamp = now()->format('YmdHis');

        $format = "$timestamp-$uuid/$state/$storageCode/$month/$year";

        return $format;
    }
}
