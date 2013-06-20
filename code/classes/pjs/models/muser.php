<?php
class mUser{
	var $id;
	var $uname;
	var $ip;
	var $fullname;
	var $firstname;
	var $middlename;
	var $lastname;
	var $state;
	var $photo_id;
	var $about;
	var $phone;
	var $fax;
	var $homepage;
	var $journals;

	function __construct($pData) {
		$this->id = (int)$pData['id'];
		$this->state = (int)$pData['state'];
		$this->uname = $pData['uname'];
		$this->ip = $pData['ip'];
		$this->fullname = $pData['fullname'];
		$this->firstname = $pData['firstname'];
		$this->middlename = $pData['middlename'];
		$this->lastname = $pData['lastname'];

		$this->about = $pData['about'];
		$this->phone = $pData['phone'];
		$this->fax = $pData['fax'];
		$this->homepage = $pData['homepage'];
		$this->photo_id = $pData['photo_id'];
		$this->journals = $pData['journals'];

		return 1;
	}
}

?>