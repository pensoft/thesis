<?php

/**
 * The view class for the login page
 *
 * @author peterg
 *
 */
class pFPass_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.fpasspage',
		);

		$this->m_objectsMetadata['fpass_success_content'] = array(
			'templs'=>array(
				G_DEFAULT => 'loginform.fpass_success',
			),
		);

		$this->m_objectsMetadata['fpass_form'] = array(
			'templs'=>array(
				G_FORM_TEMPLATE => 'loginform.fpass',
			),
		);
	}

}

?>