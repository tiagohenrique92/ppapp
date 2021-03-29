<?php
namespace PPApp\Services;

use Exception;
use Monolog\Logger;
use PPApp\Dto\TransactionCreatedDto;
use PPApp\Dto\TransactionCreateDto;
use PPApp\Exceptions\Payment\InvalidPaymentAmountException;
use PPApp\Exceptions\Payment\PayeeNotFoundException;
use PPApp\Exceptions\Payment\PayeeWalletNotFoundException;
use PPApp\Exceptions\Payment\PayerAndPayeeAreTheSamePersonException;
use PPApp\Exceptions\Payment\PayerIsBusinessUserException;
use PPApp\Exceptions\Payment\PayerNotFoundException;
use PPApp\Exceptions\Payment\PayerWalletInsufficientBalanceException;
use PPApp\Exceptions\Payment\PayerWalletNotFoundException;
use PPApp\Exceptions\Payment\PaymentExternalNotificationException;
use PPApp\Exceptions\User\UserNotFoundException;
use PPApp\Exceptions\User\UserWalletNotFoundException;
use PPApp\Infra\DB;
use PPApp\Models\UserModel;
use PPApp\Repositories\TransactionRepository;
use PPApp\Services\ExternalAuthorizationService;
use PPApp\Services\ExternalNotificationService;
use PPApp\Services\UserService;
use PPApp\Services\WalletService;
use PPApp\Utils\Uuid;

class PaymentService
{
    const PAYMENT_NOTIFICATION_QUEUE = "payment";

    /**
     * @var ExternalAuthorizationService
     */
    private $externalAuthorizationService;

    /**
     * @var ExternalNotificationService
     */
    private $externalNotificationService;

    /**
     * @var Logger
     */
    private $logger;

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

    public function __construct(Logger $logger, ExternalAuthorizationService $externalAuthorizationService, ExternalNotificationService $externalNotificationService, TransactionRepository $transactionRepository, UserService $userService, WalletService $walletService)
    {
        $this->logger = $logger;
        $this->externalAuthorizationService = $externalAuthorizationService;
        $this->externalNotificationService = $externalNotificationService;
        $this->transactionRepository = $transactionRepository;
        $this->userService = $userService;
        $this->walletService = $walletService;
    }

    /**
     * registerTransactionDatabase
     *
     * @param string $uuid
     * @param integer $idPayer
     * @param integer $idPayee
     * @param float $amount
     * @return void
     */
    private function registerTransactionDatabase(string $uuid, int $idPayer, int $idPayee, float $amount): void
    {
        DB::transaction(function () use ($uuid, $idPayer, $idPayee, $amount) {
            $transactionCreated = $this->transactionRepository->create(array(
                "uuid" => $uuid,
                "id_payer" => $idPayer,
                "id_payee" => $idPayee,
                "amount" => $amount,
            ));

            if (false === $transactionCreated) {
                throw new Exception("falha ao criar transacao");
            }

            $idWalletPayer = $this->walletService->getWalletIdByUserId($idPayer);
            $debited = $this->walletService->debit($idWalletPayer, $amount);

            if (false === $debited) {
                throw new Exception("falha ao debitar transacao");
            }

            $idWalletPayee = $this->walletService->getWalletIdByUserId($idPayee);
            $credited = $this->walletService->credit($idWalletPayee, $amount);

            if (false === $credited) {
                throw new Exception("falha ao creditar transacao");
            }
        });
    }

    /**
     * sendTransactionNotification
     *
     * @param array $payload
     * @return void
     */
    private function sendTransactionNotification(array $payload): void
    {
        try {
            $this->externalNotificationService->send(json_encode($payload), self::PAYMENT_NOTIFICATION_QUEUE);
        } catch (PaymentExternalNotificationException $e) {
            $this->logger->critical($e->getMessage(), array(
                "details" => $e->getDetails(),
                "trace" => $e->getTraceAsString(),
            ));
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), array(
                "trace" => $e->getTraceAsString(),
            ));
        }
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
        $this->externalAuthorizationService->authorize();

        $uuid = Uuid::create();
        $idPayer = $this->userService->getUserIdByUuid($transactionCreateDto->getPayerUuid());
        $idPayee = $this->userService->getUserIdByUuid($transactionCreateDto->getPayeeUuid());
        $amount = $transactionCreateDto->getAmount();
        $payerName = $this->userService->getUserNameByUuid($transactionCreateDto->getPayerUuid());

        $this->registerTransactionDatabase($uuid, $idPayer, $idPayee, $amount);
        $this->sendTransactionNotification(array(
            "payee" => $transactionCreateDto->getPayeeUuid(),
            "message" => "You have received a new payment of {{$amount}} from {{$payerName}}.",
        ));

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
            throw InvalidPaymentAmountException::create(array("amount" => $amount));
        }

        if ($amount > $balance) {
            throw PayerWalletInsufficientBalanceException::create();
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
            throw PayerNotFoundException::create(array("payerUuid" => $payerUuid));
        }

        if ($payer->getType() === UserModel::USER_TYPE_BUSINESS) {
            throw PayerIsBusinessUserException::create(array("payerUuid" => $payerUuid));
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
            throw PayeeNotFoundException::create(array("payeeUuid" => $payeeUuid));
        }

        if ($payeeUuid === $payerUuid) {
            throw PayerAndPayeeAreTheSamePersonException::create(array(
                "payerUuid" => $payerUuid,
                "payeeUuid" => $payeeUuid,
            ));
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
            throw PayeeWalletNotFoundException::create(array("payeeId" => $payeeId));
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
            throw PayerWalletNotFoundException::create(array("payerId" => $payerId));
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
