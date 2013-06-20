<?php

class crs_array extends crs_custom_pageing {//Kato crs samo 4e dannite se pazat v array za da moje da se razcykat predi da se display-nat
	var $m_resultArr = array();
	function GetRows() {
		foreach($this->m_resultArr as $lCurrentRow) {
			$this->parseNextRow($lCurrentRow);
			
			if ($this->m_pubdata['templadd'])
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL , $this->m_currentRecord[$this->m_pubdata['templadd']]));
			else 
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
				
			$this->m_pubdata['rownum']++;			
		}
		return $lRet;
	}
	
	function parseNextRow($pRowTempl) {		
		if (is_array($pRowTempl)) {
			foreach ($pRowTempl as $k => $v) {
				$this->m_pubdata[$k] = $this->m_currentRecord[$k] = $v;
			}			
		}
	}
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1) {
			$this->m_pubdata['rsh'] = $this->con->Execute($this->m_pubdata['sqlstr']);
			$this->con->MoveFirst();
			$this->m_recordCount = $this->con->RecordCount();
			$this->m_pubdata['records'] = $this->m_recordCount;
			if ($this->m_pageSize) $this->con->SetPage($this->m_pageSize, (int)$this->m_page);
			$this->m_state++;
			$this->m_resultArr = array();
			if ($this->m_recordCount) {
				$this->m_pubdata['rownum'] = 1;
				foreach ($this->con->mRs as $k => $v) {
					$this->m_pubdata[$k] =$this->m_currentRecord[$k] = $v;
				}
				while(!$this->con->Eof()){
					$this->m_resultArr[] = $this->con->mRs;
					$this->con->MoveNext();
				}
			}
		}
	}
}

?>