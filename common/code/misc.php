<?php
//Lets get a nice pretty debugging tool
function print_pre($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}

function safe_value($val){
	return mysql_real_escape_string($val);

}
?>