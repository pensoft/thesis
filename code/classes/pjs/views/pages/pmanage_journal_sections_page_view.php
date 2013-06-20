<?php

/**
 * The view class for the manage journal sections page
 */
class pManage_Journal_Sections_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['journal_sections_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'journalsection.browse_head',
				G_STARTRS => 'journalsection.browse_startrs',
				G_ROWTEMPL => 'journalsection.browse_row',
				G_ENDRS => 'journalsection.browse_endrs',
				G_FOOTER => 'journalsection.browse_foot',
				G_NODATA => 'journalsection.browse_empty'
			)
		);
	}
}

?>