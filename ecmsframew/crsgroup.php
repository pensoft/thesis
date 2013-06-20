<?php
class crsgroup extends crs {

	function __construct($pFieldTempl) {
		$this->m_splitcol = $pFieldTempl['splitcol'];
		parent::__construct($pFieldTempl);		
	}
	
	function LoadDefTempls() {
		$this->m_defTempls = array(G_HEADER => D_EMPTY, G_FOOTER => D_EMPTY, G_STARTRS => D_EMPTY, G_ENDRS => D_EMPTY, G_NODATA => D_EMPTY, G_PAGEING => D_EMPTY);
	}
	
	function GetRows() {
		if (is_array($this->m_splitcol)) return $this->GetRowsArr();
		$splitftrord = 1;
				
		$prevval = $this->con->mRs[$this->m_splitcol];
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER));
		while (!$this->con->Eof()) {
			$thisval = $this->con->mRs[$this->m_splitcol];
			$split = ($thisval != $prevval);
			
			if ($split) {
				$this->m_pubdata['splitftrord'] = $splitftrord;
				$splitftrord++;
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITFOOTER));
			}
			$prevval = $this->con->mRs[$this->m_splitcol];
			$this->GetNextRow();
			if ($split) {
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER));
			}
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
						
		}
		$this->m_pubdata['splitftrord'] = $splitftrord;
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITFOOTER));
		return $lRet;
	}
	
	function GetRowsArr() {
		foreach($this->m_splitcol as $k => $v) {
			$this->m_pubdata['splitftrord' . $k] = 1;
			$this->m_pubdata['split' . $k] = 0;
			$prevval[$k] = $this->con->mRs[$v];
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER . $k));
		}
		
		while (!$this->con->Eof()) {
			
			foreach($this->m_splitcol as $k => $v) {
				$thisval[$k] = $this->con->mRs[$v];
				$split[$k] = ($thisval[$k] != $prevval[$k]);
				$this->m_pubdata['split' . $k] = ($split[$k] ? 1 : 0);
				if ($split[$k]) {
					for ($i = count($this->m_splitcol) - 1; $i >= $k; $i--) {
						$this->m_pubdata['splitftrord' . $k] ++;
						$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITFOOTER . $i));
						$prevval[$i] = '';
					}
				}
				
				$prevval[$k] = $this->con->mRs[$v];
			}
			
			$this->GetNextRow();
			
			foreach($this->m_splitcol as $k => $v) {
				if ($split[$k]) {
					//~ for ($i = $k; $i < count($this->m_splitcol); $i++) {
						//~ $lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER . $i));
					//~ }
					$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER . $k));
				}
				
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL . $k));
			}
		}
		foreach($this->m_splitcol as $k => $v) {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITFOOTER . $k));
		}
		return $lRet;
	}

}
?>