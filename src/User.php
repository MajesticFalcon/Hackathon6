<?php
namespace Hackathon;
require_once($_SERVER['DOCUMENT_ROOT']."/common/include.php");

class User {
    private $user;
    private $errors;

    function __construct($arr = array()) {
        $this->errors = array();
    }

    function getErrors() {
        return implode('', $this->errors);
    }

    function verifyUser($arr) {
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
            return true;
        }
        return false;
    }

    function hasErrors() {
        return sizeof($this->errors);
    }

    function insertUser($user) {
        $crypt_pass = crypt(safe_value($user['password']),'$2a$09$WHYAMISTORINGTHISSALTPLAINLYINSOURCECODE?$');
        $u = safe_value($user['username']);
        $p = safe_value($u['password']);
        $sql=<<<SQL
INSERT INTO hackathon.users (`username`, `password`) VALUES ({$u}, {$p});
SQL;
        action_q($sql);
    }
}