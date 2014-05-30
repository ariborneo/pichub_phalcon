<?php

define('PHALCONDEBUG', true);

error_reporting(E_ALL);

//try {

    (new \Phalcon\Debug())->listen(true, true);

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../app/config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../app/config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../app/config/services.php";

    if (PHALCONDEBUG == true)
    {
        $loader->registerNamespaces(array('PDW' => realpath('../app/plugins/PDW')));
        $debugWidget = new \PDW\DebugWidget($di);
    }

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

/*
} catch (\Exception $e) {
    echo $e->getMessage();
}
*/
