<?php
declare (strict_types = 1);
namespace PPApp;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use PPApp\Dto\UserDto;
use PPApp\Exceptions\Payment\InvalidPaymentAmountException;
use PPApp\Exceptions\Payment\PayerAndPayeeAreTheSamePersonException;
use PPApp\Exceptions\Payment\PayerIsBusinessUserException;
use PPApp\Exceptions\Payment\PayerWalletInsufficientBalanceException;
use PPApp\Models\UserModel;
use PPApp\Repositories\TransactionRepository;
use PPApp\Services\ExternalAuthorizationService;
use PPApp\Services\ExternalNotificationService;
use PPApp\Services\PaymentService;
use PPApp\Services\UserService;
use PPApp\Services\WalletService;
use ReflectionClass;
use ReflectionMethod;

final class PaymentTest extends TestCase
{
    public static function getMethod(string $name, string $class): ReflectionMethod
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * getPersonUserDto
     *
     * @return UserDto
     */
    protected function getPersonUserDto(): UserDto
    {
        $uuid = "1eb8f484-5467-6396-afbf-02423599a7ad";
        $name = "Person User 1";
        $email = "person.user.1@email.com";
        $password = "000001";
        $type = UserModel::USER_TYPE_PERSON;
        $userDto = new UserDto($uuid, $name, $email, $password, $type);
        return $userDto;
    }
    protected function getBusinessUserDto(): UserDto
    {
        $uuid = "1eb8f49b-f137-67fe-8313-02423599a7ad";
        $name = "Business User 1";
        $email = "business.user.1@email.com";
        $password = "000001";
        $type = UserModel::USER_TYPE_BUSINESS;
        $userDto = new UserDto($uuid, $name, $email, $password, $type);
        return $userDto;
    }

    public function testUsuarioComumPodeFazerPagamento(): void
    {
        $userDto = $this->getPersonUserDto();

        $fakeUserService = $this->createMock(UserService::class);
        $fakeUserService
            ->method("getUserByUuid")
            ->willReturn($userDto)
        ;
        $fakeTransactionRepository = $this->createMock(TransactionRepository::class);
        $fakeWalletService = $this->createMock(WalletService::class);
        $fakeLogger = $this->createMock(Logger::class);
        $fakeExternalAuthorizationService = $this->createMock(ExternalAuthorizationService::class);
        $fakeExternalNotificationService = $this->createMock(ExternalNotificationService::class);

        $paymentService = new PaymentService(
            $fakeLogger,
            $fakeExternalAuthorizationService,
            $fakeExternalNotificationService,
            $fakeTransactionRepository,
            $fakeUserService,
            $fakeWalletService
        );

        $validatePayer = self::getMethod("validatePayer", PaymentService::class);
        $this->assertNotInstanceOf(\Exception::class, $validatePayer->invokeArgs($paymentService, array(
            "payerUuid" => $userDto->getUuid(),
        )));
    }

    public function testUsuarioEpresarialNaoPodeFazerPagamento(): void
    {
        $userDto = $this->getBusinessUserDto();

        $fakeUserService = $this->createMock(UserService::class);
        $fakeUserService
            ->method("getUserByUuid")
            ->willReturn($userDto)
        ;
        $fakeTransactionRepository = $this->createMock(TransactionRepository::class);
        $fakeWalletService = $this->createMock(WalletService::class);
        $fakeLogger = $this->createMock(Logger::class);
        $fakeExternalAuthorizationService = $this->createMock(ExternalAuthorizationService::class);
        $fakeExternalNotificationService = $this->createMock(ExternalNotificationService::class);

        $paymentService = new PaymentService(
            $fakeLogger,
            $fakeExternalAuthorizationService,
            $fakeExternalNotificationService,
            $fakeTransactionRepository,
            $fakeUserService,
            $fakeWalletService
        );

        $validatePayer = self::getMethod("validatePayer", PaymentService::class);
        $this->expectException(PayerIsBusinessUserException::class);
        $validatePayer->invokeArgs($paymentService, array(
            "payerUuid" => $userDto->getUuid(),
        ));
    }

    public function testPagadorDeveSerDiferenteDoRecebedor(): void
    {
        $userDto = $this->getPersonUserDto();
        $payerUuid = $userDto->getUuid();
        $payeeUuid = $userDto->getUuid();

        $fakeUserService = $this->createMock(UserService::class);
        $fakeUserService
            ->method("getUserByUuid")
            ->willReturn($userDto)
        ;
        $fakeTransactionRepository = $this->createMock(TransactionRepository::class);
        $fakeWalletService = $this->createMock(WalletService::class);
        $fakeLogger = $this->createMock(Logger::class);
        $fakeExternalAuthorizationService = $this->createMock(ExternalAuthorizationService::class);
        $fakeExternalNotificationService = $this->createMock(ExternalNotificationService::class);

        $paymentService = new PaymentService(
            $fakeLogger,
            $fakeExternalAuthorizationService,
            $fakeExternalNotificationService,
            $fakeTransactionRepository,
            $fakeUserService,
            $fakeWalletService
        );

        $validatePayee = self::getMethod("validatePayee", PaymentService::class);
        $this->expectException(PayerAndPayeeAreTheSamePersonException::class);
        $validatePayee->invokeArgs($paymentService, array(
            "payeeUuid" => $payeeUuid,
            "payerUuid" => $payerUuid,
        ));
    }

    public function testValorDoPagamentoNaoDeveSerMenorOuIgualAZero(): void
    {
        $amount = 0;
        $balance = 100;
        $fakeUserService = $this->createMock(UserService::class);
        $fakeTransactionRepository = $this->createMock(TransactionRepository::class);
        $fakeWalletService = $this->createMock(WalletService::class);
        $fakeLogger = $this->createMock(Logger::class);
        $fakeExternalAuthorizationService = $this->createMock(ExternalAuthorizationService::class);
        $fakeExternalNotificationService = $this->createMock(ExternalNotificationService::class);

        $paymentService = new PaymentService(
            $fakeLogger,
            $fakeExternalAuthorizationService,
            $fakeExternalNotificationService,
            $fakeTransactionRepository,
            $fakeUserService,
            $fakeWalletService
        );

        $validateAmount = self::getMethod("validateAmount", PaymentService::class);
        $this->expectException(InvalidPaymentAmountException::class);
        $validateAmount->invokeArgs($paymentService, array(
            "amount" => $amount,
            "balance" => $balance,
        ));
    }

    public function testValorDoPagamentoNaoDeveSerMaiorQueOSaldoDaCarteira(): void
    {
        $amount = 101;
        $balance = 100;
        $fakeUserService = $this->createMock(UserService::class);
        $fakeTransactionRepository = $this->createMock(TransactionRepository::class);
        $fakeWalletService = $this->createMock(WalletService::class);
        $fakeLogger = $this->createMock(Logger::class);
        $fakeExternalAuthorizationService = $this->createMock(ExternalAuthorizationService::class);
        $fakeExternalNotificationService = $this->createMock(ExternalNotificationService::class);

        $paymentService = new PaymentService(
            $fakeLogger,
            $fakeExternalAuthorizationService,
            $fakeExternalNotificationService,
            $fakeTransactionRepository,
            $fakeUserService,
            $fakeWalletService
        );

        $validateAmount = self::getMethod("validateAmount", PaymentService::class);
        $this->expectException(PayerWalletInsufficientBalanceException::class);
        $validateAmount->invokeArgs($paymentService, array(
            "amount" => $amount,
            "balance" => $balance,
        ));
    }
}
