<?php
//Lets get a nice pretty debugging tool
function print_pre($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}

function safe_value($val){
	//I cant get this damn mysqli escape shit to work
	return $val;
	// print_pre($val);
	// print_pre(array(mysqli_real_escape_string($link, "sadafsdfasdf")));
	// check_sql_connection();
	// print_pre("sql connection ".$sql_connection);
	// return mysqli_real_escape_string($sql_connection, $val);

}

function jump($val){
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

	header("Location: http://$host$uri/$val");
}
?>