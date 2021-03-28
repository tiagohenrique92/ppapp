<?php

use PPApp\Controllers\v1\PaymentController;
use PPApp\Middlewares\DetailedErrorResponseMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/bootstrap.php';
require_once __DIR__ . '/../app/container.php';

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("<h1>PPApp :)</h1>");
    return $response;
});

$app->group("/api/v1", function (RouteCollectorProxy $group) {
    $group->post('/payment', PaymentController::class . ':transfer');
})->add(new DetailedErrorResponseMiddleware());

$app->run();
