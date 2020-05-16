<?php
/**
 * xssPdo.php
 *
 * Demonstrate the vulnerable proof of concept.
 *
 * The second status feature is based on the PDO extension
 * and does not provide any defences against Cross-Site Scripting.
 * However, the main difference between the first and second implementation
 * is that the former is also vulnerable to SQL Injection, and the latter is not.
 * This is because the second version implements prepared statements which
 * prevent the SQL Injection attack.
 *
 * If the user’s attempt to perform Cross-Site Scripting is incorrect,
 * the server-side script detects the unsuccessful attempt and redirects
 * the user to the “unsuccessful attempt” subpage.
 *
 * If the user’s attempt is correct, the server redirects
 * the user to the “successful attempt” subpage.
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

$app->post('/xssPdo',
    function (Request $request, Response $response) use ($app) {

        $tainted_parameter = $request->getParsedBody();
        $info = $tainted_parameter['info1'];
        $xss_test = storeAndRetrieveUserInfoPdo($app, $info, 'john');

        $check = patternCheck($xss_test);

        if ($check !== true) {

            return $this->view->render($response,
                'xss_failure.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'info' => $info,
                ]);
        } else {
            echo $xss_test;

            return $this->view->render($response,
                'xss_success.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'info' => $info,
                ]);
        }

    })->setName('xssPdo');


/**
 * updates user status
 *
 * @param $app
 * @param $info
 * @param $username
 * @return mixed
 */
function storeAndRetrieveUserInfoPdo($app, $info, $username)
{
    $database_wrapper = $app->getContainer()->get('DatabaseWrapper');
    $sql_queries = $app->getContainer()->get('SqlQueries');
    $auth_model = $app->getContainer()->get('MysqliAndPdo');

    $settings = $app->getContainer()->get('settings');

    $database_connection_settings = $settings['pdo_settings'];

    $auth_model->setSqlQueries($sql_queries);
    $auth_model->setDatabaseConnectionSettings($database_connection_settings);
    $auth_model->setDatabaseWrapper($database_wrapper);

    $auth_model->updateDbPdo($info, $username);
    $test = $auth_model->getInfoDbPdo($username);

    return $test;
}
