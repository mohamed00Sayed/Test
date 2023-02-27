<?php

use Dotenv\Dotenv;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Middlewares\Cors;
use Moham\Test\Servlet\HttpServlet;
use Moham\Test\Util\ParsersContainer;
use Moham\Test\Util\ResponseFactory as UtilResponseFactory;
use Neomerx\Cors\Analyzer;
use Neomerx\Cors\Strategies\Settings;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Http\Message\ResponseInterface;
use Relay\RelayBuilder;
use Yiisoft\Request\Body\RequestBodyParser;

require 'vendor/autoload.php';
/*CORS settings*/
$settings = (new Settings())->init("http", "localhost", 8000);
$settings->setAllowedOrigins(["*"]);
$settings->setAllowedHeaders(["Content-Type"]);
$settings->setAllowedMethods(["GET", "POST", "DELETE"]);
$analyzer = Analyzer::instance($settings);

/*Read environment variables from .env file*/
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

/*Create request body parser*/
$requestBodyParser =  new RequestBodyParser(new UtilResponseFactory(), new ParsersContainer);

/*Configure dispatcher servlet & all request handlers*/
$dispatcherServlet = new HttpServlet();

$dispatcherServlet->get("/products/", function ($request) {
    return createResponse(200, "It's Ok");
});

$dispatcherServlet->post("/products/", function ($request) {
    return createResponse(200, json_encode($request->getParsedBody() . " OK ^_^"));
});

$dispatcherServlet->delete("/products/", function ($request) {
    return createResponse(200, "Yes indeedeo !!! chandler bing");
});

/*Build Relay*/
$builder = new RelayBuilder();
$relay = $builder->newInstance([
    new Cors($analyzer),
    $requestBodyParser,
    $dispatcherServlet
]);

/*Create request from globals*/
$request = createRequest();

/*Forward to all middlewares and handlers*/
$response = $relay->handle($request);

/*Emit response*/
(new SapiEmitter())->emit($response);
/*****************************************************************************
 ****************************************************************************/
/*Utility Methods*/
/*A function to create request from globals*/
function createRequest()
{
    $psr17Factory = new Psr17Factory();
    $request = (new ServerRequestCreator(
        $psr17Factory,
        $psr17Factory,
        $psr17Factory,
        $psr17Factory
    ))->fromGlobals();
    return $request;
}
/*A function to create response*/
function createResponse(int $status, string $data): ResponseInterface
{
    $psr17Factory = new Psr17Factory();
    $responseBody = $psr17Factory->createStream($data);
    $response = $psr17Factory->createResponse($status)->withBody($responseBody);
    return $response;
}