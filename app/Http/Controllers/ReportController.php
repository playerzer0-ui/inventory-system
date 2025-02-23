<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Product;
use App\Models\Storage;
use App\Service\StorageReport;
use App\Service\ExcelService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    protected $storageReport;
    protected $excel;

    public function __construct(StorageReport $storageReport, ExcelService $excel)
    {
        $this->storageReport = $storageReport;
        $this->excel = $excel;
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

        return $this->storageReport->getDebtReport($storageCode, $month, $year);
    }

    public function receivables()
    {
        return view("reports.receivables", ["title" => "receivables"]);
    }

    public function getReceivablesReport(Request $req)
    {
        $month = $req->month;
        $year = $req->year;

        return $this->storageReport->getreceivablesReport($month, $year);
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

    public function excel_stock(Request $req)
    {
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;
        $result = "";

        //dd($storageCode, $month, $year);
        switch(session('userType')){
            case 0:
                $result = $this->excel->report_stock_excel_normal($storageCode, $month, $year);
                break;
            case 1:
                $result = $this->excel->report_stock_excel($storageCode, $month, $year);
                break;
            default:
                break;
        }

        return $result;
    }

    public function excel_debt(Request $req)
    {
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        return $this->excel->excel_debt_receivable($storageCode, $month, $year, "debt");
    }

    public function excel_receivable(Request $req)
    {
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        return $this->excel->excel_debt_receivable($storageCode, $month, $year, "receivable");
    }
}
