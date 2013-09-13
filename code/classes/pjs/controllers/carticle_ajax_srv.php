<?php
// Disable error reporting because it can break the json output
// ini_set('error_reporting', 'off');
class cArticle_Ajax_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_action;
	var $m_action_result;
	var $m_articlesModel;
	var $m_tempPageView;
	var $m_articleId;

	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array ();
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		$this->m_action_result = array ();
		$this->m_articlesModel = new mArticles();
		$this->m_tempPageView = new pArticles_Ajax_Srv();
		$this->m_articleId = (int) $this->GetValueFromRequestWithoutChecks('article_id');
		
		if (! $this->m_articleId) {
			$this->m_errCnt ++;
			$this->m_errMsg = getstr('pjs.noArticleId');
		} else {
			switch ($this->m_action) {
				default :
					$this->m_errCnt ++;
					$this->m_errMsg = getstr('pjs.unrecognizedAction');
					break;
				case 'get_main_list_element' :
					$this->GetMainListElement();
					break;
				case 'get_figure_element' :
					$this->GetFigureElement();
					break;
				case 'get_table_element' :
					$this->GetTableElement();
					break;
				case 'get_taxon_element' :
					$this->GetTaxonElement();
					break;
				case 'get_sup_file_element' :
					$this->GetSupFileElement();
					break;
				case 'get_reference_element' :
					$this->GetReferenceElement();
					break;
				case 'get_author_element' :
					$this->GetAuthorElement();
					break;
				case 'get_article_localities':
					$this->GetArticleLocalities();
					break;
					
			}
		}
		$lResultArr = array_merge($this->m_action_result, array (
			'err_cnt' => $this->m_errCnt,
			'err_msg' => $this->m_errMsg 
		));
		// var_dump($lResultArr);
		$this->m_pageView = new pArticles_Ajax_Srv($lResultArr);
	}

	function GetMainListElement() {
		$lElementType = (int) $this->GetValueFromRequestWithoutChecks('element_type');
		
		$lResult = '';
		switch ($lElementType) {
			default :
			case (int) ARTICLE_MENU_ELEMENT_TYPE_CONTENTS :
				$lResult = $this->m_articlesModel->GetContentsListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_FIGURES :
				$lResult = $this->m_articlesModel->GetFiguresListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_TABLES :
				$lResult = $this->m_articlesModel->GetTablesListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_REFERENCES :
				$lResult = $this->m_articlesModel->GetReferencesListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_SUP_FILES :
				$lResult = $this->m_articlesModel->GetSupFilesListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_LOCALITIES :
				$lResult = $this->m_articlesModel->GetLocalitiesListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_TAXON :
				$lResult = $this->m_articlesModel->GetTaxonListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_AUTHORS :
				$lResult = $this->m_articlesModel->GetAuthorsListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_CITATION :
				$lResult = $this->m_articlesModel->GetCitationListHtml($this->m_articleId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_RELATED :
				$lResult = $this->GetRelatedList();
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_METRICS :
				$lResult = $this->GetMetricsList();
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_SHARE :
				$lResult = $this->GetShareList();
				break;
		}
		$this->m_action_result ['html'] = $lResult;
	}

	protected function GetElement($pElementType) {
		$lElementId = (int) $this->GetValueFromRequestWithoutChecks('element_id');
		$lElementName = trim($this->GetValueFromRequestWithoutChecks('element_name'));
		if (! $lElementId && $pElementType != ARTICLE_MENU_ELEMENT_TYPE_TAXON) {
			$this->m_errCnt ++;
			$this->m_errMsg = getstr('pjs.noElementId');
			return;
		}
		
		if (! $lElementName && $pElementType == ARTICLE_MENU_ELEMENT_TYPE_TAXON) {
			$this->m_errCnt ++;
			$this->m_errMsg = getstr('pjs.noTaxonName');
			return;
		}
		$lResult = '';
		switch ($pElementType) {
			default :
				$this->m_errCnt ++;
				$this->m_errMsg = getstr('pjs.unknownElementType');
				return;
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_FIGURES :
				$lResult = $this->m_articlesModel->GetFigureHtml($this->m_articleId, $lElementId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_TABLES :
				$lResult = $this->m_articlesModel->GetTableHtml($this->m_articleId, $lElementId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_REFERENCES :
				$lResult = $this->m_articlesModel->GetReferenceHtml($this->m_articleId, $lElementId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_SUP_FILES :
				$lResult = $this->m_articlesModel->GetSupFileHtml($this->m_articleId, $lElementId);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_TAXON :
				$lResult = $this->m_articlesModel->GetTaxonHtml($lElementName);
				break;
			case (int) ARTICLE_MENU_ELEMENT_TYPE_AUTHORS :
				$lResult = $this->m_articlesModel->GetAuthorHtml($this->m_articleId, $lElementId);
				break;
		}
		$this->m_action_result ['html'] = $lResult;
		$this->m_action_result ['element_type'] = (int) $pElementType;
	}

	function GetFigureElement() {
		$this->GetElement((int) ARTICLE_MENU_ELEMENT_TYPE_FIGURES);
	}

	function GetTableElement() {
		$this->GetElement((int) ARTICLE_MENU_ELEMENT_TYPE_TABLES);
	}

	function GetReferenceElement() {
		$this->GetElement((int) ARTICLE_MENU_ELEMENT_TYPE_REFERENCES);
	}

	function GetSupFileElement() {
		$this->GetElement((int) ARTICLE_MENU_ELEMENT_TYPE_SUP_FILES);
	}

	function GetTaxonElement() {
		$this->GetElement((int) ARTICLE_MENU_ELEMENT_TYPE_TAXON);
	}

	function GetAuthorElement() {
		$this->GetElement((int) ARTICLE_MENU_ELEMENT_TYPE_AUTHORS);
	}
	
	function GetArticleLocalities(){
		$this->m_action_result ['localities'] = $this->m_articlesModel->GetLocalities($this->m_articleId);
	}
	
	function GetRelatedList(){
		$lResult = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'related_list',
			'view_object' => $this->m_tempPageView,
		));
		return $lResult->Display();
	}
	
	function GetMetricsList(){
		$lHTMLMetrics = $this->m_articlesModel->GetArticleHtmlMetricDetails($this->m_articleId);
		$lXMLMetrics = $this->m_articlesModel->GetArticleXmlMetricDetails($this->m_articleId);
		$lPDFMetrics = $this->m_articlesModel->GetArticlePdfMetricDetails($this->m_articleId);
		$lMetricsToSum = array($lHTMLMetrics, $lXMLMetrics, $lPDFMetrics);
		$lTotalMetric = array();
		foreach ($lMetricsToSum as $lCurrentMetric){
			foreach ($lCurrentMetric as $lDetailType => $lDetailData){
				if(!array_key_exists($lDetailType, $lTotalMetric)){
					$lTotalMetric[$lDetailType] = 0;
				}
				$lTotalMetric[$lDetailType] += (int)$lDetailData;
			}
		}		
		$lFigureMetricsDetails = $this->m_articlesModel->GetArticleFiguresMetrics($this->m_articleId);
		$lTableMetricsDetails = $this->m_articlesModel->GetArticleTablesMetrics($this->m_articleId);
		$lSupplFilesMetricsDetails = $this->m_articlesModel->GetArticleSupplFilesMetrics($this->m_articleId);
		
		$lFiguresMetrics = new evList_Display(array(
			'name_in_viewobject' => 'metrics_figures_list',
			'view_object' => $this->m_tempPageView,
			'controller_data' => $lFigureMetricsDetails
		));
		
// 		var_dump($lFiguresMetrics->Display(), $lFigureMetricsDetails);
		
		$lTablesMetrics = new evList_Display(array(
			'name_in_viewobject' => 'metrics_tables_list',
			'view_object' => $this->m_tempPageView,
			'controller_data' => $lTableMetricsDetails
		));
		
		$lSupplFilesMetrics = new evList_Display(array(
			'name_in_viewobject' => 'metrics_suppl_files_list',
			'view_object' => $this->m_tempPageView,
			'controller_data' => $lSupplFilesMetricsDetails
		));
		
		
		$lResult = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'metrics_list',
			'html_views_cnt' => (int)$lHTMLMetrics['view_cnt'],
			'html_unique_views_cnt' => (int)$lHTMLMetrics['view_unique_cnt'],
			
			'pdf_views_cnt' => (int)$lPDFMetrics['view_cnt'],
			'pdf_unique_views_cnt' => (int)$lPDFMetrics['view_unique_cnt'],
			
			'xml_views_cnt' => (int)$lXMLMetrics['view_cnt'],
			'xml_unique_views_cnt' => (int)$lXMLMetrics['view_unique_cnt'],
						
			'total_views_cnt' => (int)$lTotalMetric['view_cnt'],
			'total_unique_views_cnt' => (int)$lTotalMetric['view_unique_cnt'],
			'view_object' => $this->m_tempPageView,
			
			'figures_metrics' => $lFiguresMetrics,
			'tables_metrics' => $lTablesMetrics,
			'suppl_files_metrics' => $lSupplFilesMetrics,			
		));
		return $lResult->Display();
	}
	
	function GetShareList(){
		$lData = $this->m_articlesModel->GetArticleInfoForShare($this->m_articleId);
		$lData['document_name'] = strip_tags($lData['document_name']);
		$lResult = new evSimple_Block_Display(array(
			'name_in_viewobject' => 'share_list',
			'view_object' => $this->m_tempPageView,
			'article_id' => $this->m_articleId,
			'controller_data' => $lData,
		));
		return $lResult->Display();
	}
}

?>