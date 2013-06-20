<?php

class cView_Document_Dedicated_Reviewer extends cView_Document {
	var $m_editor_mode;
	function InitObjects() {
		$this->m_viewingRole = DEDICATED_REVIEWER_ROLE;

		$this->GetDocumentInfo();
		$this->GetActiveSection();
		$this->GetSectionTabsObjByRole($this->m_viewingRole);
		$this->GetDocumentInfoObject();
		
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
			default:
				$this->GetCurState();
				break;
		}
		
		$pViewPageObjectsDataArray['contents'] = $this->m_contentObject;
		$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.editorial');
		$this->AddJournalObjects($this->m_journalId);
		$this->m_pageView = new pView_Document_Dedicated_Reviewer(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		
		$this->m_allowedViewingModes = $this->GetUserJournalDashboardAllowedViewingModes($this->m_journalId);
		$this->InitLeftcolObjects();
	}

	function GetCurState() {
		global $user;
		$this->InitUserModel();
		$lDocumentModel = new mDocuments_Model();
		$lUserModel = $this->m_models['user_model'];
		$lInvitationData = $lUserModel->GetDocumentCurrentRoundReviewerInvitationData(
			$this->m_documentId, 
			$this->GetUserId(), 
			array(DEDICATED_REVIEWER_ROLE, COMMUNITY_REVIEWER_ROLE)
		);
		$lReviewrRoundsData = $lDocumentModel->GetReviewerData($this->m_documentId, $this->GetUserId(), 1);
		$lHasTabs = 1;

		switch ($this->m_documentState) {
			default :
				$roundObj = new evSimple_Block_Display(array(
					'controller_data' => $lReviewrRoundsData[0],
					'name_in_viewobject' => 'assigned_reviewer_veiw',
					'document_id' => $this->m_documentId,
					'cnt_rounds' => 2,
				));
				
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'history_section',
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => 1,
					'review_round_' . REVIEW_ROUND_ONE => $roundObj,
				)); 
				
