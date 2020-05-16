<?php
/**
 * DatabaseWrapper.php
 *
 * Class to access the WebAppV database.
 *
 * Includes mysqli and pdo
 *
 * Authors: CF Ingrams / Sebastian Jez
 * Email: <clinton@cfing.co.uk>
 * Email: P16180116@my365.dmu.ac.uk
 */

namespace WEBAPPV;

class DatabaseWrapper
{
    private $db_handle;
    private $sql_queries;
    private $prepared_statement;
    private $errors;
    private $database_connection_settings;

    /**
     * DatabaseWrapper constructor.
     */
    public function __construct()
    {
        $this->db_handle = null;
        $this->sql_queries = null;
        $this->prepared_statement = null;
        $this->errors = [];
    }

    /**
     * @param $database_connection_settings
     */
    public function setDatabaseConnectionSettings($database_connection_settings)
    {
        $this->database_connection_settings = $database_connection_settings;
    }


    /**
     * pdo connection
     *
     * @return string
     */
    public function makeDatabaseConnection()
    {
        $pdo_error = '';

        $database_settings = $this->database_connection_settings;
        $host_name = $database_settings['rdbms'] . ':host=' . $database_settings['host'];
        $port_number = ';port=' . '3306';
        $user_database = ';dbname=' . $database_settings['dbname'];
        $host_details = $host_name . $port_number . $user_database;
        $user_name = $database_settings['username'];
        $user_password = $database_settings['userpassword'];
        @$pdo_attributes = $database_settings['options'];

        try {
            $pdo_handle = new \PDO($host_details, $user_name, $user_password, $pdo_attributes);
            $this->db_handle = $pdo_handle;
        } catch (\PDOException $exception_object) {
            trigger_error('error connecting to database - PDO');
            $pdo_error = 'error connecting to database';
        }

        return $pdo_error;
    }

    /**
     * mysqli connection
     *
     * @return \mysqli
     */
    public function makeDatabaseConnectionMysqli()
    {
        $database_settings = $this->database_connection_settings;
        $host = $database_settings['host'];
        $user_name = $database_settings['username'];
        $user_password = $database_settings['userpassword'];
        $db_name = $database_settings['dbname'];

        try {
            $mysqli_handle = new \mysqli($host, $user_name, $user_password, $db_name);
        } catch (Exception $exception) {
            trigger_error('error connection to database - MYSQLI');
        }

        return $mysqli_handle;
    }

    /**
     * @param $query_string
     * @param null $params
     * @return mixed
     */
    public function safeQuery($query_string, $params = null)
    {
        $this->errors['db_error'] = false;
        $query_parameters = $params;

        try {
            $this->prepared_statement = $this->db_handle->prepare($query_string);
            $execute_result = $this->prepared_statement->execute($query_parameters);
            $this->errors['execute-OK'] = $execute_result;
        } catch (PDOException $exception_object) {
            $error_message = 'PDO Exception caught. ';
            $error_message .= 'Error with the database access.' . "\n";
            $error_message .= 'SQL query: ' . $query_string . "\n";
            $error_message .= 'Error: ' . var_dump($this->prepared_statement->errorInfo(), true) . "\n";
            $this->errors['db_error'] = true;
            $this->errors['sql_error'] = $error_message;
        }
        return $this->errors['db_error'];
    }

    /**
     * @param $query_string
     */
    public function notsafeQuery($query_string)
    {
        $this->errors['db_error'] = false;

        $this->prepared_statement = $this->db_handlemysqli->query($query_string);
    }


    /**
     * @return mixed
     */
    public function countRows()
    {
        $num_rows = $this->prepared_statement->rowCount();
        return $num_rows;
    }

    /**
     * @return mixed
     */
    public function safeFetchRow()
    {
        $record_set = $this->prepared_statement->fetch(\PDO::FETCH_NUM);
        return $record_set;
    }

    /**
     * @return mixed
     */
    public function safeFetchArray()
    {
        $arr_row = $this->prepared_statement->fetch(\PDO::FETCH_ASSOC);
        return $arr_row;
    }
}