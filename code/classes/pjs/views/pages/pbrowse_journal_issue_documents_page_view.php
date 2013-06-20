<?php

/**
 * The view class for the browse journal issues page
 */
class pBrowse_Journal_Issue_Documents_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.big_left_col_page'
		);

		$this->m_objectsMetadata['journal_issue_documents_list_templs'] = array(
			'templs' => array(
				G_STARTRS => 'browse.journal_issue_head',
				G_ROWTEMPL => 'browse.journal_issue_row',
				G_ENDRS => 'browse.journal_issue_foot',
				G_NODATA => 'browse.journal_issue_empty'
			)
		);
		
		$this->m_objectsMetadata['leftcol'] = array(
			'templs' => array(
				G_DEFAULT => 'journalissue.sidebar_left_issues',
			)
		);
	}
}

?>