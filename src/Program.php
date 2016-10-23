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


    function getProgramRecord($programUuid){
        $program = id_q("select * from program where uuid = {$programUuid}");
		$provider = id_q("select name, phone from providers where uuid = {$program['provider_uuid']}");
	if (sizeof($program)) {
		$zips = $this->getZipsForProgram($programUuid);
		$requirements = $this->getRequirementsForProgram($programUuid);
		$ret['uuid'] = $program['uuid'];
		$ret['last_updated'] = $program['last_updated'];
		$ret["program_name"] = $program["name"];
		$ret["provider_name"] = $provider["name"];
		$ret["phone_number"] = $provider["phone"];
		$ret["amount"] = $program["budget"];
		$ret["zips"] = $zips;
		$ret["requirements"] = $requirements;
		return $ret;
	}
	else {
		return array();
	}
    }

    function insertProgram($programArr){
        $providerUuid = 1;
        $name = $programArr["program_name"];
        $budget = $programArr["amount"];
        $zipCodeUuids = $this->getZipCodeUuids($programArr["zipCodes"]);
        $reqUuids = $this->getRequirementUuids($programArr["requirements"]);
        $programQuery = "insert into program (provider_uuid, name, budget) values ({$providerUuid}, '{$name}', {$budget})";
        $programUuid = action_q($programQuery);
        $this->insertZipsForProgram($programUuid, $zipCodeUuids);
        $this->insertRequirementsForProgram($programUuid, $reqUuids);
    }

    function insertZipsForProgram($programUuid, $zipUuids){
        $deleteQuery = "delete from program_link_zip_codes where program_uuid = {$programUuid}";
        action_q($deleteQuery);

        foreach($zipUuids as $zipArr){
            $zipUuid = $zipArr["uuid"];
            $insertQuery = "insert into program_link_zip_codes (program_uuid, zip_code_uuid) values ({$programUuid}, {$zipUuid})";
            action_q($insertQuery);
        }
    }

    function insertRequirementsForProgram($programUuid, $reqUuids){
        $deleteQuery = "delete from program_link_service_requirement where program_uuid = {$programUuid}";
        action_q($deleteQuery);
        foreach($reqUuids as $reqArr){
            $reqUuid = $reqArr["uuid"];
            $insertQuery = "insert into program_link_service_requirement (program_uuid, service_requirement_uuid) values ({$programUuid}, {$reqUuid})";
            action_q($insertQuery);
        }
    }

    function convertToCsv($arr){
        $strOut = '';
        foreach($arr as $str){
            if($strOut == ''){
                $strOut = "\"".$str."\"";
            }else{
                $strOut = $strOut.",\"".$str."\"";
            }
        }
        return $strOut;
    }

    function getZipCodeUuids($zips){
        $zipCsv = $this->convertToCsv($zips);
        $query = "select uuid from zip_code where zip in ({$zipCsv})";
        return select_q($query);
    }

    function getRequirementUuids($requirementAcks){
        $reqAckCsv = $this->convertToCsv($requirementAcks);
        $query = "select uuid from service_requirement where ack in ({$reqAckCsv})";
        return select_q($query);
    }

    function getRequirementUuidFromAck($ack){
        $query = "select uuid from service_requirement where ack = '{$ack}'";
        return select_q($query);
    }


    function getZipsForProgram($programUuid){
        $query = "select zip_code.zip from zip_code join program_link_zip_codes on program_link_zip_codes.zip_code_uuid = zip_code.uuid where program_link_zip_codes.program_uuid = {$programUuid}";
        $results = select_q($query);
        return $results;
    }

//return an array of requirements for a given provider
//access using $results[0]["criterion"];
    function getRequirementsForProgram($programUuid){
        $query = "select service_requirement.ack from program_link_service_requirement join service_requirement on program_link_service_requirement.service_requirement_uuid = service_requirement.uuid where program_link_service_requirement.program_uuid = {$programUuid}";
        $results = select_q($query);
        return $results;
    }

//return an array of service types for a given provider
//access using $results[0]["name"];
    function getServiceTypesForProvider($provider){
        $query = "select service_type.name from provider_link_service_type join service_type on provider_link_service_type.service_type_uuid = service_type.uuid where provider_link_service_type.provider_uuid = {$provider['uuid']}";
        $results = select_q($query);
        if(!$results){
            echo "invalid getServiceTypesForProvider()";
        }
        else{
            return $results;
        }
    }

    function getProgramsForProvider($providerUuid){
        $query = "select uuid from program where provider_uuid = {$providerUuid}";
        $uuids = select_q($query);
        $programs = array();
        foreach($uuids as $uuid){
            $program = $this->getProgramRecord($uuid['uuid']);
            array_push($programs, $program);
        }
        foreach ($programs as $program) {
            $program['link'] = '/program/'.$program['uuid'];
        }
        return $programs;
    }

    function getProgramsFromRequirements($reqUuids){
        if(empty($reqUuids)){
            return array();
        }
        $csvReq = $this->convertToCsv($reqUuids);
        $query = "select program_uuid from program_link_service_requirement where service_requirement_uuid in ({$csvReq})";
        $programUuids = select_q($query);
        if(empty($programUuids)){
            return array();
        }
        $programs = array();
        foreach($programUuids as $programUuid){
            $program = $this->getProgramRecord($programUuid['program_uuid']);
            array_push($programs, $program);
        }
        return $programs;
    }
}
