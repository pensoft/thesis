<?php

/**
 * The view class for the edit journal section page
 */
class pEdit_Journal_Group_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);
		
		$this->m_objectsMetadata['journal_group_fusers_in_group'] = array(
			'templs' => array(
				G_HEADER => 'global.empty',
				G_STARTRS => 'journalgroups.browse_startrs_users_list',
				G_ROWTEMPL => 'journalgroups.browse_row_users_list',
				G_ENDRS => 'journalgroups.browse_endrs',
				G_FOOTER => 'journalgroups.browse_foot_users_list',
				G_NODATA => 'journalgroups.no_users_data'
			)
		);
		//~ $this->m_objectsMetadata['journal_group_subgroups'] = array(
			//~ 'templs' => array(
				//~ G_HEADER => 'global.empty',
				//~ G_STARTRS => 'journalgroups.browse_startrs_subgroupss_list',
				//~ G_ROWTEMPL => 'journalgroups.browse_row_subgroups_list',
				//~ G_ENDRS => 'journalgroups.browse_endrs',
				//~ G_FOOTER => 'global.empty',
				//~ G_NODATA => 'journalgroups.no_users_data'
			//~ )
		//~ );
		$this->m_objectsMetadata['journal_group_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'journalgroups.edit_journal_groups_form',
			)
		);
	}

}

?>