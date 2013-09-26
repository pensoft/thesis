<?php

class cView_Poll_Controller extends cBase_Controller {
	var $m_action_result;
	var $m_Model;
	var $m_tempPageView;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_success = 0;
	var $m_PollAnswersData = array();
	var $m_relElementType;
	var $m_relElementId;

	function __construct() {
		global $rewrite;
		global $gQuestions;
		parent::__construct();
		$pViewPageObjectsDataArray = array ();
		$this->m_action_result = array ();
		
		$this->m_relElementType = (int) $this->GetValueFromRequestWithoutChecks('rel_element_type');
		$this->m_relElementId = (int) $this->GetValueFromRequestWithoutChecks('rel_element_id');
		
		switch ($this->m_relElementType) {
			case AOF_COMMENT_POLL_ELEMENT_TYPE:
				$this->m_Model = new mArticles();
				$this->m_PollAnswersData = $this->m_Model->GetAOFViewCommentPollAnswers($this->m_relElementId);
				$lNameInViewObject = 'aof_poll_answers';
				break;
			case REVIEWER_POLL_ELEMENT_TYPE:
				break;
			default:
				break;
		}
		// var_dump($this->m_relElementType);
		// var_dump($this->m_relElementId);
		if(!$this->m_relElementType || !$this->m_relElementId) {
			$this->m_errCnt++;
			$this->m_errMsg = getstr('pjs.not_enough_info_for_poll_view');
		} else {
			$this->m_tempPageView = new pView_Poll_Page_View(array());
			
			$lForumList = new evList_Display(array(
				'name_in_viewobject' => $lNameInViewObject,
				'view_object' => $this->m_tempPageView,
				'controller_data' => $this->m_PollAnswersData,
			));
			
			$this->m_action_result['html'] = $lForumList->Display();	
		}

		$this->m_action_result['err_cnt'] = (int)$this->m_errCnt;
		$this->m_action_result['err_msg'] = $this->m_errMsg;
		
		$this->m_pageView = new pView_Poll_Page_View($this->m_action_result);
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>