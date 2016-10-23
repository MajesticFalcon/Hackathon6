<?php
namespace Hackathon;

class Provider
{
    private $provider;
    private $errors;

    function __construct() {
        $this->errors = array();
    }

    function verifyProvider($arr = array()) {
        if (!isset($arr['name'])) {
            $this->errors[] = 'A name is required.';
        }
        if (!isset($arr['phone'])) {
            $this->errors[] = 'A phone number is required.';
        }
        if (!isset($arr['poc'])) {
            $this->errors[] = 'A POC is required.';
        }
        if (!isset($arr['address'])) {
            $this->errors[] = "An address is required.";
        }
        if (!isset($arr['city'])) {
            $this->errors[] = 'A city is required.';
        }
        if (!isset($arr['state'])) {
            $this->errors[] = 'A state is required.';
        }
        if (!isset($arr['zip'])) {
            $this->errors[] = 'A zip code is required.';
        }
        if (!isset($arr['coc'])) {
            $this->errors[] = 'A COC is required.';
        }
        if (!sizeof($this->errors)) {
             return true;
        }
        return false;
    }

    function getErrors() {
        return $this->errors;
    }

    function insertProvider($arr) {
        $n = safe_value($arr['name']);
        $p = safe_value($arr['phone']);
        $poc = safe_value($arr['poc']);
        $a = safe_value($arr['address']);
        $c = safe_value($arr['city']);
        $s = safe_value($arr['state']);
        $zip = safe_value($arr['zip']);
        $coc = safe_value($arr['coc']);

        $sql="INSERT INTO hackathon.providers (`name`, `phone`, `poc`, `address`, `city`, `state`, `zip`, `coc`) VALUES ('{$n}','{$p}','{$poc}', '{$a}', '{$c}', '{$s}', '{$zip}', '{$coc}')";
        action_q($sql);
    }

	//Im swapping arr and ID. 
    function updateProvider($id,$arr) {
         // print_pre("UPDATE hackathon.providers SET `name` = '".safe_value($arr['name'])."', `phone` = '".safe_value($arr['phone'])."', `poc` = '".safe_value($arr['poc'])."', `address` = '".safe_value($arr['address'])."', `city` = '".safe_value($arr['city'])."', `state` = '".safe_value($arr['state'])."', `zip` = '".safe_value($arr['zip'])."', `coc` = '".safe_value($arr['coc'])."' WHERE uuid = '".$id."''" );
        // die();
		// action_q("UPDATE hackathon.providers SET `name` = '".safe_value($arr['name'])."', `phone` = '".safe_value($arr['phone'])."', `poc` = '".safe_value($arr['poc'])."', `address` = '".safe_value($arr['address'])."', `city` = '".safe_value($arr['city'])."', `state` = '".safe_value($arr['state'])."', `zip` = '".safe_value($arr['zip'])."', `coc` = '".safe_value($arr['coc'])."' WHERE uuid = '".$id."''" );
         // print_pre("UPDATE hackathon.providers SET `name` = '".safe_value($arr['name'])."', `phone` = '".safe_value($arr['phone'])."', `poc` = '".safe_value($arr['poc'])."', `address` = '".safe_value($arr['address'])."', `city` = '".safe_value($arr['city'])."', `state` = '".safe_value($arr['state'])."', `zip` = '".safe_value($arr['zip'])."', `coc` = '".safe_value($arr['coc'])."' WHERE uuid = ".$id."" );
         action_q("UPDATE hackathon.providers SET `name` = '".safe_value($arr['name'])."', `phone` = '".safe_value($arr['phone'])."', `poc` = '".safe_value($arr['poc'])."', `address` = '".safe_value($arr['address'])."', `city` = '".safe_value($arr['city'])."', `state` = '".safe_value($arr['state'])."', `zip` = '".safe_value($arr['zip'])."', `coc` = '".safe_value($arr['coc'])."' WHERE uuid = ".$id."" );
    }
}