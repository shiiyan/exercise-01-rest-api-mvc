<?php
use Phalcon\Mvc\Router;


$router = new Router(false);

$router->addGet(
    '/api/products',
    'Index::getall'
);

$router->addGet(
    '/api/products/search/{name}',
    'Index::getbyname'
);

$router->addGet(
    '/api/products/search/{id:[0-9]+}',
    'Index::getbyid'
);

$router->addPost(
    '/api/products',
    'Index::add'
);

$router->addPut(
    '/api/products/{id:[0-9]+}',
    'Index::updatebyid'
);

$router->addDelete(
    '/api/products/{id:[0-9]+}',
    'Index::deletebyid'
);

//return $router;