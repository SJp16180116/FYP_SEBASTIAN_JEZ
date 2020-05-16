<?php
/**
 * xss.php
 *
 * Display the cross-site scripting page.
 *
 * The XSS page begins with a brief description of the attack
 * and primary defences to introduce the user to the vulnerability.
 *
 * Allows the user to manually inject malicious payload.
 * Contains three different implementations of the status feature:
 *
 * 1. Only the first two implementations are vulnerable to Cross-Site Scripting.
 * 2. The third one adopts the most important security measures.
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

$app->post('/xss', function (Request $request, Response $response) use ($app) {
    $quantitiy = countUserCommentsXss($app);
    $comments_data = retrieveUserCommentsXss($app, "user_comments");

    $username = ifSetUsername($app)['username'];
    $this->get('logger')->info("User (" . $username . ")successfully entered /xss.");

    return $this->view->render($response,
        'xss.html.twig',
        [
            'method' => 'post',
            'action1' => LANDING_PAGE,
            'action2' => 'sqli',
            'action3' => 'xss',
            'action4' => 'csrf',
            'action5' => 'logout',
            'action6' => 'xssMysqli',
            'action7' => 'xssPdo',
            'action8' => 'xssDoctrine',
            'action9' => 'comment',
            'quantity' => $quantitiy,
            'comments' => $comments_data,
        ]);

})->setName('xss');


/**
 * counts xss comments
 *
 * @param $app
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function countUserCommentsXss($app)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $test = $doctrine_queries::queryCountUserCommentsXss($queryBuilder);

    return $test;
}

/**
 * retrieves xss comments from the database
 *
 * @param $app
 * @param $table
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function retrieveUserCommentsXss($app, $table)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $comments = $doctrine_queries::queryRetrieveUserCommentsXss($queryBuilder, $table);

    return $comments;
}