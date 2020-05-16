<?php
/**
 * Doctrine.php
 *
 * Class to contain all database access using Doctrine's QueryBuilder.
 *
 * This web application is based on the Doctrines's QueryBuilder.
 * (apart from xssMysqli.php, xssPdo.php, sqliMysqli.php, sqliPdo.php POCs)
 *
 * More information about the QueryBuilder:
 * https://www.doctrine-project.org/projects/doctrine-orm/en/2.7/reference/query-builder.html
 *
 * Author: Sebastian Jez
 * Email: P16180116@my365.dmu.ac.uk
 * Date: 01/03/2020
 */

namespace WEBAPPV;


class Doctrine
{
    /**
     * @param $queryBuilder
     * @param $username
     * @param $password
     * @return mixed
     */
    public static function queryRetrieveUsernamePassword($queryBuilder, $username, $password)
    {
        $queryBuilder = $queryBuilder->select('username')
            ->addSelect('password')
            ->from('bank')
            ->where("username = :username")
            ->andWhere("password = :password")
            ->setParameter('username', $username)
            ->setParameter('password', $password);

        $outcome = $queryBuilder->execute();
        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount > 0) {
            $row = $outcome->fetchAll();
            $params['username'] = $row[0]['username'];
            $params['password'] = $row[0]['password'];
            $params['sql'] = $queryBuilder->getSQL();
        } else {
            $params['username'] = 'Invalid_credentials';
            $params['password'] = 'Invalid_credentials';
            $params['sql'] = $queryBuilder->getSQL();
        }
        return $params;

    }

    /**
     * @param $queryBuilder
     * @param $info
     * @param $username
     */
    public static function queryUpdateUserInfo($queryBuilder, $info, $username)
    {
        $queryBuilder = $queryBuilder->update('bank')
            ->set("info", ":info")
            ->where("username = :username")
            ->setParameter('info', $info)
            ->setParameter('username', $username);

        $queryBuilder->execute();
    }

    /**
     * @param $queryBuilder
     * @param $username
     * @return string
     */
    public static function queryRetrieveUserInfo($queryBuilder, $username)
    {
        $queryBuilder = $queryBuilder->select('info')
            ->from('bank')
            ->where("username = :username")
            ->setParameter('username', $username);

        $outcome = $queryBuilder->execute();
        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount > 0) {
            $row = $outcome->fetchAll();
            $info = $row[0]['info'];
        } else {
            $info = 'Error: empty field';
        }
        return $info;
    }

    /**
     * @param $queryBuilder
     * @param $cleaned_comment
     * @param $userid
     * @return mixed
     */
    public static function queryStoreUserCommentSqli($queryBuilder, $cleaned_comment, $userid)
    {
        $queryBuilder = $queryBuilder->insert('user_comments_sql')
            ->values([
                'userid' => ':userid',
                'comment' => ':comment',
            ])
            ->setParameters([
                ':userid' => $userid,
                ':comment' => $cleaned_comment,
            ]);

        $store_result['outcome'] = $queryBuilder->execute();
        $store_result['sql_query'] = $queryBuilder->getSQL();

        return $store_result;
    }

    /**
     * @param $queryBuilder
     * @return string
     */
    public static function queryCountUserCommentsSqli($queryBuilder)
    {
        $queryBuilder = $queryBuilder->select('comment')
            ->from('user_comments_sql');

        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount > 0) {
            $outcome = $rowCount;
        } else {
            $outcome = '0 user comments';
        }
        return $outcome;

    }

    /**
     * @param $queryBuilder
     * @return mixed
     */
    public static function queryRetrieveUserComments($queryBuilder)
    {
        $queryBuilder = $queryBuilder->select('userid')
            ->addSelect('comment')
            ->addSelect('date')
            ->from('user_comments_sql');

        $outcome = $queryBuilder->execute();
        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount >= 0) {
            $x = 0;
            $row = $outcome->fetchAll();

            while ($x < $rowCount) {

                $params[$x]['userid'] = $row[$x]['userid'];
                $params[$x]['comment'] = $row[$x]['comment'];
                $params[$x]['date'] = $row[$x]['date'];

                $x++;
            }
        }
        return $params;
    }

    /**
     * @param $queryBuilder
     * @param $username
     * @return string
     */
    public static function queryRetrieveUserBalanceAccountSortCode($queryBuilder, $username)
    {
        $queryBuilder = $queryBuilder->select('balance')
            ->addSelect('Account')
            ->addSelect('SortCode')
            ->from('bank')
            ->where("username = :username")
            ->setParameter('username', $username);

        $outcome = $queryBuilder->execute();
        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount > 0) {
            $row = $outcome->fetchAll();
            $info[1] = $row[0]['balance'];
            $info[2] = $row[0]['Account'];
            $info[3] = $row[0]['SortCode'];
        } else {
            $info = 'Invalid credentials';
        }
        return $info;

    }

    /**
     * @param $queryBuilder
     * @param $cleaned_comment
     * @param $userid
     * @return mixed
     */
    public static function queryStoreUserCommentCsrf($queryBuilder, $cleaned_comment, $userid)
    {
        $queryBuilder = $queryBuilder->insert('user_comments_csrf')
            ->values([
                'userid' => ':userid',
                'comment' => ':comment',
            ])
            ->setParameters([
                ':userid' => $userid,
                ':comment' => $cleaned_comment,
            ]);

        $store_result['outcome'] = $queryBuilder->execute();
        $store_result['sql_query'] = $queryBuilder->getSQL();

        return $store_result;
    }

    /**
     * @param $queryBuilder
     * @return string
     */
    public static function queryCountUserCommentsCsrf($queryBuilder)
    {
        $queryBuilder = $queryBuilder->select('comment')
            ->from('user_comments_csrf');

        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount > 0) {
            $outcome = $rowCount;
        } else {
            $outcome = '0 user comments';
        }
        return $outcome;

    }

    /**
     * @param $queryBuilder
     * @return mixed
     */
    public static function queryRetrieveUserCommentsCsrf($queryBuilder)
    {
        $queryBuilder = $queryBuilder->select('userid')
            ->addSelect('comment')
            ->addSelect('date')
            ->from('user_comments_csrf');

        $outcome = $queryBuilder->execute();
        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount >= 0) {
            $x = 0;
            $row = $outcome->fetchAll();

            while ($x < $rowCount) {

                $params[$x]['userid'] = $row[$x]['userid'];
                $params[$x]['comment'] = $row[$x]['comment'];
                $params[$x]['date'] = $row[$x]['date'];

                $x++;
            }
        }

        return $params;
    }

    /**
     * @param $queryBuilder
     * @param $balance
     * @param $username
     */
    public static function queryUpdateUserBalance($queryBuilder, $balance, $username)
    {
        $queryBuilder = $queryBuilder->update('bank')
            ->set("balance", ":balance")
            ->where("username = :username")
            ->setParameter('balance', $balance)
            ->setParameter('username', $username);

        $queryBuilder->execute();
    }

    /**
     * @param $queryBuilder
     * @param $username
     * @return mixed
     */
    public static function queryRetrieveUsernamePasswordRole($queryBuilder, $username)
    {
        $queryBuilder = $queryBuilder->select('username')
            ->addSelect('password')
            ->addSelect('role')
            ->from('user_data')
            ->where("username = :username")
            ->setParameter('username', $username);

        $outcome = $queryBuilder->execute();
        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount > 0) {
            $row = $outcome->fetchAll();
            $params['username'] = $row[0]['username'];
            $params['password'] = $row[0]['password'];
            $params['role'] = $row[0]['role'];
            $params['sql'] = $queryBuilder->getSQL();
        } else {
            $params['username'] = 'Invalid_credentials';
            $params['password'] = 'Invalid_credentials';
            $params['role'] = 'Invalid_credentials';
            $params['sql'] = $queryBuilder->getSQL();
        }
        return $params;

    }

    /**
     * @param $queryBuilder
     * @param $cleaned_comment
     * @param $userid
     * @return mixed
     */
    public static function queryStoreUserCommentXss($queryBuilder, $cleaned_comment, $userid)
    {
        $queryBuilder = $queryBuilder->insert('user_comments')
            ->values([
                'userid' => ':userid',
                'comment' => ':comment',
            ])
            ->setParameters([
                ':userid' => $userid,
                ':comment' => $cleaned_comment,
            ]);

        $store_result['outcome'] = $queryBuilder->execute();
        $store_result['sql_query'] = $queryBuilder->getSQL();

        return $store_result;
    }

    /**
     * @param $queryBuilder
     * @return string
     */
    public static function queryCountUserCommentsXss($queryBuilder)
    {
        $queryBuilder = $queryBuilder->select('comment')
            ->from('user_comments');

        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount > 0) {
            $outcome = $rowCount;
        } else {
            $outcome = '0';
        }
        return $outcome;

    }

    /**
     * @param $queryBuilder
     * @return mixed
     */
    public static function queryRetrieveUserCommentsXss($queryBuilder)
    {
        $queryBuilder = $queryBuilder->select('userid')
            ->addSelect('comment')
            ->addSelect('date')
            ->from('user_comments');

        $outcome = $queryBuilder->execute();
        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount >= 0) {
            $x = 0;
            $row = $outcome->fetchAll();

            while ($x < $rowCount) {

                $params[$x]['userid'] = $row[$x]['userid'];
                $params[$x]['comment'] = $row[$x]['comment'];
                $params[$x]['date'] = $row[$x]['date'];

                $x++;
            }
        }

        return $params;
    }

    public static function queryStoreUserTicket($queryBuilder, $ticket)
    {
        $queryBuilder = $queryBuilder->insert('user_tickets')
            ->values([
                'user_ticket' => ':ticket',
            ])
            ->setParameters([
                ':ticket' => $ticket,
            ]);

        $queryBuilder->execute();
    }

    public static function queryRetrieveUserTickets($queryBuilder)
    {
        $queryBuilder = $queryBuilder->select('user_ticket')
            ->addSelect('id')
            ->from('user_tickets');

        $outcome = $queryBuilder->execute();
        $rowCount = $queryBuilder->execute()->rowCount();

        if ($rowCount >= 0) {
            $x = 0;
            $row = $outcome->fetchAll();

            while ($x < $rowCount) {

                $params[$x]['id'] = $row[$x]['id'];
                $params[$x]['user_ticket'] = $row[$x]['user_ticket'];

                $x++;
            }
        }

        return $params;
    }

    public static function queryStoreUserData($queryBuilder, $cleaned_parameters, $bcrypt_password)
    {
        $username = $cleaned_parameters['sanitised_username'];
        $email = $cleaned_parameters['sanitised_email'];
        $role = $cleaned_parameters['sanitised_role'];

        $queryBuilder = $queryBuilder->insert('user_data')
            ->values([
                'username' => ':username',
                'email' => ':email',
                'password' => ':password',
                'role' => ':role',
            ])
            ->setParameters([
                ':username' => $username,
                ':email' => $email,
                ':password' => $bcrypt_password,
                ':role' => $role
            ]);

        $queryBuilder->execute();
    }

    public static function queryRetrieveUsername($queryBuilder, $cleaned_username)
    {
        $queryBuilder = $queryBuilder->select('username')
            ->from('user_data')
            ->where("username = :username")
            ->setParameter('username', $cleaned_username);

        $store_result = $queryBuilder->execute()->rowCount();

        return $store_result;
    }
}
