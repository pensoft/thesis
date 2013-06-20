<?php

/**
 * The view class for the user journal expertises page
 */
class pUser_Journal_Expertises_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['user_journal_expertises_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'expertises.user_journal_expertises_form'
			)
		);
		
		$this->m_objectsMetadata['tree_list'] = array(
			'templs' => array(
				G_HEADER => 'treeview.treeviewtop',
				G_ROWTEMPL => 'treeview.treeviewrowtempl',
				G_FOOTER => 'treeview.treeviewfoot',
			)
		);
		
		$this->m_objectsMetadata['tree_script'] = array(
			'templs' => array(
				G_DEFAULT => 'treeview.treescripttempl'
			)
		);
		
		$this->m_objectsMetadata['user_expertises_success_content'] = array(
			'templs' => array(
				G_DEFAULT => 'expertises.frmwrappersuccess'
			)
		);
	}
}

?>