				break;
			case DOCUMENT_IN_REVIEW_STATE :
				if((int)$lInvitationData['review_user_state'] == (int)REVIEWER_REMOVED) {
					$roundObj = new evSimple_Block_Display(array(
						'controller_data' => $lReviewrRoundsData[0],
						'name_in_viewobject' => 'assigned_reviewer_veiw',
						'document_id' => $this->m_documentId,
						'cnt_rounds' => 2,
					));
					
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'history_section',
						'document_id' => $this->m_documentId,
						'tabs' => $this->m_tabsObject,
						'document_info' => $this->m_documentInfoObject,
						'has_tabs' => 1,
						'review_round_' . REVIEW_ROUND_ONE => $roundObj,
					)); 
				} else {
					
					if(! (int) $lInvitationData['id']){
						$roundObj = new evSimple_Block_Display(array(
							'controller_data' => $lReviewrRoundsData[0],
							'name_in_viewobject' => 'assigned_reviewer_veiw',
							'document_id' => $this->m_documentId,
							'cnt_rounds' => 2,
						));
						
						$this->m_contentObject = new evSimple_Block_Display(array(
							'controller_data' => $this->m_documentData,
							'name_in_viewobject' => 'history_section',
							'document_id' => $this->m_documentId,
							'tabs' => $this->m_tabsObject,
							'document_info' => $this->m_documentInfoObject,
							'has_tabs' => 1,
							'review_round_' . REVIEW_ROUND_ONE => $roundObj,
						));
					}else{
						
						/**
						 * cheking if it is a new invitation and the reviewer is panel or if it's a dedicated reviewer and the invitation is confirmed
						 */
						if(($lInvitationData['state_id'] == REVIEWER_INVITATION_NEW_STATE && $lInvitationData['role_id'] == COMMUNITY_REVIEWER_ROLE) || 
						$lInvitationData['state_id'] == REVIEWER_CONFIRMED_STATE || 
						$lInvitationData['state_id'] == REVIEWER_CONFIRMED_BY_SE_STATE) {
							$lReviwerDocumentData = $lDocumentModel->GetReviewerDocumentVersion($this->m_documentId, $user->id, (int)$this->m_documentData['round_number']);
							//var_dump($lReviwerDocumentData);
							if((int) $lInvitationData['decision_id']){
								$this->m_contentObject = new evSimple_Block_Display(array(
									'controller_data' => $this->m_documentData,
									'name_in_viewobject' => 'confirmed_invitation_decision_taken',
									'decision_name' => $lInvitationData['decision_name'],
									'decision_date' => $lInvitationData['decision_date'],
									'document_id' => $this->m_documentId,
									'tabs' => $this->m_tabsObject,
									'document_info' => $this->m_documentInfoObject,
									'has_tabs' => $lHasTabs,
									'round_user_id' => $lInvitationData['round_user_id'],
									'view_role' => $lInvitationData['role_id'],
									'document_version_id' => $lReviwerDocumentData["document_version_id"],
								));
							}else{
								$this->m_contentObject = new evSimple_Block_Display(array(
									'controller_data' => $this->m_documentData,
									'name_in_viewobject' => 'confirmed_invitation',
									'round_user_id' => $lInvitationData['round_user_id'],
									'document_id' => $this->m_documentId,
									'tabs' => $this->m_tabsObject,
									'document_info' => $this->m_documentInfoObject,
									'has_tabs' => $lHasTabs,
									'view_role' => $lInvitationData['role_id'],
									'document_version_id' => $lReviwerDocumentData["document_version_id"],
								));
							}
						} else {
						
							switch ($lInvitationData['state_id']) {
								case (int) REVIEWER_INVITATION_NEW_STATE :
									$this->m_contentObject = new evSimple_Block_Display(array(
										'controller_data' => $this->m_documentData,
										'name_in_viewobject' => 'new_invitation',
										'invitation_id' => $lInvitationData['id'],
										'document_id' => $this->m_documentId,
										'tabs' => $this->m_tabsObject,
										'document_info' => $this->m_documentInfoObject,
										'has_tabs' => $lHasTabs,
									));
									break;
								case (int) REVIEWER_CANCELLED_BY_SE_STATE :
								case (int) REVIEWER_CANCELLED_STATE :
									$roundObj = new evSimple_Block_Display(array(
										'controller_data' => $lReviewrRoundsData[0],
										'name_in_viewobject' => 'assigned_reviewer_veiw',
										'document_id' => $this->m_documentId,
										'cnt_rounds' => 2,
									));
									
									$this->m_contentObject = new evSimple_Block_Display(array(
										'controller_data' => $this->m_documentData,
										'name_in_viewobject' => 'history_section',
										'document_id' => $this->m_documentId,
										'tabs' => $this->m_tabsObject,
										'document_info' => $this->m_documentInfoObject,
										'has_tabs' => 1,
										'review_round_' . REVIEW_ROUND_ONE => $roundObj,
									));
									break;
							}
						}
					}
				}
				break;
		}

	}

	function GetHistory(){
		global $user;
		$lDocumentModel = new mDocuments_Model();
		
		$lReviewrRoundsData = $this->m_HistoryData;
		$lReviewRoundOneObj = '';
		$lReviewRoundTwoObj = '';
		$lCnt = 0;
		foreach ($lReviewrRoundsData as $key => $value) {
			$lCnt++;
			$roundObj = new evSimple_Block_Display(array(
				'controller_data' => $lReviewrRoundsData[$key],
				'name_in_viewobject' => 'assigned_reviewer_veiw',
				'document_id' => $this->m_documentId,
				'cnt_rounds' => $lCnt,
			));
			
			if((int)$value['round_number'] == REVIEW_ROUND_ONE) {
				$lReviewRoundOneObj = $roundObj; 
			} elseif((int)$value['round_number'] == REVIEW_ROUND_TWO) {
				$lReviewRoundTwoObj = $roundObj;	
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
		)); 
		
	}

	function Display(){

		return $this->m_pageView->Display();

	}

}

?>