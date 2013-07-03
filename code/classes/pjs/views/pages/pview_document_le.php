<?php

class pView_Document_LE extends pView_Document {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->InitObjects();
	}

	function InitObjects() {
		$this->m_objectsMetadata['document_not_in_layout_editing'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_le.document_not_in_layout_editing'
			)
		);

		$this->m_objectsMetadata['document_approved_for_publish'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_le.document_approved_for_publish'
			)
		);

		$this->m_objectsMetadata['document_in_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_le.document_in_review'
			)
		);

		$this->m_objectsMetadata['document_waiting_for_author_after_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_le.document_waiting_for_author_after_review'
			)
		);
		
		$this->m_objectsMetadata['editor_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'global.document_tabs',
			)
		);
		
		$this->m_objectsMetadata['submission_notes'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_le.document_le_notes',
			)
		);
		
		$this->m_objectsMetadata['document_se_decision'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_se_decision'
			)
		);
		
		$this->m_objectsMetadata['document_view_source'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.document_view_source'
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

		// $this->m_objectsMetadata['le_form'] = array(
			// 'templs' => array(
				// G_FORM_TEMPLATE => 'view_document_le.form',
			// )
		// );
	}
}

?>