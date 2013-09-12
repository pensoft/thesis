<?php

/**
 * The view class for the browse journal issues page
 */
class pArticles_Ajax_Srv extends epPage_Json_View {
	function __construct($pData) {
		parent::__construct($pData);

		$this->m_objectsMetadata['related_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.related'
			)
		);

		$this->m_objectsMetadata['metrics_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.metrics',
			)
		);
		
		$this->m_objectsMetadata['metrics_figures_list'] = array(
			'templs' => array(
				G_HEADER => 'article.figures_metrics_head',
				G_FOOTER => 'article.figures_metrics_foot',
				G_STARTRS => 'article.figures_metrics_start',
				G_ENDRS => 'article.figures_metrics_end',
				G_NODATA => 'article.figures_metrics_nodata',
				G_ROWTEMPL => 'article.figures_metrics_row' 
			)
		);
		
		$this->m_objectsMetadata['metrics_tables_list'] = array(
			'templs' => array(
				G_HEADER => 'article.tables_metrics_head',
				G_FOOTER => 'article.tables_metrics_foot',
				G_STARTRS => 'article.tables_metrics_start',
				G_ENDRS => 'article.tables_metrics_end',
				G_NODATA => 'article.tables_metrics_nodata',
				G_ROWTEMPL => 'article.tables_metrics_row' 
			)
		);
		
		$this->m_objectsMetadata['metrics_suppl_files_list'] = array(
			'templs' => array(
				G_HEADER => 'article.suppl_files_metrics_head',
				G_FOOTER => 'article.suppl_files_metrics_foot',
				G_STARTRS => 'article.suppl_files_metrics_start',
				G_ENDRS => 'article.suppl_files_metrics_end',
				G_NODATA => 'article.suppl_files_metrics_nodata',
				G_ROWTEMPL => 'article.suppl_files_metrics_row' 
			)
		);
		
		$this->m_objectsMetadata['share_list'] = array(
			'templs' => array(
				G_DEFAULT => 'articles.share',
			)
		);
	}
}

?>