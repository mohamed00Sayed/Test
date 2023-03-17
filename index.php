<?php

use Dotenv\Dotenv;
use Moham\Test\Server\RequestStations;
use Moham\Test\Server\RequestBodyJsonParser;
use Moham\Test\Server\RequestCreator;
use Moham\Test\Server\SapiEmitter;
use Moham\Test\Servlet\HttpServlet;

require 'vendor/autoload.php';
require 'utils.php';

/*Read environment variables from .env file*/
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

/*Configure dispatcher servlet & all request handlers*/
$dispatcherServlet = new HttpServlet();
/*Handle preflight request to make subsequent requests proceed*/
$dispatcherServlet->options("/products/", function ($request) {
    return createResponse(200, "OK");
});

$dispatcherServlet->get("/products/", function ($request) {
    $booksRepo = REPO_FACTORY->getRepository(BOOK);
    $dvdsRepo = REPO_FACTORY->getRepository(DVD);
    $furnituresRepo = REPO_FACTORY->getRepository(FURNITURE);

    $resData = array_merge([], $booksRepo->getAll());
    $resData = array_merge($resData, $dvdsRepo->getAll());
    $resData = array_merge($resData, $furnituresRepo->getAll());
    return createResponse(200, json_encode($resData));
});

$dispatcherServlet->post("/products/", function ($request) {
    $parsedBody = convert($request->getParsedBody());

    $booksRepo = REPO_FACTORY->getRepository(BOOK);
    $dvdsRepo = REPO_FACTORY->getRepository(DVD);
    $furnituresRepo = REPO_FACTORY->getRepository(FURNITURE);

    $saveRepo = REPO_FACTORY->getRepository($parsedBody->type);

    $builder = PRODUCT_BUILDER_FACTORY->getBuilder($parsedBody->type);
    $product = $builder->getProductInstance($parsedBody->data);
    /*check existence of the product in all tables*/
    $sku_in_books = $booksRepo->exists($product->getSku());
    $sku_in_dvds = $dvdsRepo->exists($product->getSku());
    $sku_in_furnitures = $furnituresRepo->exists($product->getSku());

    $res = ['error' => 'Already exists'];
    $code = 403;
    if (!$sku_in_books && !$sku_in_dvds && !$sku_in_furnitures) {
        $res = $saveRepo->save($product);
        if ($res) {
            $code = 200;
        }
    }
    return createResponse($code, json_encode($res));
});

$dispatcherServlet->delete("/products/", function ($request) {
    $booksRepo = REPO_FACTORY->getRepository(BOOK);
    $dvdsRepo = REPO_FACTORY->getRepository(DVD);
    $furnituresRepo = REPO_FACTORY->getRepository(FURNITURE);

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

/*Configure the stations the request will pass through*/
$stations = new RequestStations([
    new RequestBodyJsonParser(),
    $dispatcherServlet
]);

/*Create request from globals*/
$request = (new RequestCreator())->fromGlobals();

/*Forward to all middlewares and handlers*/
$response = $stations->handle($request);

/*Emit response*/
(new SapiEmitter())->emit($response);
