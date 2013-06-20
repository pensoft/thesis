<?php

/**
 * The view class for the edit journal section page
 */
class pEdit_Journal_Section_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['journal_section_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'journalsection.edit_journal_section_form',
			)
		);
	}

}

?>