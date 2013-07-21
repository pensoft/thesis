<?php

class cView_Document_Editor extends cView_Document {

	var $m_editor_mode;
	
	function InitObjects() {
		$this->m_viewingRole = JOURNAL_EDITOR_ROLE;
		
		$lEditorViewMode = $this->GetValueFromRequest('mode', 'GET', 'int', false, false);
		$this->m_editor_mode = $lEditorViewMode['value'];
		
		$this->GetDocumentInfo();
		$this->GetActiveSection();
		$this->GetSectionTabsObjByRole($this->m_viewingRole);
		$this->GetDocumentInfoObject();
		switch ($this->m_section) {
			case GET_METADATA_SECTION:
				$this->GetMetadata();
				break;
			case GET_HISTORY_SECTION:
				$this->GetHistory();
				break;
			case GET_SUBMITTED_FILES_SECTION:
				$this->GetSubmittedFiles();
				break;
			case GET_DISCOUNTS_SECTION:
				$this->GetDiscounts();
				break;
			case GET_SCHEDULING_SECTION:
				$this->GetScheduling();
				break;
			default:
				$this->GetCurState();
				break;
		}
		$pViewPageObjectsDataArray['contents'] = $this->m_contentObject;
		$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.editorial');
		$this->AddJournalObjects($this->m_journalId);
		$this->m_pageView = new pView_Document_Editor(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->m_allowedViewingModes = $this->GetUserJournalDashboardAllowedViewingModes($this->m_journalId);
		$this->InitLeftcolObjects();
	}
	function GetDiscounts() {
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'document_se_decision',
			'document_id' => $this->m_documentId,
			'document_info' => $this->m_documentInfoObject,
			'tabs' => $this->m_tabsObject,
			'has_tabs' => (!(int)$this->m_editor_mode ? 1 : 0),
		));
	}
	
	function GetScheduling() {
	$lJournalId = $this->GetValueFromRequest('journal_id', 'GET', 'int', false, false);
	$lJournalId = $lJournalId['value'];
	$lDocumentId = $this->GetValueFromRequest('id', 'GET', 'int', false, false);
	$lDocumentId = $lDocumentId['value'];
		$lForm = new eScheduling_Wrapper(array(
			'ctype' => 'eScheduling _Wrapper',
			'page_controller_instance' => $this,
			'name_in_viewobject' => 'scheduling_form',
			'use_captcha' => 0,
			'form_method' => 'POST',
			'form_name' => 'scheduling_form',
			'dont_close_session' => true,
				'fields_metadata' => array(
					'issue_id' => array(
						'CType' => 'select',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.scheduling.form.journal'),
						'SrcValues' => 'SELECT null as id, (\'-- CURRENT ISSUE --\') as name, 0 as num UNION
									SELECT ji.id, (j.name || \' \' || ji.year || \' \' || COALESCE(ji.name, \'\')) as name, 1 as num
											FROM pjs.journal_issues ji
											LEFT JOIN pjs.documents d ON d.issue_id = ji.id
											JOIN journals j ON j.id = ji.journal_id
											WHERE ji.journal_id = ' . $lJournalId . ' AND is_current = true
												AND ji.is_published = FALSE
									UNION SELECT null as id, (\'-- FUTURE ISSUES --\') as name, 2 as num
									UNION SELECT ji.id, (j.name || \' \' || ji.year || \' \' || COALESCE(ji.name, \'\')) as name, 3 as num
													FROM pjs.journal_issues ji
													LEFT JOIN pjs.documents d ON d.issue_id = ji.id
													JOIN journals j ON j.id = ji.journal_id
													WHERE ji.journal_id = ' . $lJournalId . '
														AND ji.is_published = FALSE
													
									UNION SELECT null as id, (\'-- PUBLISHED ISSUES --\') as name, 4 as num
									UNION (SELECT ji.id, (j.name || \' \' || ji.year || \' \' || COALESCE(ji.name, \'\')) as name, 5 as num
													FROM pjs.journal_issues ji
													LEFT JOIN pjs.documents d ON d.issue_id = ji.id
													JOIN journals j ON j.id = ji.journal_id
													WHERE ji.journal_id = ' . $lJournalId . '
														AND ji.is_published = TRUE)
														ORDER BY num
						',
						'AllowNulls' => false,
						'AddTags' => array(
							'class' => 'journals',
							//~ 'onfocus' => 'updateDocumentAutoPrice(1, this)',
							//~ 'onblur'  => 'changeFocus(2, this)',
						),
					),
					'startpage' => array(
						'CType' => 'text',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.scheduling.form.startpage'),
						'AllowNulls' => false,
						'Checks' => array(
							'checkValidNumber({startpage})',
						),
						'AddTags' => array(
							'class' => 'inputFld',
							'onchange' => 'updateDocumentAutoPrice(this)',
							'id' => 'startpage',
						)
					),
					'endpage' => array(
						'CType' => 'text',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.scheduling.form.endpage'),
						'AllowNulls' => false,
						'Checks' => array(
							//~ 'checkValidNumber({endpage})',
						),
						'AddTags' => array(
							'class' => 'inputFld',
							' onchange' => 'updateDocumentAutoPrice(this)',
							'id' => 'endpage',
						)
					),					
					'colorpage' => array(
						'CType' => 'text',
						'VType' => 'int',
						'DisplayName' => getstr('pjs.scheduling.form.colorpage'),
						'AllowNulls' => true,
						'Checks' => array(
							//~ 'checkValidNumber({endpage})',
						),
						'AddTags' => array(
							'class' => 'inputFld',
							//~ 'onchange' => 'updateDocumentAutoPrice(this)',
						)
					),	
					'price' => array(
						'CType' => 'text',
						'VType' => 'string',
						'DisplayName' => getstr('pjs.scheduling.form.price'),
						'AllowNulls' => true,
						'AddTags' => array(
							'class' => 'inputFld',
						)
					),
					'autoprice' => array(
						'CType' => 'text',
						'VType' => 'string',
						'DisplayName' => getstr('pjs.scheduling.form.price'),
						'AllowNulls' => true,
						'Checks' => array(
							
						),
						'AddTags' => array(
							'class' => 'inputFld',
						)
					),
					'new' => array(
						'CType' => 'action',
						'SQL' => 'SELECT * FROM pjs.spManageDocumentPrices(1, ' . $lDocumentId . ', null, null, null, null, null, null)',
						'ActionMask' => ACTION_EXEC | ACTION_FETCH,
						'DisplayName' => '',
					),
					'save' => array(
						'CType' => 'action',
						'SQL' => 'SELECT * FROM pjs.spManageDocumentPrices(2, ' . $lDocumentId . ', {startpage}, {endpage}, {colorpage}, COALESCE({price}::numeric, {autoprice}::numeric), null, {issue_id})',
						'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_SHOW,
						'CheckCaptcha' => 0,
						'DisplayName' => getstr('pjs.scheduling.form.save'),
						'AddTags' => array(
							'class' => 'inputBtn'
						)
					),
				)
			)
		);
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'document_se_decision',
			'document_id' => $this->m_documentId,
			'document_info' => $this->m_documentInfoObject,
			'tabs' => $this->m_tabsObject,
			'has_tabs' => (!(int)$this->m_editor_mode ? 1 : 0),
			'form' => $lForm,
		));
	}
	function GetCurState() {
		$lFilterSuggestedViewModeData = $this->GetValueFromRequest('suggested', 'GET', 'int');
		$lFilterSuggestedViewMode = $lFilterSuggestedViewModeData['value']; 
		
		$lFilterSearchViewModeLetter = $this->GetValueFromRequest('letter', 'GET');
		$lFilterSearchViewModeLetterValue = $lFilterSearchViewModeLetter['value']; 

		$lJournalModel = new mJournal();
		$lDocumentModel = new mDocuments_Model();
		$lCanInviteUsers = $this->m_documentData['review_lock'];
		
		$lHasTabs = 0;
		if(!(int)$this->m_editor_mode) {
			$lHasTabs = 1;
		}
		/**
		 * Ended document review rounds
		 */
		$lReviewRoundsData = $lDocumentModel->GetDocumentReviewRounds($this->m_documentId, 1);
		$lReviewRoundOneObj = '';
		$lReviewRoundTwoObj = '';
		$lReviewRoundThreeObj = '';
		$lHasRound1 = 0;
		$lHasRound2 = 0;
		$lHasRound3 = 0;
		foreach ($lReviewRoundsData as $key => $value) {
			$lRoundData = array();
			$lInvitedPanelData = array();
			$lReviewedPanelPublicData = array();
			$lInvitedReviewers = '';
			$lReviewersReviewedDocObj = '';
			$lInvitedReviewedPublicPanelReviewersObj1 = '';
			$lAssignedDedicatedReviewers = '';
			$lRoundData = $lDocumentModel->GetAssignedDedicatedReviewersListByRound((int)$this->m_documentId, (int)$value['id']);
			$lInvitedPanelData = $lDocumentModel->GetInvitedPanelReviewersList($this->m_documentId, (int)$value['id']);
			$lReviewedPanelPublicData = $lDocumentModel->GetReviewedPanelPublicReviewersList($this->m_documentId, (int)$value['id']);
			
			$lInvitedReviewers = new evList_Display(array(
				'controller_data' => $lInvitedPanelData,
				'name_in_viewobject' => 'invited_reviewers_obj_list',
				'document_id' => $this->m_documentId,
			));
			
			$lReviewersReviewedDocObj = new evList_Display(array(
				'controller_data' => $lReviewedPanelPublicData,
				'name_in_viewobject' => 'reviewers_reviewed_document',
				'document_id' => $this->m_documentId,
			));
			
			if(count($lInvitedPanelData) || count($lReviewedPanelPublicData)) {
				
				if((int)$value['round_number'] == $this->m_documentData['round_number']) {
					$lNamePublicPanelInViewObj = 'public_panel_reviewers_holder';
				} else {
					$lNamePublicPanelInViewObj = 'public_panel_reviewers_holder_view';
				}
				
				$lInvitedReviewedPublicPanelReviewersObj1 = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => $lNamePublicPanelInViewObj,
					'invited_reviewers' => $lInvitedReviewers,
					'reviewed_reviewers' => $lReviewersReviewedDocObj,
				));	
			}
			
			
			$lAssignedDedicatedReviewers = new evList_Display(array(
				'controller_data' => $lRoundData,
				'name_in_viewobject' => 'view_review_rounds',
				'round_number' => $value['round_number'],
			));	
				
			$lHideReviewersText = 0;
			if(!count($lRoundData) && !count($lInvitedPanelData) && !count($lReviewedPanelPublicData)) {
				$lHideReviewersText = 1;
			}
			
			$roundObj = new evSimple_Block_Display(array(
				'controller_data' => '',
				'name_in_viewobject' => 'assigned_invited_reviewers_veiw',
				'dedicated_reviewers' => $lAssignedDedicatedReviewers,
				'panel_public_reviewers' => $lInvitedReviewedPublicPanelReviewersObj1,
				'round_number' => $value['round_number'],
				'version_num' => $value['version_num'],
				'author_version_id' => $value['author_version_id'],
				'se_version_id' => $value['se_version_id'],
				'round_name' => $value['round_name'],
				'decision_round_name' => $value['decision_name'],
				'hide_reviewers_text' => $lHideReviewersText,
				'document_id' => $this->m_documentId,
			));
			
			if((int)$value['round_number'] == REVIEW_ROUND_ONE) {
				$lHasRound1 = 1;
				$lReviewRoundOneObj = $roundObj; 
			} elseif((int)$value['round_number'] == REVIEW_ROUND_TWO) {
				$lHasRound2 = 1;
				$lReviewRoundTwoObj = $roundObj;	
			} else {
				$lHasRound3 = 1;
				$lReviewRoundThreeObj = $roundObj;	
			}

		}
		//Overview mode
		if(!(int)$this->m_editor_mode) { 
			$lAssignedSEsData = $lDocumentModel->GetAssignedSEList($this->m_documentId);
			$lAssignedSEs = new evList_Display(array(
				'controller_data' => $lAssignedSEsData,
				'name_in_viewobject' => 'se_assigned_list',
				'round_number' => $this->m_documentData['round_number'],
				'round_type' => $this->m_documentData['round_type'],
				'state_id' => $this->m_documentData['state_id'],
				'version_num' => $this->m_documentData['version_num'],
				'author_version_id' => $this->m_documentData['author_version_id'],
				'round_name' => $this->m_documentData['round_name'],
				'document_id' => $this->m_documentId,
				'check_invited_users' => $this->m_documentData['check_invited_users'],
				'merge_flag' => $this->m_documentData['merge_flag'],
			));
		
			switch ((int)$this->m_documentState) {
				case DOCUMENT_WAITING_SE_ASSIGNMENT_STATE :
					$lSubmissionActionsObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'submission_actions',
						'userid' => $this->GetUserId(),	));
					
					$this->m_contentObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_se_decision',
						'submission_actions' => $lSubmissionActionsObject,
						));
					break;
				case DOCUMENT_IN_REVIEW_STATE :
					$lNodataType = 0;
					$lDocumentRoundData = $lDocumentModel->GetSEDocumentCurrentRoundNumberAndUserId($this->m_documentId, $this->GetUserId());
					$lSEDocumentRoundUserId = $lDocumentRoundData['id'];
					$lDocumentRoundNumber = $lDocumentRoundData['round_number'];
					$lDocumentRoundDueDate = $lDocumentRoundData['round_due_date'];
					$lUserRoundDueDate = $lDocumentRoundData['user_due_date'];
					
					$lAssignedReviewersData = $lDocumentModel->GetAssignedDedicatedReviewersList($this->m_documentId);
					$lInvitedPanelData = $lDocumentModel->GetInvitedPanelReviewersList($this->m_documentId, $this->m_documentData['current_round_id'], 1);
					$lReviewedPanelPublicData = $lDocumentModel->GetReviewedPanelPublicReviewersList($this->m_documentId, $this->m_documentData['current_round_id']);
					
					$lAssignedReviewersShowActions = $lAssignedReviewersData['showactions'];
					$lAssignedReviewersMainData = $lAssignedReviewersData['data'];
					//$lRoundReviewersDataCheck = $this->CheckWaitingReviewers($lAssignedReviewersData, $lDocumentRoundNumber);
					$lRoundReviewersDataCheck = $this->m_documentData['enough_reviewers'];
					
					$lShowBorder = 1;
          			if(count($lAssignedReviewersData) || count($lInvitedPanelData) || count($lReviewedPanelPublicData)){
						$lReviewRoundSECheck = $this->m_documentData['can_proceed'];
						//~ $lInvitationData = $lUserModel->GetDocumentCurrentRoundReviewerInvitationData($this->m_documentId, $this->GetUserId(), (int) DEDICATED_REVIEWER_ROLE);
						//~ vaR_dump($lAssignedReviewersData);
						$lAssignedReviewers = new evList_Display(array(
							'controller_data' => $lAssignedReviewersData,
							'name_in_viewobject' => 'dedicated_reviewer_assigned_list',
							'document_id' => $this->m_documentId,
							'showactions' => $lAssignedReviewersShowActions,
							'round_number' => $this->m_documentData['round_number'],
							'state_id' => $this->m_documentData['state_id'],
							'round_type' => $this->m_documentData['round_type'],
							'version_num' => $this->m_documentData['version_num'],
							'author_version_id' => $this->m_documentData['author_version_id'],
							'round_name' => $this->m_documentData['round_name'],
							'reviewers_check' => $lRoundReviewersDataCheck,
							'round_user_id' => $lSEDocumentRoundUserId,
							'document_id' => $this->m_documentId,
							'reviwer_version_id' => $this->m_documentData['user_version_id'],
							'check_invited_users' => $this->m_documentData['check_invited_users'],
							'merge_flag' => $this->m_documentData['merge_flag'],
						));
						$lShowBorder = 0;
						//var_dump($lReviewRoundSECheck);
						
						$lInvitedReviewers = new evList_Display(array(
							'controller_data' => $lInvitedPanelData,
							'name_in_viewobject' => 'invited_reviewers_obj_list',
							'document_id' => $this->m_documentId,
						));
						
						$lReviewersReviewedDocObj = new evList_Display(array(
							'controller_data' => $lReviewedPanelPublicData,
							'name_in_viewobject' => 'reviewers_reviewed_document',
							'document_id' => $this->m_documentId,
						));
						
						if(count($lInvitedPanelData) || count($lReviewedPanelPublicData)) {
							$lInvitedReviewedPublicPanelReviewersObj = new evSimple_Block_Display(array(
								'controller_data' => $this->m_documentData,
								'name_in_viewobject' => 'public_panel_reviewers_holder',
								'invited_reviewers' => $lInvitedReviewers,
								'reviewed_reviewers' => $lReviewersReviewedDocObj,
							));
							if(!count($lAssignedReviewersData)) {
								$lNodataType = 1;
							}
						}else {
							if(count($lAssignedReviewersData)) {
								$lNodataType = 2;
							}
						}
						
						$lAssignedReviewersObj = new evSimple_Block_Display(array(
							'controller_data' => $this->m_documentData,
							'name_in_viewobject' => 'assigned_invited_reviewers',
							'dedicated_reviewers' => $lAssignedReviewers,
							'panel_public_reviewers' => $lInvitedReviewedPublicPanelReviewersObj,
							'userid' => $this->GetUserId(),
							'showactions' => true,
							'no_data_type' => (int)$lNodataType,
						));
						if($lReviewRoundSECheck == 'true') {
							$lShowBorder = 0;
							if($this->m_documentData['document_review_type_id'] == DOCUMENT_NON_PEER_REVIEW) {
								$lNameInViewObject = 'assigned_reviewers_list_se_can_take_decision';
							} else {
								$lNameInViewObject = 'assigned_reviewers_list_se_can_take_decision_closed_peer';
							}
						} else {
							$lNameInViewObject = 'assigned_reviewers_list_waiting_reviewers';
						}
						$lSECanTakeDecison = new evSimple_Block_Display(array(
							'controller_data' => $this->m_documentData,
							'name_in_viewobject' => $lNameInViewObject,
							'document_id' => $this->m_documentId,
							'subject_editor_name' => $lAssignedSEsData[0]['first_name'] . ' ' . $lAssignedSEsData[0]['last_name'],
							'round_due_date_main' => $lDocumentRoundData['user_due_date'],
							'current_round_id' => $lDocumentRoundData['current_round_id'],
							'reviewers_check' => $lRoundReviewersDataCheck,
							'role_id' => $this->m_viewingRole,
							'round_user_id' => $lSEDocumentRoundUserId,
							'round_number' => $lDocumentRoundData["round_number"],
							'can_invite_reviewers' => $lCanInviteUsers
						));
					} else {
						if(
							in_array($lDocumentRoundNumber, array(REVIEW_ROUND_TWO, REVIEW_ROUND_THREE)) || 
							$this->m_documentData['document_review_type_id'] == DOCUMENT_NON_PEER_REVIEW ||
							($lDocumentRoundNumber == REVIEW_ROUND_ONE && $this->m_documentData['can_proceed'] == 'true')
						) {
							$lShowBorder = 0;
							$lNameInViewObject = 'assigned_reviewers_list_se_can_take_decision';
						} else {
							$lNameInViewObject = 'assigned_reviewers_list';
						}
						$lAssignedReviewersObj = new evSimple_Block_Display(array(
							'controller_data' => $this->m_documentData,
							'name_in_viewobject' => $lNameInViewObject,
							'document_id' => $this->m_documentId,
							'subject_editor_name' => $lAssignedSEsData[0]['first_name'] . ' ' . $lAssignedSEsData[0]['last_name'],
							'round_due_date_main' => $lDocumentRoundData['user_due_date'],
							'reviewers_check' => $lRoundReviewersDataCheck,
							'role_id' => $this->m_viewingRole,
							'userid' => $this->GetUserId(),
							'round_user_id' => $lSEDocumentRoundUserId,
							'can_invite_reviewers' => $lCanInviteUsers
						));	
						
					}
					
					$this->m_contentObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_editor_assigned_se',
						'assigned_reviewers' => $lAssignedReviewersObj,
						'se_can_take_decision' => $lSECanTakeDecison,
						'border' => $lShowBorder,
					));	
					break;
				case DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_REVIEW_STATE:
					//~ var_dump($lDocumentModel->GetAuthorRoundDetails($this->m_documentId, AUTHOR_ROUND_TYPE));
					$lWaitAuthorRevisionObject = new evSimple_Block_Display(array(
						'controller_data' => $lDocumentModel->GetAuthorRoundDetails($this->m_documentId, AUTHOR_ROUND_TYPE),
						'name_in_viewobject' => 'document_editor_waiting_author_version_after_review_object',
						'round_number' => ($this->m_documentData['round_number'] + 1),
						'version_num' => $this->m_documentData['version_num'],
						'author_version_id' => $this->m_documentData['author_version_id'],
						'current_round_id' => $this->m_documentData['current_round_id'],
					));	
					$this->m_contentObject = new evSimple_Block_Display(
						array('controller_data' => $this->m_documentData,
							'name_in_viewobject' => 'document_editor_waiting_author_version_after_review',
							'assigned_reviewers' => $lAssignedReviewers,
							'se_can_take_decision' => $lSECanTakeDecison,
							'waiting_author' => $lWaitAuthorRevisionObject,
						)
					);	
					break;
				case DOCUMENT_READY_FOR_COPY_REVIEW_STATE :
					$lCEDecisionData = $lDocumentModel->GetCEDataList($this->m_documentId);
					$lCERoundsCount = (int)count($lCEDecisionData);
					if($lCERoundsCount) {
						$lCEListObj = new evList_Display(array(
							'controller_data' => $lCEDecisionData, 
							'name_in_viewobject' => 'document_ce_rounds',
							'document_id' => $this->m_documentId,
							'author_version_id' => $this->m_documentData['author_version_id'],
						)); 
					}
				
					$lCurrentRoundNumber = count($lCEDecisionData) + 1;
					
					$lCEAssignObj = new evSimple_Block_Display(array(
						'controller_data' => $lDocumentModel->GetAuthorRoundDetails($this->m_documentId, E_ROUND_TYPE), 
						'name_in_viewobject' => 'document_waiting_ce_assign_obj',
						'round_name' => $this->m_documentData['round_name'],
						'round_number' => (int)$lCurrentRoundNumber,
						'round_type' => $this->m_documentData['round_type'],
						'state_id' => $this->m_documentData['state_id'],
						'version_num' => $this->m_documentData['version_num'],
						'author_version_id' => $this->m_documentData['author_version_id'],
						'document_id' => $this->m_documentId,
						'check_invited_users' => $this->m_documentData['check_invited_users'],
						'merge_flag' => $this->m_documentData['merge_flag'],
						'ce_rounds_count' => $lCERoundsCount,
					)); 
					
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_waiting_ce_assign',
						'ce_assign_obj' => $lCEAssignObj,
						'ce_rounds' => $lCEListObj,
					)); 
					break;
				case DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE: 			
				case DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE:
				case DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE:
				case DOCUMENT_IN_COPY_REVIEW_STATE :
					
					$lCEDecisionData = $lDocumentModel->GetCEDataList($this->m_documentId);
					$lCERoundsCount = (int)count($lCEDecisionData);
					if($lCERoundsCount) {
						$lCEListObj = new evList_Display(array(
							'controller_data' => $lCEDecisionData, 
							'name_in_viewobject' => 'document_ce_rounds',
							'document_id' => $this->m_documentId,
							'author_version_id' => $this->m_documentData['author_version_id'],
						)); 
					}
					
					$lCurrentCERoundNumber = count($lCEDecisionData) + 1;
					
					
					if(in_array($this->m_documentState, array(
						DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE,
						DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE,
						DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE
						))
					) {
						$lWaitAuthorRevisionObject = new evSimple_Block_Display(array(
							'controller_data' => $lDocumentModel->GetAuthorRoundDetails($this->m_documentId, AUTHOR_ROUND_TYPE),
							'name_in_viewobject' => 'document_editor_waiting_author_version_after_review_rounds_object',
							//'round_number' => ($this->m_documentData['version_num'] + 1),
							'version_num' => $this->m_documentData['version_num'],
							'current_round_id' => $this->m_documentData['current_round_id'],
							'state_id' => $this->m_documentState,
							'author_version_id' => $this->m_documentData['author_version_id'],
							'ce_rounds_count' => $lCERoundsCount,
						));	
					}
					if($this->m_documentState == DOCUMENT_IN_COPY_REVIEW_STATE) {
					 //var_dump($this->m_documentData);
						$lCEObj = new evSimple_Block_Display(array(
							'controller_data' => $lDocumentModel->GetAuthorRoundDetails($this->m_documentId, CE_ROUND_TYPE), 
							'name_in_viewobject' => 'document_waiting_ce_obj',
							'round_name' => $this->m_documentData['round_name'],
							'round_number' => (int)$lCurrentCERoundNumber,
							'round_type' => $this->m_documentData['round_type'],
							'state_id' => $this->m_documentData['state_id'],
							'version_num' => $this->m_documentData['version_num'],
							'author_version_id' => $this->m_documentData['author_version_id'],
							'current_round_id' => $this->m_documentData['current_round_id'],
							'document_id' => $this->m_documentId,
							'check_invited_users' => $this->m_documentData['check_invited_users'],
							'merge_flag' => $this->m_documentData['merge_flag'],
							'ce_rounds_count' => $lCERoundsCount,
						)); 
					}
					if(in_array($this->m_documentState, array(
						DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE,
						DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE
						))
					) {
						
						//~ $lCEDecisionData = $lDocumentModel->GetCEData($this->m_documentId);
						//~ if(count($lCEDecisionData)) {
							//~ $lCEObj = new evSimple_Block_Display(array(
								//~ 'controller_data' => $lCEDecisionData, 
								//~ 'name_in_viewobject' => 'document_waiting_ce_decision',
								//~ 'copy_editor_version_id' => $this->m_documentData['copy_editor_version_id'],
								//~ 'document_id' => $this->m_documentId,
								//~ 'version_num' => $this->m_documentData['version_num'],
								//~ 'author_version_id' => $this->m_documentData['author_version_id'],
							//~ )); 
						//~ }
					}
					
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_in_ce_state',
						'waiting_author' => $lWaitAuthorRevisionObject,
						'ce_assigned' => $lCEObj,
						'ce_rounds' => $lCEListObj,
					)); 
					break;
				case DOCUMENT_READY_FOR_LAYOUT_STATE :
					$lCEDecisionData = $lDocumentModel->GetCEData($this->m_documentId);
					if(count($lCEDecisionData)) {
						$lCEObj = new evSimple_Block_Display(array(
							'controller_data' => $lCEDecisionData, 
							'name_in_viewobject' => 'document_waiting_ce_decision',
							'copy_editor_version_id' => $this->m_documentData['copy_editor_version_id'],
							'document_id' => $this->m_documentId,
							'version_num' => $this->m_documentData['version_num'],
							'author_version_id' => $this->m_documentData['author_version_id'],
						)); 
					}
					
					$lLEAssignObj = new evSimple_Block_Display(array(
						'controller_data' => $lDocumentModel->GetAuthorRoundDetails($this->m_documentId, E_ROUND_TYPE), 
						'name_in_viewobject' => 'document_waiting_le_assign_obj',
						'round_name' => $this->m_documentData['round_name'],
						'round_number' => $this->m_documentData['round_number'],
						'round_type' => $this->m_documentData['round_type'],
						'state_id' => $this->m_documentData['state_id'],
						'version_num' => $this->m_documentData['version_num'],
						'author_version_id' => $this->m_documentData['author_version_id'],
						'document_id' => $this->m_documentId,
						'check_invited_users' => $this->m_documentData['check_invited_users'],
						'merge_flag' => $this->m_documentData['merge_flag'],
					)); 
					
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData, 
						'name_in_viewobject' => 'document_waiting_le_assign',
						'le_assign_obj' => $lLEAssignObj,
						'ce_obj' => $lCEObj
					)); 
					break;
				case DOCUMENT_IN_LAYOUT_EDITING_STATE :
					$lCEDecisionData = $lDocumentModel->GetCEDataList($this->m_documentId);
					if(count($lCEDecisionData)) {
						$lCEListObj = new evList_Display(array(
							'controller_data' => $lCEDecisionData, 
							'name_in_viewobject' => 'document_ce_rounds',
							'document_id' => $this->m_documentId,
							'author_version_id' => $this->m_documentData['author_version_id'],
						)); 
					}
					
					$lLEObj = new evSimple_Block_Display(array(
						'controller_data' => $lDocumentModel->GetAuthorRoundDetails($this->m_documentId, LE_ROUND_TYPE), 
						'name_in_viewobject' => 'document_waiting_le_obj',
						'round_name' => $this->m_documentData['round_name'],
						'round_number' => $this->m_documentData['round_number'],
						'round_type' => $this->m_documentData['round_type'],
						'state_id' => $this->m_documentData['state_id'],
						'version_num' => $this->m_documentData['version_num'],
						'author_version_id' => $this->m_documentData['author_version_id'],
						'current_round_id' => $this->m_documentData['current_round_id'],
						'document_id' => $this->m_documentId,
						'check_invited_users' => $this->m_documentData['check_invited_users'],
						'merge_flag' => $this->m_documentData['merge_flag'],
					)); 
					
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_in_le_state',
						'le_assigned' => $lLEObj,
						'ce_obj' => $lCEListObj,
					)); 
					break;
				case DOCUMENT_PUBLISHED_STATE :						
				case DOCUMENT_APPROVED_FOR_PUBLISH :
					$lLEDecisionData = $lDocumentModel->GetLEData($this->m_documentId);
					if(count($lLEDecisionData)) {
						
						$lLEObj = new evSimple_Block_Display(array(
							'controller_data' => $lLEDecisionData, 
							'name_in_viewobject' => 'document_waiting_le_decision',
							'layout_version_id' => $this->m_documentData['layout_version_id'],
							'document_id' => $this->m_documentId,
							'version_num' => $this->m_documentData['version_num'],
							'author_version_id' => $this->m_documentData['author_version_id'],
						)); 
					}
					
					
					//~ $lCEDecisionData = $lDocumentModel->GetCEData($this->m_documentId);
					//~ if(count($lCEDecisionData)) {
						//~ $lCEObj = new evSimple_Block_Display(array(
							//~ 'controller_data' => $lCEDecisionData, 
							//~ 'name_in_viewobject' => 'document_waiting_ce_decision',
							//~ 'copy_editor_version_id' => $this->m_documentData['copy_editor_version_id'],
							//~ 'document_id' => $this->m_documentId,
							//~ 'version_num' => $this->m_documentData['version_num'],
							//~ 'author_version_id' => $this->m_documentData['author_version_id'],
						//~ )); 
					//~ }
					
					$lCEDecisionData = $lDocumentModel->GetCEDataList($this->m_documentId);
					if(count($lCEDecisionData)) {
						$lCEListObj = new evList_Display(array(
							'controller_data' => $lCEDecisionData, 
							'name_in_viewobject' => 'document_ce_rounds',
							'document_id' => $this->m_documentId,
							'author_version_id' => $this->m_documentData['author_version_id'],
						)); 
					}
					
					$this->m_contentObject = new evSimple_Block_Display(array( 
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_approved_for_publish',
						'ce_obj' => $lCEObj,
						'le_obj' => $lCEListObj,
					)); break;
				
				case DOCUMENT_REJECTED_STATE :
				case DOCUMENT_REJECTED_BUT_RESUBMISSION :
					$this->m_contentObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_rejected')); break;
				default:
					break;	
			}
			$lSubmissionNotesObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
											'name_in_viewobject' => 'submission_notes'));
			
			$lHasSE = 0;	
			if(count($lAssignedSEsData)) {
				$lHasSE = 1;
			}
			$commonDocProperties = array(
				'assigned_se' => $lAssignedSEs,
				'review_round_' . REVIEW_ROUND_ONE => $lReviewRoundOneObj,
				   'has_round_' . REVIEW_ROUND_ONE => $lHasRound1,
				'review_round_' . REVIEW_ROUND_TWO => $lReviewRoundTwoObj,
				   'has_round_' . REVIEW_ROUND_TWO => $lHasRound2,
				'review_round_' . REVIEW_ROUND_THREE => $lReviewRoundThreeObj,
				   'has_round_' . REVIEW_ROUND_THREE => $lHasRound3,
				'submission_notes' => $lSubmissionNotesObject,
				'document_id' => $this->m_documentId,
				'document_info' => $this->m_documentInfoObject,
				'tabs' => $this->m_tabsObject,
				'has_tabs' => $lHasTabs,
				'has_se' => $lHasSE,
			);
			
			foreach ($commonDocProperties as $key => $value)
					$this->m_contentObject->SetVal($key, $value);			
		} elseif($this->m_editor_mode == 1){ //Action mode
			switch ($this->m_documentState) {
				case DOCUMENT_WAITING_SE_ASSIGNMENT_STATE:
				case DOCUMENT_IN_REVIEW_STATE :
					$lAvailableSEs = new evList_Display(array(
						'controller_data' => $lJournalModel->GetAvailableSEList($this->m_documentData['journal_id'], $this->m_documentId, $lFilterSuggestedViewMode, $lFilterSearchViewModeLetterValue),
						'name_in_viewobject' => 'se_available_list',
						'round_number' => $this->m_documentData['round_number'],
						'round_type' => $this->m_documentData['round_type'],
						'state_id' => $this->m_documentData['state_id'],
						'version_num' => $this->m_documentData['version_num'],
						'author_version_id' => $this->m_documentData['author_version_id'],
						'round_name' => $this->m_documentData['round_name'],
						'document_id' => $this->m_documentId,
						'journal_id' => $this->m_documentData['journal_id'],
						'suggested' => $lFilterSuggestedViewMode == 1 ? 'current' : '',
						'all' => !($lFilterSuggestedViewMode || $lFilterSearchViewModeLetterValue) ? 'current' : '',
						
					));
					//$this->m_pageView->SetVal('pagetitle', 'Assign subject editor');
					
					$this->m_contentObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document',
						'available_se' => $lAvailableSEs
						
					));
					break;
				case DOCUMENT_READY_FOR_COPY_REVIEW_STATE:
				case DOCUMENT_IN_COPY_REVIEW_STATE:
					$this->m_contentObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_ce_list',
						'round_number' => $this->m_documentData['round_number'],
						'round_type' => $this->m_documentData['round_type'],
						'state_id' => $this->m_documentData['state_id'],
						'version_num' => $this->m_documentData['version_num'],
						'author_version_id' => $this->m_documentData['author_version_id'],
						'round_name' => $this->m_documentData['round_name'],
						'ce_list' => new evList_Display(array(
									'controller_data' => $lJournalModel->GetAvailableCEList($this->m_documentData['journal_id'], $this->m_documentId, $this->m_documentData['current_round_id']),
									'name_in_viewobject' => 'ce_allavailable_list',
									'document_id' => $this->m_documentId,
					                'round_number' => $this->m_documentData['round_number'],
									'round_type' => $this->m_documentData['round_type'],
									'state_id' => $this->m_documentData['state_id'],
					                'version_num' => $this->m_documentData['version_num'],
					                'author_version_id' => $this->m_documentData['author_version_id'],
									'check_invited_users' => $this->m_documentData['check_invited_users'],
									'merge_flag' => $this->m_documentData['merge_flag'],
					                'round_name' => $this->m_documentData['round_name'],
									'current_round_id' => $this->m_documentData['current_round_id'],
            ))));break;
				
				case DOCUMENT_READY_FOR_LAYOUT_STATE:
				case DOCUMENT_IN_LAYOUT_EDITING_STATE:
					$this->m_contentObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_le_list',
						'round_number' => $this->m_documentData['round_number'],
						'round_type' => $this->m_documentData['round_type'],
						'state_id' => $this->m_documentData['state_id'],
						'version_num' => $this->m_documentData['version_num'],
						'author_version_id' => $this->m_documentData['author_version_id'],
						'round_name' => $this->m_documentData['round_name'],
						'le_list' => new evList_Display(array(
								'controller_data' => $lJournalModel->GetAvailableLEList($this->m_documentData['journal_id'], $this->m_documentId),
								'name_in_viewobject' => 'le_allavailable_list',
								'document_id' => $this->m_documentId,
								'round_number' => $this->m_documentData['round_number'],
								'round_type' => $this->m_documentData['round_type'],
								'state_id' => $this->m_documentData['state_id'],
                				'version_num' => $this->m_documentData['version_num'],
                				'author_version_id' => $this->m_documentData['author_version_id'],
                				'round_name' => $this->m_documentData['round_name'],
								'check_invited_users' => $this->m_documentData['check_invited_users'],
								'merge_flag' => $this->m_documentData['merge_flag'],
								)))); break;
				default:
					break;
			}
		} elseif ($this->m_editor_mode == 2){
			switch ($this->m_documentState) {
				case DOCUMENT_READY_FOR_COPY_REVIEW_STATE:
				case DOCUMENT_READY_FOR_LAYOUT_STATE:
				case DOCUMENT_IN_LAYOUT_EDITING_STATE:
					$lCEDecisionData = $lDocumentModel->GetCEDataList($this->m_documentId);
					$lCERoundsCount = count($lCEDecisionData);
					
					if($this->m_documentState == DOCUMENT_READY_FOR_COPY_REVIEW_STATE && $lCERoundsCount > 0) {
						$this->m_contentObject = new evSimple_Block_Display(array('controller_data' => $this->m_documentData,
							'name_in_viewobject' => 'document_le_list',
							'round_number' => $this->m_documentData['round_number'],
							'round_type' => $this->m_documentData['round_type'],
							'state_id' => $this->m_documentData['state_id'],
							'version_num' => $this->m_documentData['version_num'],
							'author_version_id' => $this->m_documentData['author_version_id'],
							'round_name' => $this->m_documentData['round_name'],
							'le_list' => new evList_Display(array(
									'controller_data' => $lJournalModel->GetAvailableLEList($this->m_documentData['journal_id'], $this->m_documentId),
									'name_in_viewobject' => 'le_allavailable_list',
									'document_id' => $this->m_documentId,
									'round_number' => $this->m_documentData['round_number'],
									'round_type' => $this->m_documentData['round_type'],
									'state_id' => $this->m_documentData['state_id'],
									'version_num' => $this->m_documentData['version_num'],
									'author_version_id' => $this->m_documentData['author_version_id'],
									'round_name' => $this->m_documentData['round_name'],
									'check_invited_users' => $this->m_documentData['check_invited_users'],
									'merge_flag' => $this->m_documentData['merge_flag'],
									)))); 
					}
					break;
					default:
						break;
			}
		}
	}

	function Display(){
		return $this->m_pageView->Display();
	}

}
?>