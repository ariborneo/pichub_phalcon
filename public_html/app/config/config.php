<?php

return new \Phalcon\Config(array(
    'database' => array(
        'adapter'     => 'Mysql',
        'host'        => 'localhost',
        'username'    => 'root',
        'password'    => '',
        'dbname'      => 'pichub',
    ),
    'application' => array(
        'controllersDir' => __DIR__ . '/../controllers/',
        'modelsDir'      => __DIR__ . '/../models/',
        'viewsDir'       => __DIR__ . '/../views/',
        'pluginsDir'     => __DIR__ . '/../plugins/',
        'libraryDir'     => __DIR__ . '/../library/',
        'cacheDir'       => __DIR__ . '/../cache/',
        'baseUri'        => '/',
    ),
    "mail" => array(
        "fromName" => "PicHub.ru",
        "fromEmail" => "pichub@yandex.ru",
        "smtp" => array(
            "server" => "smtp.yandex.ru",
            "port" => "465",
            "security" => "ssl",
            "username" => "pichub@yandex.ru",
            "password" => "19955875aa",
        )
    )
));