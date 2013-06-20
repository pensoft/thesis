<?php

/**
 * A base class that will display list elements grouped by a specific rule.
 *
 * @author peterg
 *
 */
class evList_Group_Display extends evList_Display {
	var $m_splitCol;

	function __construct($pData){
		parent::__construct($pData);
		$this->m_splitCol = $this->m_pubdata['splitcol'];
		//~ var_dump( $this->m_controllerData );
	}

	function GetRows() {
		$this->m_pubdata['rownum'] = 0;
		/**
		 * We process only the rows on the current page
		 * @var unknown_type
		 */
		if( !$this->m_UseDbPaging ) {
			$lRowsToProcess = array();
			$lCurrentRow = 0;
			foreach($this->m_controllerData as $lRow ){
				$lCurrentRow++;
				if( $this->m_pageSize ){//Ako ima stranicirane
					if( $lCurrentRow <= ($this->m_pageSize * $this->m_pageNum))//Predhodni redove
						continue;
					if( $lCurrentRow > ($this->m_pageSize * ($this->m_pageNum + 1)))//Sledvashti redove
						break;
					$lRowsToProcess[] = $lRow;
				}else{
					$lRowsToProcess[] = $lRow;
				}
			}
			$this->m_currentRowIdx = 0;
			$this->m_rowsToProcess = $lRowsToProcess;
		} else {
			foreach($this->m_controllerData as $lRow ){
				$lRowsToProcess[] = $lRow;
			}
			$this->m_currentRowIdx = 0;
			$this->m_rowsToProcess = $lRowsToProcess;
		}
		$lRet = "";
		$this->FetchFirstRow();
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER));

		$lPrevVal = $this->m_pubdata[$this->m_splitCol];
		while($this->MoreRowsExist()){
			$this->m_pubdata['rownum'] ++;

			$lCurrentVal = $this->GetNextRowField($this->m_splitCol);
			$lHasSplit = ($lCurrentVal != $lPrevVal);
// 			var_dump($lCurrentVal, $lPrevVal);

			if ($lHasSplit) {
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITFOOTER));
			}
			$this->GetNextRow();
			if ($lHasSplit) {
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER));
			}
			if(isset($this->m_pubdata['templadd'])){
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL, $this->m_currentRecord[$this->m_pubdata['templadd']]));
			}else{
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
			}
// 			if ($lHasSplit) {
// 				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITHEADER));
// 			}


			$lPrevVal = $lCurrentVal;
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_SPLITFOOTER));

		return $lRet;
	}

	function GetNextRowField($pFieldName){
		$lCurrentRowIdx = $this->m_currentRowIdx ? $this->m_currentRowIdx : 0;
		if(($lCurrentRowIdx + 1) > count($this->m_rowsToProcess)){
			return;
		}

		return $this->m_rowsToProcess[$lCurrentRowIdx][$pFieldName];
	}

	function FetchFirstRow() {
		if(!$this->MoreRowsExist()){
			return;
		}
		$lCurrentRow = $this->m_rowsToProcess[0];

		$this->FetchRow($lCurrentRow);
// 		var_dump($this->m_pubdata);
	}



}

?>