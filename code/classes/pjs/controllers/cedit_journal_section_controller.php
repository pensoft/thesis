<?php

class cEdit_Journal_Section_Controller extends cBase_Controller {
	
	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			!$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		
		$lBaseFormArr = array(
			'ctype' => 'editForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'journal_section_form',
			'use_captcha' => 0,
			//'debug' => 1,
			'form_method' => 'POST',
			//'js_validation' => $lJsValidation,
			'form_name' => 'journal_section_form',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'guid' => array(
					'VType' => 'int',
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'journal_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
					//~ 'AddTags' => array(
						//~ 'id' => 'journalIdFld'
					//~ ),
				),
				'title' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.title'),
					'AllowNulls' => false,
				),
				'abbreviation' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.abbreviation'),
					'AllowNulls' => false,
				),
				'policy' => array(
					'CType' => 'textarea',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.policy'),
					'AllowNulls' => true,
				),
				'review_type' => array(
					'CType' => 'checkbox',
					'VType' => 'int',
					'DisplayName' => getstr('pjs.review_type'),
					'SrcValues' => 'SELECT dt.id as id, dt.name as name 
										FROM pjs.journal_document_review_types jt
										JOIN pjs.document_review_types dt ON jt.review_type_id =  dt.id
										WHERE journal_id = ' . (int)$lJournalId,
					'TransType' => MANY_TO_SQL_ARRAY,
					'AllowNulls' => false,
					'DefValue' => 0,
				),
				'paper_type' => array (
					'VType' => 'int' ,
					'CType' => 'select' ,
					'DisplayName' => getstr('pjs.paper_type'),
					'SrcValues' => 'SELECT 0 as id, \'\' as name
									UNION
									SELECT id as id, name as name FROM pwt.papertypes
									ORDER BY id asc',
					'AllowNulls' => true,
				),
				'showedit' => array(
					'CType' => 'action',
					'SQL' => 'SELECT journal_id, title, abr as abbreviation, policy, 
										pwt_paper_type_id as paper_type, review_type_id as review_type 
								FROM pjs.journal_sections 
								WHERE id = {guid} AND journal_id = ' . (int)$lJournalId,
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'delete' => array(
					'CType' => 'action',
					'SQL' => 'DELETE FROM pjs.journal_sections WHERE id = {guid} AND journal_id = ' . (int)$lJournalId,
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW | ACTION_REDIRECT,
					'RedirUrl' => '/manage_journal_sections.php?journal_id= ' . (int)$lJournalId,
				),
				
				'save' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spSaveJournalSection({guid}, {journal_id}, {title}, {abbreviation}, {policy}, {review_type}, {paper_type})',
					'CheckCaptcha' => 0,
					'DisplayName' => 'save',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
					'RedirUrl' => '/manage_journal_sections.php?journal_id= ' . (int)$lJournalId,
				)
			)
		);
		
		$lForm = new editForm_Wrapper($lBaseFormArr);
		
		$pViewPageObjectsDataArray['contents'] = $lForm;
		
		$this->AddJournalObjects($lJournalId);
		$this->m_pageView = new pEdit_Journal_Section_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}
	
	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>