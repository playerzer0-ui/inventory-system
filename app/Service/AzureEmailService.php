<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class AzureEmailService {
    private $email;
    private $resourceEndpoint;
    private $secretKey;
    private $apiVersion;

    public function __construct()
    {
        $this->email = config("mail.mailers.azure.sender");
        $this->resourceEndpoint = config("mail.mailers.azure.endpoint");
        $this->secretKey = config("mail.mailers.azure.access_key");
        $this->apiVersion = config("mail.mailers.azure.api_version");
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
}
