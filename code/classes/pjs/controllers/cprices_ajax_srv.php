<?php
// Disable error reporting because it can break the json output
//ini_set('error_reporting', 'off');

class cPrices_Ajax_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsgs = array();
	var $m_action;
	var $m_action_result;
	var $m_eventsParamString = '';
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		switch ($this->m_action) {
			default :
				$this->m_errCnt ++;
				$this->m_errMsgs[] = array(
					'err_msg' => getstr('pjs.unrecognizedAction')
				);
				break;
			case 'automatic_price' :
				$lJournalId = $this->m_action = $this->GetValueFromRequestWithoutChecks('journal_id');
				$lStartpage = (int)$this->GetValueFromRequestWithoutChecks('startpage');
				$lEndPage = (int)$this->GetValueFromRequestWithoutChecks('endpage');
				$this->CalculateDocumentAutomaticPrice($lJournalId, $lStartpage, $lEndPage, null);
				break;

		}
		
		$lResultArr = array_merge($this->m_action_result, array(
			'err_cnt' => $this->m_errCnt,
			'err_msgs' => $this->m_errMsgs,
			'url_params' => $this->m_eventsParamString, 
		));
		$this->m_pageView = new epPage_Json_View($lResultArr);
	}
	function CalculateDocumentAutomaticPrice($pJournalId, $pStartPage, $pEndPage, $pColorPage) {
		//~ $this->m_eventsParamString = 'dddd';
		$lJournalPrices = new mJournal_Prices_Model();
		$this->m_action_result = $lJournalPrices->CalculateAutomaticPrice($pJournalId, $pStartPage, $pEndPage, $pColorPage);
		if($this->m_action_result['err_cnt']){
			$this->m_errCnt = $this->m_action_result['err_cnt'];
			$this->m_errMsgs = $this->m_action_result['err_msgs'];
		} else {
			$this->m_eventsParamString = 'price ' . $this->m_action_result['price'];
		}
	}	
}

?>