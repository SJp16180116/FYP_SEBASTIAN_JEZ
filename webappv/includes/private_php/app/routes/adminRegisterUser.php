<?php
/**
 * adminRegisterUser.php
 *
 * Store new user data.
 *
 * Performs multiple checks and inserts cleaned data into the database.
 *
 * Author: Sebastian Jez
 * Email: P16180116@my365.dmu.ac.uk
 * Date: 01/03/2020
 */

use Slim\Http\Request;
use Slim\Http\Response;
use Doctrine\DBAL\DriverManager;

/**
 * @param Request $request
 * @param Response $response
 * @return mixed
 */

$app->post('/adminRegisterUser',
    function (Request $request, Response $response) use ($app) {

        $tainted_parameters = $request->getParsedBody();
        $token = $tainted_parameters['token'];
        $tokens_match = compareSecureTokens($app, $token, $_SESSION['token']);

        $cleaned_parameters = cleanupParameters($app, $tainted_parameters);
        $hashed_password = hashPasswordBcrypt($app, $cleaned_parameters['sanitised_password']);

        $session_check = sessionCheckAdmin($app);
        $username_count = checkDuplicateUsername($app, $cleaned_parameters);
        $username_error = usernameCheck($cleaned_parameters['sanitised_username']);
        $password_error = passwordCheck($cleaned_parameters['sanitised_password']);

        if ($username_count == 0 && $username_error == false && $password_error == false && $session_check == true) {

            if ($tokens_match == true) {
                $this->get('logger')->info("New user successfully registered: " . $cleaned_parameters['sanitised_username']);
                storeUserDetailsAdmin($app, $cleaned_parameters, $hashed_password);
            }
            return $this->view->render($response,
                'register_user_result.html.twig',
                [
                    'method' => 'post',
                    'action' => LANDING_PAGE,
                    'sanitised_username' => $cleaned_parameters['sanitised_username'],
                    'sanitised_email' => $cleaned_parameters['sanitised_email'],
                ]);

        } elseif ($session_check == true) {
            $this->get('logger')->info("Registration process failed because the conditions were not met");
            if ($username_count !== 0) {
                $username_duplicate = 'Your username already exists in our database.';
            } else {
                $username_duplicate = false;
            }
            return $this->view->render($response,
                'register_error.html.twig',
                [
                    'action' => 'adminRegister',
                    'username_error' => $username_error,
                    'password_error' => $password_error,
                    'username_duplicate' => $username_duplicate,
                ]);

        } else {
            $this->get('logger')->critical("Unprivileged user attempted to enter admin area.");
            $response = $response->withredirect(LANDING_PAGE);
            return $response;
        }

    })->setName('register');

/**
 * inserts user details into a database
 *
 * @param $app
 * @param $cleaned_parameters
 * @param $hashed_password
 * @throws \Doctrine\DBAL\DBALException
 */
function storeUserDetailsAdmin($app, $cleaned_parameters, $hashed_password)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $doctrine_queries::queryStoreUserData($queryBuilder, $cleaned_parameters, $hashed_password);
}

/**
 * checks if username already exists
 *
 * @param $app
 * @param $cleaned_parameters
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function checkDuplicateUsername($app, $cleaned_parameters)
{
    $cleaned_username = $cleaned_parameters['sanitised_username'];
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $username_count = $doctrine_queries::queryRetrieveUsername($queryBuilder, $cleaned_username);

    return $username_count;
}

/**
 * hashes password using bcrypt algorithm
 *
 * @param $app
 * @param $password_to_hash
 * @return mixed
 */
function hashPasswordBcrypt($app, $password_to_hash)
{
    $bcrypt_wrapper = $app->getContainer()->get('bcryptWrapper');
    $hashed_password = $bcrypt_wrapper->createHashedPassword($password_to_hash);
    return $hashed_password;
}

/**
 * performs username validation
 *
 * @param $username
 * @return string
 */
function usernameCheck($username)
{
    $error = '';

    if ((strlen($username) <= 4) || (strlen($username) >= 15)) {
        $error = 'Your username must be at least 5 and maximum 15 characters long.';
    } elseif (ctype_alnum($username) == false) {
        $error = 'Your username must contain only letters and digits.';
    }

    return $error;
}

/**
 * performs password validation
 *
 * @param $password
 * @return string
 */
function passwordCheck($password)
{
    $error = '';

    if ((strlen($password)) <= 8) {
        $error = "Your password must contain at least 8 characters.";
    } elseif (!preg_match("#[0-9]+#", $password)) {
        $error = "Your password must contain at least 1 number.";
    } elseif (!preg_match("#[A-Z]+#", $password)) {
        $error = "Your password must contain at least 1 capital letter.";
    } elseif (!preg_match("#[a-z]+#", $password)) {
        $error = "Your password must contain at least 1 lowercase letter.";
    }

    return $error;
}

/**
 * performs sanitization of user's parameters
 *
 * @param $app
 * @param $tainted_parameters
 * @return array
 */
function cleanupParameters($app, $tainted_parameters)
{
    $cleaned_parameters = [];
    $validator = $app->getContainer()->get('Validator');

    $tainted_username = $tainted_parameters['username'];
    $tainted_email = $tainted_parameters['email'];
    $tainted_role = $tainted_parameters['role'];
    $tainted_password = $tainted_parameters['password'];

    $cleaned_parameters['sanitised_role']
        = $validator->sanitiseString($tainted_role);

    $cleaned_parameters['sanitised_password']
        = $validator->sanitiseString($tainted_password);

    $cleaned_parameters['sanitised_username']
        = $validator->sanitiseString($tainted_username);

    $cleaned_parameters['sanitised_email']
        = $validator->sanitiseEmail($tainted_email);

    return $cleaned_parameters;
}