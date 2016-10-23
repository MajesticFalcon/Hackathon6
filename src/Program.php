<?php

namespace Hackathon;
require_once __DIR__.'/../common/code/sql.php';

class Program
{
    private $program;

    function __construct($array = array()) {
        $array = $this->getStandardizedProgram($array);
        $this->program = $array;
    }

    function getStandardizedProgram($array) {
        if (!isset($array['program_name'])) {
            $errors[] = 'Program name required';
        }
        if (!isset($array['amount'])) {
            $errors[] = 'Program amount required.';
        } else {
            if (!preg_match('/[0-9]*\.[0-9]{0,1,2}./', $array['amount'])) {
                $errors[] = 'Invalid amount. We need dollars.';
            }
        }
        if (!isset($array['ami'])) {
            $array['ami'] = 0;
        } else {
            $array['ami'] = 1;
        }
        if (!isset($array['veteran_dishonor'])) {
            $array['veteran_dishonor'] = 0;
        } else {
            $array['veteran_dishonor'] = 1;
        }
        if (!isset($array['veteran_any'])) {
            $array['veteran_any'] = 0;
        } else {
            $array['veteran_any'] = 1;
        }
        if (!isset($array['veteran_honorable'])) {
            $array['veteran_honorable'] = 0;
        } else {
            $array['veteran_honorable'] = 1;
        }
        if (!isset($array['family_under_5'])) {
            $array['family_under_5'] = 0;
        } else {
            $array['family_under_5'] = 1;
        }
        if (!isset($array['family_under_18'])) {
            $array['family_under_18'] = 0;
        } else {
            $array['family_under_18'] = 1;
        }
        if (!isset($array['sixty_plus'])) {
            $array['sixty_plus'] = 0;
        } else {
            $array['sixty_plus'] = 1;
        }
        if (!isset($array['youth_16_24'])) {
            $array['youth_16_24'] = 0;
        } else {
            $array['youth_16_24'] = 1;
        }
        if (!isset($array['private_funds'])) {
            $array['private_funds'] = 0;
        } else {
            $array['private_funds'] = 1;
        }
        if (!isset($array['private_restricted'])) {
            $array['private_restricted'] = 0;
        } else {
            $array['private_restricted'] = 1;
        }
        if (!isset($array['restrictions_text'])) {
            $array['restrictions_text'] = '';
        }
        if (!isset($array['resident_city'])) {
            $array['resident_city'] = 0;
        } else {
            $array['resident_city'] = 1;
        }
        if (!isset($array['resident_county'])) {
            $array['resident_county'] = 0;
        } else {
            $array['resident_county'] = 1;
        }
        return $array;
    }

    function getProgram() {
        $arr = array();
        $arr['provider_uuid'] = isset($_SESSION['provider_id']) ? $_SESSION['provider_id'] : 1;
        $arr['program_name'] = $this->program['program_name'];
        $arr['amount'] = isset($this->program['amount']) ? number_format((int)$this->program['amount'], 2) : 0;
        return $arr;
    }

    function getProgramRequirements() {
        $arr = $this->program;
        unset($arr['program_name']);
        unset($arr['amount']);
        $result = array();
        foreach ($arr as $attr => $val) {
            if ($val) {
                $result[] = $attr;
            }
        }
        return array('requirements' => $result);
    }

    function getZipcodes() {
        return array('zipCodes' => $this->program['zipCodes']);
    }
}
