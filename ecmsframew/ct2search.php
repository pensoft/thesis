<?php
define('T2STOPWORD_LEN', 3);
class ct2search extends crs {
	var $sqlstr;
	var $skipwarr;
	var $LikeSrch = array();
	var $VectorSrch = '';

	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		
		$this->m_pubdata['allownull'] = (int)$this->m_pubdata['allownull'];
		
		if (!is_array($this->m_pubdata['vectors'])) 
			$this->m_pubdata['vectors'] = array();
			
		if (!is_array($this->m_pubdata['fields'])) 
			$this->m_pubdata['fields'] = array();
		
		if (!$this->m_pubdata['startsel'])
			$this->m_pubdata['startsel'] = '<span>';
			
		if (!$this->m_pubdata['stopsel'])
			$this->m_pubdata['stopsel'] = '</span>';
			
		if (!$this->m_pubdata['shema'])
			$this->m_pubdata['shema'] = 'default';
			
		if (!(int)$this->m_pubdata['minwords'])
			$this->m_pubdata['minwords'] = 30;
		
		$this->m_pubdata['stext'] = $_REQUEST["stext"];
		if (!$this->m_pubdata['stext'])
			$this->m_pubdata['stext'] = $_POST['stext'];
			
		$this->skipwarr = array();
	}
	
	protected function BuildFlatClauses($txt) {
		static $prev_oper = false;
		if ($this->m_state == 2) {
			$this->m_state = 3;		
			// Buildvame like stringa za tursene na frazi
			if (preg_match_all('/"[^"]+"/', $txt, $a)) {
				foreach ($a[0] as $stext) {
					$this->LikeSrch[] = mb_strtolower(str_replace('"', '', $stext), 'UTF-8');
				}
			}
			
			setlocale(LC_CTYPE, 'bg_BG');
			// Buildvame tsearch stringa za tursene na dumi
			$lArr1 = array(UnicodeToWin(" и "), UnicodeToWin(" или "), " and ", " or ");
			$lArr2 = array(" & ", " | ", " & ", " | ");
			$lArr3 = array('&', '|', '(', ')');
			
			$txt = WinToUnicode(str_ireplace($lArr1, $lArr2, UnicodeToWin($txt)));
			setlocale(LC_CTYPE, '');
			$res = explode(' ', $txt);
			
			$lTmpStr = '';
			if (count($res)) {
				$i = 0;
				foreach ($res as $rrr) {
					if (mb_strlen($rrr, 'UTF-8') == 0) continue;
					
					// vrushta true ako e operaciq
					$this_oper = in_array($rrr, $lArr3);
					if (!$prev_oper && !$this_oper) {
						if ($i) $lTmpStr .= ' &';
					}
					if (!$this_oper) {
						// mahame neshtata deto ne sa dumi
						$rrr = str_replace(array('"', ',', ';', '.'), '', $rrr);
						// Proverqvame za kusi dumi
						if (mb_strlen($rrr, 'UTF-8') < (int)T2STOPWORD_LEN) {
							// Ako dumata e kusa mahame neq i operaciata predi neq
							$lTmpStr = mb_substr($lTmpStr, 0, (mb_strlen($lTmpStr, 'UTF-8') - 2), 'UTF-8');
							$this->skipwarr[$rrr] = $rrr;
							continue;
						}
					}
					
					$prev_oper = $this_oper;
					$lTmpStr .= ' ' . $rrr;
					$i++;
				}
				
				$this->VectorSrch = trim(q($lTmpStr));
			}
		}
	}
	
	
	protected function ParceT2Query($vectors, $flds) {
		// Buildvame clauzite za like i za tsearch
		$this->BuildFlatClauses($this->m_pubdata['stext']);
		
		if ($this->m_state == 3) {
			$tsearchStr = '';
			$likeStr = '';
			
			foreach ($flds as $fld) {
				foreach ($this->LikeSrch as $k => $like) {
					$res2[] = 'lower(s.' . $fld . ') LIKE \'%' . $like . '%\'';
				}
				if (count($res2))
					$res1[] = '(' . implode(' AND ', $res2) . ')';
			}
			if (count($res1))
				$likeStr = ' AND (' . implode(' OR ', $res1) . ')';
			
			if ($this->VectorSrch) {
				$res3 = array();
				foreach ($vectors as $fld) {
					$res3[] =  $fld . ' @@ to_tsquery(\'' . $this->m_pubdata['shema'] . '\', $$\'' . $this->VectorSrch . '\'$$)';
				}
				$tsearchStr = '(' . implode(' OR ', $res3) . ')';
				
			}
			if( $tsearchStr == ''  ) $tsearchStr=' true ';
			return ' WHERE ' . $tsearchStr . $likeStr . $this->m_pubdata['addwhere'] . $this->m_pubdata['orderby'];
		}
		
		if ((int)$this->m_pubdata['allownull']) {
			return ' WHERE true ' . $this->m_pubdata['addwhere'] . $this->m_pubdata['orderby'];
		}
		
		return ' WHERE false';
	}	
	
	function CheckVals() {
		if ($this->m_pubdata['stext']) {
			$this->m_state = 2;
		} elseif (!$this->m_pubdata['stext'] && !(int)$this->m_pubdata['allownull']) {
			$this->m_state = 1;
		}
	}
	
	function GetData() {
		if ($this->m_state == 3) {
			$this->m_pubdata['rsh'] = $this->con->Execute($this->sqlstr);
			$this->con->MoveFirst();
			$this->m_recordCount = $this->con->RecordCount();
			$this->m_pubdata['records'] = $this->m_recordCount;
			if ($this->m_pageSize) $this->con->SetPage($this->m_pageSize, (int)$this->m_page);
			if ($this->m_recordCount) {
				$this->m_pubdata['rownum'] = 1;
				foreach ($this->con->mRs as $k => $v) 
					$this->m_pubdata[$k] = $v;
			}
			$this->m_state = 4;
		}
	}
	
	protected function parseResult() {
		if (!is_array($this->m_pubdata['toparse']) || $this->m_state != 3 || !trim($this->VectorSrch)) {
			if(!trim($this->VectorSrch) && !(int)$this->m_pubdata['allownull'])
				$this->m_state=1;
			foreach ($this->m_pubdata['toparse'] as $k => $fld) {
				$selStr[] = $fld . ' as ' . $fld . '_p';
			}
			if (count($selStr))
				$selStr = ', ' . implode(', ', $selStr);
			else 
				$selStr = '';
				
			return 'SELECT s.*' . $selStr.  ' FROM (' . $this->sqlstr . ') s';
		}
		
		foreach ($this->m_pubdata['toparse'] as $k => $fld) {
			/* Patch for supporting old tsearch procedures BEGIN */
			$lSupportedProcs = array('ts_headline', 'headline');
			$lPreferredProc = null;
			foreach ($lSupportedProcs as $proc) {
				$this->con->Execute('SELECT EXISTS(SELECT * FROM pg_proc WHERE proname = \'' . q($proc) . '\')::int AS exists');
				$this->con->MoveFirst();
				if ((int)$this->con->mRs['exists']) {
					$lPreferredProc = $proc;
					break;
				}
			}
			if (is_null($lPreferredProc))
				trigger_error('None of the procedures: "' . implode(', ', $lSupportedProcs) . '" exists!'. "\n", E_USER_ERROR);
			/* Patch for supporting old tsearch procedures END */
			
			$selStr[] = $lPreferredProc . '(\'' . $this->m_pubdata['shema'] . '\', ' . $fld . ', q, \'StartSel=' . $this->m_pubdata['startsel'] . ', StopSel=' . $this->m_pubdata['stopsel'] . ', MinWords=' . $this->m_pubdata['minwords'] . '\') as ' . $fld . '_p';
		}
		if (count($selStr))
			$selStr = ', ' . implode(', ', $selStr);
		else 
			$selStr = '';
		
		return 'SELECT s.*' . $selStr.  ' FROM (' . $this->sqlstr . ') s, to_tsquery(\'' . $this->m_pubdata['shema'] . '\', $$\'' . $this->VectorSrch . '\'$$) AS q ';
	}
	
	function Display() {
		$this->CheckVals();
		
		// Zalepqme sql-a sus buldnatata where klauza
		$this->sqlstr = $this->m_pubdata['sqlstr'] . $this->ParceT2Query($this->m_pubdata['vectors'], $this->m_pubdata['fields']);
		
		if ((int)$this->m_pubdata['allownull']) {
			$this->m_state = 3;
		}
		
		// Pravim sql-a za ocvetqvaneto
		$this->sqlstr = $this->parseResult();
		
		if ((int)$this->m_pubdata['debug']) {
			echo '<pre>' . $this->sqlstr . '</pre>';
		}
		
		$this->GetData();
		
		$this->m_pubdata['nav'] = $this->DisplayPageNav($this->m_page);
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_state == 4 || $this->m_state == 1) {
			if ($this->m_recordCount == 0) {
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
			} else {
				$this->m_pubdata['skipw'] = '';
				if (count($this->skipwarr)) {
					$this->m_pubdata['skipw'] = implode(', ', $this->skipwarr);
				}
				if ($this->m_pubdata['skipw']) $this->m_pubdata['skipw'] = "Следните думи не бяха включени в търсенето Ви: <b>" . $this->m_pubdata['skipw'] . "</b>";
				$this->m_pubdata['docnumtext']  = $this->m_recordCount ." намерен" . ($this->m_recordCount > 1 ? 'и' : 'а' );
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
				$lRet .= $this->GetRows();
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
			}
		}
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
		
		if ($this->m_pageSize && $this->m_recordCount && !(int)$this->m_pubdata['hidedefpaging'])
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING));
		
		return $lRet;
	}
}
?>