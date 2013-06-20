<?php
class cpage extends cbase {
	var $m_HTempl;
	
	function __construct($pFieldTempl, $pTemplId) {
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $pTemplId;
	}
	
	function CheckVals() {
		return;
	}
	
	function GetData() {
		return;
	}
	
	function Display() {
		$templ = $this->getObjTemplate(G_MAINBODY);
		echo $this->ReplaceHtmlFields($templ);
	}
}

?>