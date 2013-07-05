<?php

/**
 * The view class for the journals list page
 */
class pGenerate_PDF_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.generate_pdf'
		);
		$this->m_objectsMetadata['generate_pdf'] = array(
			'templs' => array(
				G_DEFAULT => 'generate_pdf.main'
			)
		);
	}
}

?>