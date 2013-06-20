<?php

class pView_Document_Author extends pView_Document {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->InitObjects();
	}

	/**
	 * This function inits the templates for the objects used when viewing
	 * the document as author
	 */
	function InitObjects() {
		// Author role
		$this->m_objectsMetadata['document_waiting_se'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_waiting_se'
			)
		);

		$this->m_objectsMetadata['document_in_copy_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_in_copy_review'
			)
		);
		
		$this->m_objectsMetadata['document_in_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_in_review'
			)
		);

		$this->m_objectsMetadata['document_approved_for_publish'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_approved_for_publish'
			)
		);

		$this->m_objectsMetadata['document_submit_review_version'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_submit_review_version'
			)
		);

		$this->m_objectsMetadata['document_waiting_to_proceed_to_layout'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_waiting_to_proceed_to_layout'
			)
		);
		
		$this->m_objectsMetadata['document_waiting_to_proceed_to_copyedit'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_waiting_to_proceed_to_copyedit'
			)
		);

		$this->m_objectsMetadata['document_in_layout'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_in_layout'
			)
		);

		$this->m_objectsMetadata['document_submit_layout_version'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_submit_layout_version'
			)
		);

		$this->m_objectsMetadata['document'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document'
			)
		);

		$this->m_objectsMetadata['document_rejected'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.document_rejected'
			)
		);

		// SE List for assignment by journal editor
		$this->m_objectsMetadata['se_assigned_list'] = array(
			'templs' => array(
				G_STARTRS => 'view_document_editor.seAssignedListStart',
				G_ENDRS => 'view_document_editor.seAssignedListEnd',
				G_ROWTEMPL => 'view_document_author.seAssignedListRow',
			)
		);
		
		$this->m_objectsMetadata['se_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.se_decision'
			)
		);
		
		$this->m_objectsMetadata['ce_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.ce_decision'
			)
		);
		
		$this->m_objectsMetadata['history_section'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.history_section',
			)
		);
		
		$this->m_objectsMetadata['assigned_invited_reviewers_veiw'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.AssignedInvitedReviewersHolderView'
			)
		);

	}
}

?>