<?php

class pArticles extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.article_page'
		);


		$this->m_objectsMetadata['errors'] = array(
			'templs' => array(
				G_ROWTEMPL => 'articles.error_row'
			)
		);
		
		$this->m_objectsMetadata['contents'] = array(
				'templs' => array(
						G_DEFAULT => 'articles.contents'
				)
		);

	}
}

?>