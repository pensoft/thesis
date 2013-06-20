<?php
require_once('static.php');
global $gEcmsLibRequest;

class DBList {
	var $print;
	var $mRowCounter;
	var $mSqlStr;
	var $mPageSize;
	var $mGrpStep;
	var $mDefOrder;
	var $mAntetFieldArr; 
	var $mOrder;
	var $mOrderD;
	var $ordby;
	var $ordd;
	
	var $msetTemplate = 0;
	var $mTemplateStr;
	var $mTemplateArr;
	var $mTemplateMask;
	var $mTableOpenTag;
	var $mTableCloseTag;
	var $mAlternateColors;
	var $mColors ;
	var $mColorsCount ;
	
	function DBList($opentag = false) {
		$this->print = 0;
		$this->mPageSize = 0;
		$this->mRowCounter = 0;
		$this->ordby = 'ordby';
		$this->ordd = 'ordd';
		$this->mTableOpenTag = ($opentag ? $opentag : '<table width="100%" class="datatable">');
		$this->mTableCloseTag = '</table>';
		$this->SetAlternateColors(true);
	} 
	
	function SetAlternateColors($pIsActive, $pColors = array('white_row', 'color_row')) {
		$this->mAlternateColors = $pIsActive;
		$this->mColors = $pColors;
		$this->mColorsCount = count($pColors);
		if (!$this->mColorsCount) $this->mAlternateColors = false;
	}
	
	function SetPageSize($pPageSize) {
		$this->mPageSize = $pPageSize;
	}
	
	function SetGrpStep($pGrpStep) {
		$this->mGrpStep = $pGrpStep;
	}
	
	function SetCloseTag($pCloseTag) {
		$this->mTableCloseTag = $pCloseTag;
	}
	
	function SetTemplate($pTempl = 0, $pTemplMask = 0) {
		$this->msetTemplate = 1;
		$this->mTemplateStr = $pTempl;
		$this->mTemplateMask = $pTemplMask;
	}

	function SetQuery($pQueryStr) {
		$this->mSqlStr = $pQueryStr;
	}
	
	function SetPrint($print) {
		$this->print = $print;
	}
	
	function SetOrderParams($pOrdField, $pPosoka) {
		$this->mOrder = $pOrdField;
		$this->mOrderD = $pPosoka;
	}
	
	function SetOrderParamNames($ordby = 'ordby', $ordd = 'ordd') {
		$this->ordby = $ordby;
		$this->ordd = $ordd;
	}
	
	function SetAntet($p){
		$this->mAntetFieldArr = $p;
	}
	
	function GetAntet($pFieldArr) {
		if (is_array($pFieldArr)) {
			foreach ($_REQUEST as $key => $val) {
				if ($key == 'p' || $key == $this->ordby || $key == $this->ordd) continue;
				if (is_array($val))
					foreach($val as $val1)
						$lUrl .= "{$key}[]={$val1}&";
				else 
					$lUrl .= "{$key}={$val}&";
			}
			$this->mTableOpenTag .= '<tr>';
			$lUrl = $_SERVER['SCRIPT_NAME'] . ($lUrl ? '?' . $lUrl : '?');
			foreach($pFieldArr as $k => $v) {
				if (!$this->mOrder && in_array('def', $v)) {
					$this->mOrder = $k;
				}
				if ($k == (int)$this->mOrder) {
					$lArrowStr = '&nbsp;&#' . ($this->mOrderD ? 9650 : 9660 ) . ';'; 
					switch ($v['deforder']) {
						case 'asc': 
							$deford = 'asc';
							$adord = 'desc';
							break;
						case 'desc':
						default:
							$deford = 'desc';
							$adord = 'asc';
							break;
					}
				} else {
					$lArrowStr = '';
				}
				
				$lUrlNew = $lUrl . $this->ordby . '=' . $k . '&' . $this->ordd . '=' . !$this->mOrderD;
				
				if ($k > 0) 
					$this->mTableOpenTag .= '<th ' . $v['addtags'] . '><a href="' . $lUrlNew . '">' . $v['caption'] . '</a>' . $lArrowStr . '</th>';
				else 
					$this->mTableOpenTag .= '<th ' . $v['addtags'] . '>' . $v['caption'] . '</th>';
				$lUrlNew = '';
			}
			$this->mTableOpenTag .= '</tr>';
			$this->mSqlStr .= ' ORDER BY ' . $this->mOrder . ' ' . ($this->mOrderD ? $adord : $deford);
		}
	}
	
