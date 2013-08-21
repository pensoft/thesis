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
	var $m_versionModel;
	var $m_mode = 0;
	
	/**
	 * @param $pMode - if this preview is used for PDF or for HTML view
	 * $pMode = 0 - HTML
	 * $pMode = 1 - PDF
	 * 
	 * */
	function __construct($pMode = 0) {
		parent::__construct();
		$this->m_mode = $pMode;
		if(!$this->m_mode)
			$this->RedirectIfNotLogged();

		$pViewPageObjectsDataArray = array();
		$this->m_versionId = (int)$this->GetValueFromRequestWithoutChecks('version_id');
		
		$this->m_ReadOnlyPreview = 0;

		$this->m_versionModel = new mVersions();
		$lIsReadonly = $this->m_versionModel->CheckIfVersionIsReadonly($this->m_versionId);		
		if($lIsReadonly) {
			$this->m_ReadOnlyPreview = 1;
		}
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
		
		$lVersionUidChanges = $this->m_versionModel->GetVersionPwtChangeUserIds($this->m_versionId);
		$pViewPageObjectsDataArray = array();
		$pViewPageObjectsDataArray['users_with_changes'] = $lVersionUidChanges;
		global $user;		
		if($this->m_errCnt){
			$pViewPageObjectsDataArray['contents'] = array(
					'ctype' => 'evSimple_Block_Display',
					'name_in_viewobject' => 'preview_with_error',
					'err_msg' => $this->m_errMsgs[0]['err_msg'],
			);
		}else{
			$pViewPageObjectsDataArray['contents'] = array(
					'ctype' => 'evSimple_Block_Display',
					'name_in_viewobject' => 'preview',							
					'preview' => $this->m_action_result['preview'],
					'current_user_id' => $user->id,
					'current_user_name' => $user->fullname,
					'version_id' => $this->m_versionId,
			);
		}		
		
		$this->m_pageView = new pPreview_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));		
	}

	function GetDocumentPreview(){
// 		var_dump($this->m_versionXml);
		$lQueryResult = executeExternalQuery(
			PWT_VERSION_PREVIEW_URL, 
			array(
				'document_id' => $this->m_documentPwtId, 
				'xml' => $this->m_versionXml, 
				'readonly_preview' => $this->m_ReadOnlyPreview,
				'pdf_preview' => (int)$this->m_mode
			)
		);
		$lQueryResult = json_decode($lQueryResult, true);
// 		var_dump($lQueryResult['preview']);

		if(!is_array($lQueryResult) || $lQueryResult['err_cnt'] || $lQueryResult['preview'] == ''){
			$this->m_errCnt++;
			$this->m_errMsgs[] = array('err_msg' => getstr('pjs.couldNotLoadXmlPreview'));
			return;
		}
		$this->m_action_result['preview'] = $lQueryResult['preview'];
	}

}

?>