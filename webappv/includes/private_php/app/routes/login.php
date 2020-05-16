<?php
/**
 * login.php
 *
 * Display the login page.
 *
 * Allows the user to sign in using an existing account.
 *
 * Author: Sebastian Jez
 * Email: P16180116@my365.dmu.ac.uk
 * Date: 01/03/2020
 *
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * @param Request $request
 * @param Response $response
 * @return mixed
 */

$app->post('/login', function (Request $request, Response $response) use ($app) {

    $this->get('logger')->info("Unlogged user entered /login.");
    return $this->view->render($response,
        'login.html.twig',
        [
            'method' => 'post',
            'action' => 'userarea',
        ]);

})->setName('login');