<?php

class pView_Document_SE extends pView_Document {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->InitObjects();
	}


	/**
	 * This function inits the templates for the objects used when viewing
	 * the document as SE
	 */
	function InitObjects() {
		// SE role

		$this->m_objectsMetadata['document_in_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_in_review'
			)
		);

		$this->m_objectsMetadata['document_waiting_author_version_after_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_waiting_author_version_after_review'
			)
		);

		$this->m_objectsMetadata['document_passed_review_state_copyediting'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_passed_review_state_copyediting'
			)
		);
		
		$this->m_objectsMetadata['document_passed_review_state_layout'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_passed_review_state_layout'
			)
		);
		
		$this->m_objectsMetadata['document_in_layout_editing'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_in_layout_editing'
			)
		);
		
		
		$this->m_objectsMetadata['document_in_copy_editing'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_in_copy_editing'
			)
		);
		
		$this->m_objectsMetadata['document_in_review_cant_assign_reviewers'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_in_review_cant_assign_reviewers'
			)
		);
		

		$this->m_objectsMetadata['document_approved_for_publish'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_approved_for_publish'
			)
		);
		
		$this->m_objectsMetadata['assigned_invited_reviewers'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.AssignedInvitedReviewersHolder'
			)
		);

		$this->m_objectsMetadata['public_panel_reviewers_holder'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.public_panel_reviewers_holder'
			)
		);
		
		$this->m_objectsMetadata['public_panel_reviewers_holder_view'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.public_panel_reviewers_holder_view'
			)
		);

		$this->m_objectsMetadata['invited_reviewers_obj_list'] = array(
			'templs' => array(
				G_ROWTEMPL => 'view_document_se.invited_reviewers_obj_list_row',
			)
		);
		
		$this->m_objectsMetadata['reviewers_reviewed_document'] = array(
			'templs' => array(
				G_ROWTEMPL => 'view_document_se.invited_reviewers_obj_list_row',
			)
		);

		// Dedicated reviewer List who may be invited by the SE
		$this->m_objectsMetadata['dedicated_reviewer_assigned_list'] = array(
			'templs' => array(
				G_HEADER => 'view_document_se.dedicatedReviewerAssignedListHeader',
				G_FOOTER => 'view_document_se.dedicatedReviewerAssignedListFooter',
				G_STARTRS => 'view_document_se.dedicatedReviewerAssignedListStart',
				G_ROWTEMPL => 'view_document_se.dedicatedReviewerAssignedListRow',
				G_ENDRS => 'view_document_se.dedicatedReviewerAssignedListEnd',
			)
		);

		// Dedicated reviewer List who have been invited by the SE
		$this->m_objectsMetadata['dedicated_reviewer_available_list'] = array(
			'templs' => array(
				G_HEADER => 'view_document_se.dedicatedReviewerAvailableListHeader',
				G_FOOTER => 'view_document_se.dedicatedReviewerAvailableListFooter',
				G_STARTRS => 'view_document_se.dedicatedReviewerAvailableListStart',
				G_ROWTEMPL => 'view_document_se.dedicatedReviewerAvailableListRow',
				G_ENDRS => 'view_document_se.dedicatedReviewerAvailableListEnd',
				G_NODATA => 'view_document_se.noSuggestedReviewersAvailable'
			)
		);

		$this->m_objectsMetadata['decision_form'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.decision_form'
			)
		);
		
		$this->m_objectsMetadata['document_se_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_se_decision'
			)
		);
		
		$this->m_objectsMetadata['document_se_decision_no_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_se_decision_no_decision'
			)
		);
		
		$this->m_objectsMetadata['document_se_only_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_se_only_decision'
			)
		);
		
		$this->m_objectsMetadata['document_cant_invite_reviewers_for_this_round'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_cant_invite_reviewers_for_this_round'
			)
		);
		
		$this->m_objectsMetadata['editor_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'global.document_tabs',
			)
		);
		
		$this->m_objectsMetadata['submission_actions'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_e_actions',
			)
		);

		$this->m_objectsMetadata['document_rejected'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_rejected'
			)
		);
		
		$this->m_objectsMetadata['submission_actions_non_peer_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.submission_actions_non_peer_review'
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
		
		$this->m_objectsMetadata['submission_notes'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_ese_notes'
			)
		);
		
		$this->m_objectsMetadata['history_section'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.history_section',
			)
		);
		
		$this->m_objectsMetadata['assigned_invited_reviewers_veiw'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.AssignedInvitedReviewersHolderView'
			)
		);
		
				// Dedicated reviewer List
		$this->m_objectsMetadata['view_review_rounds'] = array(
			'templs' => array(
				G_HEADER => 'view_document_se.dedicatedReviewerAssignedOldListHeader',
				G_FOOTER => 'view_document_se.dedicatedReviewerAssignedOldListFooter',
				G_STARTRS => 'view_document_se.dedicatedReviewerAssignedOldListStart',
				G_ROWTEMPL => 'view_document_se.dedicatedReviewerAssignedOldListRow',
				G_ENDRS => 'view_document_se.dedicatedReviewerAssignedOldListEnd',
			)
		);
		
	}
}

?>