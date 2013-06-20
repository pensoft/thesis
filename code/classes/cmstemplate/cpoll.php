<?php
class cpoll extends crs {
	private $lm_ColWidth;
	
	function __construct($pFieldTempl) {
		$this->con = new DBCn;
		$this->con->Open();
		$this->m_state = 1;
		
		$this->m_page = (int)$_GET['p'];
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		$this->lm_ColWidth = $this->m_pubdata['colwidth'];
		if( !$this->lm_ColWidth)
			$this->lm_ColWidth = 200; //tva e shirinata na diasnata kolona i ia izpolzvam za termometrite
		$this->m_pageSize = ( int ) $this->m_pubdata['pagesize'];
		
		if ((int) $_REQUEST['posid']) {
			$this->m_pubdata['posid'] = (int) $_REQUEST['posid'];
		}
		
		if (!$this->m_pubdata['formmethod']) {
			$this->m_pubdata['formmethod'] = 'GET';
		}
		
		if (!$this->m_pubdata['pollid']) {
			$this->m_pubdata['pollid'] = (int)$_REQUEST['pollid'];
		}
		
		$activePoll = 1;
		if ($this->m_pubdata['pollid']) {
			if ($this->m_pubdata['archiv']) {
				$activePoll = 0;
			} else {				
				$sql = 'SELECT active FROM poll WHERE id = ' . $this->m_pubdata['pollid'];
				$this->con->Execute($sql);
				$this->con->MoveFirst();
				if (!$this->con->Eof()) {
					if (!($this->con->mRs['active'] )) {
						$activePoll = 0;
					}
				}
			}
		}elseif($this->m_pubdata['showtype'] != 1){//Izbirame aktivna anketa za tekushtata poziciq
			$sql = 'SELECT * FROM poll WHERE pos = ' . $this->m_pubdata['posid'] . ' AND active = 1 AND startdate::date <= now()::date AND enddate::date >= now()::date AND lang = ' . getlang();
			$this->con->Execute($sql);
			$this->con->MoveFirst();
			if (!$this->con->Eof()) {
				$this->m_pubdata['pollid'] = $this->con->mRs['id'];
			}
		}
		
		if ($_REQUEST['formname'] == "anketa" && $_REQUEST['votenow']) { //imame post
			$this->m_state = 1;
			$activePoll = 0;
			if (is_array($_REQUEST['anketaans'])) {
				foreach ($_REQUEST['anketaans'] as $k => $v) {
					$this->m_pubdata['answers'] .= (int) $v . ";";
				}
				$this->m_pubdata['answers'] = substr($this->m_pubdata['answers'], 0, -1);
			} else {
				$this->m_pubdata['answers'] = (int) $_REQUEST['anketaans'];
			}
		}
		if( $this->m_pubdata['showtype'] == 1 ) {//Browse
			$this->m_pubdata['sqlstr'] = 'SELECT * FROM getAllPolls('. ( int ) $this->m_pubdata['posid'] . ', '  . ( int ) $this->m_pubdata['siteid'] . ', ' . getlang() . ' ) ' . ( (int) $this->m_pubdata['limit'] ? ' LIMIT ' . (int) $this->m_pubdata['limit']  : '' );
			//~ $this->m_pubdata['sqlstr'] = 'SELECT * FROM getAllPolls( ' . ( int ) $this->m_pubdata['admin'] . ', '. ( int ) $this->m_pubdata['posid'] . ', '  . ( int ) $this->m_pubdata['siteid'] . ' ) ' . ( (int) $this->m_pubdata['limit'] ? ' LIMIT ' . (int) $this->m_pubdata['limit']  : '' ) . ' WHERE FALSE';
		}else{
			if ($activePoll) {		// aktivna => vuzmozhnost za glasuvane
				$this->m_pubdata['sqlstr'] = 'SELECT *
					FROM 
					GetAnketa(\'' . $_SERVER['REMOTE_ADDR'] . '\', ' . $this->m_pubdata['posid'] . ', ' . $this->m_pubdata['siteid'] . ', '. $this->m_pubdata['pollid'] .', ' . getlang() . ') 
					';
					
			} else {				// bez vuzmozhnost za glasuvane
				$this->m_pubdata['sqlstr'] = 'SELECT * FROM 
					GetAnketaArchiv(' . $this->m_pubdata['pollid'] . ', ' . $this->m_pubdata['siteid'] . ', ' . getlang() . ' )
				';
			}
		}		
	}
	
	function CheckVals() {
		if ($this->m_pubdata['answers'] && $this->m_pubdata['pollid'] && $this->m_state == 1) {
			$this->m_state = 2;
		}
	}
	
	function SaveData() {
		$this->CheckVals();
		if ($this->m_state == 2) {
			$lcn = con();
			$SqlStr = "SELECT * FROM AnketaVote(" . $this->m_pubdata['pollid'] . ", '" . $this->m_pubdata['answers'] . "', '" . $_SERVER['REMOTE_ADDR'] . "')";
			$lcn->Execute($SqlStr);
			if ($this->m_pubdata['backurl']) {
				header("Location: " . urldecode($this->m_pubdata['backurl'] . "#anketa"));
				exit;
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
			if ($this->m_recordCount) {
				foreach ($this->con->mRs as $k => $v)
					$this->m_pubdata[$k] =$this->m_currentRecord[$k] = $v;
			}
			$this->m_pubdata['question'] = $this->m_currentRecord['polltxt'];
		}
	}
	
	function GetRows() {
		$lPrevVal = -5;
		while (!$this->con->Eof()) {
			$curline = $this->con->mCurrentRecord;
			$this->m_pubdata['display_back'] = '';
			if( $this->m_pubdata['showtype'] == 1 ){//Browse
				$lThisval = (int) $this->con->mRs['polltype'];
				$this->GetNextRow();
				$lSplit = ( ! ($lThisval == $lPrevVal ) );
				$lPrevVal = $lThisval ;
				if( $lSplit )
					$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER));
			}else{
				// viewtype 1 - znachi stulbcheta inache radio/checkboxi
				$this->GetNextRow();
				$this->m_pubdata['viewtype'] = (int) $this->m_pubdata['pollviewtype'];
				
				if (!$this->m_pubdata['viewtype'] && $_REQUEST['viewtype']) {
					$this->m_pubdata['viewtype'] = 1;
					$this->m_pubdata['display_back'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_BACKBUTTON));
					//~ var_dump($this->m_pubdata['display_back']);
				}
										
				if ($this->m_pubdata['viewtype'] == 1) {
					$this->m_pubdata['answidth'] = ($this->m_pubdata['sum'] ? ((int)$this->m_pubdata['votes'] / $this->m_pubdata['sum']) * $this->lm_ColWidth : 1);
					if (!$this->m_pubdata['answidth']) {
						$this->m_pubdata['answidth'] = 1;
					}
					$this->m_pubdata['ansprocent'] = ($this->m_pubdata['sum'] ? (round($this->m_pubdata['votes'] / $this->m_pubdata['sum']*100, 2)) : 0);
				} else {
					$this->m_pubdata['inptype'] = ($this->m_pubdata['mult'] ? "checkbox" : "radio");
				}
					
				if (!$this->m_pubdata['viewtype']) {
					$this->m_pubdata['string'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_ANSINPUT));
				} else {
					$this->m_pubdata['string'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_ANSRESULT));
				}
			}
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
		}
		return $lRet;
	}
	
	function Display() {
		if ($this->m_state == 1) {
			$this->SaveData();
		}	
		
		$this->GetData();
		$this->m_pubdata['nav'] = $this->DisplayPageNav($this->m_page);
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if ($this->m_recordCount == 0) {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		} else {
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $this->GetRows();
			if ($this->m_pubdata['viewtype']) {
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRSNOBUT));
			} else {
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
			}
		}
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));		
		return $lRet;	
	}
}
?>