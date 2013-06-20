<?php

class ctplkfor extends cbase {
	var $kfor;
	
	function __construct($pFieldTempl, $debug = false) {
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		
		if (!$this->m_pubdata['method']) $this->m_pubdata['method'] = 'POST';
		if (!$this->m_pubdata['pvformhtml']) $this->m_pubdata['pvformhtml'] = null;
		if (!$this->m_pubdata['addbackurl']) $this->m_pubdata['addbackurl'] = 1;
		if (!$this->m_pubdata['setformname']) $this->m_pubdata['setformname'] = null;
		if (!$this->m_pubdata['js_validation']) $this->m_pubdata['js_validation'] = 0;
		if (!is_array($this->m_pubdata['path_fields'])) $this->m_pubdata['path_fields'] = array();
		
		$this->kfor = new kfor($this->m_pubdata['flds'], $this->getObjTemplate(G_DEFAULT), $this->m_pubdata['method'], $this->m_pubdata['pvformhtml'], $this->m_pubdata['addbackurl'], $this->m_pubdata['setformname'], $this->m_pubdata['js_validation'], $this->m_pubdata['path_fields']);
		$this->kfor->debug = $debug;
	}
	
	function CheckVals() {
		
	}
	
	function GetData() {
		$this->kfor->ExecAction();
	}
	
	function KforErrCnt() {
		return $this->kfor->lErrorCount;
	}
	
	function KforSetErr($p, $err) {
		$this->kfor->SetError($p, $err);
	}
	
	function getKforVal($p) {
		return $this->kfor->lFieldArr[$p]['CurValue'];
	}
	
	function getValName($p) {
		return $this->kfor->lFieldArr[$p]['DisplayName'];
	}
	
	function SetFormHtml($html) {
		$this->kfor->SetFormHtml($this->getObjTemplate($html));
	}
	
	function SetFormAction($pAction) {
		$this->kfor->SetFormAction($pAction);
	}
	
	function StopErrDisplay($errdispl) {
		$this->kfor->StopErrDisplay($errdispl);
	}
	
	function GetErrStr() {
		return $this->kfor->GetErrStr();
	}
	
	function SetFormName($formname) {
		$this->kfor->SetFormName($formname);
	}
	
	function setKforVal($p, $v) {
		$this->kfor->lFieldArr[$p]['CurValue'] = $v;
	}
	
	function getProp($fld, $prop) {
		return $this->kfor->lFieldArr[$fld][$prop];
	}
	
	function setProp($fld, $prop, $val) {
		$this->kfor->lFieldArr[$fld][$prop] = $val;
	}
	
	function KforAction() {
		return $this->kfor->lCurAction;
	}
	
	function KforSql($sql) {
		return $this->kfor->ReplaceSqlFields($sql);
	}
	
	function Display($pExtraTags = '') {
		return $this->kfor->Display($pExtraTags);
	}
	
}

?>