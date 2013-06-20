<?php

class pView_Version extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.simplepage'
		);


		$this->m_objectsMetadata['errors'] = array(
			'templs' => array(
				G_ROWTEMPL => 'view_version.error_row'
			)
		);

	}
}

?>