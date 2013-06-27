<?php

class cView_Document_CE extends cView_Document {
	function InitObjects() {
		$this->m_viewingRole = CE_ROLE;

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
		$this->m_pageView = new pView_Document_CE(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		
		$this->m_allowedViewingModes = $this->GetUserJournalDashboardAllowedViewingModes($this->m_journalId);
		$this->InitLeftcolObjects();
		
	}

	function GetCurState() {
		$this->InitUserModel();
		$lDocumentModel = new mDocuments_Model();
		$lUserModel = $this->m_models['user_model'];
		$lUserModel = new mUser_Model();
		$lRoundUserData = $lUserModel->GetDocumentCurrentRoundCEUserData($this->m_documentId, $this->GetUserId());
		
		
		//var_dump(count($lRoundUserData));
		if(!is_array($lRoundUserData) || !count($lRoundUserData)) {
			$lCEDecisionData = $lDocumentModel->GetCEDataList($this->m_documentId, $this->GetUserId());
			$lCERoundsCount = (int)count($lCEDecisionData);
			$lCEDecisionData = 	$lCEDecisionData[$lCERoundsCount - 1];
		}
		
		switch ((int) $this->m_documentState) {
			default :
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => '',
					'name_in_viewobject' => 'document_not_in_copy_editing',
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => 1,
					'ce_rounds_count' => $lCERoundsCount,
					'version_num' => $lCEDecisionData['version_num'],
					'round_number' => $lCERoundsCount,
					'author_version_id' => $lCEDecisionData['author_round_version_id'],
					'user_version_id' => $lCEDecisionData['copy_editor_version_id'],
				));
				break;
			case (int) DOCUMENT_IN_COPY_REVIEW_STATE :
				if(count($lRoundUserData) && is_array($lRoundUserData)){
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => $this->m_documentData,
						'name_in_viewobject' => 'document_in_review',
						'round_user_id' => $lRoundUserData['round_user_id'],
						'document_id' => $this->m_documentId,
						'tabs' => $this->m_tabsObject,
						'document_info' => $this->m_documentInfoObject,
						'has_tabs' => 1,
					));	
				} else {
					$this->m_contentObject = new evSimple_Block_Display(array(
						'controller_data' => '',
						'name_in_viewobject' => 'document_not_in_copy_editing',
						'document_id' => $this->m_documentId,
						'tabs' => $this->m_tabsObject,
						'document_info' => $this->m_documentInfoObject,
						'has_tabs' => 1,
						'ce_rounds_count' => $lCERoundsCount,
						'version_num' => $lCEDecisionData['version_num'],
						'round_number' => $lCERoundsCount,
						'author_version_id' => $lCEDecisionData['author_round_version_id'],
						'user_version_id' => $lCEDecisionData['copy_editor_version_id'],
					));
				}
				break;
		}

	}

	function GetHistory(){

		$lDocumentModel = new mDocuments_Model();
		
		/* CE rounds object START*/
		$lCEDecisionData = $this->m_HistoryData;
		$lCERoundsCount = (int) count($lCEDecisionData);
		$lHasCE = 0;
		
		// we have to remove the last CE round when document state is DOCUMENT_WAITING_AUTHOR_TO_PROCEED_TO_COPY_EDITING_STATE (it's in the Current status tab)
		unset($lCEDecisionData[$lCERoundsCount - 1]);	
		$lCERoundsCount = (int) count($lCEDecisionData);
		if($lCERoundsCount) {
			$lHasCE = 1;
			$lCEListObj = new evList_Display(array(
				'controller_data' => $lCEDecisionData, 
				'name_in_viewobject' => 'document_ce_rounds',
				'document_id' => $this->m_documentId,
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
			'review_round_ce' => $lCEListObj,
		));
		
	}

	function Display(){

		return $this->m_pageView->Display();

	}

}

?>