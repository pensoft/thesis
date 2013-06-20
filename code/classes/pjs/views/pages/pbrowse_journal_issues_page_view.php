<?php

/**
 * The view class for the browse journal issues page
 */
class pBrowse_Journal_Issues_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.big_left_col_page'
		);

		$this->m_objectsMetadata['browse_journal_issues_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'browse.journal_issues_head',
				G_STARTRS => 'browse.journal_issues_startrs',
				G_ROWTEMPL => 'browse.journal_issues_row',
				G_ENDRS => 'browse.journal_issues_endrs',
				G_FOOTER => 'browse.journal_issues_foot',
				G_NODATA => 'browse.journal_issues_empty'
			)
		);

		$this->m_objectsMetadata['leftcol'] = array(
			'templs' => array(
				G_DEFAULT => 'journalissue.sidebar_left_browse_issues',
			)
		);
	}
}

?>