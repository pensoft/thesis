<?php

class cGenerate_PDF_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();

		$lPreviewController = new cPreview_Ajax_Srv();
		$lDocumentPreview = $lPreviewController->m_action_result['preview'];
		
		$lViewPageObjectsDataArray['contents'] = new evSimple_Block_Display(array(
			'controller_data' => '',
			'name_in_viewobject' => 'generate_pdf',
			'content' => $lDocumentPreview,
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