	function DisplayRow($pRs) {
		global $gEcmsLibRequest;
		if( !$gEcmsLibRequest ){//Defaultno povedenie
			$lTemplateStr = $this->mTemplateStr;
			$lTemplateStr = preg_replace('/{_([\w\d]+)}/me', "\\1(\$pRs)", $lTemplateStr);
			$lTemplateStr = preg_replace('/{_(([\w\d]+)\(([\w\d]+)\))}/me', '\2($pRs[\3])', $lTemplateStr);
			
			if ( $this->mAlternateColors ) {
				$lTemplateStr = preg_replace("/\<td/m", '<td class="' . $this->mColors[$this->mRowCounter % $this->mColorsCount] . '" ', $lTemplateStr);
			}
			
			$this->mRowCounter++;
			return str_replace($this->mTemplateArr, $this->ParseRow($pRs), $lTemplateStr);
		}else{//Vryshta masiv sys stoinosti key=>val na vsi4ki poleta ot rezultata na sql zaqvkata
			$lResult = array();
			foreach($pRs as $key => $val ){
				$lResult[$key] = $val;
			}
			return $lResult;
		}
	}


	function ParseRow($pArrIn) {
		$i = 0;
		foreach($pArrIn as $k => $v) {
			if (!is_numeric($k)) {
				switch($this->mTemplateMask[$i++]) {
					case UNILIST_P_NULL:
						$lArrOut[$k] = $v;
						break;
					default:
						$lArrOut[$k] = $v;						
				}
			}
		}
		return $lArrOut;
	}
	
