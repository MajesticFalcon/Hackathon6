<?php
namespace Hackathon;

class User
{
    private $user;
    private $errors;

    function __construct($arr = array())
    {
        $this->errors = array();
    }

    function getErrors()
    {
        return implode('', $this->errors);
    }

    function getUser()
    {
        return $this->user;
    }

    function verifyUser($arr)
    {
        if (!isset($arr['username'])) {
            $this->errors[] = 'Username is required.';
        }
        if (!isset($arr['password'])) {
            $this->errors[] = 'Password is required.';
        }
        if (!isset($arr['verify_password'])) {
            $this->errors[] = 'You must enter a password verification.';
        }
        if ($arr['password'] !== $arr['verify_password']) {
            $this->errors[] = 'Your password and your password verification must match.';
        }
        if (strlen($arr['password']) < 8) {
            $this->errors[] = 'Your password must be 8 or more characters.';
        }
        if (empty($this->errors)) {
            $this->user = $arr;
            return true;
        }
        return false;
    }

    function hasErrors()
    {
        return sizeof($this->errors);
    }

    function insertUser($user)
    {
        $p = crypt(safe_value($user['password']), '$2a$09$WHYAMISTORINGTHISSALTPLAINLYINSOURCECODE?$');
        $u = safe_value($user['username']);
        $old_p_id = id_q("SELECT MAX(p_id) as p_id from hackathon.users");
        print_pre($old_p_id);
        $old_p_id['p_id']++;
        $user_sql = "INSERT INTO hackathon.users (`p_id`, `username`, `password`) VALUES ('" . $old_p_id['p_id'] . "', '$u','$p')";
        $provider_sql = "INSERT INTO hackathon.providers (`uuid`) VALUES (" . $old_p_id['p_id'] . ")";
        action_q($user_sql);
        action_q($provider_sql);
        return $old_p_id['p_id'];
    }

    function fetchUser($userId)
    {
        $sql = "SELECT * FROM hackathon.users WHERE id='$userId'";
        $result = id_q($sql);
        return $result;
    }
}
