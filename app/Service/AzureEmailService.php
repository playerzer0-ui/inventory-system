<?php 

use GuzzleHttp\Client;

class AzureEmailService
{
    private $endpoint;
    private $accessKey;

    public function __construct()
    {
        $this->endpoint = config("mail.mailers.azure.endpoint");
        $this->accessKey = config("mail.mailers.azure.access_key");
    }

    /**
     * sends an email with the help of azure
     */
    public function sendEmail($sender, $recipient, $subject, $content, $attachments = [])
    {
        $timestamp = gmdate('D, d M Y H:i:s T');
        $client = new Client();

        $contentHash = base64_encode(hash('sha256', $content, true));

        // Create the string to sign and generate HMAC signature
        $stringToSign = "POST\n" . "/emails\n" . $timestamp . ";" . parse_url($this->endpoint, PHP_URL_HOST) . ";" . $contentHash;
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($this->accessKey), true));

        // Prepare the attachment payload
        $encodedAttachments = [];
        foreach ($attachments as $attachment) {
            $encodedAttachments[] = [
                'name' => $attachment['name'], // e.g., "file.pdf"
                'contentType' => $attachment['type'], // e.g., "application/pdf"
                'contentBytesBase64' => base64_encode(file_get_contents($attachment['path'])),
            ];
        }

        // Build the email payload
        $payload = [
            'senderAddress' => $sender,
            'content' => [
                'subject' => $subject,
                'plainText' => $content,
            ],
            'recipients' => [
                'to' => [
                    ['address' => $recipient],
                ],
            ],
        ];

        if (!empty($encodedAttachments)) {
            $payload['attachments'] = $encodedAttachments;
        }

        // Send the email
        $response = $client->post($this->endpoint . '/emails', [
            'headers' => [
                'x-ms-date' => $timestamp,
                'x-ms-content-sha256' => $contentHash,
                'Authorization' => "HMAC-SHA256 SignedHeaders=x-ms-date;host;x-ms-content-sha256&Signature=$signature",
                'Content-Type' => 'application/json',
            ],
            'json' => $payload,
        ]);

        return $response->getBody()->getContents();
    }
}

?>