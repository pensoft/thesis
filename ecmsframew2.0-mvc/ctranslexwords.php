<?php

class ctranslexwords extends cbase {
	var $con;
	
	function __construct($pFieldTempl) {
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->con = new DBCn;
		$this->con->Open();
		$this->GetData();
	}
	
	function CheckVals() {
		return;
	}
	
	function GetData() {
		$this->m_pubdata['transliterationwords'] = array();
		$lSqlStr = 'SELECT id, LOWER(word_bg) AS word_bg, LOWER(word_en) AS word_en FROM transliteration_words';
		$lSqlStrErr = 'SELECT exists(SELECT tablename FROM pg_tables WHERE tablename = \'transliteration_words\')::int AS exists';
		$this->con->Execute($lSqlStrErr);
		$this->con->MoveFirst();
		$NoRaiseErr = $this->con->mRs['exists'];
		
		
		if((int)$NoRaiseErr) {
			$this->con->Execute($lSqlStr);
			$this->con->MoveFirst();
			while (!$this->con->Eof()) {
				$this->m_pubdata['transliterationwords'][$this->con->mRs['word_bg']] = $this->con->mRs['word_en'];
				$this->con->MoveNext();
			}
		}
	}
	
	function Display() {
		return serialize($this->m_pubdata['transliterationwords']);
	}
}
?>