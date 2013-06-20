<?php
class CUser {
	var $id;
	var $uname;
	var $passwd;
	var $ip;
	var $grpnames;
	var $arrPerm;
	var $fullname;
	var $error;
	
	function __construct() {
		$con = Con();
		if (func_num_args() == 3) {
			list($uname, $passwd, $ip) = func_get_args();
			$SqlStr = "SELECT * FROM spLogin('" . q($uname) . "', '" . q($passwd) . "', '" . $ip . "')";
			//~ echo $SqlStr;
			$con->Execute($SqlStr);
			$con->MoveFirst();
		}
		else {
			trigger_error("Error !!!", E_USER_WARNING);
			return 0;
		}
		
		$this->error = (int)$con->mRs["error"];
		
		if (!$this->error) {
			$this->id = (int)$con->mRs["id"]; 
			$this->uname = $con->mRs["uname"];
			$this->fullname = $con->mRs["fullname"];
			while (!$con->Eof()) {
				$this->arrPerm[$con->mRs["url"]] = $con->mRs["actype"];
				$con->MoveNext();
			}
			$con->CloseRs();
			$con->Execute('SELECT DISTINCT s.name 
				FROM secgrpdet sd 
				JOIN secgrp s ON s.id = sd.gid 
				WHERE sd.uid = ' . $this->id);
			$con->MoveFirst();
			while (!$con->Eof()) {
				$this->grpnames[$con->mRs["name"]] = $con->mRs["name"];
				$con->MoveNext();
			}
			$con->CloseRs();
		} else {
			return 0;
		}
		return 1;
	}
}
$user = unserialize($_SESSION["suser"]);

?>