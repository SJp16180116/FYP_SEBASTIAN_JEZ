<?php
/**
 * sqliPdo.php
 *
 * Demonstrate the semi-vulnerable proof of concept.
 *
 * The second login system is based on the PHP Data Objects extension.
 * While the first login system does not provide any protection against
 * the SQL Injection, the second login system implements parameterized
 * queries which prevent the user from performing a successful SQL Injection attack.
 *
 * However, because both the implementation one and implementation two do not perform
 * any form of input validation or sanitization, the Cross-Site Scripting vulnerability
 * is not mitigated.
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

/**
 * @param Request $request
 * @param Response $response
 * @return Response
 */

$app->post('/sqliPdo',
    function (Request $request, Response $response) use ($app) {

        $tainted_parameters = $request->getParsedBody();
        $outcome = retrieveUserDataPdo($app, $tainted_parameters['user_name'], $tainted_parameters['password']);

        $login_db = $outcome['username'];
        $password_db = $outcome['password'];
        $sql_query = $outcome['query_string'];
        $username = $tainted_parameters['user_name'];
        $password = $tainted_parameters['password'];

        if ($login_db == 'Invalid_credentials' || $password_db == 'Invalid_credentials') {

            return $this->view->render($response,
                'sqlinjection_pdo_failure.html.twig',
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
                ]);
        } else {
            return $this->view->render($response,
                'sqlinjection_pdo_success.html.twig',
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
                ]);
        }
    })->setName('sqliPdo');


/**
 * retrieves user credentials from the database
 *
 * @param $app
 * @param $username
 * @param $password
 * @return mixed
 */
function retrieveUserDataPdo($app, $username, $password)
{
    $database_wrapper = $app->getContainer()->get('DatabaseWrapper');
    $sql_queries = $app->getContainer()->get('SqlQueries');
    $auth_model = $app->getContainer()->get('MysqliAndPdo');

    $settings = $app->getContainer()->get('settings');

    $database_connection_settings = $settings['pdo_settings'];

    $auth_model->setSqlQueries($sql_queries);
    $auth_model->setDatabaseConnectionSettings($database_connection_settings);
    $auth_model->setDatabaseWrapper($database_wrapper);

    $params = $auth_model->getParamsDb($username, $password);

    return $params;
}
