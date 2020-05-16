<?php
/**
 * xssDoctrine.php
 *
 * Demonstrates the secure proof of concept.
 *
 * The third status feature is based on the Doctrine Database Abstraction Layer (DBAL)
 * on the top of PDO. However, this implementation is not vulnerable to both the
 * Cross-Site Request Forgery and SQL Injection because input sanitization is
 * implemented. After each attempt, the user is prompted with a notification
 * displaying the input before and after the sanitization to demonstrate the outcome.
 *
 * The user’s attempt to perform Cross-Site Scripting is incorrect (Secure PoC),
 * the server-side script detects the unsuccessful attempt and redirects
 * the user to the “unsuccessful attempt” subpage.
 *
 * Author: Sebastian Jez
 * Email: P16180116@my365.dmu.ac.uk
 * Date: 01/03/2020
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Doctrine\DBAL\DriverManager;

/**
 * @param Request $request
 * @param Response $response
 * @return Response
 */

$app->post('/xssDoctrine',
    function (Request $request, Response $response) use ($app) {

        $tainted_parameter = $request->getParsedBody();
        $info = $tainted_parameter['info2'];

        $cleaned_info = cleanupUsername($app, $info);
        $xss_test = storeAndRetrieveUserInfoDoctrine($app, $cleaned_info, 'john');

        return $this->view->render($response,
            'xss_failure_doctrine.html.twig',
            [
                'method' => 'post',
                'action1' => LANDING_PAGE,
                'action2' => 'sqli',
                'action3' => 'xss',
                'action4' => 'csrf',
                'action5' => 'logout',
                'test' => $xss_test,
                'info' => $info,
            ]);

    })->setName('xssDoctrine');


/**
 * updates user status
 *
 * @param $app
 * @param $info
 * @param $username
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function storeAndRetrieveUserInfoDoctrine($app, $info, $username)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();
    $queryBuilder1 = $database_connection->createQueryBuilder();

    $doctrine_queries::queryUpdateUserInfo($queryBuilder, $info, $username);
    $test = $doctrine_queries::queryRetrieveUserInfo($queryBuilder1, $username);

    return $test;
}

/**
 * sanitizes username
 *
 * @param $app
 * @param $tainted_username
 * @return mixed
 */
function cleanupUsername($app, $tainted_username)
{

    $validator = $app->getContainer()->get('Validator');

    $cleaned_username = $validator->sanitiseString($tainted_username);

    return $cleaned_username;
}

/**
 * sanitizes password
 *
 * @param $app
 * @param $tainted_password
 * @return mixed
 */
function cleanupPassword($app, $tainted_password)
{

    $validator = $app->getContainer()->get('Validator');

    $cleaned_password = $validator->sanitiseString($tainted_password);

    return $cleaned_password;
}
