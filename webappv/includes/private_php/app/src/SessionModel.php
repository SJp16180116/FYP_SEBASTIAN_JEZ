<?php
/**
 * SessionModel.php
 *
 * Class to store validated values in the relevant storage locations
 *
 * Authors: CF Ingrams / Sebastian Jez
 * Email: <clinton@cfing.co.uk>
 * Email: P16180116@my365.dmu.ac.uk
 *
 */

namespace WEBAPPV;


class SessionModel
{
    private $username;
    private $password;
    private $storage_result;
    private $session_wrapper_file;

    public function __construct()
    {
        $this->username = null;
        $this->password = null;
        $this->sid = null;
        $this->storage_result = null;
        $this->session_wrapper_file = null;
    }

    public function __destruct()
    {

    }

    /**
     * @return null
     */
    public function getStorageResult()
    {
        return $this->storage_result;
    }

    /**
     * @param $username
     */
    public function setSessionUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param $password
     */
    public function setSessionPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param $sid
     */
    public function setSessionId($sid)
    {
        $this->sid = $sid;
    }

    /**
     * @param $role
     */
    public function setSessionRole($role)
    {
        $this->role = $role;
    }

    /**
     * @param $session_wrapper
     */
    public function setSessionWrapperFile($session_wrapper)
    {
        $this->session_wrapper_file = $session_wrapper;
    }

    /**
     * @return bool
     */
    public function storeData()
    {
        $store_result = false;
        $store_result_username
            = $this->session_wrapper_file->setSessionVar('username', $this->username);

        $store_result_password
            = $this->session_wrapper_file->setSessionVar('password', $this->password);

        $store_result_sid
            = $this->session_wrapper_file->setSessionVar('sid', $this->sid);

        $store_result_role
            = $this->session_wrapper_file->setSessionVar('role', $this->role);

        if ($store_result_username !== false && $store_result_password !== false && $store_result_sid !== false && $store_result_role !== false) {
            $store_result = true;
        }
        return $store_result;
    }

}