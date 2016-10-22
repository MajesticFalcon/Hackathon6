<?php
include "sql.php";
include "pureImagination2.php";

echo time();
check_sql_connection();
getServiceProviders();


function getServiceProviders(){
  $query = "SELECT * FROM service_provider";
  $results = select_q($query);
  if (!$results) {
    echo "invalid";
  }
  else{
    echo "bla";
    foreach($results as $result){
  		echo "stuff<br>";
  	}
  }

}

function geoCodeAddress($street, $city, $state){
  return NULL;
}
?>
