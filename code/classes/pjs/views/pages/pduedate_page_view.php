<?php

/**
 * The view class for the edit page
 */
class pDueDate_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.duedatepopup'
		);

		$this->m_objectsMetadata['duedate_edit_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'duedatepopup.edit_form',
			)
		);
	}

}

?>