<?php

class cEdit_Controller extends cBase_Controller {
	var $m_story_content;
	var $m_journal_id;
	
	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();
		
		$this->m_journal_id = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lStoryId   		= (int)$this->GetValueFromRequestWithoutChecks('guid');
		$lDirection   		= (int)$this->GetValueFromRequestWithoutChecks('direction');
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		if( !$this->GetUserId() || !$this->m_journal_id ||
			!$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $this->m_journal_id) ){
			header('Location: /index.php');
		}
		
		if( $lStoryId )
			$this->m_story_content = $this->m_models['mEdit_Model']->GetStoryContent($lStoryId);
		
		$lBaseFormArr = array(
			'ctype' => 'editForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'journal_form',
			'use_captcha' => 0,
			//'debug' => 1,
			'form_method' => 'POST',
			//'js_validation' => $lJsValidation,
			'form_name' => 'add_story_to_journal',
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
					'AddTags' => array(
						'id' => 'journalIdFld'
					),
				),
				'parent_id' => array(
					'VType' => 'int',
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'title' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.story_title'),
					'AllowNulls' => false,
				),
				'description' => array(
					'CType' => 'textarea',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.journal_story_description'),
					'AllowNulls' => false,
					'AddTags' => array(
						'class' => 'inputFld',
						'id' => 'description_textarea',
						'rows' => 10,
						'cols' => 20,
					)
				),
				'add_to_sidebar' => array(
					'VType' => 'int',
					'CType' => 'checkbox',
					'SrcValues' => array(
						1 => getstr('pjs.journal_story_add_to_sidebar'),
					),
					'DefValue' => 0,
					'TransType' => MANY_TO_BIT,
					'DisplayName' => getstr('pjs.journal_story_add_to_sidebar'),
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => 'inputFld',
					)
				),
				'showedit' => array(
					'CType' => 'action',
					'SQL' => 'SELECT s.*, sp.show_in_sidebar as add_to_sidebar FROM GetStoriesBaseData({guid}, ' . getlang() . ') s
								JOIN sid1storyprops sp ON sp.guid = {guid}',
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'delete' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spdeletejournalstory({guid}, {journal_id})',
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW // | ACTION_REDIRECT,
					//'RedirUrl' => '/manage_journal_about_pages.php?journal_id=' . $this->m_journal_id . '&mode=1',
				),
				'moveupdown' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spmovestoryupdown({guid}, {journal_id}, ' . $lDirection . ')',
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW // | ACTION_REDIRECT,
					//'RedirUrl' => '/manage_journal_about_pages.php?journal_id=' . $this->m_journal_id . '&mode=1',
				),
				'save' => array(
					'CType' => 'action',
					'SQL' => 'SELECT spsavejournalstorydata(
										{guid}, ' . (int)CMS_SITEID . ', \'' . getlang(true) . '\', {title}, null, null, now()::timestamp, null, 
										\'' .  (int)$this->GetUserId() . '\', null, 3, null, null, null, null, 1, 1, 
										' . (int)STORIES_DSCID . ', {description}, {journal_id}, {parent_id}, {add_to_sidebar}
									) as guid',
					'CheckCaptcha' => 0,
					'DisplayName' => 'save',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH //| ACTION_REDIRECT,
					//'RedirUrl' => '/manage_journal_about_pages.php?journal_id=' . $this->m_journal_id . '&mode=1',
				)
			)
		);
		
		$lForm = new editForm_Wrapper($lBaseFormArr);
		
		$pViewPageObjectsDataArray['form'] = $lForm;
		
		$this->AddJournalObjects($this->m_journal_id);
		$this->m_pageView = new pEdit_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}
	
	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>