<?php
/**
 * SqlQueries.php
 *
 * Class to contain sql queries of the following proofs of concepts:
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

class SqlQueries
{


    /**
     * @param $username
     * @param $password
     * @return string
     */
    public function getUsernamePassword($username, $password)
    {
        $query_string = "SELECT * ";
        $query_string .= "FROM bank ";
        $query_string .= "WHERE username='$username' ";
        $query_string .= "AND password='$password'";
        return $query_string;
    }

    /**
     * @return string
     */
    public function getUsernamePasswordRole()
    {
        $query_string = "SELECT * ";
        $query_string .= "FROM bank ";
        $query_string .= "WHERE username = :username ";
        $query_string .= "AND password = :password;";
        return $query_string;
    }


    /**
     * @param $info
     * @param $username
     * @return string
     */
    public function updateInfo($info, $username)
    {
        $query_string = "UPDATE bank ";
        $query_string .= "SET info = '$info' ";
        $query_string .= "WHERE username = '$username'";
        return $query_string;
    }

    /**
     * @return string
     */
    public function updateInfoPdo()
    {
        $query_string = "UPDATE bank ";
        $query_string .= "SET info = :info ";
        $query_string .= "WHERE username = :username;";
        return $query_string;
    }


    /**
     * @param $username
     * @return string
     */
    public function getInfo($username)
    {
        $query_string = "SELECT info ";
        $query_string .= "FROM bank ";
        $query_string .= "WHERE username = '$username'";
        return $query_string;
    }

    /**
     * @return string
     */
    public function getInfoPdo()
    {
        $query_string = "SELECT info ";
        $query_string .= "FROM bank ";
        $query_string .= "WHERE username = :username;";
        return $query_string;
    }

}