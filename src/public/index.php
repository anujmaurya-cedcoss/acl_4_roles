<?php
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Config;
use handler\Listener\Listener;


$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

$loader = new Loader();
$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);
$loader->registerNamespaces([
    "handler\Listener" => APP_PATH . "/handlers/",
    "handler\Aware" => APP_PATH . "/handlers/",
]);

$loader->register();

$container = new FactoryDefault();
$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);
$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);
$eventsManager = $container->get('eventsManager');

$eventsManager->attach(
    'application:beforeHandleRequest',
    new Listener()
);
$container->set('EventsManager', $eventsManager);
$application->setEventsManager($eventsManager);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
