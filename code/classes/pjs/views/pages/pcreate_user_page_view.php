<?php

/**
 * The view class for the create user page
 */
class pCreate_User_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		
		
		if($pData['big_right_col'] == 1) {
			$lPageTpl = 'global.dashboard';
		} else {
			$lPageTpl = 'global.big_right_col';
		}
		
		$this->m_Templs = array(
			G_DEFAULT => $lPageTpl
		);

		$this->m_objectsMetadata['create_user_form_templ'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'createuser.form'
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