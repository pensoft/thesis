<?php

/**
 * The view class for the manage journal users page
 */
class pManage_Journal_Users_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['journal_users_list_templs'] = array(
			'templs' => array(
				G_HEADER => 'browse.journal_users_head',
				G_STARTRS => 'browse.journal_users_start',
				G_ROWTEMPL => 'browse.journal_users_row',
				G_ENDRS => 'browse.journal_users_endrs',
				G_FOOTER => 'browse.journal_users_foot',
				G_NODATA => 'browse.journal_users_empty'
			)
		);
	}
}

?>