<?php
class crs_cms extends crs {
	
	function __construct($pFieldTempl) {
		$this->m_page = (int)$_REQUEST['p'];
		$this->con = new DbCn(DBTYPE_CMS);
		$this->con->Open(PGDB_CMS_SRV, PGDB_CMS_DB, PGDB_CMS_USR, PGDB_CMS_PASS, PGDB_CMS_PORT);
		//$this->con = Con();
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		if ($this->m_pubdata['debug']) $this->con->debug=$this->m_pubdata['debug'];
		$this->m_pageSize = (int)$this->m_pubdata["pagesize"];
		$this->LoadDefTempls();
	}
}
?>