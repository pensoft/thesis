<?php
class pCreate_Pwt_Document extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.simplepage',
		);

		$this->m_objectsMetadata['create_document_errors'] = array(

			'templs'=>array(
				G_ROWTEMPL => 'create_document.error_row',
			),

		);
	}
}

?>