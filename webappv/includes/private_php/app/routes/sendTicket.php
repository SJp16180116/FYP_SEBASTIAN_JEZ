<?php
/**
 * sendTicket.php
 *
 * Display the user registration ticket feature.
 *
 * Sanitizes and processes user ticket.
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

$app->post('/sendTicket', function (Request $request, Response $response) use ($app) {

    $tainted_ticket = $request->getParsedBody();
    $tainted_ticket = $tainted_ticket['ticket'];

    $cleaned_ticket = cleanupString($app, $tainted_ticket);

    storeUserTicket($app, $cleaned_ticket);

    return $this->view->render($response,
        'ticket.html.twig',
        [
            'method' => 'post',
            'action1' => LANDING_PAGE,
            'action2' => 'sendTicket',
        ]);

})->setName('sendTicket');

/**
 * inserts user ticket into the database
 *
 * @param $app
 * @param $ticket
 * @throws \Doctrine\DBAL\DBALException
 */
function storeUserTicket($app, $ticket)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $doctrine_queries::queryStoreUserTicket($queryBuilder, $ticket);
}

/**
 * sanitizes user input
 *
 * @param $app
 * @param $tainted_string
 * @return mixed
 */
function cleanupString($app, $tainted_string)
{
    $validator = $app->getContainer()->get('Validator');

    $cleaned_string = $validator->sanitiseString($tainted_string);

    return $cleaned_string;
}