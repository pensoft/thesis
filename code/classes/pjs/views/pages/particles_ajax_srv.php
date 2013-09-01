<?php

/**
 * The view class for the browse journal issues page
 */
class pArticles_Ajax_Srv extends epPage_Json_View {
	function __construct($pData) {
		parent::__construct($pData);

		$this->m_objectsMetadata['related_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.related'
			)
		);

		$this->m_objectsMetadata['metrics_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.metrics',
			)
		);
		
		$this->m_objectsMetadata['share_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.share',
			)
		);
	}
}

?>