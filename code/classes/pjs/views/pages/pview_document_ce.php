<?php

class pView_Document_CE extends pView_Document {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->InitObjects();
	}


	function InitObjects() {
		$this->m_objectsMetadata['document_not_in_copy_editing'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_ce.document_not_in_copy_editing'
			)
		);

		$this->m_objectsMetadata['document_approved_for_publish'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_ce.document_approved_for_publish'
			)
		);

		$this->m_objectsMetadata['document_in_review'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_ce.document_in_review'
			)
		);

		$this->m_objectsMetadata['editor_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'global.document_tabs',
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