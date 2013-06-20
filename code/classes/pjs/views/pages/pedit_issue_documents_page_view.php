<?php

/**
 * The view class for the edit journals issue documents page
 */
class pEdit_Issue_Documents_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);
		
		$this->m_objectsMetadata['issue_documents_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'journalissue.edit_document_head',
				G_STARTRS => 'journalissue.edit_document_startrs',
				G_ROWTEMPL => 'journalissue.edit_document_row',
				G_ENDRS => 'journalissue.edit_document_endrs',
				G_FOOTER => 'journalissue.edit_document_foot',
			)
		);
	}
}

?>