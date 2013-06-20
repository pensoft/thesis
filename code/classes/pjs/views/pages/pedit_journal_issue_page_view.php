<?php

/**
 * The view class for the edit journal issue page
 */
class pEdit_Journal_Issue_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['journal_issue_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'journalissue.edit_journal_issue_form',
				G_FORM_CHECKBOX_ROW => 'form.checkbox_input_row_without_array_name',
			)
		);
	}

}

?>