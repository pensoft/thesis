<?php

class cEdit_Journal_Issue_Controller extends cBase_Controller {
	var $m_journalId;
	var $m_backIssue;

	function __construct() {
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();

		$this->m_models['mEdit_Model'] = new mEdit_model();

		$this->m_journalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$this->m_backIssue = (int)$this->GetValueFromRequestWithoutChecks('back_issue');

		if(!$this->m_journalId || !$this->m_models['mEdit_Model']->CheckJournalExist($this->m_journalId) ||
			!$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalIssueRights($this->GetUserId(), $this->m_journalId)){
			header('Location: /index.php');
		}

		$lBaseFormArr = array(
			'ctype' => 'edit_Issue_Form_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'journal_issue_form',
			'use_captcha' => 0,
			//'debug' => 1,
			'form_method' => 'post',
			//'js_validation' => $lJsValidation,
			'form_name' => 'journal_issue_form',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'issue_id' => array(
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
				),
				'previewpicid' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'volume' => array (
					'VType' => 'int' ,
					'CType' => 'text',
					'DisplayName' => getstr('pjs.volume'),
					'AllowNulls' => true,
				),
				'number' => array (
					'VType' => 'int' ,
					'CType' => 'text',
					'DisplayName' => getstr('pjs.number'),
					'AllowNulls' => true,
				),
				'year' => array (
					'VType' => 'int' ,
					'CType' => 'text',
					'DisplayName' => getstr('pjs.year'),
					'AllowNulls' => true,
				),
				'is_regular_issue' => array(
					'VType' => 'int',
					'CType' => 'checkbox',
					'TransType' => MANY_TO_BIT_ONE_BOX,
					'SrcValues' => array(
						1 => getstr('pjs.special_issue'),
					),
					'DefValue' => 0,
					'DisplayName' => getstr('pjs.issue_identification'),
					'AllowNulls' => true,
				),
				'is_active' => array(
					'VType' => 'int',
					'CType' => 'checkbox',
					'TransType' => MANY_TO_BIT_ONE_BOX,
					'SrcValues' => array(
						1 => getstr('pjs.active'),
					),
					'DefValue' => 0,
					'DisplayName' => getstr('pjs.active'),
					'AllowNulls' => true,
				),
				'special_issue_editors' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.special_issue_editors'),
					'AllowNulls' => true,
				),
				'title' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.title'),
					'AllowNulls' => true,
				),
				'description' => array(
					'CType' => 'textarea',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.issue_description'),
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => 'inputFld',
						'id' => 'description_textarea',
						'rows' => 10,
						'cols' => 20,
					)
				),
				'previewpic' => array(
					'CType' => 'file',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.cover_image'),
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => 'inputFld',
						'id' => 'fileInput',
						'size' => '20',
					)
				),
				'cover_caption' => array(
					'CType' => 'textarea',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.cover_caption'),
					'AllowNulls' => true,
					'AddTags' => array(
						'rows' => 10,
						'cols' => 20,
					)
				),
				'price' => array(
					'CType' => 'text',
					'VType' => 'float',
					'DisplayName' => getstr('pjs.price'),
					'AllowNulls' => true,
				),
				'showedit' => array(
					'CType' => 'action',
					'SQL' => 'SELECT ji.id as issue_id, ji.journal_id, ji.volume, ji.number, ji.year,
									ji.is_regular_issue::int as is_regular_issue,
									ji.is_active::int as is_active,
									ji.special_issue_editors, ji.name as title, ji.description, ji.price,
									p.description as cover_caption, ji.previewpicid
								FROM pjs.journal_issues ji
								LEFT JOIN photos p ON p.guid = ji.previewpicid
								WHERE ji.id = {issue_id}
									AND ji.journal_id = ' . (int)$this->m_journalId,
					'CheckCaptcha' => 0,
					'DisplayName' => '',
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),

				'delete' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spDeleteJournalIssue({issue_id}, ' . (int)$this->m_journalId . ') as previewpicid',
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.delete.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,// | ACTION_REDIRECT,
				),

				'changestate' => array(
					'CType' => 'action',
					'SQL' => 'UPDATE pjs.journal_issues SET is_published = TRUE, date_published = now() WHERE id = {issue_id} AND journal_id = ' . (int)$this->m_journalId,
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.delete.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,// | ACTION_REDIRECT,
				),

				'makecurrent' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spChangeCurrentIssue({issue_id}, ' . (int)$this->m_journalId . ')',
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.delete.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,// | ACTION_REDIRECT,
				),

				'save' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spSaveJournalIssue({issue_id}, {journal_id},
										{volume}, {number}, {year},
										{is_regular_issue}, {special_issue_editors},
										{title}, {description}, {price}, {is_active}) issue_id',
					'CheckCaptcha' => 0,
					'DisplayName' => 'save',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH,// | ACTION_REDIRECT,
				)
			)
		);

		$lForm = new edit_Issue_Form_Wrapper($lBaseFormArr);

		$pViewPageObjectsDataArray['contents'] = $lForm;
		// must be $pViewPageObjectsDataArray['contents'] = $lForm;

		$this->AddJournalObjects($this->m_journalId);
		$this->m_pageView = new pEdit_Journal_Issue_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->InitLeftcolObjects();
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}

	function AddPictureToIssue($pPicId, $pIssueId){
		$lModel = new mJournal_Issues_Model();
		$lModel->AddPicToIssue($pPicId, $pIssueId);
	}
}

?>