<?php
/**
 * Helper.php
 *
 * Class to contain all helper functions.
 *
 * Author: Sebastian Jez
 * Email: P16180116@my365.dmu.ac.uk
 * Date: 01/03/2020
 */

namespace WEBAPPV;


class Helper
{
    /**
     * helps to read log files
     *
     * @param $string
     * @param $start
     * @param $end
     * @return bool|string
     */
    public function getTheValue($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * compares secure tokens
     *
     * @param $http_request_token
     * @param $session_variable_token
     * @return bool
     */
    function compareTokens($http_request_token, $session_variable_token)
    {
        if ($http_request_token !== $session_variable_token) {
            $outcome = false;
        } else {
            $outcome = true;
        }
        return $outcome;
    }

    /**
     * detects JavaScript tags
     *
     * @param $entity
     * @return bool
     */
    function patternCheck($entity)
    {
        if (strpos($entity, '<script>') !== false) {
            return true;
        } else {
            return false;
        }
    }
}