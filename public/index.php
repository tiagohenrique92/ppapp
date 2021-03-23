<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use PPApp\Controllers\v1\PaymentController;
use PPApp\Middleware\JsonBodyParserMiddleware;
use PPApp\Services\ExternalAuthorizationService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/bootstrap.php';

$container = new Container();
$container->set("externalAuthorizationService", function(){
    $url = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";
    return new ExternalAuthorizationService($url);
});

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->group("/api/v1", function(RouteCollectorProxy $group){
    $group->post('/payment', PaymentController::class . ':transfer');
});

$app->run();