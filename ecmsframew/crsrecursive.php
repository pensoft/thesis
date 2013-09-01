<?php
define("MT_LINK", 2);
class crsrecursive extends crs {
	var $endrs;
	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		$this->endrs=0;
	}
	
	function LoadDefTempls() {
		parent::LoadDefTempls(); 
		$this->m_defTempls = array_merge($this->m_defTempls , array(G_RAZDTEMPL => D_RAZDTEMPL));
	}
	function GetRows($varr=array()) {
		$getnextrow=1;
		while (!$this->con->Eof() || (!$getnextrow && !$this->endrs)) {
			if ($getnextrow) $this->GetNextRow();
			$getnextrow=1;
			if ($this->m_pubdata['type']==MT_LINK)
				$objTemplate= $this->getObjTemplate(G_RAZDTEMPL , ($this->m_pubdata['templrazdadd'] ? $this->m_pubdata[$this->m_pubdata['templrazdadd']] : ""));
			else 
				$objTemplate= $this->getObjTemplate(G_ROWTEMPL , ($this->m_pubdata['templadd'] ? $this->m_pubdata[$this->m_pubdata['templadd']] : ""));
			if ($this->m_pubdata['recursivecolumn']) {
				/*
				if ($varr && (count($varr)>0)) {
					var_export($varr);
					echo "<br>Exit:". in_array($this->m_pubdata[$this->m_pubdata['recursivecolumn']], $varr) ."-".$this->m_pubdata['name']."<br>";
				} else echo "<br>Obrabotka: ".$this->m_pubdata['name']."<br>";
				*/
				if (in_array($this->m_pubdata[$this->m_pubdata['recursivecolumn']], $varr)) return $lRet; //krai na rekursia
				
				$objTemplateArr=explode("{&}",$objTemplate);
				if (count($objTemplateArr) > 1) {
					$objResArr=array();
					foreach($objTemplateArr as $k=>$v) {
						$objResArr[$k]=$this->ReplaceHtmlFields($v);
					}
					if (!$this->con->Eof()) {
						$lres=$this->GetRows(array_merge($varr,array($this->m_pubdata[$this->m_pubdata['recursivecolumn']]))); //rekursia
						$getnextrow=0;
					} else $lres=""; 
					$lRet .= implode($lres, $objResArr);
				} else $lRet .= $this->ReplaceHtmlFields($objTemplate);
			} else $lRet .= $this->ReplaceHtmlFields($objTemplate);				
			$this->m_pubdata['rownum']++;			
		}
		if ($this->con->Eof()) $this->endrs=1; 
		return $lRet;
	}
}

?>