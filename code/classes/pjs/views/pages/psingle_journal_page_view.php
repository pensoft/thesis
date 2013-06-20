<?php

/**
 * The view class for the journal articles page
 */
class pSingle_Journal_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.journal_home_page'
		);

		$this->m_objectsMetadata['journal_page'] = array(
			'templs' => array(
				G_HEADER => 'journals.journal_documents_head',
				G_ROWTEMPL => 'journals.journal_documents_row',
				G_FOOTER => 'journals.journal_documents_foot',
				G_EMPTY => 'journals.journal_documents_empty',
			)
		);
		
		$this->m_objectsMetadata['journal_features_templates'] = array(
			'templs' => array(
				G_HEADER => 'browse.journal_fetures_head',
				G_ROWTEMPL => 'browse.journal_fetures_row',
				G_FOOTER => 'browse.journal_fetures_foot'
			)
		);
	}
}

?>