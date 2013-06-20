<?php
// Disable error reporting because it can break the json output
//ini_set('error_reporting', 'off');

class cPreview_Ajax_Srv extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsgs = array();
	var $m_action;
	var $m_action_result;
	var $m_eventsParamString = '';
	var $m_versionXml = '';
	var $m_versionId;
	var $m_documentPwtId;
	var $m_ReadOnlyPreview;

	function __construct() {
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$this->m_versionId = (int)$this->GetValueFromRequestWithoutChecks('version_id');
		$this->m_ReadOnlyPreview = (int)$this->GetValueFromRequestWithoutChecks('readonly_preview');

		$this->m_versionModel = new mVersions();
		$this->m_documentPwtId = $this->m_versionModel->GetVersionDocumentPwtId($this->m_versionId);

		try{
			if($this->m_versionModel->CheckIfVersionHasUnprocessedPwtChanges($this->m_versionId)){
				$lProcession = $this->m_versionModel->ProcessVersionPwtChanges($this->m_versionId);

				if($lProcession['err_cnt']){
					throw  new Exception($lProcession['err_msgs'][0]['err_msg']);
				}
			}
			$lXmlData = $this->m_versionModel->GetVersionPwtXml($this->m_versionId);
// 			var_dump($lXmlData);
			if($lXmlData['err_cnt']){
				throw new Exception($lProcession['err_msgs'][0]['err_msg']);
			}
			$this->m_versionXml = $lXmlData['xml'];
			if(!$lXmlData['is_cached']){
				$lCommentsModel = new mComments();
				$lInstanceComments = $lCommentsModel->GetVersionInstanceComments($this->m_versionId);
				$this->m_versionXml = InsertDocumentCommentPositionNodes($this->m_versionXml, $lInstanceComments);
			}
// 			var_dump($this->m_versionXml);

		}catch(Exception $pException){
			$this->m_errCnt++;
			$this->m_errMsgs[] = array('err_msg' => $pException->getMessage());
		}

		$this->GetDocumentPreview();

		$lResultArr = array_merge($this->m_action_result, array(
			'err_cnt' => $this->m_errCnt,
			'err_msgs' => $this->m_errMsgs,
			'url_params' => $this->m_eventsParamString,
		));
		$this->m_pageView = new epPage_Json_View($lResultArr);
	}

	function GetDocumentPreview(){

		$lQueryResult = executeExternalQuery(PWT_VERSION_PREVIEW_URL, array('document_id' => $this->m_documentPwtId, 'xml' => $this->m_versionXml, 'readonly_preview' => $this->m_ReadOnlyPreview));
		$lQueryResult = json_decode($lQueryResult, true);

		if(!is_array($lQueryResult) || $lQueryResult['err_cnt'] || $lQueryResult['preview'] == ''){
			$this->m_errCnt++;
			$this->m_errMsgs[] = array('err_msg' => getstr('pjs.couldNotLoadXmlPreview'));
			return;
		}
		$this->m_action_result['preview'] = $lQueryResult['preview'];
	}

}

?>