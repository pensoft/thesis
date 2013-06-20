<?php

/**
 * A base class that will display lists.
 * It gets its data from a controller and is being displayed by a view
 * An output is generated for each row given by the controller
 *
 * @author peterg
 *
 */
class evList_Display extends evbase_view {
	/**
	 * The data provided by the controller. It should be an array of rows (arrays which contain
	 * data for the current row in the format key => value).
	 *
	 * @var array
	 */
	var $m_controllerData;

	/**
	 * The actual rows which will be displayed (May differ form m_controllerData if page size and page num are used)
	 * @var unknown_type
	 */
	var $m_rowsToProcess;

	/**
	 * The number of records in the list
	 *
	 * @var int
	 */
	var $m_recordCount;
	/**
	 * The number of records per page (0 - everything is in 1 page)
	 *
	 * @var unknown_type
	 */
	var $m_pageSize;
	/**
	 * The number of the current page
	 *
	 * @var unknown_type
	 */
	var $m_pageNum;
	/**
	 * An array containing the data of the current row during the loop through
	 * all the rows
	 *
	 * @var unknown_type
	 */
	var $m_currentRecord;

	/**
	 * The idx of the current row
	 * @var unknown_type
	 */
	var $m_currentRowIdx;

	/**
	 * The name of the parameter which will hold the page number
	 * in the request
	 * @var unknown_type
	 */
	var $m_pageParameterName;
	
	/*
		Paging type
	*/
	var $m_UseDbPaging ;
	
	function __construct($pData) {
		parent::__construct($pData);

		if( gettype($this->m_pubdata['controller_data']) == 'object' ) {
			$this->m_controllerData = $this->m_pubdata['controller_data']->m_Data;
			
			$this->m_pageNum = $this->m_pubdata['controller_data']->m_PageNum;
			$this->m_pubdata['page_num'] = $this->m_pubdata['controller_data']->m_PageNum;
			
			$this->m_recordCount = $this->m_pubdata['controller_data']->m_RecordCount;
			$this->m_pageSize = $this->m_pubdata['controller_data']->m_PageSize; // we need this because we can not call setPageSizeAndPageNum with db paging 
			
			$this->m_UseDbPaging = true;
		} else {
			$this->m_controllerData = $this->m_pubdata['controller_data'];
			$this->m_recordCount = count($this->m_controllerData);
			$this->m_UseDbPaging = false;
		}
		
		if(! is_array($this->m_controllerData)){
			$this->m_controllerData = array();
		}
		
		$this->m_pubdata['records'] = $this->m_recordCount;
		$this->m_pageParameterName = $this->m_pubdata['page_parameter_name'];

		if($this->m_pageParameterName == ''){
			$this->m_pageParameterName = DEFAULT_LIST_PAGE_PARAMETER_NAME;
		}
		$this->m_pubdata['page_parameter_name'] = $this->m_pageParameterName;

		/**
		 * Sets the default page size and page num
		 * Each view should be able to change the page size and num if necessary
		 */ 
		 if( !$this->m_UseDbPaging )
			$this->setPageSizeAndPageNum($this->m_pubdata['default_page_size'], $this->m_pubdata['page_num']);
	}

	/**
	 * Sets the current pagesize and page num
	 * @param unknown_type $pPageSize
	 * @param unknown_type $pPageNum
	 */
	function setPageSizeAndPageNum($pPageSize, $pPageNum){
		$this->m_pageSize = (int) $pPageSize;
		if($this->m_pageSize < 0){
			$this->m_pageSize = 0;
		}
		$this->setPageNum($pPageNum);
	}

