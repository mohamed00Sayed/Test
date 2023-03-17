<?php

use Moham\Test\Builder\BookBuilder;
use Moham\Test\Builder\DvdBuilder;
use Moham\Test\Builder\FurnitureBuilder;
use Moham\Test\Builder\ProductBuilderFactory;
use Moham\Test\Repository\BookRepository;
use Moham\Test\Repository\DvdRepository;
use Moham\Test\Repository\FurnitureRepository;
use Moham\Test\Repository\RepositoryFactory;
use Moham\Test\Server\Response;
use Moham\Test\Server\Stream;
use Psr\Http\Message\ResponseInterface;

/*Define some constants*/

define('BOOK', 'book');
define('DVD', 'dvd');
define('FURNITURE', 'furniture');

define('REPO_FACTORY', new RepositoryFactory([
    BOOK => BookRepository::class,
    DVD => DvdRepository::class,
    FURNITURE => FurnitureRepository::class
]));

define('PRODUCT_BUILDER_FACTORY', new ProductBuilderFactory([
    BOOK => BookBuilder::class,
    DVD => DvdBuilder::class,
    FURNITURE => FurnitureBuilder::class
]));

/*Utility Methods*/
/*A function to create response*/
function createResponse(int $status, string $data): ResponseInterface
{
    $responseBody = Stream::create($data);
    $response = (new Response($status))->withBody($responseBody);
    return $response->withHeader('Access-Control-Allow-Origin', $_ENV['ALLOWED_ORIGINS'])
        ->withHeader('Access-Control-Allow-Headers', $_ENV['ALLOWED_HEADERS'])
        ->withHeader('Access-Control-Allow-Methods', $_ENV['ALLOWED_METHODS']);
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
