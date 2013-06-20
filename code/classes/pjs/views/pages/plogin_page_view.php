<?php

/**
 * The view class for the login page
 *
 * @author peterg
 *
 */
class pLogin_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.loginpage',
		);

		$this->m_objectsMetadata['feedback_success_content'] = array(
			'templs'=>array(
				G_DEFAULT => 'contacts.success',
			),
		);

		$this->m_objectsMetadata['login_form'] = array(
			'templs'=>array(
				G_FORM_TEMPLATE => 'loginform.form',
				G_FORM_CAPTCHA_ROW => 'loginform.captcha',
			),
		);
	}

}

?>