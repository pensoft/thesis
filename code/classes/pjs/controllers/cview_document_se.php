<?php

class cView_Document_SE extends cView_Document {
	var $m_editor_mode;
	var $m_editor_back;

	function InitObjects() {
		$this->m_viewingRole = SE_ROLE;

		$this->GetDocumentInfo();
		$this->GetActiveSection();
		$this->GetSectionTabsObjByRole($this->m_viewingRole);
		$this->GetDocumentInfoObject();
		$lEditorViewMode = $this->GetValueFromRequest('mode', 'GET', 'int', false, false);
		$lEditorBack = $this->GetValueFromRequest('e_back', 'GET', 'int', false, false);
		
		$this->m_editor_back = (int)$lEditorBack['value'];
		$this->m_editor_mode = $lEditorViewMode['value'];
		
		switch ($this->m_section) {
			case GET_METADATA_SECTION:
				$this->GetMetadata();
				break;
			case GET_HISTORY_SECTION:
				if(!(int)$this->m_HasHistory) {
					header ("location: " . SITE_URL . "view_document.php?id=" . $this->m_documentData['document_id'] . "&view_role=" . $this->m_viewingRole);
				}
				$this->GetHistory();
				break;
			case GET_SUBMITTED_FILES_SECTION:
				$this->GetSubmittedFiles();
				break;
			default:
				$this->GetCurState();
				break;
		}
		$pViewPageObjectsDataArray['contents'] = $this->m_contentObject;
		if($this->m_editor_mode) {
			$pViewPageObjectsDataArray['pagetitle'] = getstr('Invite reviewers');
		} else {
			$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.editorial');
		}
		$this->AddJournalObjects($this->m_journalId);
		$this->m_pageView = new pView_Document_SE(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));		
		$this->m_allowedViewingModes = $this->GetUserJournalDashboardAllowedViewingModes($this->m_journalId);
		$this->InitLeftcolObjects();
	}

	function GetCurState() {	

		$lFilterSearchViewMode = $this->GetValueFromRequest('reviewer_search', 'POST');
		$lFilterSearchViewModeValue = $lFilterSearchViewMode['value']; 
		$lCanInviteUsers = $this->m_documentData['review_lock'];
		$lHasPanelReviews = 0;
		
		$lHasTabs = 0;
		if(!(int)$this->m_editor_mode){
			$lHasTabs = 1;
		}
		
		$lDocumentModel = new mDocuments_Model();
		$lJournalModel = new mJournal();
		
		switch ((int) $this->m_documentState) {
			default :
				$lLastRoundDecisionData = $lDocumentModel->GetLastReviewRoundDecisionData($this->m_documentId);
				
				if((int) $this->m_documentState == DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE) {
					$lDocumentViewObject = 'document_passed_review_state_layout';
				} else {
					$lDocumentViewObject = 'document_passed_review_state_copyediting';
				}
				
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => $lDocumentViewObject,
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => $lHasTabs,
					'round_number_accept' => $lLastRoundDecisionData['round_number'],
					'decision_date' => $lLastRoundDecisionData['decision_date'],
				));
				break;
			case (int) DOCUMENT_APPROVED_FOR_PUBLISH :
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'document_approved_for_publish',
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => $lHasTabs,
				));
				break;
			case (int) DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_REVIEW_STATE :
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'document_waiting_author_version_after_review',
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => $lHasTabs,
				));
				break;
			case (int) DOCUMENT_IN_REVIEW_STATE :
				$lDocumentRoundData = $lDocumentModel->GetSEDocumentCurrentRoundNumberAndUserId($this->m_documentId, $this->GetUserId());
				$lSEDocumentRoundUserId = $lDocumentRoundData['id'];
				$lDocumentRoundNumber = $lDocumentRoundData['round_number'];
				$lDocumentRoundDueDate = $lDocumentRoundData['round_due_date'];
				/**
				 * Assign reviewers mode
				 */
				
				if($this->m_editor_mode) {
					if($lCanInviteUsers == 'true') {
						$this->Redirect('/index.php');
					}
					$pViewPageObjectsDataArray['pagetitle'] = getstr('Invite reviewers');
					//$lCanTakeDecision = $lDocumentModel->CheckIfSECanTakeDecision($this->m_documentId);
					$lCanTakeDecision = $this->m_documentData['can_proceed'];
					$lSEDocumentRoundUserIdData = $lDocumentModel->GetSEDocumentCurrentRoundNumberAndUserId($this->m_documentId, $this->GetUserId());
					//var_dump($lCanTakeDecision);
					if($lDocumentRoundNumber < MAX_REVIEW_ROUNDS && $this->m_documentData['document_review_type_id'] != DOCUMENT_NON_PEER_REVIEW) {
						$lAvailableDedicatedReviewers = new evList_Display(array(
							'controller_data' => $lJournalModel->GetAvailableDedicatedReviewersList($this->m_documentData['journal_id'], $this->m_documentId, $lFilterSearchViewModeValue),
							'name_in_viewobject' 	=> 'dedicated_reviewer_available_list',
							'document_id' 			=> $this->m_documentId,
							'round_number' 			=> $this->m_documentData['round_number'],
							'round_type' 			=> $this->m_documentData['round_type'],
							'state_id' 				=> $this->m_documentData['state_id'],
							'version_num' 			=> $this->m_documentData['version_num'],
							'round_name' 			=> $this->m_documentData['round_name'],
							'check_invited_users'	=> $this->m_documentData['check_invited_users'],
							'merge_flag' 			=> $this->m_documentData['merge_flag'],
							'current_round_id' 		=> $lSEDocumentRoundUserIdData['current_round_id'],
							'journal_id'			=> $this->m_documentData['journal_id'],
							'review_process_type'	=> $this->m_documentData['document_review_type_id'],
							'author_version_id' => $this->m_documentData['author_version_id'],
							'view_role' 			=> ((int)$this->GetValueFromRequestWithoutChecks('view_role') ? (int)$this->GetValueFromRequestWithoutChecks('view_role') : $this->m_viewingRole),
						));
						if($lCanTakeDecision == 'true'){
							$lDecisionObject = new evSimple_Block_Display(array(
								'controller_data' => array(),
								'name_in_viewobject' => 'decision_form',
								'round_user_id' => $lSEDocumentRoundUserId,
								'round_number' => $this->m_documentData['round_number'],
								'round_type' => $this->m_documentData['round_type'],
								'state_id' => $this->m_documentData['state_id'],
								'version_num' => $this->m_documentData['version_num'],
								'author_version_id' => $this->m_documentData['author_version_id'],
								'round_name' => $this->m_documentData['round_name'],
								'merge_flag' => $this->m_documentData['merge_flag'],
								
							));
						}
						$lDocumentViewNameObj = 'document_in_review';
					} else {
						$lDecisionObject = new evSimple_Block_Display(array(
							'controller_data' => $this->m_documentData,
							'name_in_viewobject' => 'document_cant_invite_reviewers_for_this_round',
						));
						$lDocumentViewNameObj = 'document_in_review_cant_assign_reviewers';
					}
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => $lDocumentViewNameObj,
						'available_dedicated_reviewers' => $lAvailableDedicatedReviewers,
						'decision' => $lDecisionObject,
						'editor_back' => $this->m_editor_back,
					));
				} else {
					$lNodataType = 0;
					$lAssignedReviewersData = $lDocumentModel->GetAssignedDedicatedReviewersList($this->m_documentId);
					$lInvitedPanelData = $lDocumentModel->GetInvitedPanelReviewersList($this->m_documentId, $this->m_documentData['current_round_id'], 1);
					$lReviewedPanelPublicData = $lDocumentModel->GetReviewedPanelPublicReviewersList($this->m_documentId, $this->m_documentData['current_round_id']);
					$lHasPanelReviews = ((int)count($lReviewedPanelPublicData) ? 1 : 0);
					
					$lRoundReviewersDataCheck = $this->m_documentData['can_proceed'];
					
					$lCustomCheck = FALSE;
					if(
						((
							(count($lAssignedReviewersData) || count($lInvitedPanelData) || count($lReviewedPanelPublicData)) || 
							$this->m_documentData['can_proceed'] == 'false')
						)
					) {
						$lCustomCheck = TRUE;	
					} else {
						if(
							$this->m_documentData['can_proceed'] == 'true' && 
							$this->m_documentData['document_review_type_id'] != DOCUMENT_NON_PEER_REVIEW &&
							$lDocumentRoundNumber == REVIEW_ROUND_TWO
						) {
							$lCustomCheck = TRUE;
						}
					}
					 
					
					if($lDocumentRoundNumber < MAX_REVIEW_ROUNDS && 
					$this->m_documentData['document_review_type_id'] != DOCUMENT_NON_PEER_REVIEW &&
					$lCustomCheck
					
					//((!count($lAssignedReviewersData) && !count($lInvitedPanelData) && !count($lReviewedPanelPublicData)) && ($this->m_documentData['can_proceed'] == 'true' && $lDocumentRoundNumber == REVIEW_ROUND_TWO))
					) {
						if(count($lAssignedReviewersData) || count($lInvitedPanelData) || count($lReviewedPanelPublicData)) {
							$lAssignedDedicatedReviewers = new evList_Display(array(
								'controller_data' => $lAssignedReviewersData,
								'name_in_viewobject' => 'dedicated_reviewer_assigned_list',
								'document_id' => $this->m_documentId,
								'userid' => $this->GetUserId(),
								'showactions' => true,
								'round_number' => $this->m_documentData['round_number'],
								'round_type' => $this->m_documentData['round_type'],
								'state_id' => $this->m_documentData['state_id'],
								'version_num' => $this->m_documentData['version_num'],
								'author_version_id' => $this->m_documentData['author_version_id'],
								'round_name' => $this->m_documentData['round_name'], 
								'can_proceed' => $this->m_documentData['can_proceed'],
								'check_invited_users' => $this->m_documentData['check_invited_users'],
								'merge_flag' => $this->m_documentData['merge_flag'],
								'reviewers_lock_flag' => $lCanInviteUsers,
							));
							
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
									'has_panel_reviews' => (int)$lHasPanelReviews,
								));
								if(!count($lAssignedReviewersData)) {
									$lNodataType = 1;
								}
							} else {
								if(count($lAssignedReviewersData)) {
									$lNodataType = 2;
								}
							}
							
							$lAssignedReviewersObj = new evSimple_Block_Display(array(
								'controller_data' => $this->m_documentData,
								'name_in_viewobject' => 'assigned_invited_reviewers',
								'dedicated_reviewers' => $lAssignedDedicatedReviewers,
								'panel_public_reviewers' => $lInvitedReviewedPublicPanelReviewersObj,
								'userid' => $this->GetUserId(),
								'showactions' => true,
								'no_data_type' => (int)$lNodataType,
							));
							
							$lViewDocNameObj = 'document_se_decision';
							
						}
						
						if(!count($lAssignedReviewersData) && !count($lInvitedPanelData) && !count($lReviewedPanelPublicData)) {
							$lSubmissionActionsObject = new evSimple_Block_Display(array(
								'controller_data' => $this->m_documentData,
								'name_in_viewobject' => 'submission_actions',
								'userid' => $this->GetUserId(),
								'document_id' => $this->m_documentId,
								'reviewers_check' => $lRoundReviewersDataCheck,
								'round_due_date' => $lDocumentRoundDueDate,
								'view_role' => $this->m_viewingRole,
								'round_number' => $this->m_documentData['round_number'],
								'check_invited_users' => $this->m_documentData['check_invited_users'],
								'merge_flag' => $this->m_documentData['merge_flag'],
								'round_user_id' => $lSEDocumentRoundUserId,
								'can_invite_reviewers' => $lCanInviteUsers,
							));
							$lViewDocNameObj = 'document_se_decision_no_decision';
						}
					} else {
						$lSubmissionActionsObject = new evSimple_Block_Display(array(
							'controller_data' => $this->m_documentData,
							'name_in_viewobject' => 'submission_actions_non_peer_review',
							'userid' => $this->GetUserId(),
						));
						$lViewDocNameObj = 'document_se_only_decision';
					}
					$lDocumentRoundData = $lDocumentModel->GetSEDocumentCurrentRoundNumberAndUserId($this->m_documentId, $this->GetUserId());
					$lSEDocumentRoundUserId = $lDocumentRoundData['id'];
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => $lViewDocNameObj,
						'submission_actions' => $lSubmissionActionsObject,
						'assigned_reviewers' => $lAssignedReviewersObj,
						'reviewers_check' => $lRoundReviewersDataCheck,
						'round_due_date' => $lDocumentRoundDueDate,
						'round_number' => (int)$lDocumentRoundNumber,
						'userid' => $this->GetUserId(),
						'document_id' => $this->m_documentId,
						'tabs' => $this->m_tabsObject,
						'document_info' => $this->m_documentInfoObject,
						'has_tabs' => $lHasTabs,
						'round_number' => $this->m_documentData['round_number'],
						'round_type' => $this->m_documentData['round_type'],
						'state_id' => $this->m_documentData['state_id'],
						'version_num' => $this->m_documentData['version_num'],
						'round_name' => $this->m_documentData['round_name'],
						'view_role' => $this->m_viewingRole,
						'check_invited_users' => $this->m_documentData['check_invited_users'],
						'merge_flag' => $this->m_documentData['merge_flag'],
						'round_user_id' => $lSEDocumentRoundUserId,
						'reviewers_lock_flag' => $lCanInviteUsers,
					));
				}
				break;
				case (int) DOCUMENT_READY_FOR_COPY_REVIEW_STATE :
				case (int) DOCUMENT_IN_COPY_REVIEW_STATE :
				case (int) DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE :
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_in_copy_editing',
						'document_id' => $this->m_documentId,
						'tabs' => $this->m_tabsObject,
						'document_info' => $this->m_documentInfoObject,
						'has_tabs' => $lHasTabs,
					));
				break;
				case (int) DOCUMENT_READY_FOR_LAYOUT_STATE :
				case (int) DOCUMENT_IN_LAYOUT_EDITING_STATE :
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_in_layout_editing',
						'document_id' => $this->m_documentId,
						'tabs' => $this->m_tabsObject,
						'document_info' => $this->m_documentInfoObject,
						'has_tabs' => $lHasTabs,
					));
				break;
				case DOCUMENT_REJECTED_STATE:
				case DOCUMENT_REJECTED_BUT_RESUBMISSION:
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_rejected',
						'document_id' => $this->m_documentId,
						'tabs' => $this->m_tabsObject,
						'document_info' => $this->m_documentInfoObject,
						'has_tabs' => $lHasTabs,
					));
				break;
		}

		$lSubmissionNotesObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'submission_notes'
		));

		$this->m_contentObject->SetVal('submission_notes', $lSubmissionNotesObject);

	}

	function GetHistory(){
		$lDocumentModel = new mDocuments_Model();
		$lHasPanelReviews = 0;
		
		$lReviewRoundsData = $this->m_HistoryData;
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
			$lInvitedReviewedPublicPanelReviewersObj = '';
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
				$lHasPanelReviews = ((int)count($lReviewedPanelPublicData) ? 1 : 0);
				$lInvitedReviewedPublicPanelReviewersObj = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'public_panel_reviewers_holder_view',
					'invited_reviewers' => $lInvitedReviewers,
					'reviewed_reviewers' => $lReviewersReviewedDocObj,
					'has_panel_reviews' => (int)$lHasPanelReviews,
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
				'panel_public_reviewers' => $lInvitedReviewedPublicPanelReviewersObj,
				'round_number' => $value['round_number'],
				'version_num' => $value['version_num'],
				'se_version_id' => $value['se_version_id'],
				'author_version_id' => $value['author_version_id'],
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
		
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'history_section',
			'document_id' => $this->m_documentId,
			'tabs' => $this->m_tabsObject,
			'document_info' => $this->m_documentInfoObject,
			'has_tabs' => 1,
			'review_round_' . REVIEW_ROUND_ONE => $lReviewRoundOneObj,
			'review_round_' . REVIEW_ROUND_TWO => $lReviewRoundTwoObj,
			'review_round_' . REVIEW_ROUND_THREE => $lReviewRoundThreeObj,
			'review_round_ce' => $lReviewRoundCEObj,
			'review_round_le' => $lReviewRoundLEObj,
			'has_round2' => $lHasRound2,
			'has_round3' => $lHasRound3,
			'has_se' => $lHasSE,
		));
		
	}

	function Display(){

		return $this->m_pageView->Display();

	}

}

?>