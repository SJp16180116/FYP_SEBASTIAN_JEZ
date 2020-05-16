<?php
/**
 * logout.php
 *
 * Sign out the WebAppV user.
 *
 * Unsets the following user variables:
 *
 * 1. Username
 * 2. Sid
 * 3. Role
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

$app->post('/logout',
    function (Request $request, Response $response) use ($app) {

        $username = ifSetUsername($app)['username'];
        $this->get('logger')->info("User/Admin (" . $username . ") successfully logged out.");
        unsetSessionVariables($app);
        return $this->view->render($response,
            'logout.html.twig',
            [
                'method' => 'post',
                'action' => 'login',
                'action2' => 'register',
                'action3' => 'logout',
                'username' => $username,
            ]);

    })->setName('logout');


/**
 * unsets session variables
 *
 * @param $app
 */
function unsetSessionVariables($app)
{
    $session_wrapper = $app->getContainer()->get('SessionWrapper');
    $session_wrapper->unsetSessionVar('username');
    $session_wrapper->unsetSessionVar('sid');
    $session_wrapper->unsetSessionVar('role');
}