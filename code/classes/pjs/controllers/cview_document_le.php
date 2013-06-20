<?php

class cView_Document_LE extends cView_Document {
	function InitObjects() {
		$this->m_viewingRole = LE_ROLE;
		
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
			case GET_VIEW_SOURCE_SECTION:
				$this->GetViewSourceSection();
				break;
			default:
				$this->GetCurState();
				break;
		}
		
		$pViewPageObjectsDataArray['contents'] = $this->m_contentObject;
		$pViewPageObjectsDataArray['title.suffix'] = getstr('pjs.editorial');
		$this->AddJournalObjects($this->m_journalId);
		$this->m_pageView = new pView_Document_LE(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
		
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
			'has_tabs' => 1,
		));
	}
	
	function GetScheduling() {
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'document_se_decision',
			'document_id' => $this->m_documentId,
			'document_info' => $this->m_documentInfoObject,
			'tabs' => $this->m_tabsObject,
			'has_tabs' => 1,
		));
	}
	
	function GetViewSourceSection() {
		$lDocumentModel = new mDocuments_Model();
		$lCurrentVersionDocumentXML = $lDocumentModel->GetDocumenXmlByVersion($this->m_documentId);
		
		$this->m_contentObject = new evSimple_Block_Display(array(
			'controller_data' => $this->m_documentData,
			'name_in_viewobject' => 'document_view_source',
			'document_id' => $this->m_documentId,
			'document_info' => $this->m_documentInfoObject,
			'tabs' => $this->m_tabsObject,
			'has_tabs' => 1,
			'document_current_version_xml' => $lCurrentVersionDocumentXML['document_current_version_xml'],
		));
	}
	
	function GetCurState() {
		$this->InitUserModel();
		$lDocumentModel = new mDocuments_Model();
		$lUserModel = $this->m_models['user_model'];
		$lUserModel = new mUser_Model();
		$lRoundUserData = $lUserModel->GetDocumentCurrentRoundLEUserData($this->m_documentId, $this->GetUserId());

		switch ((int) $this->m_documentState) {
			default :
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'document_not_in_layout_editing',
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => 1,
				));
				break;
			case (int) DOCUMENT_APPROVED_FOR_PUBLISH :
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'document_approved_for_publish',
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => 1,
				));
				break;
			case (int) DOCUMENT_IN_LAYOUT_EDITING_STATE :
				$lSubmissionNotesObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'submission_notes',
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => 1,
				));
				
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'document_in_review',
					'round_user_id' => $lRoundUserData['round_user_id'],
					'le_note' => $lSubmissionNotesObject,
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => 1,
				));
				break;
			case (int) DOCUMENT_WAITING_AUTHOR_VERSION_AFTER_LAYOUT_STATE :
				$this->m_contentObject = new evSimple_Block_Display(array(
					'controller_data' => $this->m_documentData,
					'name_in_viewobject' => 'document_waiting_for_author_after_review',
					'document_id' => $this->m_documentId,
					'tabs' => $this->m_tabsObject,
					'document_info' => $this->m_documentInfoObject,
					'has_tabs' => 1,
				));
				break;
		}
		
	}

	function Display(){

		return $this->m_pageView->Display();

	}

}

?>