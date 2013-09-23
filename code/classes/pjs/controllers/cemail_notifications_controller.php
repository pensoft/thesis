<?php

class cEmail_Notifications_Controller extends cBase_Controller {
	
	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lTemplateId = (int)$this->GetValueFromRequestWithoutChecks('id');
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			!$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		$this->m_models['mTask_Model'] = new mTask_Model();
		$pViewPageObjectsDataArray['contents'] = array(
			'ctype' => 'Email_Notifications_Form_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'email_notification_form',
			'use_captcha' => 0,
			//~ 'debug' => 1,
			'form_method' => 'POST',
			//'js_validation' => $lJsValidation,
			'form_name' => 'email_notifications',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'event_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'AllowNulls' => true,
				),
				'success' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'AllowNulls' => true,
					'AddTags' => array(
						'id' => 'success'
					),
				),
				'journal_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'AllowNulls' => false,
				),
				'recipients' => array (
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.recipients'),
					'AllowNulls' => false,
					/*'AddTags' => array(
						'class' => 'clearStyles',
					),*/
				),		
				'subject' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.email_subject'),
					'AllowNulls' => false,
					/*'AddTags' => array(
						'class' => 'template_subject',
						'disabled' => 'disabled',
					),*/
				),
				'template_body' => array(
					'CType' => 'textarea',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.email_body'),
					'AllowNulls' => false,
					'RichText' => 1,
					/*'AddTags' => array(
						'class' => 'template_body',
						'disabled' => 'disabled',
					),*/
				),
				'send' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spCreateEvent(' . SEND_EMAIL_NOTIFICATION_EVENT_TYPE_ID . ', null, ' . $this->GetUserId() . ', 1, null, null);
					/*{journal_id}{recipients}{subject}{template_body}{event_id}{success}*/',
					'CheckCaptcha' => 0,
					'DisplayName' => 'Send',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
					//'RedirUrl' => '/email_notifications',
				),
			)
		);
		$this->AddJournalObjects($lJournalId);
		$this->m_pageView = new pEmail_Notifications_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}
}

?>