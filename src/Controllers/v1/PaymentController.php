<?php
namespace PPApp\Controllers\v1;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use PPApp\Vos\TransactionVo;
use PPApp\Services\UserService;
use PPApp\Services\PaymentService;

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
        
        try {
            if (isset($data['payer_uuid']) && !empty($data['payer_uuid'])) {
                $payer = $this->userService->getUserByUuid($data['payer_uuid']);
            } else {
                throw new \Exception("Param 'payer_uuid' not found");
            }
            
            if (isset($data['payee_uuid']) && !empty($data['payee_uuid'])) {
                $payee = $this->userService->getUserByUuid($data['payee_uuid']);
            } else {
                throw new \Exception("Param 'payee_uuid' not found");
            }
            
            if (isset($data['amount']) && !empty($data['amount'])) {
                $amount = $data['amount'];
            } else {
                throw new \Exception("Param 'amount' not found");
            }
            
            $transactionVo = new TransactionVo();
            $transactionVo->setIdPayer($payer->getId());
            $transactionVo->setIdPayee($payee->getId());
            $transactionVo->setAmount($amount);

            $this->paymentService->transfer($transactionVo);

            $httpStatusCode = 200;
        } catch (\Exception $e) {
            $payload = json_encode(array(
                "error" => $e->getMessage()
            ));
            $response->getBody()->write($payload);
            $httpStatusCode = 400;
        }

        return $response
        ->withHeader("Content-Type", "application/json")
        ->withStatus($httpStatusCode);
    }
}
