<?php
/**
 * sqli.php
 *
 * Display the sql injection page.
 *
 * The SQLI page begins with a brief description of the attack
 * and primary defences to introduce the user to the vulnerability.
 *
 * Allows the user to manually inject malicious payload.
 * Contains three different implementations of the login system:
 *
 * 1. Only the first one is vulnerable to SQL Injection.
 * 2. The second one is not vulnerable to SQL Injection.
 *    However, it does not implement all recommended security measures.
 * 3. The third one adopts the most important security measures.
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
 * @return mixed
 */

$app->post('/sqli', function (Request $request, Response $response) use ($app) {

    $quantitiy = countUserCommentsSqli($app);
    $comments_data = retrieveUserComments($app, "user_comments");

    $username = ifSetUsername($app)['username'];
    $this->get('logger')->info("User (" . $username . ")successfully entered /sqli.");

    return $this->view->render($response,
        'sqli.html.twig',
        [
            'method' => 'post',
            'action1' => LANDING_PAGE,
            'action2' => 'sqli',
            'action3' => 'xss',
            'action4' => 'csrf',
            'action5' => 'logout',
            'action6' => 'sqliMysqli',
            'action7' => 'sqliPdo',
            'action8' => 'sqliDoctrine',
            'action9' => 'NewCommentSqli',
            'quantity' => $quantitiy,
            'comments' => $comments_data,
        ]);

})->setName('sqli');

/**
 * counts sqli comments
 *
 * @param $app
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function countUserCommentsSqli($app)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $number = $doctrine_queries::queryCountUserCommentsSqli($queryBuilder);

    return $number;
}

/**
 * retrieves sqli comments from the database
 *
 * @param $app
 * @param $table
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function retrieveUserComments($app, $table)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $comments = $doctrine_queries::queryRetrieveUserComments($queryBuilder, $table);

    return $comments;
}