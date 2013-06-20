<?php

/**
 * A base class that will display recursive lists.
 *
 * @author peterg
 *
 */
class evList_Recursive_Display extends evList_Display {

	function __construct($pData){
		parent::__construct($pData);
		
		//~ var_dump( $this->m_controllerData );
	}

	function GetRowsRecursive($varr = array()){
		$lLast = '';
		$lRet = "";
		if(! $this->MoreRowsExist()){
			return $lRet;
		}
		$lGetNextRow = 1;

		while($this->MoreRowsExist() || (! $lGetNextRow && $this->MoreRowsExist())){
		
			
			
			if($lGetNextRow)
				$this->GetNextRow();

			$lGetNextRow = 1;
			
			if($this->m_pubdata['type'] == MT_LINK)
				$objTemplate = $this->getObjTemplate(G_RAZDTEMPL, ($this->m_pubdata['templrazdadd'] ? $this->m_pubdata[$this->m_pubdata['templrazdadd']] : ""));
			else
				$objTemplate = $this->getObjTemplate(G_ROWTEMPL, ($this->m_pubdata['templadd'] ? $this->m_pubdata[$this->m_pubdata['templadd']] : ""));
				
			if($this->m_pubdata['recursivecolumn']){
				if(in_array($this->m_pubdata[$this->m_pubdata['recursivecolumn']], $varr)) {
					// ако сме взели последния ред и не съществуват повече редове - показваме и него 
					if( !$this->MoreRowsExist() && $lGetNextRow && $this->m_pubdata['type'] ==  MT_LINK ) {
						$lRet .= $this->ReplaceHtmlFields($objTemplate);
					}
					return $lRet; // krai na rekursia
				}
				

				//~ $objTemplateArr = split("\{\&\}", $objTemplate);
				$objTemplateArr = preg_split("/\{\&\}/", $objTemplate);
				if(count($objTemplateArr) > 1){
					$objResArr = array();
					foreach($objTemplateArr as $k => $v){
						$objResArr[$k] = $this->ReplaceHtmlFields($v);
					}
					if($this->MoreRowsExist()){
						$lres = $this->GetRowsRecursive(array_merge($varr, array(
							$this->m_pubdata[$this->m_pubdata['recursivecolumn']]
						))); // rekursia
						
							// ако сме взели последния ред и не съществуват повече редове - показваме и него 
							if( !$this->MoreRowsExist() && $lGetNextRow && $this->m_pubdata['type'] !=  MT_LINK ) {
								$lLast = $this->ReplaceHtmlFields($objTemplate);
							}
						
						$lGetNextRow = 0;
					}else
						$lres = "";
					$lRet .= implode($lres, $objResArr);
				}else
					$lRet .= $this->ReplaceHtmlFields($objTemplate);
			}else
				$lRet .= $this->ReplaceHtmlFields($objTemplate);
			$this->m_pubdata['rownum'] ++;
		}
		return $lRet . $lLast;
	}

	function GetRows() {
		$this->m_pubdata['rownum'] = 0;
			/**
			 * We process only the rows on the current page
			 * @var unknown_type
			 */
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
// 			var_dump($this->m_pubdata['templadd']);

		return $this->GetRowsRecursive();

	}




}

?>