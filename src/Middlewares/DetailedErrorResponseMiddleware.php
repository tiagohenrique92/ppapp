<?php
namespace PPApp\Middlewares;

use Exception;
use PPApp\Client\Http;
use PPApp\Exceptions\DetailedExceptionInterface;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class DetailedErrorResponseMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        try {
            $response = $handler->handle($request);
        } catch (DetailedExceptionInterface $e) {
            $payload = $this->getPayloadErrorMessage($e->getCode(), $e->getMessage(), $e->getDetails());
            $response = $this->getResponse($payload, Http::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            $payload = $this->getPayloadUnexpectedErrorMessage();
            $response = $this->getResponse($payload, Http::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $response;
    }

    /**
     * getPayloadErrorMessage
     *
     * @param integer $code
     * @param string $message
     * @param array $details
     * @return string
     */
    public function getPayloadErrorMessage(int $code, string $message, array $details = null): string
    {
        $data = array(
            "error" => $code,
            "message" => $message,
        );

        if (null !== $details) {
            $data["details"] = $details;
        }

        return json_encode($data);
    }

    /**
     * getPayloadUnexpectedErrorMessage
     *
     * @return string
     */
    public function getPayloadUnexpectedErrorMessage(): string
    {
        return $this->getPayloadErrorMessage(1, "Unexpected error");
    }

    public function getResponse(string $payload, int $httpStatusCode): Response
    {
        $response = new SlimResponse();
        $response->getBody()->write($payload);
        return $response
            ->withHeader("Content-Type", "application/json")
            ->withStatus($httpStatusCode);
    }
}
