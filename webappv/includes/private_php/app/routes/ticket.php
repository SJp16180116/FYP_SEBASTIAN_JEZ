<?php
/**
 * ticket.php
 *
 * Display the user ticket form.
 *
 * Allows the user to create a registration ticket.
 *
 * Author: Sebastian Jez
 * Email: P16180116@my365.dmu.ac.uk
 * Date: 01/03/2020
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * @param Request $request
 * @param Response $response
 * @return mixed
 */

$app->post('/ticket', function (Request $request, Response $response) use ($app) {

    return $this->view->render($response,
        'ticket.html.twig',
        [
            'method' => 'post',
            'action1' => LANDING_PAGE,
            'action2' => 'sendTicket',
        ]);

})->setName('ticket');