<?php
class clogin {
	var $id;
	var $uname;
	var $ip;
	var $fullname;
	var $state;
	var $type;
	var $errcode;
	function __construct() {
		$con = Con();
		if (func_num_args() == 3) {
			list($uname, $passwd, $ip) = func_get_args();
			$SqlStr = 'SELECT * FROM SiteLogin(\'' . q($uname) . '\', \'' . q($passwd) . '\', \'' . $ip . '\')';
			$con->Execute($SqlStr);
			$con->MoveFirst();
		} else {
			trigger_error('Error !!!', E_USER_WARNING);
			return -1;
		}
		$this->errcode = 3;
		if (!(int)$con->mRs['id']) {$this->errcode = 0; return 0;}
		if (!(int)$con->mRs['state']) {$this->errcode = 1; return 0;}
		if (!(int)$con->mRs['ipallowed']) {$this->errcode = 2; return 0;}
		
		$this->id = (int)$con->mRs['id']; 
		$this->state = (int)$con->mRs['state']; 
		$this->uname = $con->mRs['username'];
		$this->ip = $con->mRs['ip'];
		$this->fullname = $con->mRs['fullname'];
		$this->type = $con->mRs['type'];
		$con->CloseRs();
		
		return 1;
	}
}

?>