<?php
/**
 * userArea.php
 *
 * Display the user area page.
 *
 * The user area presents the user with a welcome page
 * which displays the user’s name, role and navigation menu.
 *
 * Implements both the login system and session management.
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

$app->post('/userarea',
    function (Request $request, Response $response) use ($app) {

        $tainted_parameters = $request->getParsedBody();
        $tainted_username = $tainted_parameters['user_name'];
        $password = $tainted_parameters['password'];

        $cleaned_username = cleanupUsername($app, $tainted_username);
        $db_params = retrieveUsernamePasswordRole($app, $cleaned_username);
        $outcome = compareInputAndStoredPassword($app, $db_params['password'], $password);

        $sid = session_id();
        $user_role = $db_params['role'];

        if ($outcome == true) {
            doSession($app, $cleaned_username, $sid, $user_role);
        }

        $isloggedin = ifSetUsername($app)['introduction'];
        $username = ifSetUsername($app)['username'];
        $role = ifSetUsername($app)['role'];

        if ($outcome == false) {
            $this->get('logger')->info("User (" . $cleaned_username . ") provided invalid credentials during logging in.");
            return $this->view->render($response,
                'invalid_login.html.twig',
                [
                    'method' => 'post',
                    'action' => 'login',
                    'is_logged_in' => $isloggedin,
                    'username' => $username,
                ]);
        } elseif (($user_role == 'Non-technical user') || ($user_role == 'Executive') || ($user_role == 'Web Developer')) {
            $this->get('logger')->info("User (" . $username . ") provided correct credentials during logging in.");
            return $this->view->render($response,
                'homepageformsuccess.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'is_logged_in' => $isloggedin,
                    'username' => $username,
                    'role' => $role,
                ]);
        } else {
            $response = $response->withredirect(LANDING_PAGE . '/adminArea');
            return $response;
        }

    })->setName('userarea');


/**
 * retrieves username from the database
 *
 * @param $app
 * @param $username
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function retrieveUsernamePasswordRole($app, $username)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $params = $doctrine_queries::queryRetrieveUsernamePasswordRole($queryBuilder, $username);

    return $params;
}


/**
 * compares input password with hashed password stored in the database
 *
 * @param $app
 * @param $db_pass
 * @param $typed_pass
 * @return bool
 */
function compareInputAndStoredPassword($app, $db_pass, $typed_pass)
{
    if ($db_pass == 'Invalid_credentials') {
        $passwordCheck = false;
    } else {

        $compare = $app->getContainer()->get('bcryptWrapper');
        $passwordCheck = $compare->authenticatePassword($typed_pass, $db_pass);
    }

    if ($passwordCheck == true) {
        $outcome = true;
    } else {
        $outcome = false;
    }

    return $outcome;
}

/**
 * sets session variables
 *
 * @param $app
 * @param $username
 * @param $sid
 * @param $role
 * @return array
 */
function doSession($app, $username, $sid, $role)
{
    $session_wrapper = $app->getContainer()->get('SessionWrapper');
    $session_model = $app->getContainer()->get('SessionModel');

    $session_model->setSessionUsername($username);
    $session_model->setSessionId($sid);
    $session_model->setSessionRole($role);
    $session_model->setSessionWrapperFile($session_wrapper);
    $session_model->storeData();

    $store_var = array($session_wrapper->getSessionVar('username'),
        $session_wrapper->getSessionVar('sid'),
        $session_wrapper->getSessionVar('role'));

    return $store_var;
}

/**
 * checks if session variable exist
 *
 * @param $app
 * @return mixed
 */
function ifSetUsername($app)
{

    $session_wrapper = $app->getContainer()->get('SessionWrapper');
    $username = $session_wrapper->getSessionVar('username');
    $sid = $session_wrapper->getSessionVar('sid');
    $role = $session_wrapper->getSessionVar('role');

    if (!empty($username) || !empty($sid) || !empty($role)) {
        $result['introduction'] = 'You are logged in as ';
        $result['username'] = $username;
        $result['role'] = $role;
    } else {
        $result['introduction'] = 'Log in to see WebAppV project';
        $result['username'] = '';
        $result['role'] = '';
    }
    return $result;
}

/**
 * checks if session variable exists
 *
 * @param $app
 * @return bool
 */
function sessionCheck($app)
{
    $session_wrapper = $app->getContainer()->get('SessionWrapper');

    $sessionUsernameSet = $session_wrapper->getSessionVar('username');
    $sessionPasswordSet = $session_wrapper->getSessionVar('role');
    $sessionIdSet = $session_wrapper->getSessionVar('sid');

    if ($sessionUsernameSet == false && $sessionPasswordSet == false && $sessionIdSet == false) {
        $check = false;
    } else {
        $check = true;
    }
    return $check;
}