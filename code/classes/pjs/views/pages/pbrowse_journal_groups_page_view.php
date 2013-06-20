<?php

/**
 * The view class for the browse journal authors page
 */
class pBrowse_Journal_Groups_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.big_left_col_page'
		);
		/* Users by group list right col */
		$this->m_objectsMetadata['browse_group_list_templs'] = array(
			'templs' => array(
				G_HEADER =>   'journalgroups.head',
				G_STARTRS =>  'journalgroups.startrs',
				G_ROWTEMPL => 'journalgroups.row',
				G_ENDRS =>    'journalgroups.endrs',
				G_FOOTER =>   'journalgroups.foot',
				G_NODATA =>   'journalgroups.nodata'
			)
		);
		
		/* Subject Editors by group list right col */
		$this->m_objectsMetadata['browse_group_se_list_templs'] = array(
			'templs' => array(
				G_HEADER =>   'journalgroups.head',
				G_STARTRS =>  'journalgroups.startrs',
				G_ROWTEMPL => 'journalgroups.SE_row',
				G_ENDRS =>    'journalgroups.endrs',
				G_FOOTER =>   'journalgroups.foot',
				G_NODATA =>   'journalgroups.nodata'
			)
		);

		//~ $this->m_objectsMetadata['leftcol'] = array(
			//~ 'templs' => array(
				//~ G_HEADER => 'global.empty',
				//~ G_STARTRS => 'journalgroups.sidebar_left_browse_groups_header',
				//~ G_ROWTEMPL => 'journalgroups.sidebar_left_browse_groups_row',
				//~ G_ENDRS => 'global.empty',
				//~ G_FOOTER => 'journalgroups.sidebar_left_browse_groups_foot',
				//~ G_NODATA => 'global.empty'
			//~ )
		//~ );
		$this->m_objectsMetadata['leftcol'] = array(
			'templs' => array(
				G_HEADER => 'journalgroups.sidebar_left_browse_groups_header',
				G_STARTRS => 'global.empty',
				G_ROWTEMPL => 'journalgroups.sidebar_left_browse_groups_row',
				G_ENDRS => 'global.empty',
				G_FOOTER => 'journalgroups.sidebar_left_browse_groups_foot',
				G_NODATA => 'global.empty'
			)
		);
		$this->m_objectsMetadata['browse_groups_form_templ'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'journalgroups.search_form',
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