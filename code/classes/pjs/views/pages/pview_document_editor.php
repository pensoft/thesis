<?php

class pView_Document_Editor extends pView_Document {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->InitObjects();
	}

	/**
	 * This function inits the templates for the objects used when viewing
	 * the document as Journal Editor
	 */
	function InitObjects() {
		// Journal Editor role
		$this->m_objectsMetadata['document_waiting_se'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_se'
			)
		);
		
		$this->m_objectsMetadata['document_waiting_author_decision_layout'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_author_decision_layout'
			)
		);
		
		$this->m_objectsMetadata['document_waiting_author_decision_copyedit'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_author_decision_copyedit'
			)
		);
		
		$this->m_objectsMetadata['document_rejected'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_rejected'
			)
		);
		
		$this->m_objectsMetadata['document_se_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_se_decision'
			)
		);
		$this->m_objectsMetadata['scheduling_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'view_document_editor.scheduling_form'
			)
		);
		
		$this->m_objectsMetadata['document'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document'
			)
		);

		$this->m_objectsMetadata['document_approved_for_publish'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_approved_for_publish'
			)
		);

		$this->m_objectsMetadata['document_editor_assigned_se'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_editor_assigned_se'
			)
		);

		// SE List for assignment by journal editor
		$this->m_objectsMetadata['se_assigned_list'] = array(
			'templs' => array(
				G_STARTRS => 'view_document_editor.seAssignedListStart',
				G_ENDRS => 'view_document_editor.seAssignedListEnd',
				G_ROWTEMPL => 'view_document_editor.seAssignedListRow',
			)
		);
		
		$this->m_objectsMetadata['assigned_reviewers_list'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.assigned_reviewers_list'
			)
		);
		

		// SE List assigned by journal editor
		$this->m_objectsMetadata['se_available_list'] = array(
			'templs' => array(
				G_HEADER   => 'view_document_editor.seAvailableListHeader',
				G_FOOTER   => 'view_document_editor.seAvailableListFooter',
				G_STARTRS  => 'view_document_editor.seAvailableListStart',
				G_ENDRS    => 'view_document_editor.seAvailableListEnd',
				G_ROWTEMPL => 'view_document_editor.seAvailableListRow',
				G_NODATA   => 'view_document_editor.seAvailableListNodata',
			)
		);

		$this->m_objectsMetadata['document_in_layout'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_in_layout'
			)
		);

		// LE List for assignment by journal editor
		$this->m_objectsMetadata['le_assigned_list'] = array(
			'templs' => array(
				G_STARTRS => 'view_document_editor.leAssignedListStart',
				G_ROWTEMPL => 'view_document_editor.leAssignedListRow'
			)
		);

		// LE List assigned by journal editor
		$this->m_objectsMetadata['le_available_list'] = array(
			'templs' => array(
				G_STARTRS => 'view_document_editor.leAvailableListStart',
				G_ROWTEMPL => 'view_document_editor.leAvailableListRow'
			)
		);

		$this->m_objectsMetadata['document_in_copy_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_in_copy_review'
			)
		);

		// CE List for assignment by journal editor
		$this->m_objectsMetadata['ce_assigned_list'] = array(
			'templs' => array(
				G_STARTRS => 'view_document_editor.ceAssignedListStart',
				G_ENDRS => 'view_document_editor.ceAssignedListEnd',
				G_ROWTEMPL => 'view_document_editor.ceAssignedListRow',
				
			)
		);

		$this->m_objectsMetadata['document_in_ce_state'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_in_ce_state'
			)
		);

		// CE List for assignment by journal editor
		$this->m_objectsMetadata['le_assigned_list'] = array(
			'templs' => array(
				G_STARTRS => 'view_document_editor.leAssignedListStart',
				G_ENDRS => 'view_document_editor.leAssignedListEnd',
				G_ROWTEMPL => 'view_document_editor.leAssignedListRow',
				
			)
		);

		$this->m_objectsMetadata['document_in_le_state'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_in_le_state'
			)
		);
		
		// CE List assigned by journal editor
		$this->m_objectsMetadata['ce_available_list'] = array(
			'templs' => array(
				G_STARTRS => 'view_document_editor.ceAvailableListStart',
				G_ROWTEMPL => 'view_document_editor.ceAvailableListRow'
			)
		);
		
		$this->m_objectsMetadata['editor_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'global.document_tabs',
			)
		);
		
		$this->m_objectsMetadata['submission_notes'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_ese_notes',
			)
		);
		
		$this->m_objectsMetadata['submission_actions'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_e_actions',
			)
		);
	
		// Dedicated reviewer List who may be invited by the SE
		$this->m_objectsMetadata['dedicated_reviewer_assigned_list'] = array(
			'templs' => array(
				G_HEADER => 'view_document_editor.dedicatedReviewerAssignedListHeader',
				G_FOOTER => 'view_document_editor.dedicatedReviewerAssignedListFooter',
				G_STARTRS => 'view_document_editor.dedicatedReviewerAssignedListStart',
				G_ROWTEMPL => 'view_document_editor.dedicatedReviewerAssignedListRow',
				G_ENDRS => 'view_document_editor.dedicatedReviewerAssignedListEnd',
			)
		);
		
		$this->m_objectsMetadata['document_waiting_ce_assign'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_ce_assign',
			)
		);
		
		$this->m_objectsMetadata['document_waiting_le_assign'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_le_assign',
			)
		);
		
		// Dedicated reviewer List who may be invited by the SE
		$this->m_objectsMetadata['ce_allavailable_list'] = array(
			'templs' => array(
				G_HEADER => 'view_document_editor.allceavailableListHeader',
				G_FOOTER => 'view_document_editor.allceavailableListFooter',
				G_STARTRS => 'view_document_editor.allceavailableListStart',
				G_ROWTEMPL => 'view_document_editor.allceavailableListRow',
				G_ENDRS => 'view_document_editor.allceavailableListEnd',
			)
		);
		
		$this->m_objectsMetadata['document_ce_list'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_ce_assign_list',
			)
		);
		
		// Dedicated reviewer List who may be invited by the SE
		$this->m_objectsMetadata['le_allavailable_list'] = array(
			'templs' => array(
				G_HEADER => 'view_document_editor.allleavailableListHeader',
				G_FOOTER => 'view_document_editor.allleavailableListFooter',
				G_STARTRS => 'view_document_editor.allleavailableListStart',
				G_ROWTEMPL => 'view_document_editor.allleavailableListRow',
				G_ENDRS => 'view_document_editor.allleavailableListEnd',
			)
		);
		
		$this->m_objectsMetadata['document_le_list'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_le_assign_list',
			)
		);
		
		$this->m_objectsMetadata['document_in_ce_state'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_in_ce_state',
			)
		);
		
		$this->m_objectsMetadata['assigned_reviewers_list_se_can_take_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.assigned_reviewers_list_se_can_take_decision',
			)
		);
		
		$this->m_objectsMetadata['assigned_reviewers_list_waiting_reviewers'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.assigned_reviewers_list_waiting_reviewers',
			)
		);

		// Dedicated reviewer List
		$this->m_objectsMetadata['view_review_rounds'] = array(
			'templs' => array(
				G_HEADER => 'view_document_editor.dedicatedReviewerAssignedOldListHeader',
				G_FOOTER => 'view_document_editor.dedicatedReviewerAssignedOldListFooter',
				G_STARTRS => 'view_document_editor.dedicatedReviewerAssignedOldListStart',
				G_ROWTEMPL => 'view_document_editor.dedicatedReviewerAssignedOldListRow',
				G_ENDRS => 'view_document_editor.dedicatedReviewerAssignedOldListEnd',
			)
		);
		
		// CE rounds
		$this->m_objectsMetadata['document_ce_rounds'] = array(
			'templs' => array(
				G_HEADER => 'global.empty',
				G_FOOTER => 'global.empty',
				G_STARTRS => 'global.empty',
				G_ROWTEMPL => 'view_document_editor.document_ce_round_row',
				G_ENDRS => 'global.empty',
			)
		);
		
		$this->m_objectsMetadata['assigned_reviewers_list_se_can_take_decision_closed_peer'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.assigned_reviewers_list_se_can_take_decision_closed_peer',
			)
		);
		
		$this->m_objectsMetadata['document_waiting_le_obj'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_le_obj',
			)
		);
		
		$this->m_objectsMetadata['document_waiting_ce_obj'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_ce_obj',
			)
		);
		
				$this->m_objectsMetadata['document_editor_waiting_author_version_after_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_editor_waiting_author_version_after_review',
			)
		);
		
		$this->m_objectsMetadata['document_editor_waiting_author_version_after_review_object'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_editor_waiting_author_version_after_review_object',
			)
		);
		
		$this->m_objectsMetadata['document_editor_waiting_author_version_after_review_rounds_object'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_editor_waiting_author_version_after_review_rounds_object',
			)
		);
		
		$this->m_objectsMetadata['document_waiting_le_assign_obj'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_le_assign_obj',
			)
		);
		
		$this->m_objectsMetadata['document_waiting_ce_assign_obj'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_ce_assign_obj',
			)
		);
		
		$this->m_objectsMetadata['document_waiting_ce_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_ce_decision',
			)
		);
		
		$this->m_objectsMetadata['document_waiting_le_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_waiting_le_decision',
			)
		);
		
		$this->m_objectsMetadata['invited_reviewers_obj_list'] = array(
			'templs' => array(
				G_ROWTEMPL => 'view_document_se.invited_reviewers_obj_list_row',
			)
		);
		
		$this->m_objectsMetadata['public_panel_reviewers_holder'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.public_panel_reviewers_holder'
			)
		);
		
		$this->m_objectsMetadata['assigned_invited_reviewers'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.AssignedInvitedReviewersHolder'
			)
		);
		
		$this->m_objectsMetadata['assigned_invited_reviewers_veiw'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.AssignedInvitedReviewersHolderView'
			)
		);
		
		$this->m_objectsMetadata['reviewers_reviewed_document'] = array(
			'templs' => array(
				G_ROWTEMPL => 'view_document_se.invited_reviewers_obj_list_row',
			)
		);
		
	}
}

?>