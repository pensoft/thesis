<?php

/**
 * The view class for the browse journal issues page
 */
class pGenerate_XML_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);

		$this->m_Templs = array(
			G_DEFAULT => 'global.nml_xml'
		);
	}

	public function Display() {
		$this->SetPageContentType('text/xml');
		return $this->ReplaceHtmlFields($this->getObjectTemplate(G_DEFAULT), $this);
	}
}

?>