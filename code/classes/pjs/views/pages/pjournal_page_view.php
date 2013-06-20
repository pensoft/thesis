<?php

/**
 * The view class for the journals list page
 */
class pJournal_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.journal_home_page'
		);
		$this->m_objectsMetadata['journal_list'] = array(
			'templs' => array(
				G_STARTRS => 'journals.list_startrs',
				G_ROWTEMPL => 'journals.list_row',
				G_ENDRS => 'journals.list_endrs',
			)
		);
	}
}

?>