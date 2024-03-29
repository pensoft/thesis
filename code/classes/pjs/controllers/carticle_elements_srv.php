<?php
// Disable error reporting because it can break the json output
// ini_set('error_reporting', 'off');
class cArticle_Elements_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_action;
	var $m_action_result;
	var $m_articlesModel;
	var $m_tempPageView;
	var $m_elementInstanceId;
	var $m_metricType;
	var $m_elementItemId;
	var $m_articleId;
	function __construct() {
		parent::__construct();		
		$pViewPageObjectsDataArray = array ();
		$this->m_action = $this->GetValueFromRequestWithoutChecks('action');
		$this->m_action_result = array ();
		$this->m_articlesModel = new mArticles();
		$this->m_tempPageView = new pArticle_Elements_Srv();
		$this->m_elementInstanceId = (int) $this->GetValueFromRequestWithoutChecks('instance_id');
		$this->m_elementItemId = $this->GetValueFromRequestWithoutChecks('item_id'); 
		
		if (! $this->m_elementInstanceId && !$this->m_elementItemId) {
			$this->m_errCnt ++;
			$this->m_errMsg = getstr('pjs.noElementId');
		} else {
			try{
				switch ($this->m_action) {
					default :
						$this->m_errCnt ++;
						$this->m_errMsg = getstr('pjs.unrecognizedAction');
						break;
					case 'download_table_csv' :
						$this->m_metricType = (int)AOF_METRIC_TYPE_TABLE;
						$this->DownloadTableCsv();
						break;
					case 'zoom_figure' :
						$this->m_metricType = (int)AOF_METRIC_TYPE_FIGURE;
						$this->ZoomFigure();
						break;
					case 'download_figure' :
						$this->m_metricType = (int)AOF_METRIC_TYPE_FIGURE;
						$this->DownloadFigure();
						break;
					case 'download_plate' :
						$this->m_metricType = (int)AOF_METRIC_TYPE_FIGURE;
						$this->DownloadPlate();
						break;
					case 'download_suppl_file' :
						$this->m_metricType = (int)AOF_METRIC_TYPE_SUP_FILE;
						$this->DownloadSupplementaryFile();
						break;
					case 'download_pdf' :
						$this->m_metricType = (int)AOF_METRIC_TYPE_PDF;
						$this->DownloadPDF();
						break;
					case 'download_xml' :
						$this->m_metricType = (int)AOF_METRIC_TYPE_NLM_XML;
						$this->DownloadXML();
						break;
				}
			}catch(Exception $pException){
				$this->m_errCnt ++;
				$this->m_errMsg = $pException->getMessage();
			}
		}
		$this->AddJournalObjects(1);
		$lResultArr = array (
			'contents' => array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'errors',
				'err_cnt' => $this->m_errCnt,
				'err_msg' => $this->m_errMsg 
			),
		);
		// var_dump($lResultArr);
		$this->m_pageView = new pArticle_Elements_Srv(array_merge($this->m_commonObjectsDefinitions, $lResultArr));
	}
	
	function GetElementItemId(){		
		$this->m_elementItemId = $this->m_articlesModel->GetItemIdFromInstanceIdAndItemType($this->m_elementInstanceId, $this->m_metricType);
		if(!$this->m_elementItemId){
			throw new Exception(getstr('pjs.articleNoSuchElement'));
		}
		$this->m_articleId = $this->m_articlesModel->GetArticleIdFromInstanceIdAndItemType($this->m_elementInstanceId, $this->m_metricType);
	}
	
	protected function RegisterElementMetricDetail($pDetailType = AOF_METRIC_DETAIL_TYPE_DOWNLOAD){
		$this->GetElementItemId();
		$this->m_articlesModel->RegisterArticleMetricDetail($this->m_elementItemId, $this->m_metricType, $pDetailType);
	}
	
	function DownloadFigure(){
		$this->RegisterElementMetricDetail(AOF_METRIC_DETAIL_TYPE_DOWNLOAD);
		$lPicId = $this->m_articlesModel->GetFigurePicId($this->m_elementItemId);
		$lUrl = str_replace('{pic_id}', $lPicId, PWT_FIGURE_DOWNLOAD_SRV);		
		$this->Redirect($lUrl);
	}
	
	function DownloadPlate(){
		$this->RegisterElementMetricDetail(AOF_METRIC_DETAIL_TYPE_DOWNLOAD);
		$lPlatePartItemIds = $this->m_articlesModel->GetPlatePartItemIds($this->m_elementItemId);
		
		//Register dl of all the subparts
		foreach ($lPlatePartItemIds as $lPartData){
			$lPartItemId = $lPartData['id'];
			$this->m_articlesModel->RegisterArticleMetricDetail($lPartItemId, (int)AOF_METRIC_TYPE_FIGURE, (int)AOF_METRIC_DETAIL_TYPE_DOWNLOAD);
		}
				
		$lUrl = str_replace('{instance_id}', $this->m_elementInstanceId, PWT_PLATE_DOWNLOAD_SRV);
		$this->Redirect($lUrl);
	}
	
	function ZoomFigure(){
		$this->RegisterElementMetricDetail(AOF_METRIC_DETAIL_TYPE_VIEW);		
		$lArticleTitle = trim(strip_tags($this->m_articlesModel->GetArticleTitle($this->m_articleId)));
		$lUrl = str_replace('{instance_id}', $this->m_elementInstanceId, PWT_FIGURE_ZOOM_SRV);
		$lUrl = str_replace('{encoded_page_title}', urlencode($lArticleTitle), $lUrl);
		$this->Redirect($lUrl, 1, 'text/html');
	}
	
	function DownloadTableCsv(){
		$this->RegisterElementMetricDetail(AOF_METRIC_DETAIL_TYPE_DOWNLOAD);
		$lUrl = str_replace('{instance_id}', $this->m_elementInstanceId, PWT_TABLE_CSV_DOWNLOAD_SRV);	
// 		var_dump($lUrl);exit;	
		$this->Redirect($lUrl);
	}
	
	function DownloadSupplementaryFile(){
		$this->RegisterElementMetricDetail(AOF_METRIC_DETAIL_TYPE_DOWNLOAD);
		$lSupplementaryFileName = $this->m_articlesModel->GetSupplFileOriginalName($this->m_elementItemId);		
		$lUrl = str_replace('{file_name}', rawurlencode($lSupplementaryFileName), PWT_SUPPLEMENTARY_FILE_DOWNLOAD_SRV);
// 		var_dump($lUrl);
// 		exit;
		$this->Redirect($lUrl);
	}
	
	function DownloadPDF(){
		$this->m_articlesModel->RegisterArticleMetricDetail($this->m_elementItemId, $this->m_metricType, AOF_METRIC_DETAIL_TYPE_DOWNLOAD);
		$lUrl = '/lib/ajax_srv/generate_pdf.php?document_id=' . $this->m_elementItemId . '&readonly_preview=1';
		$this->Redirect($lUrl);
	}
	
	function DownloadXML(){
		// var_dump($this->m_metricType);
		// exit;
		$this->m_articlesModel->RegisterArticleMetricDetail($this->m_elementItemId, $this->m_metricType, AOF_METRIC_DETAIL_TYPE_VIEW);
		$lUrl = SITE_URL . '/generate_xml.php?document_id=' . $this->m_elementItemId;
		$this->Redirect($lUrl, 1, 'text/xml');
	}
}

?>