<?php

$loader = new \Phalcon\Loader();

$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir
    )
)->register();