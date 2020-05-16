<?php
/**
 * sqliMysqli.php
 *
 * Demonstrate the vulnerable proof of concept.
 *
 * The first login system is based on the MySQLi extension and does not
 * provide any defences against SQL Injection attack. Based on the input,
 * the web application redirects the user to the appropriate subpage.
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

$app->post('/sqliMysqli',
    function (Request $request, Response $response) use ($app) {

        $tainted_parameters = $request->getParsedBody();
        $outcome = retrieveUserDataMysqli($app, $tainted_parameters['user_name'], $tainted_parameters['password']);
        $login_db = $outcome['username'];
        $password_db = $outcome['password'];
        $sql_query = $outcome['query_string'];

        if ($login_db == 'Invalid_credentials' || $password_db == 'Invalid_credentials') {

            return $this->view->render($response,
                'sqlinjection_failure.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'sql_query' => $sql_query,
                ]);
        } else {
            return $this->view->render($response,
                'sqlinjection_success.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'sql_query' => $sql_query,
                ]);
        }
    })->setName('sqliMysqli');


/**
 * retrieves user credentials from the database
 *
 * @param $app
 * @param $username
 * @param $password
 * @return mixed
 */
function retrieveUserDataMySqli($app, $username, $password)
{
    $database_wrapper = $app->getContainer()->get('DatabaseWrapper');
    $sql_queries = $app->getContainer()->get('SqlQueries');
    $auth_model = $app->getContainer()->get('MysqliAndPdo');

    $settings = $app->getContainer()->get('settings');

    $database_connection_settings = $settings['mysqli_settings'];

    $auth_model->setSqlQueries($sql_queries);
    $auth_model->setDatabaseConnectionSettings($database_connection_settings);
    $auth_model->setDatabaseWrapper($database_wrapper);

    $params = $auth_model->getParamsDbMysqli($username, $password);

    return $params;

}


