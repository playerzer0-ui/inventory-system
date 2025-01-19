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
        $invoice_date = $req->invoice_date;
        $no_invoice = $req->no_invoice;
        $no_faktur = $req->no_faktur;
        $no_moving = $req->no_moving;
        $tax = $req->tax;

        $productCodes = $req->input('kd');
        $price_per_uom = $req->input("price_per_uom");
        $pageState = $req->pageState;

        if($pageState == "moving"){
            Invoice::create([
                "nomor_surat_jalan" => "-",
                "invoice_date" => $invoice_date,
                "no_invoice" => $no_invoice,
                "no_faktur" => $no_faktur,
                "no_moving" => $no_moving,
                "tax" => $tax
            ]);

            for($i = 0; $i < count($productCodes); $i++){
                Order_Product::where('no_moving', $no_moving)
                ->where('productCode', $productCodes[$i])
                ->update(['price_per_UOM' => $price_per_uom[$i]]);
            }
        }
        else{
            Invoice::create([
                "nomor_surat_jalan" => $no_sj,
                "invoice_date" => $invoice_date,
                "no_invoice" => $no_invoice,
                "no_faktur" => $no_faktur,
                "no_moving" => "-",
                "tax" => $tax
            ]);

            for($i = 0; $i < count($productCodes); $i++){
                Order_Product::where('nomor_surat_jalan', $no_sj)
                ->where('productCode', $productCodes[$i])
                ->update(['price_per_UOM' => $price_per_uom[$i]]);
            }
        }

        session()->flash('msg', 'no_SJ: ' . $no_sj);

        return redirect()->route("payment", ["state" => $pageState]);
    }

    public function remove_invoice(Request $req)
    {
        
    }

    public function amend_invoice(Request $req)
    {
        
    }
}
