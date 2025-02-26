<?php

namespace App\Service;

use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use App\Models\Storage;
use App\Models\Vendor;
use App\Models\Customer;

class PDFService
{
    function headerIn($pdf, $storageName, $vendorName, $customerName, $no_sj, $purchase_order, $customerAddress, $no_truk, $npwp, $invoice_date, $no_LPB, $no_invoice, $status){
        // PT and Vendor details
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(30, 10, 'Storage', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(80, 10, $storageName, 0, 0);
        if($status == "in"){
            $pdf->Cell(30, 10, 'Name Vendor', 0, 0);
            $pdf->Cell(5, 10, ':', 0, 0);
            $pdf->Cell(40, 10, $vendorName, 0, 1);
        }
        else{
            $pdf->Cell(30, 10, 'Name Customer', 0, 0);
            $pdf->Cell(5, 10, ':', 0, 0);
            $pdf->Cell(40, 10, $customerName, 0, 1);
        }
    
        // Second row of details
        $pdf->Cell(30, 10, 'NO. SJ', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(80, 10, $no_sj, 0, 0);
        if($status == "in"){
            $pdf->Cell(30, 10, 'Purchase Order', 0, 0);
            $pdf->Cell(5, 10, ':', 0, 0);
            $pdf->Cell(40, 10, $purchase_order, 0, 1);
        }
        else{
            $pdf->Cell(30, 10, 'Address', 0, 0);
            $pdf->Cell(5, 10, ':', 0, 0);
            $pdf->Cell(40, 10, $customerAddress, 0, 1);
        }
    
        // Third row of details
        if($status == "in"){
            $pdf->Cell(30, 10, 'Truck No', 0, 0);
            $pdf->Cell(5, 10, ':', 0, 0);
            $pdf->Cell(80, 10, $no_truk, 0, 0);
        }
        else{
            $pdf->Cell(30, 10, 'NPWP', 0, 0);
            $pdf->Cell(5, 10, ':', 0, 0);
            $pdf->Cell(80, 10, $npwp, 0, 0);
        }
        $pdf->Cell(30, 10, 'Invoice Date', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(40, 10, $invoice_date, 0, 1);
    
        // Fourth row of details
        if($status == "in"){
            $pdf->Cell(30, 10, 'NO. LPB', 0, 0);
            $pdf->Cell(5, 10, ':', 0, 0);
            $pdf->Cell(80, 10, $no_LPB, 0, 0);
        }
        else{
            $pdf->Cell(30, 10, '', 0, 0);
            $pdf->Cell(5, 10, '', 0, 0);
            $pdf->Cell(80, 10, "", 0, 0);
        }
        $pdf->Cell(30, 10, 'NO_Invoice', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(40, 10, $no_invoice, 0, 1);
    }
    
    function headerMoving($pdf, $storageCodeSender, $storageCodeReceiver, $no_moving, $moving_date, $invoice_date, $no_invoice){
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(30, 10, 'Storage Sender', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(80, 10, $storageCodeSender, 0, 0);
        $pdf->Cell(30, 10, 'Storage Receiver', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(40, 10, $storageCodeReceiver, 0, 1);
    
        // Second row of details
        $pdf->Cell(30, 10, 'no_moving', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(80, 10, $no_moving, 0, 0);
        $pdf->Cell(30, 10, 'moving_date', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(40, 10, $moving_date, 0, 1);
    
        // Third row of details
        $pdf->Cell(30, 10, 'Invoice Date', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(80, 10, $invoice_date, 0, 0);
        $pdf->Cell(30, 10, 'no_invoice', 0, 0);
        $pdf->Cell(5, 10, ':', 0, 0);
        $pdf->Cell(40, 10, $no_invoice, 0, 1);
    }
    
    function footerInvoice($pdf, $no_faktur, $total_amount, $tax, $taxPPN, $pay_amount){
        $pdf->Ln(1);
        $pdf->Cell(30, 10, 'Factor Code', 0, 0);
        $pdf->Cell(70, 10, ': ' . $no_faktur, 0, 0);
        $pdf->Cell(30, 10, 'Total Value of Goods', 0, 0);
        $pdf->Cell(30, 10, ': ' . $this->formatToIndonesianNumber($total_amount), 0, 1);
    
        $pdf->Cell(100, 10, '', 0, 0);
        $pdf->Cell(30, 10, 'Tax (%): ' . $tax, 0, 0);
        $pdf->Cell(30, 10, ': ' . $this->formatToIndonesianNumber($taxPPN), 0, 1);
    
        $pdf->Cell(100, 10, '', 0, 0);
        $pdf->Cell(30, 10, 'Required Pay Amount', 0, 0);
        $pdf->Cell(30, 10, ': ' . $this->formatToIndonesianNumber($pay_amount), 0, 1);
    }
    
    function displayProducts($pdf, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $total_amount){
        $pdf->Ln(1);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(10, 10, 'No', 1);
        $pdf->Cell(30, 10, 'Code', 1);
        $pdf->Cell(50, 10, 'Material', 1);
        $pdf->Cell(20, 10, 'QTY', 1);
        $pdf->Cell(20, 10, 'UOM', 1);
        $pdf->Cell(30, 10, 'Price/uom', 1);
        $pdf->Cell(30, 10, 'Nominal', 1);
        $pdf->Ln();
    
        $pdf->SetFont('Arial', '', 6);
        for($i = 0; $i < count($productCodes); $i++){
            $pdf->Cell(10, 7, $i + 1, 1);
            $pdf->Cell(30, 7, $productCodes[$i], 1);
            $pdf->Cell(50, 7, $productNames[$i], 1);
            $pdf->Cell(20, 7, $this->formatToIndonesianNumber($qtys[$i]), 1);
            $pdf->Cell(20, 7, $uoms[$i], 1);
            $pdf->Cell(30, 7, $this->formatToIndonesianNumber($price_per_uom[$i]), 1);
            $pdf->Cell(30, 7, $this->formatToIndonesianNumber(($qtys[$i] * $price_per_uom[$i])), 1);
            $total_amount += ($qtys[$i] * $price_per_uom[$i]);
            $pdf->Ln();
        }
    
        return $total_amount;
    }
    
    function footerPayment($pdf, $payment_date, $total_amount, $payment_amount, $tax, $taxPPN, $pay_amount){
        // Footer
        $pdf->Ln(1);
        // Left side (Payment details)
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(30, 7, 'Payment Date', 0, 0);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(45, 7, $payment_date, 0, 0);
    
        // Right side (Total, PPN, and Nilai Yg Harus Dibayar)
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(30, 7, 'Total Value of Goods', 0, 0);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(40, 7, $this->formatToIndonesianNumber($total_amount), 0, 1);
    
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(30, 7, 'Payment Amount', 0, 0);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(45, 7, $this->formatToIndonesianNumber($payment_amount), 0, 0);
    
        // Right side (Total, PPN, and Required Pay Amount)
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(30, 7, 'Tax (%): ' . $tax, 0, 0);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(40, 7, $this->formatToIndonesianNumber($taxPPN), 0, 1);
    
        $pdf->Cell(80, 7, '', 0, 0);
        $pdf->SetFont('Arial', 'B', 6);
        $pdf->Cell(30, 7, 'Required Pay Amount', 0, 0);
        $pdf->SetFont('Arial', '', 6);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(40, 7, $this->formatToIndonesianNumber($pay_amount), 0, 1);
    }
    
    function create_invoice_in_pdf($storageName, $vendorName, $no_sj, $no_truk, $purchase_order, $invoice_date,  $no_LPB, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax){
        // Create instance of Fpdf
        $total_amount = 0;
        $pdf = new Fpdf('L', 'mm', 'A5');
        $pdf->AddPage();
    
        // Set font
        $pdf->SetFont('Arial', 'B', 8);
        
        // Header
        $pdf->Cell(130, 10, 'INVOICE IN', 0, 1, 'C');
    
        $this->headerIn($pdf, $storageName, $vendorName, "", $no_sj, $purchase_order, "", $no_truk, "", $invoice_date, $no_LPB, $no_invoice, "in");
    
        // Add product table
        $total_amount = $this->displayProducts($pdf, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $total_amount);
    
        $taxPPN = $total_amount * ($tax / 100);
        $pay_amount = $total_amount + $taxPPN;
    
        // Footer
        $this->footerInvoice($pdf, $no_faktur, $total_amount, $tax, $taxPPN, $pay_amount);
    
        // Output the PDF
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
    
    function create_invoice_out_pdf($storageName, $customerName, $no_sj, $customerAddress, $npwp, $invoice_date,  $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax){
        // Create instance of Fpdf
        $total_amount = 0;
        $pdf = new Fpdf('L', 'mm', 'A5');
        $pdf->AddPage();
    
        // Set font
        $pdf->SetFont('Arial', 'B', 8);
    
        // Header
        $pdf->Cell(130, 10, 'INVOICE OUT', 0, 1, 'C');
    
        $this->headerIn($pdf, $storageName, "", $customerName, $no_sj, "", $customerAddress, "", $npwp, $invoice_date, "", $no_invoice, "out");
    
        // Add product table
        $total_amount = $this->displayProducts($pdf, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $total_amount);
    
        $taxPPN = $total_amount * ($tax / 100);
        $pay_amount = $total_amount + $taxPPN;
    
        // Footer
        $this->footerInvoice($pdf, $no_faktur, $total_amount, $tax, $taxPPN, $pay_amount);
    
        // Output the PDF
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
    
    function create_invoice_moving_pdf($storageCodeSender, $storageCodeReceiver, $no_moving, $moving_date, $invoice_date,  $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax){
        // Create instance of Fpdf
        $total_amount = 0;
        $pdf = new Fpdf('L', 'mm', 'A5');
        $pdf->AddPage();
    
        // Set font
        $pdf->SetFont('Arial', 'B', 8);
    
        // Header
        $pdf->Cell(130, 10, 'INVOICE OUT MOVING', 0, 1, 'C');
    
        // PT and Vendor details
        $this->headerMoving($pdf, $storageCodeSender, $storageCodeReceiver, $no_moving, $moving_date, $invoice_date, $no_invoice);
    
        // Add product table
        $total_amount = $this->displayProducts($pdf, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $total_amount);
    
        $taxPPN = $total_amount * ($tax / 100);
        $pay_amount = $total_amount + $taxPPN;
    
        // Footer
        $this->footerInvoice($pdf, $no_faktur, $total_amount, $tax, $taxPPN, $pay_amount);
    
        // Output the PDF
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
    
    function create_payment_in_pdf($storageName, $vendorName, $no_sj, $no_truk, $purchase_order, $invoice_date, $no_LPB, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $payment_amount, $payment_date, $tax) {
        // Create instance of Fpdf
        $total_amount = 0;
        $pdf = new Fpdf('L', 'mm', 'A5');
        $pdf->AddPage();
    
        // Set font
        $pdf->SetFont('Arial', 'B', 8);
    
        // Header
        $pdf->Cell(130, 10, 'PAYMENT IN', 0, 1, 'C');
    
        $this->headerIn($pdf, $storageName, $vendorName, "", $no_sj, $purchase_order, "", $no_truk, "", $invoice_date, $no_LPB, $no_invoice, "in");
    
        // Add product table
        $total_amount = $this->displayProducts($pdf, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $total_amount);
    
        $taxPPN = $total_amount * ($tax / 100);
        $pay_amount = $total_amount + $taxPPN;
    
        // Footer
        $this->footerPayment($pdf, $payment_date, $total_amount, $payment_amount, $tax, $taxPPN, $pay_amount);
    
        // Output the PDF
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
    
    function create_payment_moving_pdf($storageCodeSender, $storageCodeReceiver, $no_moving, $moving_date, $invoice_date,  $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $payment_amount, $payment_date, $tax) {
        // Create instance of Fpdf
        $total_amount = 0;
        $pdf = new Fpdf('L', 'mm', 'A5');
        $pdf->AddPage();
    
        // Set font
        $pdf->SetFont('Arial', 'B', 8);
    
        // Header
        $pdf->Cell(130, 10, 'PAYMENT MOVING', 0, 1, 'C');
    
        $this->headerMoving($pdf, $storageCodeSender, $storageCodeReceiver, $no_moving, $moving_date, $invoice_date, $no_invoice);
    
        // Add product table
        $total_amount = $this->displayProducts($pdf, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $total_amount);
    
        $taxPPN = $total_amount * ($tax / 100);
        $pay_amount = $total_amount + $taxPPN;
    
        // Footer
        $this->footerPayment($pdf, $payment_date, $total_amount, $payment_amount, $tax, $taxPPN, $pay_amount);
    
        // Output the PDF
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
    
    function create_payment_out_pdf($storageName, $customerName, $no_sj, $customerAddress, $npwp, $invoice_date,  $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $payment_amount, $payment_date, $tax) {
        // Create instance of Fpdf
        $total_amount = 0;
        $pdf = new Fpdf('L', 'mm', 'A5');
        $pdf->AddPage();
    
        // Set font
        $pdf->SetFont('Arial', 'B', 8);
    
        // Header
        $pdf->Cell(130, 10, 'PAYMENT OUT', 0, 1, 'C');
    
        // PT and Vendor details
        $this->headerIn($pdf, $storageName, "", $customerName, $no_sj, "", $customerAddress, "", $npwp, $invoice_date, "", $no_invoice, "out");
    
        // Add product table
        $total_amount = $this->displayProducts($pdf, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $total_amount);
    
        $taxPPN = $total_amount * ($tax / 100);
        $pay_amount = $total_amount + $taxPPN;
    
        // Footer
        $this->footerPayment($pdf, $payment_date, $total_amount, $payment_amount, $tax, $taxPPN, $pay_amount);
    
        // Output the PDF
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }
    
    function formatToIndonesianNumber($number) {
        return number_format($number, 0, ',', '.');
    }

    public function createPDF(Request $req)
    {
        $state = $req->pageState;
        $storageCode = $req->storageCode;
        $vendorCode = $req->vendorCode;
        $customerCode = $req->customerCode;
        
        $customerAddress = $req->customerAddress;
        $npwp = $req->npwp;
        $no_sj = $req->no_sj;
        $no_truk = $req->no_truk;
        $purchase_order = $req->purchase_order;
        $invoice_date = $req->invoice_date;
        $no_LPB = $req->no_LPB;
        $no_invoice = $req->no_invoice;

        $storageCodeSender = $req->storageCodeSender;
        $storageCodeReceiver = $req->storageCodeReceiver;
        $no_moving = $req->no_moving;
        $moving_date = $req->moving_date;

        $productCodes = $req->input('kd');
        $productNames = $req->input("material");
        $qtys = $req->input("qty");
        $uoms = $req->input("uom");
        $price_per_uom = $req->input("price_per_uom");
        $no_faktur = $req->no_faktur;
        $tax = $req->tax;


        if($state == "in"){
            $storageName = Storage::where("storageCode", $storageCode)->first()["storageName"];
            $vendorName = Vendor::where("vendorCode", $vendorCode)->first()["vendorName"];
            return $this->create_invoice_in_pdf($storageName, $vendorName, $no_sj, $no_truk, $purchase_order, $invoice_date, $no_LPB, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax);
        }
        elseif($state == "out" || $state == "out_tax"){
            $storageName = Storage::where("storageCode", $storageCode)->first()["storageName"];
            $customerName = Customer::where("customerCode", $customerCode)->first()["customerName"];
            return $this->create_invoice_out_pdf($storageName, $customerName, $no_sj, $customerAddress, $npwp, $invoice_date, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax);
        }
        else{
            return $this->create_invoice_moving_pdf($storageCodeSender, $storageCodeReceiver, $no_moving, $moving_date, $invoice_date, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $no_faktur, $tax);
        }
    }

    public function create_paymentPDF(Request $req)
    {
        $state = $req->pageState;
        $storageCode = $req->storageCode;
        $vendorCode = $req->vendorCode;
        $customerCode = $req->customerCode;
        
        $customerAddress = $req->customerAddress;
        $npwp = $req->npwp;
        $no_sj = $req->no_sj;
        $no_truk = $req->no_truk;
        $purchase_order = $req->purchase_order;
        $invoice_date = $req->invoice_date;
        $no_LPB = $req->no_LPB;
        $no_invoice = $req->no_invoice;

        $storageCodeSender = $req->storageCodeSender;
        $storageCodeReceiver = $req->storageCodeReceiver;
        $no_moving = $req->no_moving;
        $moving_date = $req->moving_date;

        $productCodes = $req->input('kd');
        $productNames = $req->input("material");
        $qtys = $req->input("qty");
        $uoms = $req->input("uom");
        $price_per_uom = $req->input("price_per_uom");
        $payment_amount = $req->payment_amount;
        $payment_date = $req->payment_date;
        $tax = $req->tax;

        if($state == "in"){
            $storageName = Storage::where("storageCode", $storageCode)->first()["storageName"];
            $vendorName = Vendor::where("vendorCode", $vendorCode)->first()["vendorName"];
            return $this->create_payment_in_pdf($storageName, $vendorName, $no_sj, $no_truk, $purchase_order, $invoice_date, $no_LPB, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $payment_amount, $payment_date, $tax);
        }
        elseif($state == "out" || $state == "out_tax"){
            $storageName = Storage::where("storageCode", $storageCode)->first()["storageName"];
            $customerName = Customer::where("customerCode", $customerCode)->first()["customerName"];
            return $this->create_payment_out_pdf($storageName, $customerName, $no_sj, $customerAddress, $npwp, $invoice_date, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $payment_amount, $payment_date, $tax);
        }
        else{
            return $this->create_payment_moving_pdf($storageCodeSender, $storageCodeReceiver, $no_moving, $moving_date, $invoice_date, $no_invoice, $productCodes, $productNames, $qtys, $uoms, $price_per_uom, $payment_amount, $payment_date, $tax);
        }
    }
}

?>