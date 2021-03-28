<?php
namespace PPApp\Controllers\v1;

use PPApp\Client\Http;
use PPApp\Dto\TransactionCreateDto;
use PPApp\Exceptions\Http\Request\ParamNotFoundException;
use PPApp\Services\PaymentService;
use PPApp\Services\UserService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PaymentController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var PaymentService
     */
    protected $paymentService;

    public function __construct(UserService $userService, PaymentService $paymentService)
    {
        $this->userService = $userService;
        $this->paymentService = $paymentService;
    }

    public function transfer(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        if (!isset($data['payerUuid']) || empty($data['payerUuid'])) {
            throw ParamNotFoundException::create(array("name" => "payerUuid"));
        }

        if (!isset($data['payeeUuid']) || empty($data['payeeUuid'])) {
            throw ParamNotFoundException::create(array("name" => "payeeUuid"));
        }

        if (!isset($data['amount']) || empty($data['amount'])) {
            throw ParamNotFoundException::create(array("name" => "amount"));
        }

        $transactionCreateDto = new TransactionCreateDto($data['payerUuid'], $data['payeeUuid'], $data['amount']);
        $transactionCreatedDto = $this->paymentService->transfer($transactionCreateDto);

        $payloadResponse = $transactionCreatedDto->toJson();
        $response->getBody()->write($payloadResponse);

        return $response
            ->withHeader("Content-Type", "application/json")
            ->withStatus(Http::HTTP_OK);
    }
}
