<?php

/**
 * The view class for the edit page
 */
class pChange_Review_Type_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.reviewtypepopup'
		);

		$this->m_objectsMetadata['review_edit_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'reviewtypepopup.edit_form',
			)
		);
	}

}

?>