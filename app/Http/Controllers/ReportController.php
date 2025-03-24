<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Order_Product;
use App\Models\Product;
use App\Models\Storage;
use App\Models\Truck;
use App\Models\Vendor;
use App\Service\AzureEmailService;
use App\Service\StorageReport;
use App\Service\ExcelService;
use App\Service\PDFService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    protected $storageReport;
    protected $excel;
    protected $pdf;
    protected $azure;

    public function __construct(StorageReport $storageReport, ExcelService $excel, PDFService $pdf, AzureEmailService $azure)
    {
        $this->storageReport = $storageReport;
        $this->excel = $excel;
        $this->pdf = $pdf;
        $this->azure = $azure;
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
        $storages = Storage::all();
        return view("reports.receivables", ["title" => "receivables", "storages" => $storages]);
    }

    public function getReceivablesReport(Request $req)
    {
        $storageCode = $req->storageCode;
        $month = $req->month;
        $year = $req->year;

        return $this->storageReport->getreceivablesReport($storageCode, $month, $year);
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

    public function getAllProductCodes()
    {
        return Product::all()->map(function ($product) {
            return [
                'productCode' => $product->productCode,
                'productName' => $product->productName,
            ];
        });
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

        return $this->excel->excel_debt($storageCode, $month, $year);
    }

    public function excel_receivable(Request $req)
    {
        $month = $req->month;
        $year = $req->year;

        return $this->excel->excel_receivable($month, $year);
    }

    public function excel_logs()
    {
        return $this->excel->excel_logs();
    }

    public function debug()
    {
        $this->azure->alertAdmins("in");
        //$this->azure->alertSuppliers("PO-21390231213");
        //$this->azure->supplyLowCheck("APA", "2025-03-03", ["RR-100-A", "RR-120-A"]);
    }

    // public function createPDF(Request $req)
    // {
    //     $state = $req->pageState;
    //     $storageCode = $req->storageCode;
    //     $vendorCode = $req->vendorCode;
    //     $customerCode = $req->customerCode;
        
    //     $customerAddress = $req->customerAddress;
    //     $npwp = $req->npwp;
    //     $no_sj = $req->no_sj;
    //     $no_truk = $req->no_truk;
    //     $purchase_order = $req->purchase_order;
    //     $invoice_date = $req->invoice_date;
    //     $no_LPB = $req->no_LPB;
    //     $no_invoice = $req->no_invoice;

    //     $storageCodeSender = $req->storageCodeSender;
    //     $storageCodeReceiver = $req->storageCodeReceiver;
    //     $no_moving = $req->no_moving;
    //     $moving_date = $req->moving_date;

    //     $productCodes = $req->input('kd');
    //     $productNames = $req->input("material");
    //     $qtys = $req->input("qty");
    //     $uoms = $req->input("uom");
    //     $price_per_uom = $req->input("price_per_uom");
    //     $no_faktur = $req->no_faktur;
    //     $tax = $req->tax;


    //     if($state == "in"){
    //         $storageName = Storage::where("storageCode", $storageCode)->first()["storageName"];
    //         $vendorName = Vendor::where("vendorCode", $vendorCode)->first()["vendorName"];
    //         return $this->pdf->create_invoice_in_pdf($storageName, $vendorName, $no_sj, $no_truk, $purchase_order, $invoice_date, $no_LPB, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax);
    //     }
    //     elseif($state == "out" || $state == "out_tax"){
    //         $storageName = Storage::where("storageCode", $storageCode)->first()["storageName"];
    //         $customerName = Customer::where("customerCode", $customerCode)->first()["customerName"];
    //         return $this->pdf->create_invoice_out_pdf($storageName, $customerName, $no_sj, $customerAddress, $npwp, $invoice_date, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax);
    //     }
    //     else{
    //         return $this->pdf->create_invoice_moving_pdf($storageCodeSender, $storageCodeReceiver, $no_moving, $moving_date, $invoice_date, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax);
    //     }
    // }
}
