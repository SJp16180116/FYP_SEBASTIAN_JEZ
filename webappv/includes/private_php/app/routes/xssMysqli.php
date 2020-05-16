<?php
/**
 * xssMysqli.php
 *
 * Demonstrate the vulnerable proof of concept.
 *
 * The first status feature is based on the MySQLi extension and does not provide any defences
 * against Cross-Site Scripting. According to the input, the web application redirects the user
 * to the appropriate subpage.
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

$app->post('/xssMysqli',
    function (Request $request, Response $response) use ($app) {

        $tainted_parameter = $request->getParsedBody();
        $info = $tainted_parameter['info'];
        $xss_test = storeAndRetrieveUserInfoMysqli($app, $info, 'john');

        $check = patternCheckJs($app, $xss_test);

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

    })->setName('xssMysqli');


/**
 * updates user status
 *
 * @param $app
 * @param $info
 * @param $username
 * @return mixed
 */
function storeAndRetrieveUserInfoMysqli($app, $info, $username)
{
    $database_wrapper = $app->getContainer()->get('DatabaseWrapper');
    $sql_queries = $app->getContainer()->get('SqlQueries');
    $auth_model = $app->getContainer()->get('MysqliAndPdo');

    $settings = $app->getContainer()->get('settings');

    $database_connection_settings = $settings['mysqli_settings'];

    $auth_model->setSqlQueries($sql_queries);
    $auth_model->setDatabaseConnectionSettings($database_connection_settings);
    $auth_model->setDatabaseWrapper($database_wrapper);

    $auth_model->updateDbMysqli($info, $username);
    $test = $auth_model->getInfoDbMysqli($username);

    return $test;
}

/**
 * detects JavaScript tags
 *
 * @param $entity
 * @return bool
 */
function patternCheckJs($app, $entity)
{
    $helper = $app->getContainer()->get('Helper');

    $outcome = $helper->patternCheck($entity);

    return $outcome;
}
