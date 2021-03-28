<?php
namespace PPApp;

use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PPApp\Models\TransactionModel;
use PPApp\Models\UserModel;
use PPApp\Models\WalletModel;
use PPApp\Repositories\TransactionRepository;
use PPApp\Repositories\UserRepository;
use PPApp\Repositories\WalletRepository;
use PPApp\Services\ExternalAuthorizationService;
use PPApp\Services\ExternalNotificationService;
use PPApp\Services\PaymentService;
use PPApp\Services\UserService;
use PPApp\Services\WalletService;
use Psr\Container\ContainerInterface;

$container = new Container();
$container->set("externalAuthorizationService", function () {
    $url = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";
    return new ExternalAuthorizationService($url);
});
$container->set("externalNotificationService", function () {
    $url = "https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04";
    return new ExternalNotificationService($url);
});
$container->set("logger", function () {
    $logger = new Logger('ppapp_logger');
    $file_handler = new StreamHandler(__DIR__ . '/../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
});
$container->set(UserService::class, function () {
    $userService = new UserService(new UserRepository(new UserModel()));
    return $userService;
});
$container->set(WalletService::class, function () {
    $walletService = new WalletService(new WalletRepository(new WalletModel()));
    return $walletService;
});
$container->set(UserService::class, function () {
    $userService = new UserService(new UserRepository(new UserModel()));
    return $userService;
});
$container->set(PaymentService::class, function (ContainerInterface $container) {
    $logger = $container->get("logger");
    $externalAuthorizationService = $container->get("externalAuthorizationService");
    $externalNotificationService = $container->get("externalNotificationService");
    $transactionRepository = new TransactionRepository(new TransactionModel());
    $userService = $container->get(UserService::class);
    $walletService = $container->get(WalletService::class);
    $paymentService = new PaymentService($logger, $externalAuthorizationService, $externalNotificationService, $transactionRepository, $userService, $walletService);
    return $paymentService;
});
