<?php

class cGenerate_PDF_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();

		$lPreviewController = new cPreview_Ajax_Srv(1);
		$lDocumentPreview = $lPreviewController->m_action_result['preview'];
		
		$lVersionId = (int)$this->GetValueFromRequestWithoutChecks('version_id');
		
		$lVersionModel = new mVersions();
		$lDocumentId = $lVersionModel->GetVersionDocumentPjsId($lVersionId);
		
		$lDocumentModel = new mDocuments_Model();
		$lDocumentData = $lDocumentModel->GetDocumentInfoForPDF($lDocumentId);
		//var_dump($lDocumentData);
		$lViewPageObjectsDataArray['contents'] = new evSimple_Block_Display(array(
			'controller_data' => '',
			'name_in_viewobject' => 'generate_pdf',
			'content' => $lDocumentPreview,
			'document_title' => $lDocumentData['document_title'],
			'document_id' => $lDocumentData['document_id'],
			'author_list' => $lDocumentData['author_list'],
			'document_type_name' => $lDocumentData['document_type_name'],
		));
		
		$this->m_pageView = new pGenerate_PDF_Page_View(array_merge($this->m_commonObjectsDefinitions, $lViewPageObjectsDataArray));
	}

	
/*	function head_JS_files(){
		return array(	'js/jquery', 
						'js/jquery_ui', 
						'js/jquery.tinyscrollbar.min',
						'js/jquery.dynatree.min', 
						'js/jquery.simplemodal', 
						'js/jquery_form', 
						'js/jquery.tokeninput', 
						'js/jquery.dragsort', 
						'js/ajaxupload.3.5', 
						'js/def', 
						//'ckeditor/ckeditor', 
						//'ckeditor/adapters/jquery', 
						);
	}
*/
}

?>