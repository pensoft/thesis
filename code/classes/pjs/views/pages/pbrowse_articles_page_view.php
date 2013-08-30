<?php

/**
 * The view class for the browse journal issues page
 */
class pBrowse_Articles_Page_View extends pBase_Page_View {
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_displayShowPage = $pData['display_show_page'];
		
		if((int)$this->m_displayShowPage == 1) { 
			$this->m_Templs = array(
				G_DEFAULT => 'global.browse_articles'
			);
		} else {
			$this->m_Templs = array(
				G_DEFAULT => 'global.big_left_col_page'
			);
		}

		$this->m_objectsMetadata['browse_articles_list_templs'] = array(
			'templs' => array(
				G_STARTRS => 'browse_articles.startrs',
				G_ROWTEMPL => 'browse_articles.row',
				G_ENDRS => 'browse_articles.endrs',
				G_NODATA => 'browse_articles.empty',
				G_HEADER => 'browse_articles.header',
				G_FOOTER => 'browse_articles.footer',
				G_PAGEING_STARTRS => 'pageing.startrs_nomargin',
			)
		);
		$this->m_objectsMetadata['browse_articles_templs'] = array(
			'templs' => array(
				G_STARTRS => 'browse_articles.public_startrs',
				G_ROWTEMPL => 'browse_articles.row',
				G_ENDRS => 'browse_articles.public_endrs',
				G_NODATA => 'browse_articles.empty',
				
				G_PAGEING_STARTRS => 'pageing.startrs_nomargin',
			)
		);
		
		$this->m_objectsMetadata['journal_features_templates'] = array(
			'templs' => array(
				G_HEADER => 'browse_articles.journal_fetures_head',
				G_ROWTEMPL => 'browse.journal_fetures_row',
				G_FOOTER => 'browse_articles.journal_fetures_foot'
			)
		);
		
		$this->m_objectsMetadata['browse_articles_form_templ'] = array(
			'templs' => array(
				G_FORM_TEMPLATE => 'browse_articles.search_form',
			)
		);
		$this->m_objectsMetadata['leftcol'] = array(
			'templs' => array(
				G_DEFAULT => 'browse_articles.sidebar_left',
			)
		);
		
		$this->m_objectsMetadata['tree_list'] = array(
			'templs' => array(
				G_HEADER => 'treeview.treeviewtop',
				G_ROWTEMPL => 'treeview.treeviewrowtempl',
				G_FOOTER => 'treeview.treeviewfoot',
			)
		);
		
		$this->m_objectsMetadata['tree_script'] = array(
			'templs' => array(
				G_DEFAULT => 'treeview.treescripttempl'
			)
		);
	}
}

?>