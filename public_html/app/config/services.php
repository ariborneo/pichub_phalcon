<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;

$di = new FactoryDefault();

$di->set('config', $config);

$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}, true);

$di->set('view', function () use ($config) {
    $view = new View();
    $view->setViewsDir($config->application->viewsDir);
    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {
            $volt = new VoltEngine($view, $di);
            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir . "volt/",
                'compiledSeparator' => '_'
            ));
            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));
    return $view;
}, true);

$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        "charset" => "utf8"
    ));
});

$di->set('modelsMetadata', function () use ($config) {
    return new \Phalcon\Mvc\Model\MetaData\Files(array(
        'metaDataDir' => $config->application->cacheDir . "metadata/",
        "lifetime" => 10 * 86400,
        "prefix" => "metadata"
    ));
});

$di->set('cookies', function () {
    $cookies = new Phalcon\Http\Response\Cookies();
    $cookies->useEncryption(true);
    return $cookies;
});

$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});

$di->set('crypt', function() {
    $crypt = new Phalcon\Crypt();
    $crypt->setKey("9S8(Y0=<34D№xfLj/[7<");
    return $crypt;
});

$di->set('modelsCache', function() use ($config) {
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
        "lifetime" => 86400
    ));
    $cache = new \Phalcon\Cache\Backend\File($frontCache, array(
        'cacheDir' => $config->application->cacheDir . "models/"
    ));
    return $cache;
});

$di->set('viewCache', function() use ($config) {
    $frontCache = new \Phalcon\Cache\Frontend\Output(array(
        "lifetime" => 86400
    ));
    $cache = new \Phalcon\Cache\Backend\File($frontCache, array(
        'cacheDir' => $config->application->cacheDir . "views/"
    ));
    return $cache;
});

$di->set('router', function() {
    $router = new Phalcon\Mvc\Router(false);
    include "router.php";
    return $router;
});

$di->set('mail', function(){
    return new Mail();
});


$di->set('user', function(){
    return new Auth();
});
