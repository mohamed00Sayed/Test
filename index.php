<?php

use Dotenv\Dotenv;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Middlewares\Cors;
use Moham\Test\Builder\ProductBuilderFactory;
use Moham\Test\Repository\RepositoryFactory;
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

/*Read environment variables from .env file*/
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

/*CORS settings*/
$settings = (new Settings())->init($_ENV["HOST_PROTOCOL"], $_ENV["HOST_IP"], $_ENV["HOST_PORT"]);
$settings->setAllowedOrigins((array)$_ENV["ALLOWED_ORIGINS"]);
$settings->setAllowedHeaders((array)$_ENV["ALLOWED_HEADERS"]);
$settings->setAllowedMethods((array)$_ENV["ALLOWED_METHODS"]);
$analyzer = Analyzer::instance($settings);
$cors = new Cors($analyzer);
/*Create request body parser*/
$requestBodyParser =  new RequestBodyParser(new UtilResponseFactory(), new ParsersContainer);

/*Configure dispatcher servlet & all request handlers*/
$dispatcherServlet = new HttpServlet();

$dispatcherServlet->get("/products/", function ($request) {
    $repoFactory = new RepositoryFactory();
    $booksRepo = $repoFactory->getRepository(RepositoryFactory::BOOK);
    $dvdsRepo = $repoFactory->getRepository(RepositoryFactory::DVD);
    $furnituresRepo = $repoFactory->getRepository(RepositoryFactory::FURNITURE);
    $resData = [
        'books' => $booksRepo->getAll(),
        'dvds' => $dvdsRepo->getAll(),
        'furnitures' => $furnituresRepo->getAll()
    ];
    return createResponse(200, json_encode($resData));
});

$dispatcherServlet->post("/products/", function ($request) {
    $builderFactory = new ProductBuilderFactory();
    $repoFactory = new RepositoryFactory();

    $parsedBody = convert($request->getParsedBody());

    $booksRepo = $repoFactory->getRepository(RepositoryFactory::BOOK);
    $dvdsRepo = $repoFactory->getRepository(RepositoryFactory::DVD);
    $furnituresRepo = $repoFactory->getRepository(RepositoryFactory::FURNITURE);

    $saveRepo = $repoFactory->getRepository($parsedBody->type);

    $builder = $builderFactory->getBuilder($parsedBody->type);
    $product = $builder->getProductInstance($parsedBody->data);
    /*check existence of the product in all tables*/
    $sku_in_books = $booksRepo->exists($product->getSku());
    $sku_in_dvds = $dvdsRepo->exists($product->getSku());
    $sku_in_furnitures = $furnituresRepo->exists($product->getSku());

    $res = ['error' => 'Already exists'];
    $code = 403;
    if (!$sku_in_books && !$sku_in_dvds && !$sku_in_furnitures) {
        $res = $saveRepo->save($product);
        if($res){
            $code = 200;
        }
    }
    return createResponse($code, json_encode($res));
});

$dispatcherServlet->delete("/products/", function ($request) {
    $repoFactory = new RepositoryFactory();
    $booksRepo = $repoFactory->getRepository(RepositoryFactory::BOOK);
    $dvdsRepo = $repoFactory->getRepository(RepositoryFactory::DVD);
    $furnituresRepo = $repoFactory->getRepository(RepositoryFactory::FURNITURE);

    $parsedBody = $request->getParsedBody();

    $books_skus = $parsedBody["books"];
    $dvds_skus = $parsedBody["dvds"];
    $furnitures_skus = $parsedBody["furnitures"];
    /*No side effects from deleting a non existing sku*/
    $booksRepo->deleteAll($books_skus);
    $dvdsRepo->deleteAll($dvds_skus);
    $furnituresRepo->deleteAll($furnitures_skus);
    /*So, alaway return OK*/
    return createResponse(200, "OK");
});

/*Build Relay*/
$builder = new RelayBuilder();
$relay = $builder->newInstance([
    $cors,
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
function createResponse(int $status = 200, string $data): ResponseInterface
{
    $psr17Factory = new Psr17Factory();
    $responseBody = $psr17Factory->createStream($data);
    $response = $psr17Factory->createResponse($status)->withBody($responseBody);
    return $response;
}
/*A function to convert an array to stdClass instance*/
function convert(array $array): stdClass
{
    $instance = new stdClass();
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $v = convert($v);
        }
        $instance->$k = $v;
    }
    return $instance;
}
