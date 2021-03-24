<?php
namespace PPApp\Services;

use DI\Container;
use PPApp\Dto\TransactionCreatedDto;
use PPApp\Dto\TransactionCreateDto;
use PPApp\Dto\UserDto;
use PPApp\Exceptions\Payment\InvalidPaymentAmountException;
use PPApp\Exceptions\Payment\PayeeNotFoundException;
use PPApp\Exceptions\Payment\PayeeWalletNotFoundException;
use PPApp\Exceptions\Payment\PayerAndPayeeAreTheSamePersonException;
use PPApp\Exceptions\Payment\PayerIsBusinessUserException;
use PPApp\Exceptions\Payment\PayerNotFoundException;
use PPApp\Exceptions\Payment\PayerWalletInsufficientBalanceException;
use PPApp\Exceptions\Payment\PayerWalletNotFoundException;
use PPApp\Exceptions\User\UserNotFoundException;
use PPApp\Exceptions\User\UserWalletNotFoundException;
use PPApp\Repositories\TransactionRepository;
use PPApp\Services\ExternalAuthorizationService;
use PPApp\Services\UserService;
use PPApp\Services\WalletService;

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

    public function authorizeTransaction()
    {
        $this->externalAuthorizationService->authorize();
    }

    /**
     * transfer
     *
     * @param TransactionCreateDto $transactionCreateDto
     * @return TransactionCreatedDto
     */
    public function transfer(TransactionCreateDto $transactionCreateDto): TransactionCreatedDto
    {
        $this->validateTransaction($transactionCreateDto);
        $this->authorizeTransaction();
        $this->transactionRepository->create($transactionCreatedDto);
        // implementar insert no banco
        // implementar notificacao
        $uuid = "uuid-transaction";
        $transactionCreatedDto = new TransactionCreatedDto($uuid);
        return $transactionCreatedDto;
    }

    /**
     * validateAmount
     *
     * @param float $amount
     * @param float $balance
     * @return void
     * @throws InvalidPaymentAmountException
     * @throws PayerWalletInsufficientBalanceException
     */
    protected function validateAmount(float $amount, float $balance): void
    {
        if ($amount <= 0) {
            throw new InvalidPaymentAmountException();
        }

        if ($amount > $balance) {
            throw new PayerWalletInsufficientBalanceException();
        }
    }

    /**
     * payerUuid
     *
     * @param string $payerUuid
     * @return void
     * @throws PayerNotFoundException
     * @throws PayerIsBusinessUserException
     */
    protected function validatePayer(string $payerUuid): void
    {
        try {
            $payer = $this->userService->getUserByUuid($payerUuid);
        } catch (UserNotFoundException $e) {
            throw new PayerNotFoundException();
        }

        if ($payer->getType() === UserService::USER_TYPE_BUSINESS) {
            throw new PayerIsBusinessUserException();
        }
    }

    /**
     * validatePayee
     *
     * @param string $payeeUuid
     * @param string $payerUuid
     * @return void
     * @throws PayeeNotFoundException
     * @throws PayerAndPayeeAreTheSamePersonException
     */
    protected function validatePayee(string $payeeUuid, string $payerUuid): void
    {
        try {
            $payee = $this->userService->getUserByUuid($payeeUuid);
        } catch (UserNotFoundException $e) {
            throw new PayeeNotFoundException();
        }

        if ($payeeUuid === $payerUuid) {
            throw new PayerAndPayeeAreTheSamePersonException();
        }
    }

    /**
     * validatePayeeWallet
     *
     * @param integer $payeeId
     * @return void
     * @throws PayeeWalletNotFoundException
     */
    protected function validatePayeeWallet(int $payeeId)
    {
        try {
            $walletDto = $this->walletService->getWalletByUserId($payeeId);
        } catch (UserWalletNotFoundException $e) {
            throw new PayeeWalletNotFoundException();
        }
    }

    /**
     * validatePayerWallet
     *
     * @param integer $payerId
     * @return void
     * @throws PayerWalletNotFoundException
     */
    protected function validatePayerWallet(int $payerId)
    {
        try {
            $walletDto = $this->walletService->getWalletByUserId($payerId);
        } catch (UserWalletNotFoundException $e) {
            throw new PayerWalletNotFoundException();
        }
    }

    /**
     * validateTransaction
     *
     * @param TransactionCreateDto $transactionCreateDto
     * @return void
     */
    protected function validateTransaction(TransactionCreateDto $transactionCreateDto): void
    {
        $payerUuid = $transactionCreateDto->getPayerUuid();
        $payeeUuid = $transactionCreateDto->getPayeeUuid();

        $this->validatePayer($payerUuid);
        $this->validatePayee($payeeUuid, $payerUuid);

        $idPayer = $this->userService->getUserIdByUuid($payerUuid);
        $this->validatePayerWallet($idPayer);

        $idPayee = $this->userService->getUserIdByUuid($payeeUuid);
        $this->validatePayeeWallet($idPayee);

        $payerWallet = $this->walletService->getWalletByUserId($idPayer);
        $this->validateAmount($transactionCreateDto->getAmount(), $payerWallet->getBalance());
    }
}
