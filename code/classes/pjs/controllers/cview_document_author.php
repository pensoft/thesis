<?php

class cView_Document_Author extends cView_Document {
	function InitObjects() {
		$this->m_viewingRole = AUTHOR_ROLE;
		
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
			case GET_SUBMITTED_FILES_SECTION:
				$this->GetSubmittedFiles();
				break;
			default:
				$this->GetCurState();
				break;
		}

		$pViewPageObjectsDataArray['contents'] = $this->m_contentObject;
		$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.editorial');
		$this->AddJournalObjects($this->m_journalId);
		$this->m_pageView = new pView_Document_Author(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		$this->m_allowedViewingModes = $this->GetUserJournalDashboardAllowedViewingModes($this->m_journalId);
		$this->InitLeftcolObjects();
	}

	function GetCurState() {
		switch ($this->m_documentData['state_id']) {
			case DOCUMENT_INCOMPLETE_STATE:
				header ("location: " . SITE_URL . "document_bdj_submission?document_id=" . $this->m_documentData['document_id']);
				exit();
				break;
			case (int) DOCUMENT_WAITING_SE_ASSIGNMENT_STATE :
				$lViewObjectName = 'document_waiting_se';
				break;
			case (int) DOCUMENT_APPROVED_FOR_PUBLISH :
				$lViewObjectName = 'document_approved_for_publish';
				break;
			case (int) DOCUMENT_READY_FOR_COPY_REVIEW_STATE :
			case (int) DOCUMENT_IN_COPY_REVIEW_STATE :
				$lViewObjectName = 'document_in_copy_review';
				break;
			case (int) DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_REVIEW_STATE :
				$lViewObjectName = 'document_submit_review_version';
				break;
			case (int) DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE :
			case (int) DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_AFTER_COPY_EDITING_STATE:
				$lViewObjectName = 'document_waiting_to_proceed_to_layout';
				break;
			case (int) DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE:
				$lViewObjectName = 'document_waiting_to_proceed_to_copyedit';
				break;
			case (int) DOCUMENT_READY_FOR_LAYOUT_STATE :
			case (int) DOCUMENT_IN_LAYOUT_EDITING_STATE :
				$lViewObjectName = 'document_in_layout';
				break;
			case (int) DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_LAYOUT_STATE :
				$lViewObjectName = 'document_submit_layout_version';
				break;
			case (int) DOCUMENT_IN_REVIEW_STATE :
				$lViewObjectName = 'document_in_review';
				break;
			case DOCUMENT_REJECTED_STATE :
			case DOCUMENT_REJECTED_BUT_RESUBMISSION :
				$lViewObjectName = 'document_rejected';
			break;			
			default :
				$lViewObjectName = 'document';
		}

		// assigned SE
		$lDocumentModel = new mDocuments_Model();
		
		$lCEDecisionData = $lDocumentModel->GetCEDataList($this->m_documentId);
		$lCERoundsCount = count($lCEDecisionData);
		
		// SE Decision
		if(in_array($this->m_documentData['state_id'], array(
			DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE, 
			DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE, 
			DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_REVIEW_STATE
			))
			&& !(int)$lCERoundsCount
		) {
			
			$lSEDecisionData = $lDocumentModel->GetSEDecision($this->m_documentId);
			$lSEDecisionObj = new evSimple_Block_Display(array(
				'controller_data' => $lSEDecisionData,
				'name_in_viewobject' => 'se_decision',
				'document_id' => $this->m_documentId,
				'journal_name' => $this->m_documentData['journal_name'],
			));
		}

		if(in_array($this->m_documentData['state_id'], array(
			DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE, 
			DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_LAYOUT_EDITING_STATE, 
			))
		) {
			if(!(int)$lCERoundsCount) {
				$lLastRoundDecisionData = $lDocumentModel->GetLastReviewRoundDecisionData($this->m_documentId);	
			} else {
				$lViewObjectName = 'document_waiting_to_proceed_to_layout';	
			}
		}
		
		if((int)$this->m_documentData['copy_editor_version_id']){
			$lCEObj = new evSimple_Block_Display(array(
				'controller_data' => $this->m_documentData,
				'name_in_viewobject' => 'ce_decision',
				'document_id' => $this->m_documentId,
				'journal_name' => $this->m_documentData['journal_name'],
			));
		}
//var_dump($lViewObjectName);
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => $lViewObjectName,
			'document_id' => $this->m_documentId,
			'tabs' => $this->m_tabsObject,
			'document_info' => $this->m_documentInfoObject,
			'has_tabs' => 1,
			'se_decision' => $lSEDecisionObj,
			'round_number_accept' => $lLastRoundDecisionData['round_number'],
			'ce_obj' => $lCEObj,
			'ce_rounds_count' => $lCERoundsCount,
		));
	}

	function GetHistory(){

		$lDocumentModel = new mDocuments_Model();

		$lReviewRoundsData = $this->m_HistoryData;
		$lReviewRoundOneObj = '';
		$lReviewRoundTwoObj = '';
		$lReviewRoundThreeObj = '';
		$lHasRound1 = 0;
		$lHasRound2 = 0;
		$lHasRound3 = 0;
		foreach ($lReviewRoundsData as $key => $value) {
			
			$roundObj = new evSimple_Block_Display(array(
				'controller_data' => '',
				'name_in_viewobject' => 'assigned_invited_reviewers_veiw',
				'round_number' => $value['round_number'],
				'version_num' => $value['version_num'],
				'round_name' => $value['round_name'],
				'se_version_id' => $value['se_version_id'],
				'author_version_id' => $value['author_version_id'],
				'decision_round_name' => $value['decision_name'],
				'hide_reviewers_text' => 1,
				'document_id' => $this->m_documentId,
				'journal_name' => $this->m_documentData['journal_name'],
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
		
		/* CE rounds object START*/
		$lCEDecisionData = $lDocumentModel->GetCEDataList($this->m_documentId);
		$lCERoundsCount = (int) count($lCEDecisionData);
		$lHasCE = 0;
		
		// we have to remove the last CE round when document state is DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE (it's in the Current status tab)
		if($this->m_documentData['state_id'] == DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE) {
			if($lCERoundsCount){
				unset($lCEDecisionData[$lCERoundsCount - 1]);	
			}
		}
		$lCERoundsCount = (int) count($lCEDecisionData);
		//var_dump($lCEDecisionData);
		if($lCERoundsCount) {
			$lHasCE = 1;
			$lCEListObj = new evList_Display(array(
				'controller_data' => $lCEDecisionData, 
				'name_in_viewobject' => 'document_ce_rounds',
				'document_id' => $this->m_documentId,
				'journal_name' => $this->m_documentData['journal_name'],
			)); 
		}
		/* CE rounds object END*/
		
		/* Holder object */
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
			'review_round_ce' => $lCEListObj,
			'review_round_le' => $lReviewRoundLEObj,
			'has_round2' => $lHasRound2,
			'has_round3' => $lHasRound3,
			'has_se' => $lHasSE,
			'has_ce' => $lHasCE,
			'journal_name' => $this->m_documentData['journal_name'],
		));
		
	}

	function Display() {
		return $this->m_pageView->Display();
	}

}

?>