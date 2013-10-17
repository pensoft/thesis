<?php

/**
 * The view class for the browse journal issues page
 */
class pOai extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'oai.page'
		);

		$this->m_objectsMetadata['identity'] = array(
			'templs' => array(
				G_DEFAULT => 'oai.identity'
			)
		);
				
		$this->m_objectsMetadata['sets'] = array(
			'templs' => array(
				G_HEADER   => 'oai.setsHead',
				G_FOOTER   => 'oai.setsFoot',
				G_STARTRS  => 'oai.setsStart',
				G_ENDRS    => 'oai.setsEnd',
				G_ROWTEMPL => 'oai.setsRow',
				G_NODATA   => 'oai.setsNoData',
				G_PAGEING_STARTRS => 'global.empty',
				G_PAGEING_INACTIVEFIRST => 'global.empty',
				G_PAGEING_ACTIVEFIRST => 'global.empty',
				G_PAGEING_PGSTART => 'global.empty',
				G_PAGEING_INACTIVEPAGE => 'global.empty',
				G_PAGEING_ACTIVEPAGE => 'global.empty',
				G_PAGEING_PGEND => 'global.empty',
				G_PAGEING_ENDRS => 'global.empty',
				G_PAGEING_DELIMETER => 'global.empty',
				G_PAGEING_INACTIVELAST => 'global.empty',
				G_PAGEING_ACTIVELAST => 'oai.pageingActiveLast',
				
			)
		);
		
		$this->m_objectsMetadata['identifiers'] = array(
			'templs' => array(
				G_HEADER   => 'oai.identifiersHead',
				G_FOOTER   => 'oai.identifiersFoot',
				G_STARTRS  => 'oai.identifiersStart',
				G_ENDRS    => 'oai.identifiersEnd',
				G_ROWTEMPL => 'oai.identifiersRow',
				G_NODATA   => 'oai.identifiersNoData',
				G_PAGEING_STARTRS => 'global.empty',
				G_PAGEING_INACTIVEFIRST => 'global.empty',
				G_PAGEING_ACTIVEFIRST => 'global.empty',
				G_PAGEING_PGSTART => 'global.empty',
				G_PAGEING_INACTIVEPAGE => 'global.empty',
				G_PAGEING_ACTIVEPAGE => 'global.empty',
				G_PAGEING_PGEND => 'global.empty',
				G_PAGEING_ENDRS => 'global.empty',
				G_PAGEING_DELIMETER => 'global.empty',
				G_PAGEING_INACTIVELAST => 'global.empty',
				G_PAGEING_ACTIVELAST => 'oai.pageingActiveLast',
		
			)
		);
		
		$this->m_objectsMetadata['records_mods'] = array(
			'templs' => array(
				G_HEADER   => 'oai.recordsHead',
				G_FOOTER   => 'oai.recordsFoot',
				G_STARTRS  => 'oai.recordsStart',
				G_ENDRS    => 'oai.recordsEnd',
				G_ROWTEMPL => 'oai.recordsRowMods',
				G_NODATA   => 'oai.recordsNoData',
				G_PAGEING_STARTRS => 'global.empty',
				G_PAGEING_INACTIVEFIRST => 'global.empty',
				G_PAGEING_ACTIVEFIRST => 'global.empty',
				G_PAGEING_PGSTART => 'global.empty',
				G_PAGEING_INACTIVEPAGE => 'global.empty',
				G_PAGEING_ACTIVEPAGE => 'global.empty',
				G_PAGEING_PGEND => 'global.empty',
				G_PAGEING_ENDRS => 'global.empty',
				G_PAGEING_DELIMETER => 'global.empty',
				G_PAGEING_INACTIVELAST => 'global.empty',
				G_PAGEING_ACTIVELAST => 'oai.pageingActiveLast',
		
			)
		);
		
		$this->m_objectsMetadata['records_oai_dc'] = array(
			'templs' => array(
				G_HEADER   => 'oai.recordsHead',
				G_FOOTER   => 'oai.recordsFoot',
				G_STARTRS  => 'oai.recordsStart',
				G_ENDRS    => 'oai.recordsEnd',
				G_ROWTEMPL => 'oai.recordsRowOai',
				G_NODATA   => 'oai.recordsNoData',
				G_PAGEING_STARTRS => 'global.empty',
				G_PAGEING_INACTIVEFIRST => 'global.empty',
				G_PAGEING_ACTIVEFIRST => 'global.empty',
				G_PAGEING_PGSTART => 'global.empty',
				G_PAGEING_INACTIVEPAGE => 'global.empty',
				G_PAGEING_ACTIVEPAGE => 'global.empty',
				G_PAGEING_PGEND => 'global.empty',
				G_PAGEING_ENDRS => 'global.empty',
				G_PAGEING_DELIMETER => 'global.empty',
				G_PAGEING_INACTIVELAST => 'global.empty',
				G_PAGEING_ACTIVELAST => 'oai.pageingActiveLast',
		
			)
		);
		
		$this->m_objectsMetadata['keywords_mods'] = array(
			'templs' => array(
				G_ROWTEMPL => 'oai.recordsKeywordsRowMods',				
			)
		);
		
		$this->m_objectsMetadata['keywords_oai_dc'] = array(
			'templs' => array(
				G_ROWTEMPL => 'oai.recordsKeywordsRowOai',
			)
		);
		
		$this->m_objectsMetadata['sets_mods'] = array(
			'templs' => array(
				G_ROWTEMPL => 'oai.recordsSetRowMods',
			)
		);
		
		$this->m_objectsMetadata['sets_oai_dc'] = array(
			'templs' => array(
				G_ROWTEMPL => 'oai.recordsSetRowOai',
			)
		);
		
		$this->m_objectsMetadata['authors_mods'] = array(
			'templs' => array(
				G_ROWTEMPL => 'oai.recordsAuthorsRowMods',
			)
		);
		
		$this->m_objectsMetadata['authors_oai_dc'] = array(
			'templs' => array(
				G_ROWTEMPL => 'oai.recordsAuthorsRowOai',
			)
		);
		
		$this->m_objectsMetadata['funding_agencies_mods'] = array(
			'templs' => array(
				G_ROWTEMPL => 'oai.recordsFundingAgenciesRowMods',
			)
		);
		
		$this->m_objectsMetadata['funding_agencies_oai_dc'] = array(
			'templs' => array(
				G_ROWTEMPL => 'oai.recordsFundingAgenciesRowOai',
			)
		);
		
		$this->m_objectsMetadata['single_record_mods'] = array(
			'templs' => array(
				G_HEADER   => 'oai.singleRecordHead',
				G_FOOTER   => 'oai.singleRecordFoot',
				G_STARTRS  => 'oai.singleRecordStart',
				G_ENDRS    => 'oai.singleRecordEnd',
				G_ROWTEMPL => 'oai.recordsRowMods',		
			)
		);
		
		$this->m_objectsMetadata['single_record_oai_dc'] = array(
			'templs' => array(
				G_HEADER   => 'oai.singleRecordHead',
				G_FOOTER   => 'oai.singleRecordFoot',
				G_STARTRS  => 'oai.singleRecordStart',
				G_ENDRS    => 'oai.singleRecordEnd',
				G_ROWTEMPL => 'oai.recordsRowOai',
			)
		);
		
		$this->m_objectsMetadata['metadata_formats'] = array(
			'templs' => array(
				G_HEADER   => 'oai.metadataFormatsHead',
				G_FOOTER   => 'oai.metadataFormatsFoot',
				G_STARTRS  => 'oai.metadataFormatsStart',
				G_ENDRS    => 'oai.metadataFormatsEnd',
				G_ROWTEMPL => 'oai.metadataFormatsRow',
			)
		);
		
		
		$this->m_objectsMetadata['errors'] = array(
			'templs' => array(
				G_DEFAULT => 'oai.err_row',
		
			)
		);
	}
	
	public function Display() {
		$this->SetPageContentType('text/xml');
		return parent::Display();
	}
}

?>