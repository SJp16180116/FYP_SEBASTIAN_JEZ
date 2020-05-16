<?php
/**
 * Validator.php
 *
 * Class to contain all validation / sanitization functions.
 *
 * More information about the filter_var function:
 * https://www.php.net/manual/en/function.filter-var.php
 *
 * Author: CF Ingrams
 * Email: <clinton@cfing.co.uk>
 */

namespace WEBAPPV;


class Validator
{
    /**
     * @param $string_to_sanitise
     * @return mixed|string
     */
    public function sanitiseString($string_to_sanitise)
    {
        $sanitised_string = '';

        if (!empty($string_to_sanitise)) {
            $sanitised_string = filter_var($string_to_sanitise, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        }
        return $sanitised_string;
    }

    /**
     * @param $comment_to_sanitise
     * @return mixed|string
     */
    public function sanitiseComment($comment_to_sanitise)
    {
        $sanitised_comment = '';

        if (!empty($comment_to_sanitise)) {
            $sanitised_comment = filter_var($comment_to_sanitise, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        }
        return $sanitised_comment;
    }

    /**
     * @param $email_to_sanitise
     * @return mixed|string
     */
    public function sanitiseEmail($email_to_sanitise)
    {
        $cleaned_string = '';

        if (!empty($email_to_sanitise)) {
            $sanitised_email = filter_var($email_to_sanitise, FILTER_SANITIZE_EMAIL);
            $cleaned_string = filter_var($sanitised_email, FILTER_VALIDATE_EMAIL);
        }
        return $cleaned_string;
    }
}