<?php

class csimple extends cbase {
	function __construct($pFieldTempl) {
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
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
		
		//replacvame v templat-a
		
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));
	}
}

?>