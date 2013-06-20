<?php

require_once('JSONList.php');
class JSONOrganizer {
	var $m_cnt;
	var $m_err;
	var $m_errDesc;
	var $m_objects;
	function JSONOrganizer() {
		$this->m_cnt = 0;
		$this->m_err = 0;
		$this->m_errDesc = '';
		$this->m_objects = array();
	}
	
	function addNewJSONList($pData, $pErr = 0, $pErrDesc = '') {
		$lTemp = new JSONList();
		if( $pErr ){
			$this->m_err = 1;
			$this->m_errDesc .= $pErrDesc;
		}
		foreach($pData as $lDataRow){			
			$lTemp->addtoList($lDataRow);
		}
		++$this->m_cnt;
		$this->m_objects[] = $lTemp;
	}
	
	function Display(){
		$lResultArray = array(
			'err' => (int) $this->m_err,
			'rescnt' => (int) $this->m_cnt,
			'res' => $this->m_objects,
		);
		if( (int) $this->m_err ){
			$lResultArray['errdesc'] = $this->m_errDesc;
		}
		//~ var_dump($this->m_objects);
		echo json_encode($lResultArray);
	}
}

?>