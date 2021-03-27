<?php

use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PPApp\Controllers\v1\PaymentController;
use PPApp\Middlewares\DetailedErrorResponseMiddleware;
use PPApp\Services\ExternalAuthorizationService;
use PPApp\Services\ExternalNotificationService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/bootstrap.php';

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

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->group("/api/v1", function (RouteCollectorProxy $group) {
    $group->post('/payment', PaymentController::class . ':transfer');
})->add(new DetailedErrorResponseMiddleware());

$app->run();
