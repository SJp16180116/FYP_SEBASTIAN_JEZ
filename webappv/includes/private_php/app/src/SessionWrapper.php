<?php
/**
 * SessionWrapper.php
 *
 * Class to create a wrapper for the session global array
 *
 * Author: CF Ingrams
 * Email: <clinton@cfing.co.uk>
 */

namespace WEBAPPV;

class SessionWrapper
{
    /**
     * @param $session_key
     * @param $session_value_to_set
     * @return bool
     */
    public function setSessionVar($session_key, $session_value_to_set)
    {
        $session_value_set_successfully = false;
        if (!empty($session_value_to_set)) {
            $_SESSION[$session_key] = $session_value_to_set;
            if (strcmp($_SESSION[$session_key], $session_value_to_set) == 0) {
                $session_value_set_successfully = true;
            }
        }
        return $session_value_set_successfully;
    }

    /**
     * @param $session_key
     * @return bool|mixed
     */
    public function getSessionVar($session_key)
    {
        $session_value = false;

        if (isset($_SESSION[$session_key])) {
            $session_value = $_SESSION[$session_key];
        }
        return $session_value;
    }

    /**
     * @param $session_key
     * @return bool
     */
    public function unsetSessionVar($session_key)
    {
        $unset_session = false;
        if (isset($_SESSION[$session_key])) {
            unset($_SESSION[$session_key]);
        }
        if (!isset($_SESSION[$session_key])) {
            $unset_session = true;
        }
        return $unset_session;
    }
}