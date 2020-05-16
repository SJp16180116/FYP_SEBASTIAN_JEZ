<?php
/**
 * dependencies.php
 *
 * Slim uses a dependency container to prepare, manage, and
 * inject application dependencies. Slim supports containers
 * that implement PSR-11.
 *
 * Slim’s built-in container is based on Pimple.
 *
 * Source: http://www.slimframework.com/docs/v3/concepts/di.html
 */

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(
        $container['settings']['view']['template_path'],
        $container['settings']['view']['twig'],
        [
            'debug' => true
        ]
    );

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$container['DatabaseWrapper'] = function ($container) {
    $dwrapper = new WEBAPPV\DatabaseWrapper();
    return $dwrapper;
};

$container['SqlQueries'] = function ($container) {
    $queries = new WEBAPPV\SqlQueries();
    return $queries;
};

$container['SettingsModel'] = function ($container) {
    $stmodel = new \WEBAPPV\SettingsModel();
    return $stmodel;
};

$container['MysqliAndPdo'] = function ($container) {
    $auth = new \WEBAPPV\MysqliAndPdo();
    return $auth;
};

$container['doctrineSqlQueries'] = function ($container) {
    $doctrine_sql_queries = new \WEBAPPV\Doctrine();
    return $doctrine_sql_queries;
};

$container['Validator'] = function ($container) {
    $validator = new \WEBAPPV\Validator();
    return $validator;
};

$container['SecureToken'] = function ($container) {
    $token = new \WEBAPPV\SecureToken();
    return $token;
};

$container['Helper'] = function ($container) {
    $helper = new WEBAPPV\Helper();
    return $helper;
};

$container['bcryptWrapper'] = function ($container) {
    $wrapper = new \WEBAPPV\BcryptWrapper();
    return $wrapper;
};

$container['SessionWrapper'] = function ($container) {
    $snwrapper = new \WEBAPPV\SessionWrapper();
    return $snwrapper;
};

$container['SessionModel'] = function ($container) {
    $snmodel= new \WEBAPPV\SessionModel();
    return $snmodel;
};

$container['logger'] = function ($container) {
    $logger = new \Monolog\Logger('WebAppV_Logger');
    $file_handler = new \Monolog\Handler\StreamHandler(LOG_PATH);
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['Logs'] = function ($container) {
    $clogger = new \WEBAPPV\MonologLogger();
    return $clogger;
};