	function DisplayListCn(&$prCn, $p = 0, $print = 0) {
		global $gEcmsLibRequest;
		
		$res = '';
		$i = 0;
		
		if (!$this->mTemplateStr) {
			$chkTempStr = 1;
			$this->mTemplateStr = '<tr>';
		}
		
		foreach($prCn->mRs as $k => $v) {
			if (!is_numeric($k)) {
				$this->mTemplateArr[$i] = '{' . $k . '}';
				if ($chkTempStr) $this->mTemplateStr.='<td>{'.$k.'}</td>';
				$i++;
			}
		}
		
		if ($chkTempStr) $this->mTemplateStr .= '</tr>';
		
		if ($prCn->Eof()){
			return 0;
		}
		
		$lGroupStep = ($this->mGrpStep ? $this->mGrpStep : 5);
		$lMaxRecords = $prCn->RecordCount();
		$lPageSize = $prCn->mPageSize;
		
		if ((int)$lPageSize) {
			$lMaxPages = ceil($lMaxRecords / $lPageSize);
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
						if ($key != 'p' && $key != 'rn') $url .= "{$key}={$val}&";
				}
				
				$lNavStr .= '<div class="paging">';
				
				$lNavStr .= '<div class="pageing_info">' . getstr('dblist.pageInfo', array('pagenum' => ($p+1), 'maxpages' => $lMaxPages)) . '</div>';
				
				if ($lPageGroup + 1 > $lGroupStep) {
					$lNavStr .= '<a title="' . getstr('dblist.gotoPage', array('pagenum' => $lPageGroup)) . '" href="?' . $url . 'p=' . ($lPageGroup - 1) . '">
						<img alt="' . getstr('dblist.gotoPage', array('pagenum' => $lPageGroup)) . '" src="/img/fback.gif" align="absmiddle" border="0">
					</a>&nbsp;';
				}
				
				if ($p > 0) {
					$lNavStr .= '<a title="' . getstr('dblist.prevPage') . '" href="?' . $url . 'p=' . ($p - 1) . '">
						<img alt="' . getstr('dblist.prevPage') . '" src="/img/back.gif" align="absmiddle" border="0">
					</a>&nbsp;';
				}
				
				for ($i = $lPageGroup; (($i < $lPageGroup + $lGroupStep) && ($i < $lMaxPages)); $i++) {
					if ($i == $p) {
						$lNavStr .=  '<b>' . ($i+1) . '</b>&nbsp;';
					} else {
						$lNavStr .= '<a href="?' . $url . 'p=' . $i . '" title="' . getstr('dblist.gotoPage', array('pagenum' => ($i+1))) . '">'. ($i+1) . '</a>&nbsp;';
					}
				}
				
				if ($p < $lMaxPages - 1) {
					$lNavStr .= '<a title="' . getstr('dblist.nextPage') . '" href="?' . $url . 'p=' . ($p + 1) . '">
						<img alt="' . getstr('dblist.nextPage') . '" src="/img/fwd.gif" align="absmiddle" border="0">
					</a>&nbsp;';
				}
				
				if ($lPageGroup < $lMaxPages - $lGroupStep) {
					$lNavStr .= '<a title="' . getstr('dblist.gotoPage', array('pagenum' => ($lPageGroup + $lGroupStep + 1))) . '" href="?'.$url . 'p=' . ($lPageGroup + $lGroupStep) . '">
						<img alt="' . getstr('dblist.gotoPage', array('pagenum' => ($lPageGroup + $lGroupStep + 1))) . '" src="/img/ffwd.gif" align="absmiddle" border="0">
					</a>';
				}
				
				$lNavStr .= '</div>';
			}
		}
		
		if(!(int) $gEcmsLibRequest){//Defaultno povedenie
		
		
			if ($print) $res .= $this->mTableOpenTag; else echo $this->mTableOpenTag;
			
			while (!$prCn->Eof()) {
				$res1 = $this->DisplayRow($prCn->mRs);
				if ($print) $res .= $res1; else echo $res1;
				$prCn->MoveNext();
			}
			if ($print) $res .= $this->mTableCloseTag; else echo $this->mTableCloseTag;

			if ($lMaxPages > 1) {
				if ($print) $res .= $lNavStr; else echo $lNavStr;
			}
			
			if ($print) return $res; else return 1;
		}else{//Vryshta masiv sys masivi za vseki red ot rezultatite
			$lResult = array();
			while (!$prCn->Eof()) {
				$lRowArray = $this->DisplayRow($prCn->mRs);				
				$prCn->MoveNext();
				$lResult[] = $lRowArray;
			}
			return $lResult;
			
		}
	}

	function DisplayList($pPage = 0) {
		global $gEcmsLibRequest;
		
	
		$this->GetAntet($this->mAntetFieldArr);
		$lCn = Con();
		
		if( (int) $gEcmsLibRequest )			
			$lCn->SetPageSize(0);//Za da pokajem vsi4ki rezultati
		else{
			$lCn->SetPageSize($this->mPageSize);
			$lCn->SetPageNum($pPage);
		}
		
		if (!$this->msetTemplate) 
			$this->SetTemplate();
		$lCn->Execute($this->mSqlStr);
		$lCn->MoveFirst();
		
		if( !(int) $gEcmsLibRequest ){
			if ($lCn->Eof()) 
				return '';
			else 
				return $this->DisplayListCn($lCn, $pPage, $this->print);
		}else{
			$lError = 0;
			$lErrorMsg = trim($lCn->GetLastError());
			if( $lErrorMsg != '' )
				$lError = 1;
			$lResultArray = $this->DisplayListCn($lCn, $pPage, $this->print);
			
			$lJSONOrganizer = getJSONOrganizer();
			$lJSONOrganizer->addNewJSONList($lResultArray, (int)$lError, $lErrorMsg);				
			
			return 1;//Vryshtame 1, zashtoto na pove4eto mesta, ako se vyrne prazen string, se izkarva drug string
		}
	}
}

?>
