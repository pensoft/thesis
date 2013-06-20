<?php

/**
 * A base page view class - it sets the default templates for the default objects
 * All other view classes (if any) should extend (directly or not) this class.
 *
 * @author peterg
 *
 */
class pForm_Check_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);

		// Default form templates
		$this->m_objectsMetadata['ajax_validate_form'] = array(
			'templs' => array(
				G_FORM_FIELD_ERROR_HEADER => 'global.empty',
				G_FORM_FIELD_ERROR_ROW => 'form.field_error_row_without_field_name',
				G_FORM_FIELD_ERROR_FOOTER => 'global.empty',				
			)
		);
	}

	public function Display() {
		return json_encode($this -> m_pubdata);
	}

	public function SetPubData($pPubdata) {
		$this -> m_pubdata = $pPubdata;
	}

}
?>