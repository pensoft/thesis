<?php

class cEdit_Email_Template_Controller extends cBase_Controller {
	
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
			'ctype' => 'editEmailTemplateForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'email_template_edit_form',
			'use_captcha' => 0,
			//~ 'debug' => 1,
			'form_method' => 'POST',
			//'js_validation' => $lJsValidation,
			'form_name' => 'email_template_edit_form',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'id' => array(
					'VType' => 'int',
					'CType' => 'hidden',
					'AllowNulls' => true,
				),
				'parent_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'AllowNulls' => true,
				),
				'event_type_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'AllowNulls' => false,
				),
				'journal_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'type' => array (
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.tmp.state'),
					'AllowNulls' => true,
					'AddTags' => array(
						'readonly' => 'readonly',
						'class' => 'clearStyles',
					),
				),	
				'recipients' => array (
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.recipients'),
					'AllowNulls' => true,
					'AddTags' => array(
						'readonly' => 'readonly',
						'class' => 'clearStyles',
					),
				),
				'template_name' => array (
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.template_name'),
					'AllowNulls' => false,
					'AddTags' => array(
						'readonly' => 'readonly',
						'class' => 'clearStyles',
					),
				),
				'event_type' => array (
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.event_type'),
					'AllowNulls' => false,
					'AddTags' => array(
						'readonly' => 'readonly',
						'class' => 'clearStyles',
					),
				),
				'default_subject' => array(
					'VType' => 'int',
					'CType' => 'checkbox',
					'TransType' => MANY_TO_BIT,
					'AllowNulls' => true,
					'SrcValues' => array(1 => getstr('pjs.default_subject')),
				),				
				'subject' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.email_subject'),
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => 'template_subject',
						'disabled' => 'disabled',
					),
				),
				'template_body' => array(
					'CType' => 'textarea',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.email_body'),
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => 'template_body',
						'disabled' => 'disabled',
					),
				),
				'default_body' => array(
					'VType' => 'int',
					'CType' => 'checkbox',
					'TransType' => MANY_TO_BIT,
					//~ 'TransType' => MANY_TO_SQL_ARRAY,
					'AllowNulls' => true,
					'SrcValues' => array(1 => getstr('pjs.default_body')),
				),
				'showedit' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM pjs.spmanageemailtemplates(0, ' . $lTemplateId . ', null, null, null, null, null, null, null, null)',
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'delete' => array(
					'CType' => 'action',
					'SQL' => '',
					'CheckCaptcha' => 0,
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW | ACTION_REDIRECT,
					'RedirUrl' => '/manage_journal_groups.php?journal_id=' . (int)$lJournalId,
				),
				'save' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM pjs.spmanageemailtemplates(1, ' . $lTemplateId . ', {template_name}, {event_type_id}, {journal_id}, {subject}, {template_body}, {parent_id}, {default_subject}, {default_body})',
					'CheckCaptcha' => 0,
					'DisplayName' => 'save',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
					'RedirUrl' => '/email_templates',
				),
			)
		);
		$this->AddJournalObjects($lJournalId);
		$this->m_pageView = new pManage_Email_Templates_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}
}

?>