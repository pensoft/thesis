<?php

/**
 * The view class for the stories browse and show pages
 *
 * @author peterg
 *
 */
class pDocument_Edit_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.document_edit',
		);

		$this->m_objectsMetadata['document_show'] = array(
			'templs'=>array(
				//~ G_DEFAULT => 'document_edit.document_header',
				G_DEFAULT => 'global.empty',
			),
		);
	}
}

?>