<?php
class crscols extends crs {
	var $rownum;
	var $rowsep;
	function __construct($pFieldTempl) {
		$this->m_page = (int)$_REQUEST['p'];
		$this->con = new DBCn;
		$this->con->Open();
		//$this->con = Con();
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		$this->m_pageSize = (int)$this->m_pubdata["pagesize"];
		$this->LoadDefTempls();
	}
	
	function GetRows() {
		$rowsep = round($this->m_recordCount/$this->m_pubdata['cols']);
		$rownum = 1;
		while (!$this->con->Eof()) {
			$this->GetNextRow();
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
			if ($rownum == $rowsep)  $lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SEPTEMPL));
			$rownum++;
		}
		return $lRet;
	}
	
	function Display() {
		return parent::Display();
	}

}
?>