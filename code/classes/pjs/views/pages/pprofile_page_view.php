<?php

/**
 * The view class for the prfile page
 */
class pProfile_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.profilepage',
		);

		$this->m_objectsMetadata['profile_page_content'] = array(
			'templs'=>array(
				G_DEFAULT => 'profile.profile_content',
			),
		);
	}

}

?>