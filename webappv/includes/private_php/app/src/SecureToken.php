<?php
/**
 * SecureToken.php
 *
 * Class to contain the anti-csrf token generator function
 *
 * Author: Sebastian Jez
 * Email: P16180116@my365.dmu.ac.uk
 * Date: 01/03/2020
 *
 */

namespace WEBAPPV;


class SecureToken
{

    /**
     * generates a random value via the Mersenne Twister Random Number Generator
     *
     * @return string
     */
    public function generateToken()
    {
        $chars = 'abcdefghijklmnoprstwzABCDEFGHIJKLMNOPRSTWA123456789!@#$%%^&*(';
        $my_string = '';
        $length = 64;

        for ($i = 0; $i < $length; $i++) {
            $pos = mt_rand(0, strlen($chars) - 1);
            $my_string .= substr($chars, $pos, 1);
        }

        return $my_string;
    }

}