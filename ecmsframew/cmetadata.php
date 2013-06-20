<?php

class cmetadata extends cbase {
	var $pagetitle;
	var $keywords;
	var $description;
	var $scriptname;
	var $con;
	
	function __construct($pFieldTempl) {
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		$this->scriptname = $_SERVER['SCRIPT_NAME'];
		$this->pagetitle = '';
		$this->keywords = '';
		$this->description = '';
		$this->con = new DBCn;
		$this->con->Open();
	}
	
	//~ function SetTemplate($pTemplId) {
		//~ $this->m_HTempl = $pTemplId;
	//~ }
	
	function CheckVals() {
		if($this->m_state == 0) {
			$this->m_state++;
		} else {
			// NOTICE
		}
	}
	
	function GetData() {
		global $storiespath;
		
		$this->CheckVals();
		if ($this->m_state==1) {
			$this->m_state++;
		} else {
			// NOTICE
		}
		//ako iskame da pravim izklu4eniq go pravim tuk
		//~ switch ($this->scriptname) {
			
		//~ }
		if ($this->pagetitle == '' || $this->keywords == '' || $this->description == '') {
			$this->GetDefData();
		}
	}
	
	function GetDefData () {
		$lSqlStrErr = 'SELECT exists(SELECT tablename FROM pg_tables WHERE tablename = \'metadata\')::int AS exists';
		$this->con->Execute($lSqlStrErr);
		$this->con->MoveFirst();
		$NoRaiseErr = $this->con->mRs['exists'];
		if((int)$NoRaiseErr) {
			$this->con->Execute('SELECT title, description, keywords FROM metadata;');
			$this->con->MoveFirst();
			if (!$this->con->GetLastError() && !$this->con->Eof() && $this->con->RecordCount()) {
				 if ($this->pagetitle == '') {
					$this->pagetitle = $this->con->mRs['title'];
				}
				 if ($this->keywords == '') {
					$this->keywords = $this->con->mRs['keywords'];
				}
				 if ($this->description == '') {
					$this->description = $this->con->mRs['description'];
				}
			}
		}
	}
	
	function Display() {
		$this->GetData();
		
		if ($this->m_state < 2) {
			return;
		}
		
		//~ if (!$this->m_HTempl) {
			//~ trigger_error("There is no template", E_USER_WARNING);
			//~ return;
		//~ }

		$this->m_pubdata['pagetitle'] = h($this->pagetitle);
		$this->m_pubdata['keywords'] = h($this->keywords);
		$this->m_pubdata['description'] = h($this->description);
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));
	}
}

?>