<?php

$log_path = '/Applications/XAMPP/xamppfiles/htdocs/webappv/includes/logs/WebAppV_PROD_01.log';

define('LANDING_PAGE', $_SERVER['SCRIPT_NAME']);
define ('BCRYPT_ALGO', PASSWORD_DEFAULT);
define ('BCRYPT_COST', 12);
define('LOG_PATH', $log_path);

$settings = [
    "settings" => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
        'mode' => 'development',
        'debug' => true,
        'class_path' => __DIR__ . '/src/',
        'view' => [
            'template_path' => __DIR__ . '/templates/',
            'twig' => [
                'cache' => false,
                'auto_reload' => true,
            ]],
        'pdo_settings' => [
            'rdbms' => 'mysql',
            'host' => '127.0.0.1',
            'dbname' => 'web_app_vulnerabilities',
            'port' => '3306',
            'username' => 'root',
            'userpassword' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]],
        'mysqli_settings' => [
            'rdbms' => 'mysql',
            'host' => '127.0.0.1',
            'dbname' => 'web_app_vulnerabilities',
            'port' => '3306',
            'username' => 'root',
            'userpassword' => '',
        ],
    'doctrine_settings' => [
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'dbname' => 'web_app_vulnerabilities',
        'port' => '3306',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ]],
];
return $settings;