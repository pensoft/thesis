<?php

/**
 * The view class for the register page
 */
class pUser_Expertises_PopUp_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		
		$this->m_Templs = array(
			G_DEFAULT => 'global.userexpertisespopuppage'
		);

		$this->m_objectsMetadata['userexpertisesfrm'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'createuser.userexpertisesfrm',
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
	}

}

?>