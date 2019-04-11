<?php
 function globalskiller() {		// kills all non-system variables
 $global = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_FILES', '_SERVER', '_ENV',  '_REQUEST', '_SESSION');
  foreach ($GLOBALS as $key=>$val) {
  	if(!in_array($key, $global)) {
  		if(is_array($val)) unset_array($GLOBALS[$key]);
  		else unset($GLOBALS[$key]);
  	}
  }
}

function unset_array($array) {

	foreach($array as $key) {
		if(is_array($key)) unset_array($key);
		else unset($key);
	}
}

globalskiller();

function security_slashes(&$array) {
	foreach($array as $key => $value) {
		if(is_array($array[$key])) {
			security_slashes($array[$key]);
		}
		else {
			if(get_magic_quotes_gpc()) {
				$tmp = stripslashes($value);
			}
			else {
				$tmp = $value;
			}
			if(function_exists("mysql_real_escape_string")) {
				$array[$key] = mysql_real_escape_string($tmp);
			}
			else {
				$array[$key] = addslashes($tmp);
			}
			unset($tmp);
		}
	}
}

security_slashes($_POST);
security_slashes($_COOKIE);
security_slashes($_GET);
security_slashes($_REQUEST); 	  

?>
