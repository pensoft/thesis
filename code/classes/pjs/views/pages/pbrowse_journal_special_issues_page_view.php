<?php

/**
 * The view class for the browse journal special issues page
 */
class pBrowse_Journal_Special_Issues_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.browse_journal_special_issues'
		);

		$this->m_objectsMetadata['journal_special_issues_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'journalissue.special_list_head',
				G_STARTRS => 'journalissue.special_list_startrs',
				G_ROWTEMPL => 'journalissue.special_list_row',
				G_ENDRS => 'journalissue.special_list_endrs',
				G_FOOTER => 'journalissue.special_list_foot',
				G_NODATA => 'journalissue.list_empty'
			)
		);
		
		$this->m_objectsMetadata['journal_features_templates'] = array(
			'templs' => array(
				G_HEADER => 'browse.journal_fetures_head',
				G_ROWTEMPL => 'browse.journal_fetures_row',
				G_FOOTER => 'browse.journal_fetures_foot'
			)
		);
	}
}

?>