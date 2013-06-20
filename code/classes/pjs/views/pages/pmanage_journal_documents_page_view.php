<?php

/**
 * The view class for the manage journal issues page
 */
class pManage_Journal_Documents_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['journal_documents_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'browse.journal_documents_head',
				G_STARTRS => 'browse.journal_documents_start',
				G_ROWTEMPL => 'browse.journal_documents_row',
				G_ENDRS => 'browse.journal_documents_endrs',
				G_FOOTER => 'browse.journal_documents_foot',
				G_NODATA => 'browse.journal_documents_empty'
			)
		);
	}
}

?>