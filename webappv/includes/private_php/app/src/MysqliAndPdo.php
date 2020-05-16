<?php
/**
 * MysqliAndPdo.php
 *
 * Class to contain the database access of the following proofs of concepts:
 *
 * 1. xssMysqli.php
 * 2. xssPdo.php
 * 3. sqliMysqli.php
 * 4. sqliPdo.php
 *
 * Author: Sebastian Jez
 * Email: P16180116@my365.dmu.ac.uk
 * Date: 01/03/2020
 *
 */

namespace WEBAPPV;


class MysqliAndPdo
{
    private $database_wrapper;
    private $database_connection_settings;
    private $sql_queries;

    /**
     * @param $database_wrapper
     */
    public function setDatabaseWrapper($database_wrapper)
    {
        $this->database_wrapper = $database_wrapper;
    }


    /**
     * @param $database_connection_settings
     */
    public function setDatabaseConnectionSettings($database_connection_settings)
    {
        $this->database_connection_settings = $database_connection_settings;
    }


    /**
     * @param $sql_queries
     */
    public function setSqlQueries($sql_queries)
    {
        $this->sql_queries = $sql_queries;
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     */
    public function getParamsDb($username, $password)
    {
        $query_string = $this->sql_queries->getUsernamePasswordRole();
        $params['query_string'] = $query_string;

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();

        $query_parameters = array(':username' => $username, ':password' => $password);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);

        if ($row = $this->database_wrapper->safeFetchArray()) {
            $params['username'] = $row['username'];
            $params['password'] = $row['password'];
        } else {
            $params['username'] = 'Invalid_credentials';
            $params['password'] = 'Invalid_credentials';
        }
        return $params;
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     */
    public function getParamsDbMysqli($username, $password)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $query_string = $this->sql_queries->getUsernamePassword($username, $password);
        $params['query_string'] = $query_string;
        $conn = $this->database_wrapper->makeDatabaseConnectionMysqli();

        if ($conn->connect_errno != 0) {
            echo "Error:" . $conn->connect_errno;
        } else {

            $result = $conn->query($query_string);
            $response = $result->num_rows;
            $row = $result->fetch_assoc();

            if ($response > 0) {
                $params['username'] = $row['username'];
                $params['password'] = $row['password'];
            } else {
                $params['username'] = 'Invalid_credentials';
                $params['password'] = 'Invalid_credentials';
            }

            return $params;
        }
    }

    /**
     * @param $info
     * @param $username
     */
    public function updateDbMysqli($info, $username)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $query_string = $this->sql_queries->updateInfo($info, $username);

        $conn = $this->database_wrapper->makeDatabaseConnectionMysqli();

        if ($conn->connect_errno != 0) {
            echo "Error:" . $conn->connect_errno;
        } else {

            $conn->query($query_string);
        }
    }

    /**
     * @param $info
     * @param $username
     */
    public function updateDbPdo($info, $username)
    {
        $query_string = $this->sql_queries->updateInfoPdo();

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();

        $query_parameters = array(':info' => $info, ':username' => $username);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);

    }

    /**
     * @param $username
     * @return string
     */
    public function getInfoDbMysqli($username)
    {
        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $query_string = $this->sql_queries->getInfo($username);

        $conn = $this->database_wrapper->makeDatabaseConnectionMysqli();

        if ($conn->connect_errno != 0) {
            echo "Error:" . $conn->connect_errno;
        } else {

            $result = $conn->query($query_string);
            $response = $result->num_rows;
            $row = $result->fetch_assoc();

            if ($response > 0) {
                $info = $row['info'];
            } else {
                $info = 'Invalid_credentials';
            }

            return $info;
        }
    }

    /**
     * @param $username
     * @return mixed
     */
    public function getInfoDbPdo($username)
    {
        $query_string = $this->sql_queries->getInfoPdo();

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);
        $this->database_wrapper->makeDatabaseConnection();

        $query_parameters = array(':username' => $username);
        $this->database_wrapper->safeQuery($query_string, $query_parameters);

        if ($row = $this->database_wrapper->safeFetchArray()) {
            $info = $row['info'];
        }

        return $info;

    }
}