<?php

/**
 * The view class for the browse journal issues page
 */
class pManage_Journal_Issues_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['journal_issues_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'journalissue.list_head',
				G_STARTRS => 'journalissue.list_startrs',
				G_ROWTEMPL => 'journalissue.list_row',
				G_ENDRS => 'journalissue.list_endrs',
				G_FOOTER => 'journalissue.list_foot',
				G_NODATA => 'journalissue.list_empty'
			)
		);

		$this->m_objectsMetadata['journal_back_issues_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'journalissue.list_back_head',
				G_STARTRS => 'journalissue.list_startrs',
				G_ROWTEMPL => 'journalissue.list_row',
				G_ENDRS => 'journalissue.list_endrs',
				G_FOOTER => 'journalissue.list_foot',
				G_NODATA => 'journalissue.list_empty'
			)
		);
	}
}

?>