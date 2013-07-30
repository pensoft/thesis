<?php

/**
 * The view class for the prfile page
 */
class pPreview_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.preview_page',
		);

		$this->m_objectsMetadata['preview'] = array(
			'templs'=>array(
				G_DEFAULT => 'view_version_pwt.preview_content',
			),
		);
		$this->m_objectsMetadata['preview_with_error'] = array(
				'templs'=>array(
						G_DEFAULT => 'view_version_pwt.preview_content_error',
				),
		);
	}

}

?>