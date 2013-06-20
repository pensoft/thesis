<?php

/**
 * The view class for the register page
 */
class pRegister_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.registerpage'
		);

		$this->m_objectsMetadata['register_form_1'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'registerfrm.form_step1',
				//G_FORM_FIELD_ERROR_ROW => 'form.field_error_row_without_field_name',
			)
		);

		$this->m_objectsMetadata['register_form_2'] = array(
			'templs' => array(
				G_FORM_TEMPLATE  => 'registerfrm.form_step2',
				G_FORM_RADIO_ROW => 'form.registep_radio_input_row',
				G_FORM_FIELD_ERROR_ROW => 'form.field_error_row_without_field_name',
			)
		);
		
		$this->m_objectsMetadata['register_form_3'] = array(
			'templs' => array(
				G_FORM_TEMPLATE     => 'registerfrm.form_step3',
				G_FORM_RADIO_ROW    => 'form.registep3_radio_input_row',
				G_FORM_CHECKBOX_ROW => 'form.registep_checkbox_input_row',
				G_FORM_FIELD_ERROR_ROW => 'form.field_error_row',
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
		
		$this->m_objectsMetadata['tree_script_reg'] = array(
			'templs' => array(
				G_DEFAULT => 'treeview.treescripttempl_reg'
			)
		);
	}

}

?>