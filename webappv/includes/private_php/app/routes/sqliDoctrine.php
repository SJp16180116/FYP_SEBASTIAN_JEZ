<?php
/**
 * sqliDoctrine.php
 *
 * Demonstrate the secure proof of concept.
 *
 * The third "SQLI PoC" is based on the Doctrine Database Abstraction Layer (DBAL)
 * on the top of the PHP Data Objects API. In contrast to the first two implementations,
 * the third implementation is not vulnerable to SQL Injection and Cross-Site Scripting
 * attacks. The usage of the input sanitization with addition to the parameterized queries
 * provides robust protection against SQLI and XSS attacks.
 *
 * If the user’s attempt to perform SQL Injection is incorrect,
 * the script detects the unsuccessful attempt and redirects
 * the user to the “unsuccessful attempt” subpage.
 *
 * On the other hand, if the user attempt is correct,
 * the script redirects the user to the “successful attempt” subpage.
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

$app->post('/sqliDoctrine',
    function (Request $request, Response $response) use ($app) {

        $tainted_parameters = $request->getParsedBody();
        $clean_username = cleanupString($app, $tainted_parameters['user_name']);
        $clean_password = cleanupString($app, $tainted_parameters['password']);
        $outcome = retrieveUsernamePassword($app, $clean_username, $clean_password);
        $login_db = $outcome['username'];
        $password_db = $outcome['password'];
        $username = $tainted_parameters['user_name'];
        $password = $tainted_parameters['password'];
        $sql_query = $outcome['sql'];

        if ($login_db == 'Invalid_credentials' || $password_db == 'Invalid_credentials') {

            return $this->view->render($response,
                'sqlinjection_doctrine_failure.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'sql_query' => $sql_query,
                    'username' => $username,
                    'password' => $password,
                    'clean_username' => $clean_username,
                    'clean_password' => $clean_password,
                ]);
        } else {
            return $this->view->render($response,
                'sqlinjection_doctrine_success.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'sql_query' => $sql_query,
                    'username' => $username,
                    'password' => $password,
                    'clean_username' => $clean_username,
                    'clean_password' => $clean_password,
                ]);
        }
    })->setName('sqliDoctrine');


/**
 * retrieves user credentials from the database
 *
 * @param $app
 * @param $username
 * @param $password
 * @return mixed
 * @throws \Doctrine\DBAL\DBALException
 */
function retrieveUsernamePassword($app, $username, $password)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $params = $doctrine_queries::queryRetrieveUsernamePassword($queryBuilder, $username, $password);

    return $params;
}



