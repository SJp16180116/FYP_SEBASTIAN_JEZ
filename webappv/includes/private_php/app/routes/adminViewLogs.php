<?php
/**
 * adminViewLogs.php
 *
 * Display the web application logs.
 *
 * Retrieves logs from "webappv/includes/logs/WebAppV_PROD_01.log".
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

$app->post('/logs',
    function (Request $request, Response $response) use ($app) {

        $session_check = sessionCheckAdmin($app);
        $username = ifSetUsername($app)['username'];
        $logs_data = retrieveLogs($app)['log_file'];

        if ($session_check == false) {
            $this->get('logger')->critical("Unprivileged user attempted to enter admin area.");
            $response = $response->withredirect(LANDING_PAGE);
            return $response;
        } else {
            $this->get('logger')->info("Admin (" . $username . ") successfully entered /logs.");
            return $this->view->render($response,
                'browse_logs.html.twig',
                [
                    'method' => 'post',
                    'action1' => LANDING_PAGE,
                    'action2' => 'sqli',
                    'action3' => 'xss',
                    'action4' => 'csrf',
                    'action5' => 'logout',
                    'username' => $username,
                    'logsdata' => $logs_data,
                ]);
        }

    })->setName('logs');

/**
 * retrieves logs from:
 * webappv/includes/logs/WebAppV_PROD_01.log
 *
 * @param $app
 * @return mixed
 */
function retrieveLogs($app)
{
    $logs = $app->getContainer()->get('Logs');

    $logs->setLogFile(LOG_PATH);

    $final_logs = $logs->readLogFile();

    return $final_logs;
}