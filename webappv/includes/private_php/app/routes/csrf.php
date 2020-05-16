<?php
/**
 * csrf.php
 *
 * Display the cross-site request forgery page.
 *
 * The CSRF page begins with a brief description of the attack
 * and primary defences to introduce the user to the vulnerability.
 *
 * Allows the user to simulate malicious payload.
 * Contains two different implementations of the banking system:
 *
 * 1. Only the first one is vulnerable to Cross-Site Request Forgery.
 * 2. The second one adopts the most important security measures.
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

$app->post('/csrf', function (Request $request, Response $response) use ($app) {

    $quantitiy = countUserCommentsCsrf($app);
    $comments_data = retrieveUserCommentsCsrf($app, "user_comments");

    $account_details = retrieveUserData($app, 'adam');
    $balance = $account_details[1];
    $account = $account_details[2];
    $sortcode = $account_details[3];
    $username = ifSetUsername($app)['username'];

    $secure_token = generateSecureToken($app);
    $_SESSION['token'] = $secure_token;

    $this->get('logger')->info("User (" . $username . ")successfully entered /csrf.");

    return $this->view->render($response,
        'csrf.html.twig',
        [
            'method' => 'post',
            'action1' => LANDING_PAGE,
            'action2' => 'sqli',
            'action3' => 'xss',
            'action4' => 'csrf',
            'action5' => 'logout',
            'action6' => 'csrfAttack',
            'action7' => 'csrfNotVulnerable',
            'action8' => 'newCommentCsrf',
            'balance' => $balance,
            'account' => $account,
            'sortcode' => $sortcode,
            'token' => $secure_token,
            'quantity' => $quantitiy,
            'comments' => $comments_data,
        ]);

})->setName('csrf');

/**
 * retrieves info from the database
 *
 * @param $app
 * @param $username
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function retrieveUserData($app, $username)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $test = $doctrine_queries::queryRetrieveUserBalanceAccountSortCode($queryBuilder, $username);

    return $test;
}

/**
 * generates secure token
 *
 * @param $app
 * @return mixed
 */
function generateSecureToken($app)
{
    $token = $app->getContainer()->get('SecureToken');

    $test = $token::generateToken();

    return $test;
}

/**
 * counts comments
 *
 * @param $app
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function countUserCommentsCsrf($app)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $test = $doctrine_queries::queryCountUserCommentsCsrf($queryBuilder);

    return $test;
}

/**
 * retrieves csrf comments from the database
 *
 * @param $app
 * @param $table
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function retrieveUserCommentsCsrf($app, $table)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $comments = $doctrine_queries::queryRetrieveUserCommentsCsrf($queryBuilder, $table);

    return $comments;
}

