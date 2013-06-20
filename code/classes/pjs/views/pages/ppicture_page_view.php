<?php

/**
 * The view class for the register page
 */
class pPicture_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.picture'
		);

		$this->m_objectsMetadata['profile_picture_template'] = array(
			'templs' => array(
				G_DEFAULT => 'global.profile_picture'
			)
		);

		$this->m_objectsMetadata['default_profile_picture'] = array(
			'templs' => array(
				G_DEFAULT => 'global.default_profile_picture'
			)
		);
	}

}

?>