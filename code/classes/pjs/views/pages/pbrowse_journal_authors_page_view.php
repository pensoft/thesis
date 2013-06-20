<?php

/**
 * The view class for the browse journal authors page
 */
class pBrowse_Journal_Authors_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.big_left_col_page'
		);

		$this->m_objectsMetadata['browse_journal_authors_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'journalauthors.authors_head',
				G_STARTRS => 'journalauthors.authors_startrs',
				G_ROWTEMPL => 'journalauthors.authors_row',
				G_ENDRS => 'journalauthors.authors_endrs',
				G_FOOTER => 'journalauthors.authors_foot',
				G_NODATA => 'journalauthors.authors_empty'
			)
		);

		$this->m_objectsMetadata['leftcol'] = array(
			'templs' => array(
				G_DEFAULT => 'journalauthors.sidebar_left_browse_authors',
			)
		);
	}
}

?>