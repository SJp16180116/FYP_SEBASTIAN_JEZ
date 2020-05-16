<?php
/**
 * csrfVulnerable.php
 *
 * Demonstrate the vulnerable proof of concept.
 *
 * The vulnerable implementation does not employ any protection against
 * Cross-Site Request Forgery. As a result, the insecure implementation
 * cannot differentiate between a valid request generated by a user,
 * and a malicious request.
 *
 * Proof of concept allows the user to simulate malicious payload by
 * sending an HTTP POST request containing multiple hidden fields.
 *
 * After the malicious payload is generated, the user is redirected to
 * the subpage, which provides a detailed explanation of the attack.
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
 * @return mixed
 */

$app->post('/csrfVulnerable', function (Request $request, Response $response) use ($app) {

    $hidden_fields = $request->getParsedBody();
    $balance = $hidden_fields['balance'];
    updateUserBalance($app, $balance, 'adam');
    $username = ifSetUsername($app)['username'];

    $this->get('logger')->info("User (" . $username . ") entered /csrfAttack.");

    return $this->view->render($response,
        'csrf_attack.html.twig',
        [
            'method' => 'post',
            'action1' => LANDING_PAGE,
            'action2' => 'sqli',
            'action3' => 'xss',
            'action4' => 'csrf',
            'action5' => 'logout',
        ]);

})->setName('csrfVulnerable');

/**
 * queries the database to update account balance
 *
 * @param $app
 * @param $balance
 * @param $username
 * @throws \Doctrine\DBAL\DBALException
 */
function updateUserBalance($app, $balance, $username)
{
    $database_connection_settings = $app->getContainer()->get('settings');
    $doctrine_queries = $app->getContainer()->get('doctrineSqlQueries');
    $database_connection = DriverManager::getConnection($database_connection_settings['doctrine_settings']);

    $queryBuilder = $database_connection->createQueryBuilder();

    $doctrine_queries::queryUpdateUserBalance($queryBuilder, $balance, $username);

}