<?php
class clang extends crs {
	protected $clearurl;
	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		$this->m_pubdata['sqlstr']="select code, name from languages where langid<>".getlang()." order by langid";
		$this->clearurl=ClearParaminURL($forwardurl,"lang");
	}
	function LoadDefTempls() {
		parent::LoadDefTempls();
		$this->m_defTempls[G_HEADER]='global.clangheader';
		$this->m_defTempls[G_ROWTEMPL]='global.clangrowtempl';
		$this->m_defTempls[G_FOOTER ]='global.clangfooter';
	}
	function GetData() {
		parent::GetData();
		if (($this->m_state >= 1) && ($this->m_recordCount)) {
			$this->m_currentRecord["url"] = AddParamtoURL($this->clearurl,'lang='. $this->m_currentRecord["code"]);
			$this->m_pubdata["url"] = $this->m_currentRecord["url"];
		}
	}
	function GetNextRow()  {
		parent::GetNextRow() ;
		$this->m_pubdata["url"] = $this->m_currentRecord["url"] = AddParamtoURL($this->clearurl,'lang='. $this->m_currentRecord["code"]);
	}
	
}

?>