<?php
/**
 * csrfNotVulnerable.php
 *
 * Demonstrate the secure proof of concept.
 *
 * The secure implementation utilizes the anti-csrf token generation function
 * to differentiate between a valid request generated by a user and a malicious
 * request generated by a user without their knowledge and consent.
 *
 * While a user is clicking the transfer button, the new token is generated.
 * Then, the token is saved as a session variable on the server-side.
 * At the same time, the same token is sent, along with the valid request.
 * Once the post request is received, the server-side function compares the arriving
 * token from the request with the stored token from the session variable.
 *
 * If tokens match, the server-side operation is performed.
 * On the other hand, if tokens do not match, the operation is aborted.
 *
 * After the malicious payload is generated, the user is redirected to
 * the subpage, which demonstrates defence mechanisms.
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

$app->post('/csrfNotVulnerable', function (Request $request, Response $response) use ($app) {

    $hidden_fields = $request->getParsedBody();
    $balance = $hidden_fields['balance'];
    $token = $hidden_fields['token'];
    $username = ifSetUsername($app)['username'];

    $tokens_match = compareSecureTokens($app, $token, $_SESSION['token']);

    if ($tokens_match !== true) {
        $this->get('logger')->critical("User (" . $username . ") entered /csrfNotVulnerable - tokens don't match.");

        return $this->view->render($response,
            'csrf_failure.html.twig',
            [
                'method' => 'post',
                'action' => 'csrf',
                'action1' => 'xssPdo',
                'action2' => 'sqli',
                'token' => $_SESSION['token'],
            ]);
    } else {
        updateUserBalance($app, $balance, 'adam');
        $this->get('logger')->info("User (" . $username . ") entered /csrfNotVulnerable - tokens match.");

        return $this->view->render($response,
            'csrf_success.html.twig',
            [
                'method' => 'post',
                'action' => 'csrf',
                'action1' => 'xssPdo',
                'action2' => 'sqli',
                'token' => $_SESSION['token'],
            ]);
    }

})->setName('csrfNotVulnerable');

/**
 * compares session's token with the request's token
 * @param $app
 * @param $http_request_token
 * @param $session_variable_token
 * @return bool
 */
function compareSecureTokens($app, $http_request_token, $session_variable_token)
{
    $helper = $app->getContainer()->get('Helper');

    $outcome = $helper->compareTokens($http_request_token, $session_variable_token);

    return $outcome;
}


