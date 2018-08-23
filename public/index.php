<?php

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
    ]
);

$loader->register();

// Create a DI
$di = new FactoryDefault();

// Setup the view component
$di->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$di->set(
    'router',
    function () {
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

        return $router;
    }

);

$di->set(
    'db',
    function () {
        return new PdoMysql(
            [
                'host' => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname' => 'myproducts'
            ]
        );
    }
);


$application = new Application($di);

try {
    // Handle the request
    $response = $application->handle();

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}