<?php

namespace App\Service;

use App\Service\StorageReport;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelService
{
    protected $letters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP'];
    protected $indonesianNumberFormat = '#,##0';
    protected $storageReport;

    public function __construct(StorageReport $storageReport)
    {
        $this->storageReport = $storageReport;
    }

    function report_stock_excel($storageCode, $month, $year) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $data = $this->storageReport->generateSaldo($storageCode, $month, $year);
        $count = 1;

        $spreadsheet->getProperties()->setCreator("user")
        ->setLastModifiedBy("user")
        ->setTitle("report_stock_" . $storageCode . "_" . $month . "_" . $year)
        ->setSubject("report_stock_" . $storageCode . "_" . $month . "_" . $year)
        ->setDescription("monthly report generated with storage")
        ->setKeywords("Office Excel  open XML php")
        ->setCategory("report file");

        for($i = 0; $i < 36; $i++){
            $sheet->getColumnDimension($this->letters[$i])->setAutoSize(true);
        }
        //header
        $sheet->mergeCells("A1:G1");
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1")->getFont()->setBold(5)->setSize(36);
        $sheet->setCellValue("A1", "REPORT STOCK: " . $storageCode);
        $sheet->setCellValue("A2", "MONTH: " . $month);
        $sheet->setCellValue("A3", "YEAR: " . $year);

        //Table head
        $sheet->mergeCells("A5:A7");
        $sheet->setCellValue("A5", "no.");
        $sheet->getStyle("A5")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells("B5:B7");
        $sheet->setCellValue("B5", "Code");
        $sheet->getStyle("B5")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells("C5:C7");
        $sheet->setCellValue("C5", "material");
        $sheet->getStyle("C5")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells("D5:F6");
        $sheet->setCellValue("D5", "Initial Balance");
        $sheet->getStyle("D5")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue("D7", "qty");
        $sheet->setCellValue("E7", "price/qty");
        $sheet->setCellValue("F7", "total");
        //in
        $sheet->mergeCells("G5:R5");
        $sheet->setCellValue("G5", "IN");
        $sheet->getStyle("G5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("G6:I6");
        $sheet->setCellValue("G6", "purchases");
        $sheet->getStyle("G6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("J6:L6");
        $sheet->setCellValue("J6", "movingIn");
        $sheet->getStyle("J6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("M6:O6");
        $sheet->setCellValue("M6", "repack");
        $sheet->getStyle("M6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("P6:R6");
        $sheet->setCellValue("P6", "totalIn");
        $sheet->getStyle("P6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("G7", "qty");
        $sheet->setCellValue("H7", "price/qty");
        $sheet->setCellValue("I7", "total");
        $sheet->setCellValue("J7", "qty");
        $sheet->setCellValue("K7", "price/qty");
        $sheet->setCellValue("L7", "total");
        $sheet->setCellValue("M7", "qty");
        $sheet->setCellValue("N7", "price/qty");
        $sheet->setCellValue("O7", "total");
        $sheet->setCellValue("P7", "qty");
        $sheet->setCellValue("Q7", "price/qty");
        $sheet->setCellValue("R7", "total");
        //ready to sell items
        $sheet->mergeCells("S5:U6");
        $sheet->setCellValue("S5", "Ready to sell items");
        $sheet->getStyle("S5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("S7", "qty");
        $sheet->setCellValue("T7", "price/qty");
        $sheet->setCellValue("U7", "total");
        //OUT
        $sheet->mergeCells("V5:AG5");
        $sheet->setCellValue("V5", "OUT");
        $sheet->getStyle("V5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("V6:X6");
        $sheet->setCellValue("V6", "sales");
        $sheet->getStyle("V6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("Y6:AA6");
        $sheet->setCellValue("Y6", "movingOut");
        $sheet->getStyle("Y6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("AB6:AD6");
        $sheet->setCellValue("AB6", "repack");
        $sheet->getStyle("AB6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->mergeCells("AE6:AG6");
        $sheet->setCellValue("AE6", "totalOut");
        $sheet->getStyle("AE6")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("V7", "qty");
        $sheet->setCellValue("W7", "price/qty");
        $sheet->setCellValue("X7", "total");
        $sheet->setCellValue("Y7", "qty");
        $sheet->setCellValue("Z7", "price/qty");
        $sheet->setCellValue("AA7", "total");
        $sheet->setCellValue("AB7", "qty");
        $sheet->setCellValue("AC7", "price/qty");
        $sheet->setCellValue("AD7", "total");
        $sheet->setCellValue("AE7", "qty");
        $sheet->setCellValue("AF7", "price/qty");
        $sheet->setCellValue("AG7", "total");
        //final_balance
        $sheet->mergeCells("AH5:AJ6");
        $sheet->setCellValue("AH5", "final balance");
        $sheet->getStyle("AH5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("AH7", "qty");
        $sheet->setCellValue("AI7", "price/qty");
        $sheet->setCellValue("AJ7", "total");

        $cell = 8;
        
        foreach($data as $key => $val){
            if($key == "0") continue;

            $sheet->setCellValue("A".$cell, $count++);
            $sheet->setCellValue("B".$cell, $val["productCode"]);
            $sheet->setCellValue("C".$cell, $val["productName"]);
            $sheet->setCellValue("D".$cell, $val["initial_balance"]["totalQty"]);
            $sheet->setCellValue("E".$cell, $val["initial_balance"]["price_per_qty"]);
            $sheet->setCellValue("F".$cell, $val["initial_balance"]["totalPrice"]);

            $sheet->setCellValue("G".$cell, $val["in"]["purchase"]["totalQty"]);
            $sheet->setCellValue("H".$cell, $val["in"]["purchase"]["price_per_qty"]);
            $sheet->setCellValue("I".$cell, $val["in"]["purchase"]["totalPrice"]);
            $sheet->setCellValue("J".$cell, $val["in"]["movingIn"]["totalQty"]);
            $sheet->setCellValue("K".$cell, $val["in"]["movingIn"]["price_per_qty"]);
            $sheet->setCellValue("L".$cell, $val["in"]["movingIn"]["totalPrice"]);
            $sheet->setCellValue("M".$cell, $val["in"]["repackIn"]["totalQty"]);
            $sheet->setCellValue("N".$cell, $val["in"]["repackIn"]["price_per_qty"]);
            $sheet->setCellValue("O".$cell, $val["in"]["repackIn"]["totalPrice"]);
            $sheet->setCellValue("P".$cell, $val["in"]["totalIn"]["totalQty"]);
            $sheet->setCellValue("Q".$cell, $val["in"]["totalIn"]["price_per_qty"]);
            $sheet->setCellValue("R".$cell, $val["in"]["totalIn"]["totalPrice"]);

            $sheet->setCellValue("S".$cell, $val["ready_to_sell_items"]["totalQty"]);
            $sheet->setCellValue("T".$cell, $val["ready_to_sell_items"]["price_per_qty"]);
            $sheet->setCellValue("U".$cell, $val["ready_to_sell_items"]["totalPrice"]);

            $sheet->setCellValue("V".$cell, $val["out"]["sales"]["totalQty"]);
            $sheet->setCellValue("W".$cell, $val["out"]["sales"]["price_per_qty"]);
            $sheet->setCellValue("X".$cell, $val["out"]["sales"]["totalPrice"]);
            $sheet->setCellValue("Y".$cell, $val["out"]["movingOut"]["totalQty"]);
            $sheet->setCellValue("Z".$cell, $val["out"]["movingOut"]["price_per_qty"]);
            $sheet->setCellValue("AA".$cell, $val["out"]["movingOut"]["totalPrice"]);
            $sheet->setCellValue("AB".$cell, $val["out"]["repackOut"]["totalQty"]);
            $sheet->setCellValue("AC".$cell, $val["out"]["repackOut"]["price_per_qty"]);
            $sheet->setCellValue("AD".$cell, $val["out"]["repackOut"]["totalPrice"]);
            $sheet->setCellValue("AE".$cell, $val["out"]["totalOut"]["totalQty"]);
            $sheet->setCellValue("AF".$cell, $val["out"]["totalOut"]["price_per_qty"]);
            $sheet->setCellValue("AG".$cell, $val["out"]["totalOut"]["totalPrice"]);

            $sheet->setCellValue("AH".$cell, $val["final_balance"]["totalQty"]);
            $sheet->setCellValue("AI".$cell, $val["final_balance"]["price_per_qty"]);
            $sheet->setCellValue("AJ".$cell, $val["final_balance"]["totalPrice"]);

            $cell++;
        }

        $sheet->getStyle("D8:AJ" . ($cell - 1))->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);

        //$filePath = "../files/report_stock_" . $storageCode . "_" . $month . "_" . $year . ".xlsx";
        $filePath = public_path("report_stock_{$storageCode}-{$month}-{$year}.xlsx");
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Clear output buffer
        ob_end_clean();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check, pre-check');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        header('Expires: 0');

        // Read file and send to client
        readfile($filePath);

        // Delete the file after sending it to the client
        unlink($filePath);

    }

    function report_stock_excel_normal($storageCode, $month, $year) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $data = $this->storageReport->generateSaldo($storageCode, $month, $year);
        $count = 1;

        $spreadsheet->getProperties()->setCreator("user")
        ->setLastModifiedBy("user")
        ->setTitle("report_stock_" . $storageCode . "_" . $month . "_" . $year)
        ->setSubject("report_stock_" . $storageCode . "_" . $month . "_" . $year)
        ->setDescription("monthly report generated with storage")
        ->setKeywords("Office Excel  open XML php")
        ->setCategory("report file");

        for($i = 0; $i < 16; $i++){
            $sheet->getColumnDimension($this->letters[$i])->setAutoSize(true);
        }
        //header
        $sheet->mergeCells("A1:G1");
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1")->getFont()->setBold(5)->setSize(36);
        $sheet->setCellValue("A1", "REPORT STOCK: " . $storageCode);
        $sheet->setCellValue("A2", "MONTH: " . $month);
        $sheet->setCellValue("A3", "YEAR: " . $year);

        //Table head
        $sheet->mergeCells("A5:A7");
        $sheet->setCellValue("A5", "no.");
        $sheet->getStyle("A5")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells("B5:B7");
        $sheet->setCellValue("B5", "KD");
        $sheet->getStyle("B5")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells("C5:C7");
        $sheet->setCellValue("C5", "material");
        $sheet->getStyle("C5")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $sheet->mergeCells("D5:D6");
        $sheet->setCellValue("D5", "saldo awal");
        $sheet->getStyle("D5")->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
        $sheet->setCellValue("D7", "qty");

        //in
        $sheet->mergeCells("E5:H5");
        $sheet->setCellValue("E5", "in");
        $sheet->getStyle("E5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("E6", "purchase");
        $sheet->setCellValue("F6", "MovingIn");
        $sheet->setCellValue("G6", "repack");
        $sheet->setCellValue("H6", "totalIn");
        $sheet->setCellValue("E7", "qty");
        $sheet->setCellValue("F7", "qty");
        $sheet->setCellValue("G7", "qty");
        $sheet->setCellValue("H7", "qty");

        //ready_to_sell_items
        $sheet->mergeCells("I5:I6");
        $sheet->setCellValue("I5", "ready to sell items");
        $sheet->setCellValue("I7", "qty");

        //out
        $sheet->mergeCells("J5:M5");
        $sheet->setCellValue("J5", "out");
        $sheet->getStyle("J5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue("J6", "sales");
        $sheet->setCellValue("K6", "movingOut");
        $sheet->setCellValue("L6", "repack");
        $sheet->setCellValue("M6", "totalOut");
        $sheet->setCellValue("J7", "qty");
        $sheet->setCellValue("K7", "qty");
        $sheet->setCellValue("L7", "qty");
        $sheet->setCellValue("M7", "qty");

        //final_balance
        $sheet->mergeCells("N5:N6");
        $sheet->setCellValue("N5", "final balance");
        $sheet->setCellValue("N7", "qty");

        $cell = 8;
        
        foreach($data as $key => $val){
            if($key == "0") continue;

            $sheet->setCellValue("A".$cell, $count++);
            $sheet->setCellValue("B".$cell, $val["productCode"]);
            $sheet->setCellValue("C".$cell, $val["productName"]);
            $sheet->setCellValue("D".$cell, $val["initial_balance"]["totalQty"]);

            $sheet->setCellValue("E".$cell, $val["in"]["purchase"]["totalQty"]);
            $sheet->setCellValue("F".$cell, $val["in"]["movingIn"]["totalQty"]);
            $sheet->setCellValue("G".$cell, $val["in"]["repackIn"]["totalQty"]);
            $sheet->setCellValue("H".$cell, $val["in"]["totalIn"]["totalQty"]);

            $sheet->setCellValue("I".$cell, $val["ready_to_sell_items"]["totalQty"]);

            $sheet->setCellValue("J".$cell, $val["out"]["sales"]["totalQty"]);
            $sheet->setCellValue("K".$cell, $val["out"]["movingOut"]["totalQty"]);
            $sheet->setCellValue("L".$cell, $val["out"]["repackOut"]["totalQty"]);
            $sheet->setCellValue("M".$cell, $val["out"]["totalOut"]["totalQty"]);

            $sheet->setCellValue("N".$cell, $val["final_balance"]["totalQty"]);


            $cell++;
        }

        $sheet->getStyle("D8:N" . ($cell - 1))->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);


        //$filePath = "../files/report_stock_" . $storageCode . "_" . $month . "_" . $year . ".xlsx";
        $filePath = public_path("report_stock_{$storageCode}-{$month}-{$year}.xlsx");
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Clear output buffer
        ob_end_clean();

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check, pre-check');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        header('Expires: 0');

        // Read file and send to client
        readfile($filePath);

        // Delete the file after sending it to the client
        unlink($filePath);

    }

    function excel_debt_receivable($storageCode, $month, $year, $mode) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        if($mode == "debt"){
            $data = $this->storageReport->getDebtReport($storageCode, $month, $year);
        }
        else{
            $data = $this->storageReport->getreceivablesReport($month, $year);
        }

        $totalQty = 0;
        $totalNominal = 0;
        $totalNominalAfterTax = 0;
        $totalNilaiBayar = 0;
        $totalSisaHutang = 0;

        $spreadsheet->getProperties()->setCreator("user")
        ->setLastModifiedBy("user")
        ->setTitle("report_" . $storageCode . "_" . $month . "_" . $year)
        ->setSubject("report_" . $storageCode . "_" . $month . "_" . $year)
        ->setDescription("monthly report generated")
        ->setKeywords("Office Excel open XML php")
        ->setCategory("report file");

        for($i = 0; $i < 14; $i++){
            $sheet->getColumnDimension($this->letters[$i])->setAutoSize(true);
        }

        $sheet->mergeCells("A1:G1");
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1")->getFont()->setBold(5)->setSize(36);
        if($mode == "debt"){
            $sheet->setCellValue("A1", "REPORT DEBT: " . $storageCode);
        }
        else{
            $sheet->setCellValue("A1", "REPORT RECEIVABLE: " . $storageCode);
        }
        $sheet->setCellValue("A2", "MONTH: " . $month);
        $sheet->setCellValue("A3", "YEAR: " . $year);

        $sheet->setCellValue("A5", "No.");
        $sheet->setCellValue("B5", "Invoice Date");
        if($mode == "debt"){
            $sheet->setCellValue("C5", "Vendor Name");
        }
        else{
            $sheet->setCellValue("C5", "Customer Name");
        }
        $sheet->setCellValue("D5", "No Invoice");
        $sheet->setCellValue("E5", "Material");
        $sheet->setCellValue("F5", "QTY");
        $sheet->setCellValue("G5", "Price/UOM");
        $sheet->setCellValue("H5", "Nominal");
        $sheet->setCellValue("I5", "Total Nominal");
        $sheet->setCellValue("J5", "Tax (%)");
        $sheet->setCellValue("K5", "Nominal After Tax");
        $sheet->setCellValue("L5", "Payment Date");
        $sheet->setCellValue("M5", "Amount Paid");
        $sheet->setCellValue("N5", "Remaining");

        $rowNumber = 6; // Starting row for data
        $index = 1;
        $previousNo = ''; // Track previous 'No.' value

        foreach($data as $invoice){
            $productCount = count($invoice['products']);
            $paymentCount = count($invoice['payments']);
            $rowCount = max($productCount, $paymentCount);
            $firstRow = true;

            $invoiceTotalNominal = array_reduce($invoice['products'], function($sum, $product) {
                return $sum + (float)$product['nominal'];
            }, 0);
            $tax = (float)$invoice['tax'];
            $nominalAfterTax = $invoiceTotalNominal + ($invoiceTotalNominal * ($tax / 100));
            $invoiceTotalPayment = array_reduce($invoice['payments'], function($sum, $payment) {
                return $sum + (float)$payment['payment_amount'];
            }, 0);
            $invoiceRemaining = $nominalAfterTax - $invoiceTotalPayment;

            // Update totals
            $totalQty += array_sum(array_column($invoice['products'], 'qty'));
            $totalNominal += $invoiceTotalNominal;
            $totalNominalAfterTax += $nominalAfterTax;
            $totalNilaiBayar += $invoiceTotalPayment;
            $totalSisaHutang += $invoiceRemaining;

            for ($i = 0; $i < $rowCount; $i++) {
                // Handle 'No.' merging
                if ($previousNo === $index) {
                    $sheet->mergeCells("A" . ($rowNumber - 1) . ":A{$rowNumber}");
                } else {
                    $sheet->setCellValue("A{$rowNumber}", $index);
                    $previousNo = $index;
                }

                if ($firstRow) {
                    $sheet->mergeCells("B{$rowNumber}:B" . ($rowNumber + $rowCount - 1));
                    $sheet->mergeCells("C{$rowNumber}:C" . ($rowNumber + $rowCount - 1));
                    $sheet->mergeCells("D{$rowNumber}:D" . ($rowNumber + $rowCount - 1));
                    $sheet->mergeCells("I{$rowNumber}:I" . ($rowNumber + $rowCount - 1));
                    $sheet->getStyle("I{$rowNumber}:I" . ($rowNumber + $rowCount - 1))->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
                    $sheet->mergeCells("J{$rowNumber}:J" . ($rowNumber + $rowCount - 1));
                    $sheet->mergeCells("K{$rowNumber}:K" . ($rowNumber + $rowCount - 1));
                    $sheet->getStyle("K{$rowNumber}:K" . ($rowNumber + $rowCount - 1))->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
                    $sheet->mergeCells("N{$rowNumber}:N" . ($rowNumber + $rowCount - 1));
                    $sheet->getStyle("N{$rowNumber}:N" . ($rowNumber + $rowCount - 1))->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
                    $sheet->setCellValue("B{$rowNumber}", $invoice['invoice_date']);
                    if($mode == "debt"){
                        $sheet->setCellValue("C{$rowNumber}", $invoice['vendorName']);
                    }
                    else{
                        $sheet->setCellValue("C{$rowNumber}", $invoice['customerName']);
                    }
                    $sheet->setCellValue("D{$rowNumber}", $invoice['no_invoice']);
                    $sheet->setCellValue("I{$rowNumber}", $invoiceTotalNominal);
                    $sheet->setCellValue("J{$rowNumber}", $tax);
                    $sheet->setCellValue("K{$rowNumber}", $nominalAfterTax);
                    $sheet->setCellValue("N{$rowNumber}", $invoiceRemaining);
                }

                if ($i < $productCount) {
                    $product = $invoice['products'][$i];
                    $sheet->setCellValue("E{$rowNumber}", $product['productCode']);
                    $sheet->setCellValue("F{$rowNumber}", $product['qty']);
                    $sheet->setCellValue("G{$rowNumber}", $product['price_per_UOM']);
                    $sheet->setCellValue("H{$rowNumber}", $product['nominal']);
                }

                if ($i < $paymentCount) {
                    $payment = $invoice['payments'][$i];
                    $sheet->setCellValue("L{$rowNumber}", $payment['payment_date']);
                    $sheet->setCellValue("M{$rowNumber}", $payment['payment_amount']);
                    $sheet->getStyle("M{$rowNumber}")->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
                }

                $rowNumber++;
                $firstRow = false;
            }

            $index++;
        }

        // Adding Totals Row
        $sheet->setCellValue("E{$rowNumber}", "Total");
        $sheet->setCellValue("F{$rowNumber}", $totalQty);
        $sheet->setCellValue("H{$rowNumber}", $totalNominal);
        $sheet->setCellValue("K{$rowNumber}", $totalNominalAfterTax);
        $sheet->setCellValue("M{$rowNumber}", $totalNilaiBayar);
        $sheet->setCellValue("N{$rowNumber}", $totalSisaHutang);

        // Optionally format the totals row (e.g., bold font)
        $sheet->getStyle("E{$rowNumber}:N{$rowNumber}")->getFont()->setBold(true);

        $sheet->getStyle("F6:F{$rowNumber}")->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
        $sheet->getStyle("G6:G{$rowNumber}")->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
        $sheet->getStyle("H6:H{$rowNumber}")->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
        $sheet->getStyle("I{$rowNumber}")->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
        $sheet->getStyle("K{$rowNumber}")->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
        $sheet->getStyle("M{$rowNumber}")->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);
        $sheet->getStyle("N{$rowNumber}")->getNumberFormat()->setFormatCode($this->indonesianNumberFormat);

        if ($mode == "debt") {
            $filePath = public_path("report_debt_{$storageCode}_{$month}_{$year}.xlsx");
        } else {
            $filePath = public_path("report_receivable_{$month}_{$year}.xlsx");
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Expires: 0');
        readfile($filePath);
        unlink($filePath);
    }
}

?>