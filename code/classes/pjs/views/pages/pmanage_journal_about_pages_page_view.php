<?php

/**
 * The view class for the manage about pages page
 */
class pManage_Journal_About_Pages_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['stories_tree_templates_edit'] = array(
			'templs' => array(
				G_HEADER => 'browse.head',
				G_STARTRS => 'browse.startrs',
				G_ROWTEMPL => 'browse.row_edit',
				G_ENDRS => 'browse.endrs_edit',
				G_FOOTER => 'browse.foot'
			)
		);
	}
}

?>