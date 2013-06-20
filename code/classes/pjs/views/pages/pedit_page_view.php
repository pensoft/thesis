<?php

/**
 * The view class for the edit page
 */
class pEdit_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.edit_journal_page'
		);

		$this->m_objectsMetadata['journal_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'journalfrm.edit_form',
				G_FORM_CHECKBOX_ROW => 'form.journal_story_checkbox_input_row',
			)
		);
	}

}

?>