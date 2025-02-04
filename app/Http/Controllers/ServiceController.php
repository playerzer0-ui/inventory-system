<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\OrderProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    protected $orderProductService;

    public function __construct(OrderProductService $orderProductService)
    {
        $this->orderProductService = $orderProductService;
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

        return $this->orderProductService->getOrderByNoSJ($no_sj);
    }

    public function getOrderProducts(Request $req)
    {
        $no_sj = $req->no_sj;
        $status = $req->status;

        return $this->orderProductService->getOrderProducts($no_sj, $status);
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
