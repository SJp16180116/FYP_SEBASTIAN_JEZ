<?php
/**
 * SettingsModel.php
 *
 * Author: CF Ingrams
 * Email: <clinton@cfing.co.uk>
 */

namespace WEBAPPV;

class SettingsModel
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

    public function makeConnection()
    {

        $this->database_wrapper->setDatabaseConnectionSettings($this->database_connection_settings);

        $this->database_wrapper->makeDatabaseConnection();

    }
}