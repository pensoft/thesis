<?php
class pDocument_Creation_Permissions extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.simplepage',
		);

		$this->m_objectsMetadata['create_document_errors'] = array(

			'templs'=>array(
				G_ROWTEMPL => 'create_document.error_row',
			),

		);

		$this->m_objectsMetadata['success_msg'] = array(

			'templs'=>array(
				G_DEFAULT => 'create_document.success_msg',
			),

		);


		$this->m_objectsMetadata['form_step_1_pwt'] = array(

			'templs'=>array(
				G_FORM_TEMPLATE => 'create_document.permissions_form_step_1_pwt',
			),

		);

		$this->m_objectsMetadata['form_step_2_pwt'] = array(

			'templs'=>array(
				G_FORM_TEMPLATE => 'create_document.permissions_form_step_2_pwt',
			),

		);
	}
}

?>