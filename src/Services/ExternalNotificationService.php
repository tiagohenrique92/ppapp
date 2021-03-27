<?php
namespace PPApp\Services;

use Exception;
use GuzzleHttp\Client;
use PPApp\Exceptions\Payment\PaymentExternalNotificationException;
use Psr\Http\Message\ResponseInterface;

class ExternalNotificationService
{
    const NOTIFICATION_SENT = "Enviado";
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * prepareDetails
     *
     * @param ResponseInterface $response
     * @return array
     */
    private function prepareDetails(ResponseInterface $response): array
    {
        $details = array(
            "response" => array(
                "code" => $response->getStatusCode(),
                "headers" => $response->getHeaders(),
                "body" => $response->getBody(),
            ),
        );
        return $details;
    }

    /**
     * send
     *
     * @param string $message
     * @param string $queue
     * @return void
     * @throws PaymentExternalNotificationException
     */
    public function send(string $message, string $queue): void
    {
        $client = new Client();

        try {
            $response = $client->get($this->url);
        } catch (Exception $e) {
            throw PaymentExternalNotificationException::create(array(
                "exception" => array(
                    "code" => $e->getCode(),
                    "message" => $e->getMessage(),
                )
            ));
        }

        if ($response->getStatusCode() !== 200) {
            throw PaymentExternalNotificationException::create($this->prepareDetails($response));
        }

        $payload = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw PaymentExternalNotificationException::create($this->prepareDetails($response));
        }

        if (!isset($payload['message']) || ($payload['message'] !== self::NOTIFICATION_SENT)) {
            throw PaymentExternalNotificationException::create($this->prepareDetails($response));
        }
    }
}
