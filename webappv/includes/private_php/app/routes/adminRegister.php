<?php
/**
 * adminRegister.php
 *
 * Display the user registration feature.
 *
 * Allows the admin to create new user accounts.
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
 * @return Response
 */

$app->post('/adminRegister',
    function (Request $request, Response $response) use ($app) {

        $session_check = sessionCheckAdmin($app);
        $secure_token = generateSecureToken($app);
        $_SESSION['token'] = $secure_token;

        if ($session_check == false) {
            $this->get('logger')->critical("Unprivileged user attempted to enter admin area.");
            $response = $response->withredirect(LANDING_PAGE);
            return $response;
        } else {
            $this->get('logger')->info("Admin successfully entered /adminRegister .");
            return $this->view->render($response,
                'register.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'action6' => 'adminRegisterUser',
                    'action7' => 'adminRegister',
                    'token' => $_SESSION['token'],
                ]);
        }

    })->setName('adminRegister');

