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
				G_FORM_TEMPLATE => 'registerfrm.form_step1'
			)
		);
	}

}

?>