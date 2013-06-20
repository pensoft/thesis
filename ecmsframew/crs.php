<?php
class crs extends cbase {
	var $m_recordCount;
	var $m_currentRecord;
	var $m_pageSize;
	var $m_defTempls;
	var $m_page;
	var $m_RecordNumber;
	var $con;
	var $m_dontgetdata;

	function __construct($pFieldTempl) {
		$this->m_page = (int)$_REQUEST['p'];
		$this->con = new DBCn;
		$this->con->Open();
		//$this->con = Con();
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		if ($this->m_pubdata['debug']) $this->con->debug=$this->m_pubdata['debug'];
		$this->m_pageSize = (int)$this->m_pubdata["pagesize"];
		$this->LoadDefTempls();
	}
	
	function DontGetData($p) {
		$this->m_dontgetdata = $p;
	}	
	
	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}
		
		$this->m_defTempls = array(G_HEADER => D_EMPTY, G_FOOTER => D_EMPTY, G_STARTRS => D_EMPTY, G_ENDRS => D_EMPTY, G_NODATA => D_EMPTY, G_PAGEING => D_EMPTY, G_ROWTEMPL => D_EMPTY);
	}
	
	function SetTemplate($pTemplId) {
		$this->m_HTempl = $pTemplId;
	}
	
	function GetRowFromRs($pKey) {
		return $this->m_currentRecord[$pKey];
	}
	
	function CheckVals() {
		if($this->m_state == 0) {
			//~ if (((int)$this->m_pubdata['storyid']) !== $this->m_pubdata['storyid']) {
				//~ return;
				//~ trigger_error("NE e int", E_USER_WARNING);
			//~ }
			$this->m_state++;
		} else {
			// NOTICE
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
			if ($this->m_recordCount) {
				$this->m_pubdata['rownum'] = 1;
				foreach ($this->con->mRs as $k => $v) {
					$this->m_pubdata[$k] =$this->m_currentRecord[$k] = $v;
				}	
			}
		}
	}
	
	function GetNextRow() {
		$ret = $this->con->Eof();
		if (!$ret) {
			foreach ($this->con->mRs as $k => $v) {
				$this->m_pubdata[$k] = $this->m_currentRecord[$k] = $v;
			}
			$this->con->MoveNext();
		}
	}
	
	function GetRows() {
		while (!$this->con->Eof()) {
			$this->GetNextRow();
			
			if ($this->m_pubdata['templadd'])
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL , $this->m_currentRecord[$this->m_pubdata['templadd']]));
			else 
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
				
			$this->m_pubdata['rownum']++;			
		}
		return $lRet;
	}
	
	function Display() {
		if (!$this->m_dontgetdata)
			$this->GetData();
		
		if ($this->m_state < 2) {
			return;
		}
		
		$this->m_pubdata['nav'] = $this->DisplayPageNav($this->m_page);
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_recordCount == 0) {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetRows();
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
		
		if ($this->m_pageSize && $this->m_recordCount && !(int)$this->m_pubdata['hidedefpaging'])
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING));
		
		return $lRet;
	}
	
	function DisplayPageNav($p) {
		if (!$this->m_pageSize) return '';
		if ((int)$this->m_pubdata['usecustompn']) return $this->CustomPageNav($p);
		$lGroupStep = ($this->m_pubdata['groupstep'] ? $this->m_pubdata['groupstep'] : 5);
		$lMaxPages = ceil($this->m_recordCount / $this->m_pageSize);
		if ($p > $lMaxPages) $p = 0;
		if ($p == -1) $p = $lMaxPages - 1;//kogato predadesh p = -1 te move-a do poslednata stranica
		
		if ($lMaxPages > 1) {
			$lPageGroup = (int)($p / $lGroupStep) * $lGroupStep;
			
			foreach ($_REQUEST as $key => $val) {
				if (is_array($val)) {
					foreach ($val as $v) {
						$url .= "{$key}[]={$v}&";
					}
				}
				else 
					if ($key != "p" && $key != "rn") $url .= "{$key}={$val}&";
			}
			
			if ($lPageGroup + 1 > $lGroupStep) {
				$lNavStr .= '<a class="lnkTxt3" title="Към страница ' . ($lPageGroup) . '" href="?' . $url . 'p=' . ($lPageGroup - 1) . '">«</a>';
			}
			for ($i = $lPageGroup; (($i < $lPageGroup + $lGroupStep) && ($i < $lMaxPages)); $i++) {
				if ($i == $p) {
					$lNavStr .=  '&nbsp;|&nbsp;<span class="txt8">' . ($i+1) . '</span>';
				} else {
					$lNavStr .= '&nbsp;|&nbsp;<a class="lnkTxt3" href="?' . $url . 'p=' . $i . '" title="Отиди на страница ' . ($i+1) . '">'. ($i+1) . "</a>";
				}
			}
			if ($lPageGroup < $lMaxPages - $lGroupStep) {
				$lNavStr .= '&nbsp;|&nbsp;<a title="Към страница ' . ($lPageGroup + $lGroupStep + 1) . '" class="lnkTxt3" href="?'.$url . 'p=' . ($lPageGroup + $lGroupStep) . '">»</a>';
			}
			else $lNavStr .= '&nbsp;|&nbsp;';
		}
		return $lNavStr;
	}
	
	function CustomPageNav($p) {
		if (!$this->m_pageSize) return '';
		$lGroupStep = ($this->m_pubdata['groupstep'] ? $this->m_pubdata['groupstep'] : 5);
		$lMaxPages = ceil($this->m_recordCount / $this->m_pageSize);
		$this->m_pubdata['maxpages'] = $lMaxPages;
		if ($p > $lMaxPages) $p = 0;
		if ($p == -1) $p = $lMaxPages - 1;//kogato predadesh p = -1 te move-a do poslednata stranica
		
		$this->m_pubdata['currpage'] = (int)$p + 1;
		
		if ($lMaxPages > 1) {
			$lPageGroup = (int)($p / $lGroupStep) * $lGroupStep;
			foreach ($_REQUEST as $key => $val) {				
				if (is_array($val)) {
					foreach ($val as $v) {
						$url .= $key . '[]=' . urlencode(s($v)) . '&';
					}
				}
				else {
					if ($key != "p" && $key != "rn")
						$url .= $key . '=' . urlencode(s($val)) . '&';
				}
			}
			
			$this->m_pubdata['pageingurl'] = $url;
			$this->m_pubdata['gotopage'] = 0;
			
			if ((int)$this->m_pubdata['usefirstlast']) 
				if ($p == 0) {
					$lFirst = $this->ReplaceHtmlFields($this->getTemplate('pageing.inactivefirst'));
				} else {
					$lFirst = $this->ReplaceHtmlFields($this->getTemplate('pageing.activefirst'));
				}
			
			if ($lPageGroup + 1 > $lGroupStep) {
				$this->m_pubdata['lpagegroup'] = $lPageGroup;
				$this->m_pubdata['gotopage'] = ($lPageGroup - 1);
				$lPgStart = $this->ReplaceHtmlFields($this->getTemplate('pageing.pgstart'));
			}
			
			if ($this->m_pubdata['pagingstartrevord']) {
				$lNavStr .= $lPgStart . $lFirst;
			} else {
				$lNavStr .= $lFirst . $lPgStart;
			}
			
			for ($i = $lPageGroup; (($i < $lPageGroup + $lGroupStep) && ($i < $lMaxPages)); $i++) {
				$this->m_pubdata['lpagenum'] = ($i+1);
				if ($i == $p) {
					$lNavStr .= $this->ReplaceHtmlFields($this->getTemplate('pageing.inactivepage'));
				} else {
					$this->m_pubdata['gotopage'] = $i;
					$lNavStr .= $this->ReplaceHtmlFields($this->getTemplate('pageing.activepage'));
				}
			}
			if ((int)$this->m_pubdata['usefirstlast']) 
				if (($p + 1) == $lMaxPages) {
					$lLast = $this->ReplaceHtmlFields($this->getTemplate('pageing.inactivelast'));
				} else {
					$lLast = $this->ReplaceHtmlFields($this->getTemplate('pageing.activelast'));
				}
			
			if ($lPageGroup < $lMaxPages - $lGroupStep) {
				$this->m_pubdata['lpagegroup'] = ($lPageGroup + $lGroupStep + 1);
				$this->m_pubdata['gotopage'] = ($lPageGroup + $lGroupStep);
				$lPgEnd = $this->ReplaceHtmlFields($this->getTemplate('pageing.pgend'));
			} else 
				$lPgEnd = $this->ReplaceHtmlFields($this->getTemplate('pageing.delimeter'));
			
			if((int)$this->m_pubdata['pagingendrevord']) {
				$lNavStr .= $lLast;
				$lNavStr .=$lPgEnd;
			}else{
				$lNavStr .=$lPgEnd;
				$lNavStr .= $lLast;
			}
		}
		
		return $lNavStr;
	}
}
?>