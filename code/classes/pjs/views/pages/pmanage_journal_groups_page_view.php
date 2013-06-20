<?php

/**
 * The view class for the manage journal sections page
 */
class pManage_Journal_Groups_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['journal_sections_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'journalgroups.browse_head',
				G_STARTRS => 'journalgroups.browse_startrs',
				G_ROWTEMPL => 'journalgroups.browse_row',
				G_ENDRS => 'journalgroups.browse_endrs',
				G_FOOTER => 'journalgroups.browse_foot',
				G_NODATA => 'journalgroups.browse_empty'
			)
		);
	}
}

?>