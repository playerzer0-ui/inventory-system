<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order_Product;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function invoice(Request $req)
    {
        $state = $req->state;
        $title = "INVOICE " . $state;
        return view("invoice", ["title" => $title, "state" => $state]);
    }

    public function create_invoice(Request $req)
    {
        $no_sj = $req->no_sj;
        $no_moving = $req->no_moving;
        $invoice_date = $req->invoice_date;
        $no_invoice = $req->no_invoice;
        $no_faktur = $req->no_faktur;
        $tax = $req->tax;

        $productCodes = $req->input('kd');
        $price_per_uom = $req->input("price_per_uom");
        $pageState = $req->pageState;

        Invoice::create([
            "nomor_surat_jalan" => $no_sj ?? "-",
            "no_moving" => $no_moving ?? "-",
            "invoice_date" => $invoice_date,
            "no_invoice" => $no_invoice,
            "no_faktur" => $no_faktur,
            "tax" => $tax
        ]);

        if($productCodes){
            for($i = 0; $i < count($productCodes); $i++){
                Order_Product::where('nomor_surat_jalan', $no_sj ?? $no_moving)
                ->where('productCode', $productCodes[$i])
                ->update(['price_per_UOM' => $price_per_uom[$i]]);
            }
        }

        session()->flash('msg', 'no_SJ: ' . $no_sj ?? $no_moving);

        return redirect()->route("payment", ["state" => $pageState]);
    }

    public function getInvoiceDetails(Request $req)
    {
        $no_moving = $req->no_moving;
        $no_sj = $req->no_sj;

        if($no_sj){
            $result = Invoice::where("nomor_surat_jalan", $no_sj)->first();
        }
        else{
            $result = Invoice::where("no_moving", $no_moving)->first();
        }
        return $result;
    }

    public function remove_invoice(Request $req)
    {
        
    }

    public function amend_invoice(Request $req)
    {
        
    }
}
