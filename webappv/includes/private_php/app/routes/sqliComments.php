<?php
/**
 * sqliComments.php
 *
 * Inserts the "sql injection" user comment into the database.
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

$app->post('/NewCommentSqli', function (Request $request, Response $response) use ($app) {

    $username = ifSetUsername($app)['username'];
    $tainted_parameters = $request->getParsedBody();
    $tainted_comment = $tainted_parameters['mainComment'];
    $cleaned_comment = cleanupComment($app, $tainted_comment);
    storeUserCommentSqli($app, $cleaned_comment, $username);

    $this->get('logger')->info("User (" . $username . ")successfully added a comment (sqli).");

    return $this->view->render($response,
        'comment_success.html.twig',
        [
            'method' => 'post',
            'action1' => LANDING_PAGE,
            'action2' => 'sqli',
            'action3' => 'xss',
            'action4' => 'csrf',
            'action5' => 'logout',
        ]);

})->setName('NewCommentSqli');

/**
 * stores new comment into the database
 *
 * @param $app
 * @param $tainted_parameters
 * @param $username
 * @throws \Doctrine\DBAL\DBALException
 */
function storeUserCommentSqli($app, $cleaned_comment, $username)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $doctrine_queries::queryStoreUserCommentSqli($queryBuilder, $cleaned_comment, $username);
}
