<?php
class JSONList {
	var $mcnt;
	var $mtype;
	var $mdata;
	function JSONList() {
		$this->mcnt=0;
		$this->mtype=0;
		$this->mdata=array();
	}
	function addtoList($arr) {		
		array_push($this->mdata,$arr);
		$this->mcnt++;
	}
}

?>