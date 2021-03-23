<?php

namespace PPApp\Services;

use DI\Container;
use PPApp\Vos\UserVo;
use PPApp\Vos\WalletVo;
use PPApp\Vos\TransactionVo;
use PPApp\Services\UserService;
use PPApp\Services\WalletService;
use PPApp\Repositories\TransactionRepository;
use PPApp\Services\ExternalAuthorizationService;

class PaymentService 
{
    /**
    * @var ExternalAuthorizationService
    */
    private $externalAuthorizationService;
    
    /**
    * @var TransactionRepository
    */
    private $transactionRepository;
    
    /**
    * @var UserService
    */
    private $userService;
    
    /**
    * @var WalletService
    */
    private $walletService;
    
    public function __construct(Container $container, TransactionRepository $transactionRepository, UserService $userService, WalletService $walletService)
    {
        $this->externalAuthorizationService = $container->get("externalAuthorizationService");
        $this->transactionRepository = $transactionRepository;
        $this->userService = $userService;
        $this->walletService = $walletService;
    }
    
    public function authorizeTransaction(TransactionVo $transactionVo)
    {
        $a = $this->externalAuthorizationService->getAuthorization();
        die('<pre>' . __FILE__ . '[' . __LINE__ . ']' . PHP_EOL . var_dump($a) . '</pre>');
    }
    
    public function getPayerByUuid(string $uuid): UserVo
    {
        $payer = $this->userService->getUserByUuid($uuid);
        
        if (null === $payer->getId()) {
            throw new \Exception("Payer not found");
        }
        
        if ($payer->getType() === UserService::USER_TYPE_BUSINESS) {
            throw new \Exception("The payer can't be a business user");
        }
        
        return $payer;
    }
    
    public function getPayeeByUuid(string $uuid): UserVo
    {
        $payee = $this->userService->getUserByUuid($uuid);
        
        if (null === $payee->getId()) {
            throw new \Exception("Payee not found");
        }
        
        return $payee;
    }
    
    public function transfer(TransactionVo $transactionVo): void
    {
        $this->validateTransaction($transactionVo);
        $this->authorizeTransaction($transactionVo);
    }
    
    protected function validateAmount(float $amount, float $balance): void
    {
        if ($amount <= 0) {
            throw new \Exception("Payment amount must be greater than zero");
        }
        
        if ($amount > $balance) {
            throw new \Exception("Insulficient balance");
        }
    }
    
    protected function validatePayer(UserVo $payer): void
    {
        if (null === $payer->getId()) {
            throw new \Exception("Payer not found");
        }
        
        if ($payer->getType() === UserService::USER_TYPE_BUSINESS) {
            throw new \Exception("The payer can't be a business user");
        }
    }
    
    protected function validatePayee(UserVo $payee, UserVo $payer): void
    {
        if (null === $payee->getId()) {
            throw new \Exception("Payee not found");
        }
        
        if ($payer->getId() === $payee->getId()) {
            throw new \Exception("The payer must be different from the payee");
        }
    }
    
    protected function validatePayeeWallet(WalletVo $walletVo)
    {
        if (null === $walletVo->getId()) {
            throw new \Exception("Payee wallet not found");
        }
    }
    protected function validatePayerWallet(WalletVo $walletVo)
    {
        if (null === $walletVo->getId()) {
            throw new \Exception("Payer wallet not found");
        }
    }
    
    protected function validateTransaction(TransactionVo $transactionVo): void
    {
        $payer = $this->userService->getUserById($transactionVo->getIdPayer());
        $this->validatePayer($payer);
        
        $payee = $this->userService->getUserById($transactionVo->getIdPayee());
        $this->validatePayee($payee, $payer);
        
        $payer_wallet = $this->walletService->getWalletByUserId($transactionVo->getIdPayer());
        $this->validatePayerWallet($payer_wallet);
        
        $payee_wallet = $this->walletService->getWalletByUserId($transactionVo->getIdPayee());
        $this->validatePayeeWallet($payee_wallet);
        
        $this->validateAmount($transactionVo->getAmount(), $payer_wallet->getBalance());
    }
}
