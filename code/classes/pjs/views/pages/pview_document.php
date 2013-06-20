<?php

class pView_Document extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.simplepage'
		);

		$this->m_objectsMetadata['errors'] = array(
			'templs' => array(
				G_HEADER   => 'view_document.ErrorsListHeader',
				G_FOOTER   => 'view_document.ErrorsListFooter',
				G_STARTRS  => 'view_document.ErrorsListStart',
				G_ENDRS    => 'view_document.ErrorsListEnd',
				G_ROWTEMPL => 'view_document.ErrorsListRow',
				G_NODATA   => 'view_document.ErrorsListNodata',
			)
		);
		
		$this->m_objectsMetadata['submitted_date_obj'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.submitted_date_obj'
			)
		);
		
		$this->m_objectsMetadata['document_info'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.document_info'
			)
		);
		
		$this->m_objectsMetadata['document_metadata'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.metadata'
			)
		);

		$this->m_objectsMetadata['author_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_author.author_tabs',
			)
		);
		
		$this->m_objectsMetadata['e_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_editor.e_tabs',
			)
		);
		
		$this->m_objectsMetadata['se_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_se.se_tabs',
			)
		);
		
		$this->m_objectsMetadata['ce_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_ce.ce_tabs',
			)
		);
		
		$this->m_objectsMetadata['r_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_dedicated_reviewer.r_tabs',
			)
		);
		
		$this->m_objectsMetadata['le_tabs'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document_le.le_tabs',
			)
		);
		
		$this->m_objectsMetadata['metadata_section'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.metadata_section',
			)
		);
		
		$this->m_objectsMetadata['abstract_keywords'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.abstract_keywords',
			)
		);
		
		$this->m_objectsMetadata['abstract'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.abstract',
			)
		);
		
		$this->m_objectsMetadata['keywords'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.keywords',
			)
		);
		
		$this->m_objectsMetadata['indexed_terms'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.indexed_terms',
			)
		);
		
		$this->m_objectsMetadata['history_section'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.history_section',
			)
		);
		$this->m_objectsMetadata['scheduling _section'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.history_section',
			)
		);
		
		
		// SE List assigned by journal editor
		$this->m_objectsMetadata['authors_list'] = array(
			'templs' => array(
				G_HEADER   => 'view_document.AuthorsListHeader',
				G_FOOTER   => 'view_document.AuthorsListFooter',
				G_STARTRS  => 'view_document.AuthorsListStart',
				G_ENDRS    => 'view_document.AuthorsListEnd',
				G_ROWTEMPL => 'view_document.AuthorsListRow',
				G_NODATA   => 'view_document.AuthorsListNodata',
			)
		);
		
		$this->m_objectsMetadata['submitted_files_section'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.submitted_files_section',
			)
		);
		
		// Submitted Files List by document
		$this->m_objectsMetadata['submitted_files_list'] = array(
			'templs' => array(
				G_HEADER   => 'view_document.SubmittedFilesListHeader',
				G_FOOTER   => 'view_document.SubmittedFilesListFooter',
				G_STARTRS  => 'view_document.SubmittedFilesListStart',
				G_ENDRS    => 'view_document.SubmittedFilesListEnd',
				G_ROWTEMPL => 'view_document.SubmittedFilesListRow',
				G_NODATA   => 'view_document.SubmittedFilesListNodata',
			)
		);
		
		// Ended rounds
		$this->m_objectsMetadata['view_review_rounds'] = array(
			'templs' => array(
				G_DEFAULT => 'view_document.view_review_rounds_row',
			)
		);

		// SE List for assignment by journal editor
		$this->m_objectsMetadata['se_assigned_list'] = array(
			'templs' => array(
				G_STARTRS => 'view_document.seAssignedListStart',
				G_ENDRS => 'view_document.seAssignedListEnd',
				G_ROWTEMPL => 'view_document.seAssignedListRow',
			)
		);

	}
}

?>