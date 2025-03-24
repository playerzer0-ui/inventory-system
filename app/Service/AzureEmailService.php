<?php

namespace App\Service;

use App\Models\Saldo;
use App\Models\Storage;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Service\ExcelService;
use App\Service\StorageReport;

class AzureEmailService {
    private $email;
    private $resourceEndpoint;
    private $secretKey;
    private $apiVersion;
    protected $excel;
    protected $report;

    public function __construct(ExcelService $excel, StorageReport $report)
    {
        $this->email = config("mail.mailers.azure.sender");
        $this->resourceEndpoint = config("mail.mailers.azure.endpoint");
        $this->secretKey = config("mail.mailers.azure.access_key");
        $this->apiVersion = config("mail.mailers.azure.api_version");
        $this->excel = $excel;
        $this->report = $report;
    }

    function computeContentHash($content)
    {
        
        return base64_encode(hash('sha256', $content, true));
    }
    
    function computeSignature($stringToSign, $secret)
    {
        
        $decodedSecret = base64_decode($secret);
        
        return base64_encode(hash_hmac('sha256', $stringToSign, $decodedSecret, true));
    }
    
    function sendEmail($receipient, $subject, $content, $attachments = [])
    {
        $requestUri = "$this->resourceEndpoint/emails:send?api-version=$this->apiVersion";

        $attachmentsArray = [];
        foreach ($attachments as $attachment) {
            $filePath = public_path($attachment); // Assuming the file is in the public folder
            if (file_exists($filePath)) {
                $fileContent = file_get_contents($filePath);
                $base64Content = base64_encode($fileContent);
                $attachmentsArray[] = [
                    'name' => basename($attachment),
                    'contentType' => mime_content_type($filePath),
                    'contentInBase64' => $base64Content
                ];
            }
        }

        $body = [
            'headers' => [
                'ClientCorrelationId' => '123',
                'ClientCustomHeaderName' => 'ClientCustomHeaderValue'
            ],
            'senderAddress' => $this->email,
            'content' => [
                'subject' => $subject,
                'plainText' => $content,
            ],
            'recipients' => [
                'to' => [
                    [
                        'address' => $receipient
                    ]
                ],
                'cc' => [],
                'bcc' => []
            ],
            'attachments' => $attachmentsArray,
            'userEngagementTrackingDisabled' => true
        ];
    
        $serializedBody = json_encode($body);
    
        $date = gmdate('D, d M Y H:i:s T');
        $contentHash = $this->computeContentHash($serializedBody);
    
        $host = parse_url($this->resourceEndpoint, PHP_URL_HOST);
        $stringToSign = "POST\n/emails:send?api-version=$this->apiVersion\n$date;$host;$contentHash";
    
        $signature = $this->computeSignature($stringToSign, $this->secretKey);
    
        $authorizationHeader = "HMAC-SHA256 SignedHeaders=x-ms-date;host;x-ms-content-sha256&Signature=$signature";
    
        try {
            $client = new Client([
                'verify' => false  
            ]);
            $response = $client->post($requestUri, [
                'headers' => [
                    'x-ms-date' => $date,
                    'x-ms-content-sha256' => $contentHash,
                    'Authorization' => $authorizationHeader,
                    'Content-Type' => 'application/json'
                ],
                'body' => $serializedBody
            ]);
    
            echo "Response: " . $response->getBody() . "\n";
        } catch (RequestException $e) {
            echo "Request failed: " . $e->getMessage() . "\n";
        }
    }

    public function alertSuppliers($no_PO = null)
    {
        $suppliers = User::where("userType", 0)->pluck("email");
        for($i = 0; $i < count($suppliers); $i++){
            $this->sendEmail($suppliers[$i], "purchase order created: $no_PO", "a customer has made a purchase order");
        }
    }

    public function alertAdmins($state)
    {
        $admins = User::where("userType", 1)->pluck("email");
        for($i = 0; $i < count($admins); $i++){
            $this->sendEmail($admins[$i], "order $state is made", "a supplier has made an order");
        }
    }

    public function supplyLowCheck($storageCode, $date, $productCodes)
    {
        $timestamp = now()->format('YmdHis');
        $year = substr($date, 0, 4);
        $month = substr($date, 5, 2);
        $products = [];
        $data = $this->report->generateSaldo($storageCode, $month, $year);

        foreach ($productCodes as $productCode) {
            if (isset($data[$productCode])) {
                $finalBalance = $data[$productCode]['final_balance']['totalQty'];

                if ($finalBalance < 300) {
                    $products[] = [
                        'productCode' => $productCode,
                        'productName' => $data[$productCode]['productName'], // Include product name
                        'finalBalance' => $finalBalance
                    ];
                }
            }
        }

        if (!empty($products)) {
            // Create email content
            $emailContent = "Inventory Supply LOW Alert\n\n";
            $emailContent .= "The following products have a supply level below 300 units:\n";
            $emailContent .= str_repeat("=", 40) . "\n";

            foreach ($products as $product) {
                $emailContent .= "Product Code: {$product['productCode']}\n";
                $emailContent .= "Product Name: {$product['productName']}\n";
                $emailContent .= "Final Balance: {$product['finalBalance']} units\n";
                $emailContent .= str_repeat("-", 40) . "\n";
            }

            $emailContent .= "\nPlease take the necessary actions to restock the inventory.\n";
            $emailContent .= "Thank you.";

            // Send email to all suppliers
            $suppliers = User::where("userType", 0)->pluck("email");
            foreach ($suppliers as $email) {
                $this->sendEmail($email, "Supply low: " . $timestamp, $emailContent);
            }
        }
    }

    public function mailReports()
    {
        $storages = Storage::all()->pluck("storageCode");
        $admins = User::where("userType", 1)->pluck("email");
        $suppliers = User::where("userType", 0)->pluck("email");
        $month = date("m");
        $year = date("Y");
        $adminArr = [];
        $supplierArr = [];

        for($i = 0; $i < count($storages); $i++){
            $this->excel->report_stock_excel($storages[$i], $month, $year);
            $this->excel->report_stock_excel_normal($storages[$i], $month, $year);
            $this->excel->excel_debt($storages[$i], $month, $year);
            array_push($adminArr, "files/Report_stock_{$storages[$i]}_{$month}_{$year}.xlsx");
            array_push($supplierArr, "files/Report_stock_supply_{$storages[$i]}_{$month}_{$year}.xlsx");
            array_push($adminArr, "files/Debt_Report_{$storages[$i]}_{$month}_{$year}.xlsx");
        }
        
        $this->excel->excel_receivable($month, $year);
        array_push($adminArr, "files/Receivables_Report_{$month}_{$year}.xlsx");

        for($i = 0; $i < count($admins); $i++){
            $this->sendEmail($admins[$i], "reports", "you have reports of every storage, debts and receivables this month", $adminArr);
        }
        for($i = 0; $i < count($suppliers); $i++){
            $this->sendEmail($suppliers[$i], "reports for supplier", "you have reports of every storage", $supplierArr);
        }
    }
}
