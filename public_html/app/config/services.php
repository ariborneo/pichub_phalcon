<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;

$di = new FactoryDefault();

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
                'compiledPath' => $config->application->cacheDir,
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

$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

$di->set('cookies', function () {
    $cookies = new Phalcon\Http\Response\Cookies();
    $cookies->useEncryption(true);
    return $cookies;
});

/*
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});
*/

$di->set('crypt', function() {
    $crypt = new Phalcon\Crypt();
    $crypt->setKey("9S8(Y0=<34D№xfLj/[7<");
    return $crypt;
});

$di->set('router', function() {
    $router = new Phalcon\Mvc\Router(false);
    $router->add(
        "/",
        array(
            "controller" => "index",
            "action"     => "index",
        )
    );
    $router->add(
        "/login",
        array(
            "controller" => "login",
            "action"     => "index",
        )
    );
    $router->add(
        "/logout",
        array(
            "controller" => "login",
            "action"     => "logout",
        )
    );
    $router->add(
        "/registration",
        array(
            "controller" => "login",
            "action"     => "registration",
        )
    );
    $router->add(
        "/show/([a-z0-9]+)([/]?)",
        array(
            "controller" => "image",
            "action"     => "index",
            "code" => 1
        )
    );
    $router->add(
        "/like/([a-z0-9]+)([/]?)",
        array(
            "controller" => "image",
            "action"     => "like",
            "code" => 1
        )
    );
    $router->add(
        "/dislike/([a-z0-9]+)([/]?)",
        array(
            "controller" => "image",
            "action"     => "dislike",
            "code" => 1
        )
    );
    $router->add(
        "/comment_add/([a-z0-9]+)([/]?)",
        array(
            "controller" => "image",
            "action"     => "comment_add",
            "code" => 1
        )
    );
    $router->add(
        "/comment_del/([a-z0-9]+)/([0-9]+)([/]?)",
        array(
            "controller" => "image",
            "action"     => "comment_del",
            "code" => 1,
            "id" => 2
        )
    );
    $router->add(
        "/del_request/([a-z0-9]+)([/]?)",
        array(
            "controller" => "image",
            "action"     => "del_request",
            "code" => 1
        )
    );
    $router->add(
        "/user/(.*)",
        array(
            "controller" => "user",
            "action"     => "index",
            "name" => 1
        )
    );
    $router->add(
        "/top",
        array(
            "controller" => "charts",
            "action"     => "top",
        )
    );
    $router->add(
        "/last",
        array(
            "controller" => "charts",
            "action"     => "last",
        )
    );
    $router->add(
        "/create_album",
        array(
            "controller" => "user",
            "action"     => "create_album",
        )
    );
    $router->add(
        "/album/([0-9]+)([/]?)",
        array(
            "controller" => "user",
            "action"     => "album",
            "id" => 1
        )
    );
    $router->add(
        "/upload",
        array(
            "controller" => "upload",
            "action"     => "index"
        )
    );
    $router->add(
        "/feedback",
        array(
            "controller" => "index",
            "action"     => "feedback"
        )
    );
    $router->notFound(array(
        "controller" => "index",
        "action" => "error404"
    ));
    return $router;
});