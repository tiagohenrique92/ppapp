<?php
namespace PPApp\Services;

use GuzzleHttp\Client;
use PPApp\Exceptions\Payment\PaymentExternalAuthorizationException;

class ExternalAuthorizationService
{
    const TRANSACTION_AUTHORIZED = "Autorizado";
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * authorize
     *
     * @return void
     */
    public function authorize(): void
    {
        $client = new Client();
        $response = $client->get($this->url);

        if ($response->getStatusCode() !== 200) {
            throw PaymentExternalAuthorizationException::create();
        }
        
        $payload = json_decode($response->getBody()->getContents(), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw PaymentExternalAuthorizationException::create();
        }
        
        if (!isset($payload['message']) || ($payload['message'] !== self::TRANSACTION_AUTHORIZED)) {
            throw PaymentExternalAuthorizationException::create();
        }
    }
}
