<?php
/**
 * homepage.php
 *
 * Display the web application vulnerabilities homepage.
 *
 * Allows the user to either sign in using an existing account
 * or to create a ticket in order to request a new account.
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

$app->map(['GET', 'POST'], '/', function (Request $request, Response $response) use ($app) {

    $isloggedin = ifSetUsername($app)['introduction'];
    $username = ifSetUsername($app)['username'];
    $role = ifSetUsername($app)['role'];

    $result = sessionCheck($app);

    if (($result == true) && (($role == 'Non-technical user') || ($role == 'Executive') || ($role == 'Web Developer'))) {
        $this->get('logger')->info("User (" . $username . ") already logged in, login page => home page.");
        return $this->view->render($response,
            'homepageformsuccess.html.twig',
            [
                'method' => 'post',
                'action1' => '',
                'action2' => 'sqli',
                'action3' => 'xss',
                'action4' => 'csrf',
                'action5' => 'logout',
                'is_logged_in' => $isloggedin,
                'username' => $username,
                'role' => $role,
            ]);
    } elseif ($role == 'admin') {
        $this->get('logger')->info("Admin redirected to /adminArea.");
        $response = $response->withredirect(LANDING_PAGE . '/adminArea');
        return $response;
    } else {
        $this->get('logger')->info("Unlogged user redirected to the homepage");
        return $this->view->render($response,
            'homepageform.html.twig',
            [
                'method' => 'post',
                'action' => 'login',
                'action2' => 'ticket',
                'action3' => 'logout',
                'is_logged_in' => $isloggedin,
                'username' => $username,
            ]);
    }

})->setName('homepage');


