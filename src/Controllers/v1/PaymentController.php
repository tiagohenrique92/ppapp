<?php
namespace PPApp\Controllers\v1;

use Exception;
use PPApp\Controllers\v1\ControllerAbstract;
use PPApp\Dto\TransactionCreateDto;
use PPApp\Exceptions\Http\Request\ParamNotFoundException;
use PPApp\Exceptions\Payment\InvalidPaymentAmountException;
use PPApp\Exceptions\Payment\PayeeNotFoundException;
use PPApp\Exceptions\Payment\PayeeWalletNotFoundException;
use PPApp\Exceptions\Payment\PayerAndPayeeAreTheSamePersonException;
use PPApp\Exceptions\Payment\PayerIsBusinessUserException;
use PPApp\Exceptions\Payment\PayerNotFoundException;
use PPApp\Exceptions\Payment\PayerWalletInsufficientBalanceException;
use PPApp\Exceptions\Payment\PayerWalletNotFoundException;
use PPApp\Exceptions\Payment\PaymentExternalAuthorizationException;
use PPApp\Services\PaymentService;
use PPApp\Services\UserService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PaymentController extends ControllerAbstract
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
        $invalidParam = null;
        $payloadResponse = null;
        $data = $request->getParsedBody();

        try {
            if (!isset($data['payerUuid']) || empty($data['payerUuid'])) {
                $invalidParam = "payerUuid";
                throw new ParamNotFoundException();
            }

            if (!isset($data['payeeUuid']) || empty($data['payeeUuid'])) {
                $invalidParam = "payeeUuid";
                throw new ParamNotFoundException();
            }

            if (!isset($data['amount']) || empty($data['amount'])) {
                $invalidParam = "amount";
                throw new ParamNotFoundException();
            }

            $transactionCreateDto = new TransactionCreateDto($data['payerUuid'], $data['payeeUuid'], $data['amount']);
            $transactionCreatedDto = $this->paymentService->transfer($transactionCreateDto);

            $httpStatusCode = self::HTTP_STATUS_CODE_200;
            $payloadResponse = $transactionCreatedDto->toJson();
        } catch (PayeeNotFoundException | PayeeWalletNotFoundException $e) {
            $httpStatusCode = self::HTTP_STATUS_CODE_400;
            $payloadResponse = $this->getPayloadErrorMessage($e->getCode(), $e->getMessage(), array(
                "payeeUuid" => $data['payeeUuid'],
            ));
        } catch (PayerNotFoundException | PayerWalletNotFoundException | PayerIsBusinessUserException | PayerWalletInsufficientBalanceException $e) {
            $httpStatusCode = self::HTTP_STATUS_CODE_400;
            $payloadResponse = $this->getPayloadErrorMessage($e->getCode(), $e->getMessage(), array(
                "payerUuid" => $data['payerUuid'],
            ));
        } catch (PayerAndPayeeAreTheSamePersonException $e) {
            $httpStatusCode = self::HTTP_STATUS_CODE_400;
            $payloadResponse = $this->getPayloadErrorMessage($e->getCode(), $e->getMessage(), array(
                "payerUuid" => $data['payerUuid'],
                "payeeUuid" => $data['payeeUuid'],
            ));
        } catch (ParamNotFoundException $e) {
            $httpStatusCode = self::HTTP_STATUS_CODE_400;
            $payloadResponse = $this->getPayloadErrorMessage($e->getCode(), $e->getMessage(), array(
                "name" => $invalidParam,
            ));
        } catch (InvalidPaymentAmountException $e) {
            $httpStatusCode = self::HTTP_STATUS_CODE_400;
            $payloadResponse = $this->getPayloadErrorMessage($e->getCode(), $e->getMessage(), array(
                "amount" => $data['amount'],
            ));
        } catch (PaymentExternalAuthorizationException $e) {
            $httpStatusCode = self::HTTP_STATUS_CODE_400;
            $payloadResponse = $this->getPayloadErrorMessage($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            die('<pre>' . __FILE__ . '[' . __LINE__ . ']' . PHP_EOL . print_r($e->getMessage(), true) . '</pre>');
            $httpStatusCode = self::HTTP_STATUS_CODE_500;
            $payloadResponse = $this->getPayloadUnexpectedErrorMessage();
        }

        $response->getBody()->write($payloadResponse);

        return $response
            ->withHeader("Content-Type", "application/json")
            ->withStatus($httpStatusCode);
    }
}
