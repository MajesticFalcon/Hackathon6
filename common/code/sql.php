<?php
	$sql_connected = false;
	define("SQL_HOST", "127.0.0.1");
	define("SQL_USERNAME", "root");
	define("SQL_PASSWORD", "mutt-redbull");
	define("SQL_DB", "hackathon");
	define("SQL_PORT", "3306");
	//This function should be ran before each query.
	global $sql_connection;
	
function check_sql_connection(){
	global $sql_connected;
	if(!$sql_connected){
		global $sql_connection;
		$sql_connection = mysqli_connect(SQL_HOST,SQL_USERNAME,SQL_PASSWORD,SQL_DB, SQL_PORT) or die("Cannot connect to DB");
		$sql_connected = true;
			
	}
}

function action_q($sql){
	global $sql_connection;
	check_sql_connection();
	//This error handling is shit.. 
	mysqli_query($sql_connection, $sql);
	$id = mysqli_insert_id($sql_connection);
	return $id;
}

function select_q($sql){
	global $sql_connection;
	check_sql_connection();
	$results = array();
	$query = mysqli_query($sql_connection, $sql);
	while($mysql_query_rows = mysqli_fetch_assoc($query)){
		$results[] = $mysql_query_rows;
	}
	return $results;
}

function id_q($sql){
	global $sql_connection;
	check_sql_connection();
	$results = array();
	$query = mysqli_query($sql_connection, $sql);
	while($mysql_query_rows = mysqli_fetch_assoc($query)){
		$results[] = $mysql_query_rows;
	}
	if (is_array($results) == true){
		if(!empty($results)){
			return $results[0];
		}
	}else{
		return $results;
	}
}
// action_q("INSERT into hackathon.test (`id`) VALUES (1)");
// select_q("SELECT * FROM hackathon.test where `id` = 1");
?>
