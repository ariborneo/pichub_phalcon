<?php

$loader = new \Phalcon\Loader();

$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
        $config->application->pluginsDir,
        __DIR__ . '/../plugins/Validation/Validator/',
        __DIR__ . '/../plugins/Validation/'
    )
)->register();