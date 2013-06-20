<?php

/**
 * The view class for the manage journal sections page
 */
class pManage_Email_Templates_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.dashboard'
		);

		$this->m_objectsMetadata['email_default_templates_list'] = array(
			'templs' => array(
				G_HEADER => 'emailtemplates.browse_head',
				G_STARTRS => 'emailtemplates.browse_startrs',
				G_ROWTEMPL => 'emailtemplates.browse_row',
				G_ENDRS => 'emailtemplates.browse_endrs',
				G_FOOTER => 'emailtemplates.browse_foot',
				G_NODATA => 'emailtemplates.browse_empty'
			)
		);
		$this->m_objectsMetadata['email_template_edit_form'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'emailtemplates.template_edit_form',
			)
		);
	}
}

?>