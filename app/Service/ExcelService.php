<?php

namespace App\Service;

use App\Service\StorageReport;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill as StyleFill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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

        // Define the file path
        $fileName = "Report_stock_{$storageCode}_{$month}_{$year}.xlsx";
        $filePath = public_path("files/{$fileName}");
    
        // Ensure the directory exists
        if (!file_exists(public_path('files'))) {
            mkdir(public_path('files'), 0777, true);
        }
    
        // Save the file to the public/files folder
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
    
        // Stream the file for download
        return response()->download($filePath);
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


        // Define the file path
        $fileName = "Report_stock_supply_{$storageCode}_{$month}_{$year}.xlsx";
        $filePath = public_path("files/{$fileName}");
    
        // Ensure the directory exists
        if (!file_exists(public_path('files'))) {
            mkdir(public_path('files'), 0777, true);
        }
    
        // Save the file to the public/files folder
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
    
        // Stream the file for download
        return response()->download($filePath);
    }

    function excel_debt($storageCode, $month, $year) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Set headers
        $sheet->setCellValue('A1', 'REPORT DEBT')->mergeCells('A1:N1');
        $sheet->setCellValue('A2', 'STORAGE CODE:')->setCellValue('B2', $storageCode);
        $sheet->setCellValue('A3', 'MONTH:')->setCellValue('B3', str_pad($month, 2, '0', STR_PAD_LEFT));
        $sheet->setCellValue('A4', 'YEAR:')->setCellValue('B4', $year);
    
        // Column headers
        $headers = [
            'No.', 'Invoice Date', 'Vendor Name', 'No Invoice', 'Material', 'QTY', 'Price/UOM',
            'Nominal', 'Total Nominal', 'Tax (%)', 'Nominal After Tax', 'Payment Date', 'Amount Paid', 'Remaining'
        ];
        $sheet->fromArray($headers, null, 'A6');
    
        // Apply styles to header
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => StyleFill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']]
        ];
        $sheet->getStyle('A6:N6')->applyFromArray($headerStyle);
    
        // Fetch data
        $data = $this->storageReport->getDebtReport($storageCode, $month, $year);
        $row = 7;
        $no = 1;
    
        foreach ($data as $item) {
            $firstRow = $row;
            $totalNominal = 0;
            $tax = $item['tax'] ?? 0;
    
            foreach ($item['products'] as $product) {
                $nominal = $product['qty'] * $product['price_per_UOM'];
                $totalNominal += $nominal;
    
                $sheet->fromArray([
                    $no,
                    $item['invoice_date'],
                    $item['vendorName'],
                    $item['no_invoice'],
                    $product['productCode'],
                    $product['qty'],
                    $product['price_per_UOM'],
                    $nominal,
                    '', // Total Nominal (To be set later)
                    $tax,
                    '', // Nominal After Tax (To be set later)
                    '', // Payment Date (To be set later)
                    '', // Amount Paid (To be set later)
                    ''  // Remaining (To be set later)
                ], null, "A{$row}");
    
                $row++;
            }
    
            // Merge 'No.' column for multiple rows of the same invoice
            if ($firstRow !== $row - 1) {
                $sheet->mergeCells("A{$firstRow}:A" . ($row - 1));
            }
    
            // Calculate totals
            $nominalAfterTax = $totalNominal + ($totalNominal * $tax / 100);
            $totalPayments = array_sum(array_column($item['payments'], 'payment_amount'));
            $remaining = $nominalAfterTax - $totalPayments;
    
            // Fill in Total Nominal, Nominal After Tax, Payment Date, Amount Paid, Remaining
            $sheet->setCellValue("I{$firstRow}", $totalNominal);
            $sheet->setCellValue("K{$firstRow}", $nominalAfterTax);
            $sheet->setCellValue("L{$firstRow}", implode(', ', array_column($item['payments'], 'payment_date')));
            $sheet->setCellValue("M{$firstRow}", $totalPayments);
            $sheet->setCellValue("N{$firstRow}", $remaining);
    
            $no++;
        }
    
        // Apply number formatting for currency
        $currencyColumns = ['G', 'H', 'I', 'K', 'M', 'N'];
        foreach ($currencyColumns as $col) {
            $sheet->getStyle($col . '7:' . $col . $row)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }
    
        // Auto-size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        // Define the file path
        $fileName = "Debt_Report_{$storageCode}_{$month}_{$year}.xlsx";
        $filePath = public_path("files/{$fileName}");
    
        // Ensure the directory exists
        if (!file_exists(public_path('files'))) {
            mkdir(public_path('files'), 0777, true);
        }
    
        // Save the file to the public/files folder
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
    
        // Stream the file for download
        return response()->download($filePath);
    }

    public function excel_receivable($month, $year) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Set headers
        $sheet->setCellValue('A1', 'REPORT RECEIVABLES')->mergeCells('A1:N1');
        $sheet->setCellValue('A3', 'MONTH:')->setCellValue('B3', str_pad($month, 2, '0', STR_PAD_LEFT));
        $sheet->setCellValue('A4', 'YEAR:')->setCellValue('B4', $year);
    
        // Column headers
        $headers = [
            'No.', 'Invoice Date', 'Customer Name', 'No Invoice', 'Material', 'QTY', 'Price/UOM',
            'Nominal', 'Total Nominal', 'Tax (%)', 'Nominal After Tax', 'Payment Date', 'Payment Amount', 'Remaining'
        ];
        $sheet->fromArray($headers, null, 'A6');
    
        // Apply styles to header
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => ['fillType' => StyleFill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']]
        ];
        $sheet->getStyle('A6:N6')->applyFromArray($headerStyle);
    
        // Fetch data
        $data = $this->storageReport->getreceivablesReport($month, $year);
        $row = 7;
        $no = 1;
    
        foreach ($data as $entry) {
            $firstRow = $row;
            $totalNominal = 0;
            $tax = $entry['tax'] ?? 0;
    
            foreach ($entry['products'] as $product) {
                $nominal = $product['qty'] * $product['price_per_UOM'];
                $totalNominal += $nominal;
    
                $sheet->fromArray([
                    $no,
                    $entry['invoice_date'],
                    $entry['customerName'],
                    $entry['no_invoice'],
                    $product['productCode'],
                    $product['qty'],
                    $product['price_per_UOM'],
                    $nominal,
                    '', // Total Nominal (To be set later)
                    $tax,
                    '', // Nominal After Tax (To be set later)
                    '', // Payment Date (To be set later)
                    '', // Payment Amount (To be set later)
                    ''  // Remaining (To be set later)
                ], null, "A{$row}");
    
                $row++;
            }
    
            // Merge 'No.' column for multiple rows of the same invoice
            if ($firstRow !== $row - 1) {
                $sheet->mergeCells("A{$firstRow}:A" . ($row - 1));
                $sheet->mergeCells("B{$firstRow}:B" . ($row - 1));
                $sheet->mergeCells("C{$firstRow}:C" . ($row - 1));
                $sheet->mergeCells("D{$firstRow}:D" . ($row - 1));
            }
    
            // Calculate totals
            $nominalAfterTax = $totalNominal + ($totalNominal * $tax / 100);
            $totalPayments = array_sum(array_column($entry['payments'], 'payment_amount'));
            $remaining = $nominalAfterTax - $totalPayments;
    
            // Fill in Total Nominal, Nominal After Tax, Payment Date, Payment Amount, Remaining
            $sheet->setCellValue("I{$firstRow}", $totalNominal);
            $sheet->setCellValue("K{$firstRow}", $nominalAfterTax);
            $sheet->setCellValue("L{$firstRow}", implode(', ', array_column($entry['payments'], 'payment_date')));
            $sheet->setCellValue("M{$firstRow}", $totalPayments);
            $sheet->setCellValue("N{$firstRow}", $remaining);
    
            $no++;
        }
    
        // Apply number formatting for currency
        $currencyColumns = ['G', 'H', 'I', 'K', 'M', 'N'];
        foreach ($currencyColumns as $col) {
            $sheet->getStyle($col . '7:' . $col . $row)
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }
    
        // Auto-size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        // Define the file path
        $fileName = "Receivables_Report_{$month}_{$year}.xlsx";
        $filePath = public_path("files/{$fileName}");
    
        // Ensure the directory exists
        if (!file_exists(public_path('files'))) {
            mkdir(public_path('files'), 0777, true);
        }
    
        // Save the file to the public/files folder
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
    
        // Stream the file for download
        return response()->download($filePath);
    }
}

?>