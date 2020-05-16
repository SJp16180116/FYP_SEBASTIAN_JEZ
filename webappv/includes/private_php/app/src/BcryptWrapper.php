<?php
/**
 * BcryptWrapper.php
 *
 * Wrapper class for the PHP BCrypt library.
 *
 * More information about the password_hash and password_verify functions:
 * https://www.php.net/manual/en/function.password-hash.php
 * https://www.php.net/manual/en/function.password-verify.php
 *
 * Author: CF Ingrams
 * Email: <clinton@cfing.co.uk>
 */

namespace WEBAPPV;


class BcryptWrapper
{
    /**
     * creates a password hash
     *
     * @param $string_to_hash
     * @return bool|string
     */
    public function createHashedPassword($string_to_hash)
    {
        $password_to_hash = $string_to_hash;
        $bcrypt_hashed_password = '';

        if (!empty($password_to_hash)) {
            $options = array('cost' => BCRYPT_COST);
            $bcrypt_hashed_password = password_hash($password_to_hash, BCRYPT_ALGO, $options);
        }
        return $bcrypt_hashed_password;
    }

    /**
     * verifies that a password matches a hash
     *
     * @param $string_to_check
     * @param $stored_user_password_hash
     * @return bool
     */
    public function authenticatePassword($string_to_check, $stored_user_password_hash)
    {
        $user_authenticated = false;
        $current_user_password = $string_to_check;
        $stored_user_password_hash = $stored_user_password_hash;
        if (!empty($current_user_password) && !empty($stored_user_password_hash)) {
            if (password_verify($current_user_password, $stored_user_password_hash)) {
                $user_authenticated = true;
            }
        }
        return $user_authenticated;
    }
}