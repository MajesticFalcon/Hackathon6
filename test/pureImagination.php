<?php
require_once __DIR__."/../common/include.php";
require_once __DIR__."/../common/code/sql.php";
print_pre(getProgram(7));
$derp["program_name"] = "Elderly Veterans";
$derp["provider_uuid"] = 1;
$derp["amount"] = "50.00";
$zips = array("63005", "63056", "63114");
$derp["zipCodes"] = $zips;
$requirements = array("veteran_any", "sixty_plus");
$derp["requirements"] = $requirements;
//insertProgram($derp);

function getProgramsFromZipsAndRequirements($zips, $reqUuids){
  $reqCsvRequest = converttoCsv($reqUuids);
  $programs = getProgramsFromZips($zips);
  $programsMatchingZipUuids = array();
  foreach($programs as $program){
    array_push($programsMatchingZipUuids, $program['uuid']);
  }
  $programs = getProgramsFromRequirements($reqUuid);
  $programsMatchingReqUuids = array();
  foreach($programs as $program){
    array_push($programsMatchingReqUuids, $program['uuid']);
  }
  $programUuidsMatchingBoth = array_intersect($programsMatchingZipUuids, $programsMatchingReqUuids);
  $ret = array();
  foreach($programUuidsMatchingBoth as $uuid){
    array_push($ret, $getProgramRecord($uuid));
  }
  return ret;
}

function getProgramsFromZips($zips){ 
  $zipCodeUuids = getZipCodeUuids($zips);
  $csvReq = convertToCsv($zipCodeUuids);
  $query = "select program_uuid from program_link_zip_codes where zip_code_uuid in ({$csvReq})";
  $programUuids = select_q($query);
  $programs = array();
  foreach($programUuids as $programUuid){
    $program = getProgramRecord($programUuid);
    array_push($programs, $program);
  }
}

function getProgramsFromRequirements($reqUuids){ 
  $csvReq = convertToCsv($reqUuids);
  $query = "select program_uuid from program_link_service_requirement where service_requirement_uuid in ({$csvReq})";
  $programUuids = select_q($query);
  $programs = array();
  foreach($programUuids as $programUuid){
    $program = getProgramRecord($programUuid);
    array_push($programs, $program);
  }
}

function getProgramsForProvider($providerUuid){
  $query = "select uuid from program where provider_uuid = {$providerUuid}";
  $uuids = select_q($query);
  $programs = array();
  foreach($uuids as $uuid){
    $program = getProgram($uuid);
    array_push($programs, $program);
  }
  return $programs;
}

function getProgram($programUuid){
  $program = select_q("select * from program where uuid = {$programUuid}");
  $zips = getZipsForProgram($programUuid);
  $requirements = getRequirementsForProgram($programUuid);
  $ret["program_name"] = $program["name"];
  $ret["provider_uuid"] = 1;
  $ret["amount"] = $program["budget"];
  $ret["zips"] = $zips;
  $ret["requirements"] = $requirements;
  return $ret;
}

function insertProgram($programArr){
  $providerUuid = 1;
  $name = $programArr["program_name"];
  $budget = $programArr["amount"];
  $zipCodeUuids = getZipCodeUuids($programArr["zipCodes"]);
  $reqUuids = getRequirementUuids($programArr["requirements"]);
  $programQuery = "insert into program (provider_uuid, name, budget) values ({$providerUuid}, '{$name}', {$budget})";
  $programUuid = action_q($programQuery);
  insertZipsForProgram($programUuid, $zipCodeUuids);
  insertRequirementsForProgram($programUuid, $reqUuids); 
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
  $zipCsv = convertToCsv($zips);
  $query = "select uuid from zip_code where zip in ({$zipCsv})";
  return select_q($query);
}

function getRequirementUuids($requirementAcks){
  $reqAckCsv = convertToCsv($requirementAcks);
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

}

?>
