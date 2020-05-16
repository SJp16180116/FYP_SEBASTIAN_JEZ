<?php
/**
 * adminArea.php
 *
 * Display the admin area page.
 *
 * Allows the system administrator to perform basic operations
 * such as user creation, ticket management and log review.
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

$app->get('/adminArea',
    function (Request $request, Response $response) use ($app) {

        $username = ifSetUsername($app)['username'];
        $role = ifSetUsername($app)['role'];
        $session_check = sessionCheckAdmin($app);

        if ($session_check == false) {
            $this->get('logger')->critical("Unprivileged user attempted to enter admin area.");
            $response = $response->withredirect(LANDING_PAGE);
            return $response;
        } else {
            $this->get('logger')->info("Admin (" . $username . ") logged in successfully or already logged in.");
            return $this->view->render($response,
                'admin_area.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'action6' => 'viewTickets',
                    'action7' => 'adminRegister',
                    'action8' => 'logs',
                    'username' => $username,
                    'role' => $role,
                ]);
        }

    })->setName('adminArea');

/**
 * prevents unauthorized access
 *
 * @param $app
 * @return bool
 */
function sessionCheckAdmin($app)
{
    $session_wrapper = $app->getContainer()->get('SessionWrapper');

    //getSessionVar() returns 'false' if session variable is not set
    $sessionRoleSet = $session_wrapper->getSessionVar('role');

    if ($sessionRoleSet !== 'admin') {
        $check = false;
    } else {
        $check = true;
    }
    return $check;
}