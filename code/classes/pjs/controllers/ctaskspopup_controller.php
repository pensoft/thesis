<?php

class cTasksPopUp_Controller extends cBase_Controller {
	var $m_form;
	var $m_list;
	
	/**
	 * event ids array
	 * 
	 * @var array()
	 */
	var $m_eventIds;
	
	/**
	 * controller action
	 * 0 - all (form and list)
	 * 1 - form
	 * 2 - list
	 * 
	 * @var int
	 * 
	 */
	var $m_action;
	
	/**
	 * task model
	 * 
	 */
	var $m_taskModel;
	
	/*
	 * recipients count
	 * 
	 * */
	var $m_recipients_count;
	
	function __construct() {
		parent::__construct();
		
		$this->m_eventIds = $this->GetValueFromRequestWithoutChecks('event_ids');
		if(!is_array($this->m_eventIds)) {
			$this->m_eventIds = explode(',', $this->m_eventIds);
		}
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		$lSkipOper = $this->GetValueFromRequestWithoutChecks('skip_oper');
		$lTaskDetailId = $this->GetValueFromRequestWithoutChecks('task_detail_id');
		$lSkipTaskDetailId = $this->GetValueFromRequestWithoutChecks('skip_task_detail_id');
		$lCancelReviewerTaskId = $this->GetValueFromRequestWithoutChecks('reviewer_task_id');
		$lDocumentID = $this->GetValueFromRequestWithoutChecks('document_id');
		$lFormAction = $this->GetValueFromRequestWithoutChecks('tAction');

		$this->m_taskModel = new mTask_model();

		if($this->m_action == 'skip' || $this->m_action == 'skip_refresh_form') { 	
			$this->m_taskModel->SkipTaskDetail($lSkipTaskDetailId, $lSkipOper);
		}
		
		if($this->m_action == 'cancel_invitation') {
			$lUID = $this->GetValueFromRequestWithoutChecks('uid');
			$this->m_taskModel->CancelReviewerInvitation($lCancelReviewerTaskId, $lUID, $lDocumentID);
		}
		
		if(in_array($this->m_action, array('sendthis', 'sendall', 'skipall'))) {
			$this->getTaskForm($lSelectedTaskDetailData[0]['id'], $lSelectedTaskDetailData[0]['state_id'], $this->m_action);
			$lForm = new TasksPopUpForm_Wrapper($this->m_form);
			$lTaskDetailId = null;
		}
		
		$lSelectedTaskDetailData = $this->getSelectedTaskDetailData($lTaskDetailId);
		if(!(int)count($lSelectedTaskDetailData)) {
			echo 'close';
			exit;
		}
		$pViewPageObjectsDataArray['act_templ'] = $this->m_action;
		$this->getTaskUsrList($lSelectedTaskDetailData[0]['id']);
		
		switch ($this->m_action) {
			// return only form
			case 'getform':
				$this->getTaskForm($lSelectedTaskDetailData[0]['id'], $lSelectedTaskDetailData[0]['state_id']);
				$lForm = new TasksPopUpForm_Wrapper($this->m_form);
				$pViewPageObjectsDataArray['form'] = $lForm;
				break;
				
			// return only list
			case 'getlist':
				$this->getTaskUsrList($lSelectedTaskDetailData[0]['id']);
				$pViewPageObjectsDataArray['list'] = $this->m_list;
				break;
				
			// skip recipient
			case 'skip_refresh_form':				
				$this->getTaskForm($lSelectedTaskDetailData[0]['id'], $lSelectedTaskDetailData[0]['state_id']);
				$lForm = new TasksPopUpForm_Wrapper($this->m_form);
				$pViewPageObjectsDataArray['form'] = $lForm;
				break;
				
			// return form and list
			default:
				$lList = $this->getTaskUsrList($lSelectedTaskDetailData[0]['id']);
				$this->getTaskForm($lSelectedTaskDetailData[0]['id'], $lSelectedTaskDetailData[0]['state_id']);
				$lForm = new TasksPopUpForm_Wrapper($this->m_form);
				$pViewPageObjectsDataArray['form'] = $lForm;
				$pViewPageObjectsDataArray['list'] = $this->m_list;
				break;
		}
		$this->m_pageView = new pTasksPopUp_page_view(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	private function getSelectedTaskDetailData($pTaskDetailId = NULL) {
		if(!count($this->m_eventIds)) {
			return '';
		} else {
			// get first task detail
			$lFirstTaskDetailData = $this->m_taskModel->getFirstTaskDetailData($this->m_eventIds, $pTaskDetailId);
			
			return $lFirstTaskDetailData;
		} 
	}

	private function getTaskUsrList($pSelectedId) {
		$lListData = $this->m_taskModel->getTaskDetailDataList($this->m_eventIds);
		
		$this->m_recipients_count = (int)count($lListData);
		
		$this->m_list = new evList_Display(array(
			'name_in_viewobject' => 'list',
			'controller_data' => $lListData,
			'selected' => $pSelectedId,
			'event_ids' => $this->m_eventIds,
		));
	}

	private function getTaskForm($pTaskDetailId, $pState_id, $pAction = NULL) {
		$lFieldsTempl = array(
			'reviewers_email_flag' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AllowNulls' => true,
				'AddTags' => array(
					'id'  => 'reviewers_email_flag_id',
				),
			), 
			'document_id' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AddTags' => array(
					'id'  => 'email_document_id',
				),
			), 
			'role_redirect' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AllowNulls' => true,
				'AddTags' => array(
					'id'  => 'role_redirect',
				),
			), 
			'task_detail_id' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AllowNulls' => true,
				'AddTags' => array(
					'id'  => 'task_detail_id',
				),
			),
			'state_id' => array(
				'CType' => 'hidden',
				'VType' => 'int',
				'AllowNulls' => true,
				'AddTags' => array(
					'id'  => 'state_id',
				),
			),
			'event_ids' => array(
				'CType' => 'hidden',
				'VType' => 'string',
				'AllowNulls' => true,
				'AddTags' => array(
					'id'  => 'event_ids',
				),
			),
			'to' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => 'To',
				'AddTags' => array(
					'id' => 'to',
				),
			),
			'cc' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => 'CC',
				'AddTags' => array(
					'id' => 'cc',
					'onblur' => 'saveFld(\'#task_detail_id\', this)',
				),
			),
			'bcc' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => 'BCC',
				'AddTags' => array(
					'id' => 'bcc',
				),
			),
			'subject' => array(
				'CType' => 'text',
				'VType' => 'string',
				'AllowNulls' => true,
				'DisplayName' => 'Subject',
				'AddTags' => array(
					'id' => 'subject',
					'onblur' => 'saveFld(\'#task_detail_id\', this)',
				),
			),
			'template' => array(
				'CType' => 'textarea',
				'VType' => 'string',
				'AllowNulls' => false,
				'DisplayName' => 'Content',
				'AddTags' => array(
					'id' => 'content',
					'disabled' => 'disabled',
					//'onblur' => 'saveFld(\'#task_detail_id\', this)',
				),
			),
			
			'template_notes' => array(
				'CType' => 'textarea',
				'VType' => 'string',
				'AllowNulls' => false,
				'DisplayName' => 'Content',
				'AddTags' => array(
					'id' => 'content',
					'onblur' => 'saveFld(\'#task_detail_id\', this)',
				),
			),
			
			'new' => array(
				'CType' => 'action',
				'SQL' => 'SELECT 
					etd.id as task_detail_id, 
					etd.template as template,
					etd.template_notes as template_notes,
					etd.cc,
					etd.bcc,
					etd.subject,
					etd.state_id,
					u.uname as to
				FROM pjs.email_task_details etd 
				JOIN usr u ON u.id = etd.uid
				WHERE etd.id = {task_detail_id}/*{reviewers_email_flag}{document_id}{role_redirect}*/',
				'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
			), 
			
			'sendthis' => array(
				'CType' => 'action',
				'Hidden' => true,
				'DisplayName' => 'Send and Next',
				'SQL' => 'UPDATE pjs.email_task_details SET state_id = ' . (int)TASK_DETAIL_READY_STATE_ID . ', cc = {cc}, template_notes = {template_notes} WHERE id = {task_detail_id} /*{reviewers_email_flag}{document_id}{role_redirect}*/',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH,
			),
			
			'sendall' => array(
				'CType' => 'action',
				'Hidden' => true,
				'DisplayName' => 'Send all',
				'SQL' => 'SELECT * FROM pjs.sendAllTaskRecipients({event_ids})/*{reviewers_email_flag}{document_id}{role_redirect}*/',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
			),
			
			'skipall' => array(
				'CType' => 'action',
				'Hidden' => true,
				'DisplayName' => 'Skip all',
				'SQL' => 'SELECT * FROM pjs.skipAllTaskRecipients({event_ids})/*{reviewers_email_flag}{document_id}{role_redirect}*/',
				'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
			),
		);
		$lJsValidation = 0;
		if($pState_id == TASK_DETAIL_SKIP_STATE_ID) {
			$lNameInViewObject = 'taskfrmview';
		} else {
			$lNameInViewObject = 'taskfrm';	
		}
	
		$lTaskDetailId = $pTaskDetailId;

		$this->m_form = array(
			'ctype' => 'TasksPopUpForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => $lNameInViewObject,
			'form_method' => 'POST',
			'js_validation' => $lJsValidation,
			'form_name' => 'tasksfrm',
			'fields_metadata' => $lFieldsTempl,
			'task_detail_id' => $lTaskDetailId,
			'event_ids' => $this->m_eventIds,
			'action' => $pAction,
			'recipients_count' => $this->m_recipients_count,
		);
	}

}

?>