<?php

/**
 * The view class for the browse journal articles by author page
 */
class pBrowse_Articles_By_Author_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_Templs = array(
			G_DEFAULT => 'global.big_left_col_page'
		);

		$this->m_objectsMetadata['browse_articles_list_templs'] = array(
			'templs' => array(
				G_STARTRS => 'browse_articles.by_author_startrs',
				G_ROWTEMPL => 'browse_articles.by_author_row',
				G_ENDRS => 'browse_articles.by_author_endrs',
				G_NODATA => 'browse_articles.by_author_empty',
				
				G_PAGEING_STARTRS => 'pageing.startrs_nomargin',
			)
		);
		
		$this->m_objectsMetadata['leftcol'] = array(
			'templs' => array(
				G_DEFAULT => 'browse_articles.by_author_sidebar_left',
			)
		);
	}
}

?>