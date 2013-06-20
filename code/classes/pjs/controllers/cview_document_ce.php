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
		
		switch ((int) $this->m_documentState) {
			default :
				$this->m_contentObject = new evSimple_Block_Display(array(
				'controller_data' => $this->m_documentData,
				'name_in_viewobject' => 'document_not_in_copy_editing',
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
			case (int) DOCUMENT_IN_COPY_REVIEW_STATE :
				$this->m_contentObject = new evSimple_Block_Display(array(
				'controller_data' => $this->m_documentData,
				'name_in_viewobject' => 'document_in_review',
				'round_user_id' => $lRoundUserData['round_user_id'],
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