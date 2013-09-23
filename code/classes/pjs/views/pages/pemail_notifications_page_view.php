<?php

/**
 * The view class for the manage journal sections page
 */
class pEmail_Notifications_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['email_notification_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'email_notifications.email_notification_form',
			)
		);
		
		$this->m_defTempls[G_FORM_RICHTEXT_EDITOR_ROW] = 'form.richtext_editor_normal_row';
	}
}

?>