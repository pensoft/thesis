<?php

class emResults{

	var $m_Data ;
	var $m_PageSize ;
	var $m_PageNum ;
	var $m_RecordCount ;
	
	function __construct($pData){
		$this->m_Data = $pData['controller_data'];
		$this->m_PageSize = $pData['pagesize'];
		$this->m_PageNum = $pData['page_num'];
		$this->m_RecordCount = $pData['record_count'];
	
	}
	
}

?>