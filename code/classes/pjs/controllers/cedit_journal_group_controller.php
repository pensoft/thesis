<?php

class cEdit_Journal_Group_Controller extends cBase_Controller {
	
	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		$this->InitViewingModeData();
		
		$this->m_models['mEdit_Model'] = new mEdit_model();
		
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lDirection = (int)$this->GetValueFromRequestWithoutChecks('direction');
		$lGrpId = (int)$this->GetValueFromRequestWithoutChecks('id');
		$lOper = (int)$this->GetValueFromRequestWithoutChecks('oper');
		$lUserId = (int)$this->GetValueFromRequestWithoutChecks('user_id');
		$lRole = $this->GetValueFromRequestWithoutChecks('role');
		
		if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId) || 
			!$this->GetUserId() || !$this->m_models['mEdit_Model']->CheckJournalRights($this->GetUserId(), $lJournalId)){
			header('Location: /index.php');
		}
		if ($lGrpId) {
			$lUsersModel = $this->m_models['mUsers'] = new mUsers();
			$lUsersData = $lUsersModel->GetUsersByGroup($lGrpId);
		} else {
			$lUsersData = NULL;
		}
		$pViewPageObjectsDataArray['contents'] = array(
		'users_list' => array(
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'journal_group_fusers_in_group',
			'controller_data' => $lUsersData,
			'journal_id' => $lJournalId,
			'group_id' => $lGrpId,
		),
		'form' => array(
			'ctype' => 'editGroupsForm_Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'journal_group_form',
			'use_captcha' => 0,
			//'debug' => 1,
			'form_method' => 'POST',
			//'js_validation' => $lJsValidation,
			'form_name' => 'journal_group_form',
			'dont_close_session' => true,
			'fields_metadata' => array(
				'id' => array(
					'VType' => 'int',
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
					'AddTags' => array(
						'id' => 'groupid',
					)
				),
				'grpid' => array(
					'VType' => 'int',
					'CType' => 'text',
					'DisplayName' => '',
					'DefValue' => $lGrpId,
					'AllowNulls' => true,
				),
				'journal_id' => array (
					'VType' => 'int' ,
					'CType' => 'hidden',
					'DisplayName' => '',
					'AllowNulls' => true,
				),
				'title' => array(
					'CType' => 'text',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.title'),
					'AllowNulls' => false,
					'AddTags' => array(
						'style' => 'width: 315px;',
					),
				),
				'description' => array(
					'CType' => 'textarea',
					'VType' => 'string',
					'DisplayName' => getstr('pjs.description'),
					'AllowNulls' => true,
					'AddTags' => array(
						'rows' => '5',
						'cols' => '20'
					),
				),
				'parentnode' => array(
					'CType' => 'select',
					'VType' => 'int',
					'DisplayName' => getstr('pjs.parentgroup'),
					'TransType' => MANY_TO_SQL_ARRAY,
					'SrcValues' => 'SELECT null as id, \'-- Select --\' as name, 1 as ord, null as pos 
							UNION 
							SELECT id, (repeat(\'&nbsp;\', char_length(pos) - 2) || name) as name, 2 as ord, pos FROM pjs.journal_user_groups ORDER BY ord, pos',
					'AllowNulls' => true,
					'AddTags' => array(
						'style' => 'width: 315px;',
					),
				),
				'moveupdown' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spMoveGroupOrUserUpDown(' . $lGrpId . ',' . $lOper . ', {journal_id}, ' . $lDirection . ')',
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'update' => array(
					'CType' => 'action',
					'SQL' => 'UPDATE pjs.journal_user_group_users SET role = \'' . $lRole . '\' WHERE uid = ' . $lUserId . ' AND journal_user_group_id='. $lGrpId,
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'showedit' => array(
					'CType' => 'action',
					'SQL' => 'SELECT journal_id, name as title, description, pos, CASE WHEN id <> rootnode THEN rootnode END as parentnode
								FROM pjs.journal_user_groups
								WHERE id = ' . $lGrpId . ' AND journal_id = '.(int)$lJournalId,
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				),
				'delete' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spSaveJournalGroup(3, ' . $lGrpId . ', '. (int)$lJournalId. ', null, null, null)',
					'CheckCaptcha' => 0,
					'DisplayName' => getstr('pjs.save.btn'),
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW | ACTION_REDIRECT,
					'RedirUrl' => '/manage_journal_groups.php?journal_id=' . (int)$lJournalId,
				),
				
				'save' => array(
					'CType' => 'action',
					'SQL' => 'SELECT * FROM spsavejournalgroup(1, {id}, {journal_id}, {title}, {description}, {parentnode})',
					'CheckCaptcha' => 0,
					'DisplayName' => 'save',
					'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
					'RedirUrl' => '/edit_journal_group.php?journal_id=' . (int)$lJournalId . '&tAction=showedit&id={id}',
				)
			)
		)
		);
		$this->AddJournalObjects($lJournalId);
		$this->m_pageView = new pEdit_Journal_Group_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray['contents']));
		$this->InitLeftcolObjects();
	}
	
	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>