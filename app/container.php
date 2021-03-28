<?php
namespace PPApp;

use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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
$container->set(ExternalAuthorizationService::class, function () {
    $url = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";
    return new ExternalAuthorizationService($url);
});
$container->set(ExternalNotificationService::class, function () {
    $url = "https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04";
    return new ExternalNotificationService($url);
});
$container->set(Logger::class, function () {
    $logger = new Logger('ppapp_logger');
    $file_handler = new StreamHandler(__DIR__ . '/../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
});
$container->set(PaymentService::class, function (ContainerInterface $container) {
    $logger = $container->get(Logger::class);
    $externalAuthorizationService = $container->get(ExternalAuthorizationService::class);
    $externalNotificationService = $container->get(ExternalNotificationService::class);
    $transactionRepository = $container->get(TransactionRepository::class);
    $userService = $container->get(UserService::class);
    $walletService = $container->get(WalletService::class);
    $paymentService = new PaymentService($logger, $externalAuthorizationService, $externalNotificationService, $transactionRepository, $userService, $walletService);
    return $paymentService;
});
$container->set(UserModel::class, function () {
    $userModel = new UserModel();
    return $userModel;
});
$container->set(UserRepository::class, function (ContainerInterface $container) {
    $userRepository = new userRepository($container->get(UserModel::class));
    return $userRepository;
});
$container->set(UserService::class, function (ContainerInterface $container) {
    $userService = new UserService($container->get(UserRepository::class));
    return $userService;
});
$container->set(WalletService::class, function () {
    $walletService = new WalletService(new WalletRepository(new WalletModel()));
    return $walletService;
});
