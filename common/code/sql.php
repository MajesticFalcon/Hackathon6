<?php
	define("SQL_CONNECTED", false);
	define("SQL_HOST", "127.0.0.1");
	define("SQL_USERNAME", "root");
	define("SQL_PASSWORD", "mutt-redbull");
	define("SQL_DB", "hackathon");
	define("SQL_PORT", "3306");
	//This function should be ran before each query.

function check_sql_connection(){
	if(!SQL_CONNECTED){
		global $sql_connection;
		$sql_connection = mysqli_connect(SQL_HOST,SQL_USERNAME,SQL_PASSWORD,SQL_DB, SQL_PORT) or die("Cannot connect to DB");
		define("SQL_CONNECTED", true);
	}
}

function action_q($sql){
	global $sql_connection;
	check_sql_connection();
	//This error handling is shit.. 
	mysqli_query($sql_connection, $sql) or die("MYSQL ERROR");
	$id = mysqli_insert_id($sql_connection);
	return $id;
}

function select_q($sql){
	global $sql_connection;
	check_sql_connection();
	$results = array();
	$query = mysqli_query($sql_connection, $sql) or die("MYSQL ERROR");
	while($mysql_query_rows = mysqli_fetch_assoc($query)){
		$results[] = $mysql_query_rows;
	}
	return $results;
}
// action_q("INSERT into hackathon.test (`id`) VALUES (1)");
// select_q("SELECT * FROM hackathon.test where `id` = 1");
?>
