<?php

/**
 * The view class for the browse journal issues page
 */
class pArticle_Elements_Srv extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);

		$this->m_Templs = array(
			G_DEFAULT => 'global.simplepage'
		);


		$this->m_objectsMetadata['errors'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.error_row'
			)
		);
	}
}

?>