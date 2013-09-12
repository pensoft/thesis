<?php

/**
 * The view class for the browse journal issues page
 */
class pRSS_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);

		$this->m_Templs = array(
			G_DEFAULT => 'global.rss_page'
		);
		$this->m_objectsMetadata['rss_list_templs'] = array(
			'templs' => array(
				G_STARTRS => 'rss.startrs',
				G_ROWTEMPL => 'rss.row',
				G_ENDRS => 'rss.endrs',
				G_NODATA => 'rss.empty',
				G_HEADER => 'rss.header',
				G_FOOTER => 'rss.footer',
			)
		);
	}

	public function Display() {
		$this->SetPageContentType('text/xml');
		return $this->ReplaceHtmlFields($this->getObjectTemplate(G_DEFAULT), $this);
	}
}

?>