<?php
/**
 * adminViewTickets.php
 *
 * Display the user tickets.
 *
 * Retrieves the user tickets from the database.
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

$app->post('/viewTickets',
    function (Request $request, Response $response) use ($app) {

        $session_check = sessionCheckAdmin($app);
        $tickets = retrieveTicket($app);

        if ($session_check == false) {
            $this->get('logger')->critical("Unprivileged user attempted to enter admin area.");
            $response = $response->withredirect(LANDING_PAGE);
            return $response;
        } else {
            $this->get('logger')->info("Admin successfully entered /viewTickets.");
            return $this->view->render($response,
                'view_tickets.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'tickets' => $tickets,
                ]);
        }

    })->setName('adminArea');

/**
 * retrieves users' tickets from the database
 *
 * @param $app
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function retrieveTicket($app)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $tickets = $doctrine_queries::queryRetrieveUserTickets($queryBuilder);

    return $tickets;
}

