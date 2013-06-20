<?php

class pView_Document_Dedicated_Reviewer extends pView_Document {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->InitObjects();
	}


	/**
	 * This function inits the templates for the objects used when viewing
	 * the document as Dedicated Reviewer
	 */
	function InitObjects() {
		$this->m_objectsMetadata['assigned_reviewer_veiw'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.assigned_reviewer_veiw'
			)
		);
		
		$this->m_objectsMetadata['review_round_has_passed'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.review_round_has_passed'
			)
		);
		
		$this->m_objectsMetadata['review_round_has_removed'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.review_round_has_removed'
			)
		);
		

		$this->m_objectsMetadata['document_approved_for_publish'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.document_approved_for_publish'
			)
		);

		$this->m_objectsMetadata['new_invitation'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.new_invitation'
			)
		);

		$this->m_objectsMetadata['canceled_invitation'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.canceled_invitation'
			)
		);

		$this->m_objectsMetadata['confirmed_invitation'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.confirmed_invitation'
			)
		);

		$this->m_objectsMetadata['confirmed_invitation_decision_taken'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.confirmed_invitation_decision_taken'
			)
		);
		
		$this->m_objectsMetadata['editor_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'global.document_tabs',
			)
		);
		
		$this->m_objectsMetadata['document_rejected'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.document_rejected'
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
		
	}
}

?>