	/**
	 * Sets the current page num
	 * @param unknown_type $pPageNum
	 */
	function setPageNum($pPageNum){
		$this->m_pageNum = (int) $pPageNum;
		if($this->m_pageSize == 0 || $this->m_pageNum < 0 || $this->m_pageNum > ceil($this->m_recordCount / $this->m_pageSize)){
			$this->m_pageNum = 0;
		}
		
		// it can never become -1
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
		while($this->MoreRowsExist()){
			$this->m_pubdata['rownum'] ++;
			$this->GetNextRow();

			if(isset($this->m_pubdata['templadd']))
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL, $this->m_currentRecord[$this->m_pubdata['templadd']]));
			else
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
		}
		
		return $lRet;
	}
	
	protected function FetchFirstRow(){
		$this->m_pubdata['rownum'] = 1;
		$lCurrentRow = $this->m_rowsToProcess[0];
		$this->FetchRow($lCurrentRow);
	}
	
	protected function FetchLastRow(){
		$this->m_pubdata['rownum'] = count($this->m_rowsToProcess);
		$lCurrentRow = $this->m_rowsToProcess[count($this->m_rowsToProcess) - 1];
		$this->FetchRow($lCurrentRow);
	}
	
	protected function FetchRow($pRow){
		if(is_array($pRow)){
			foreach($pRow as $k => $v){
				$this->m_pubdata[$k] = $this->m_currentRecord[$k] = $v;
			}
		}
	}

	function MoreRowsExist(){
		return $this->m_currentRowIdx < count($this->m_rowsToProcess);
	}

	function GetNextRow() {
		if(!$this->MoreRowsExist()){
			return;
		}
		$this->m_currentRowIdx++;
		$lCurrentRow = $this->m_rowsToProcess[$this->m_currentRowIdx - 1];
		$this->FetchRow($lCurrentRow);
	}

	function GetData() {
		
	}

	/**
	 * Displays navigation for the pages (i.e. links to the previous and the following pages)
	 * @param int $pPageNum - the current page num
	 * @return string|mixed
	 */
	function GetPageNavigation($pPageNum) {
	
		if(! $this->m_pageSize)
			return '';

		$lGroupStep = (isset($this->m_pubdata['groupstep']) ? $this->m_pubdata['groupstep'] : 5);
		
		if($this->m_UseDbPaging){ 
			$pPageNum = $this->m_pageNum ; // this is set in the constructor 
			$lMaxPages = ceil($this->m_recordCount / $this->m_pageSize);
			/*
				we will need to add this to the db page calculations 
				if($pPageNum == - 1)
					$pPageNum = $lMaxPages - 1;
			*/
		} else {
			$lMaxPages = ceil($this->m_recordCount / $this->m_pageSize);
			$this->m_pubdata['maxpages'] = $lMaxPages;
			if($pPageNum > $lMaxPages)
				$pPageNum = 0;

			// kogato predadesh p = -1 te move-a do poslednata stranica
			if($pPageNum == - 1)
				$pPageNum = $lMaxPages - 1;
		}
	
		$this->m_pubdata['currpage'] = (int) $pPageNum + 1; // used for changing current page highlight 

		if($lMaxPages > 1){
			$lPageGroup = (int) ($pPageNum / $lGroupStep) * $lGroupStep;
			$lPassedForbiddenRequestParams = $this->m_pubdata['forbidden_request_params'];

			// Parametrite, koito sa zabraneni za tozi obekt
			if(! is_array($lPassedForbiddenRequestParams)){
				$lPassedForbiddenRequestParams = array();
			}

			// Dobavqme i parametrite, koito sa zabraneni za vsi4ki obekti
			$lForbiddenRequestParams = array_merge(array(
				'PHPSESSID',
				'__utma',
				'__utmz'
			), $lPassedForbiddenRequestParams);

			foreach($_REQUEST as $key => $val){
				// Skipvame stoinostite, koito ne trqbva da se predavat
				if(in_array($key, $lForbiddenRequestParams))
					continue;
				if(is_array($val)){
					foreach($val as $v){
						$url .= $key . '[]=' . urlencode(s($v)) . '&';
					}
				}else{
					if($key != $this->m_pubdata['page_parameter_name'] && $key != "rn")
						$url .= $key . '=' . urlencode(s($val)) . '&';
				}
			}

			$this->m_pubdata['pageingurl'] = $url;
			$this->m_pubdata['gotopage'] = 0;

			$lNavStr .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_STARTRS));

			if((int) $this->m_pubdata['usefirstlast'])
				if($pPageNum == 0){
					$lFirst = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_INACTIVEFIRST));
				}else{
					$lFirst = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_ACTIVEFIRST));
				}

			if($lPageGroup + 1 > $lGroupStep){
				$this->m_pubdata['lpagegroup'] = $lPageGroup;
				$this->m_pubdata['gotopage'] = ($lPageGroup - 1);
				$lPgStart = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_PGSTART));
			}

			if($this->m_pubdata['pagingstartrevord']){
				$lNavStr .= $lPgStart . $lFirst;
			}else{
				$lNavStr .= $lFirst . $lPgStart;
			}

			for($i = $lPageGroup; (($i < $lPageGroup + $lGroupStep) && ($i < $lMaxPages)); $i ++){
				$this->m_pubdata['lpagenum'] = ($i + 1);
				if($i == $pPageNum){
					$lNavStr .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_INACTIVEPAGE));
				}else{
					$this->m_pubdata['gotopage'] = $i;
					$lNavStr .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_ACTIVEPAGE));
				}
			}
			if((int) $this->m_pubdata['usefirstlast'])
				if(($pPageNum + 1) == $lMaxPages){
					$lLast = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_INACTIVELAST));
				}else{
					$lLast = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_ACTIVELAST));
				}

			if($lPageGroup < $lMaxPages - $lGroupStep){
				$this->m_pubdata['lpagegroup'] = ($lPageGroup + $lGroupStep + 1);
				$this->m_pubdata['gotopage'] = ($lPageGroup + $lGroupStep);
				$lPgEnd = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_PGEND));
			}else
				$lPgEnd = $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_DELIMETER));

			if((int) $this->m_pubdata['pagingendrevord']){
				$lNavStr .= $lLast;
				$lNavStr .= $lPgEnd;
			}else{
				$lNavStr .= $lPgEnd;
				$lNavStr .= $lLast;
			}
			$lNavStr .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING_ENDRS));
		}

		return $lNavStr;
	}

	/**
	 * First display G_HEADER template.
	 * If there are no data rows -display G_NODATA template.
	 * If there are rows:
	 * 		First => display G_STARTRS template
	 * 		For each row => display G_ROWTEMPLATE
	 * 		Display G_ENDRS template
	 * In the end display G_FOOTER template
	 * If there are multiple pages - display G_PAGEING template
	 *
	 * @see cbase_view::Display()
	 */
	public function Display() {
		$this->GetData();

		$this->m_pubdata['nav'] = $this->GetPageNavigation($this->m_pageNum);

		$lRet = $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if($this->m_recordCount == 0){
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
		}else{
			$lRows = $this->GetRows();
			$this->FetchFirstRow();
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
			$lRet .= $lRows;
			$this->FetchLastRow();
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));


		if($this->m_pageSize && $this->m_recordCount && ! (int) $this->m_pubdata['hidedefpaging'])
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PAGEING));

		return $lRet;
	}

}

?>