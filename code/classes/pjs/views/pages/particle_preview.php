<?php

class pArticle_Preview extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.article_preview_page'
		);
	}
}

?>