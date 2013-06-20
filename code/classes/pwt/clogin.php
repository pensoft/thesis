<?php
class clogin {
	var $id;
	var $uname;
	var $ip;
	var $fullname;
	var $state;
	var $type;
	var $errcode;
	var $usr_title_id;
	var $photo_id;
	
	function __construct() {
		$con = Con();
		if (func_num_args() == 3) {
			list($uname, $passwd, $ip) = func_get_args();
			
			if (!$passwd && !$uname) {
				$this->errcode = 6; return 0;
			} elseif (!$uname) {
				$this->errcode = 4; return 0;
			} elseif (!$passwd) {
				$this->errcode = 5; return 0;
			}
			
			$SqlStr = 'SELECT * FROM SiteLogin(\'' . q($uname) . '\', \'' . q($passwd) . '\', \'' . $ip . '\')';
			$con->Execute($SqlStr);
			$con->MoveFirst();
		} elseif (func_num_args() == 2) {
			list($md5, $ip) = func_get_args();
			$SqlStr = 'SELECT * FROM SiteLoginRemember(\'' . q($md5) . '\', \'' . $ip . '\')';
			$con->Execute($SqlStr);
			$con->MoveFirst();
			if (!(int)$con->mRs['id']) {$this->errcode = 3; return 0;}
		} elseif (func_num_args() == 1) {
			list($lParamsArr) = func_get_args();
			if (!is_array($lParamsArr) && !array_key_exists('autologhash', $lParamsArr) ) {
				return 0;
			}
			$SqlStr = 'SELECT * FROM siteautologin(\'' . $lParamsArr['autologhash'] . '\', \'' . $lParamsArr['ip'] . '\')';
			$con->Execute($SqlStr);
			$con->MoveFirst();
		} else {
			trigger_error('Error !!!', E_USER_WARNING);
			return -1;
		}
 		//var_dump($SqlStr);
		$this->errcode = 3;
		if (!(int)$con->mRs['id']) {$this->errcode = 0; return 0;}
		if (!(int)$con->mRs['state']) {$this->errcode = 1; return 0;}
		
		$this->id = (int)$con->mRs['id']; 
		$this->state = (int)$con->mRs['state']; 
		$this->uname = $con->mRs['uname'];
		$this->ip = $con->mRs['ip'];
		$this->fullname = $con->mRs['fullname'];
		$this->salutation = $con->mRs['salutation'];
		$this->usr_title_id = $con->mRs['usr_title_id'];
		$this->photo_id = $con->mRs['photo_id'];
		$this->staff = $con->mRs['staff'];
		$this->admin = $con->mRs['admin'];
		
		$con->CloseRs();
		
		return 1;
	}
}

?>