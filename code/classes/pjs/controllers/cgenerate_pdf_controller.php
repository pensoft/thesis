<?php

class cGenerate_PDF_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();

		$lPreviewController = new cPreview_Ajax_Srv(1);
		$lDocumentPreview = $lPreviewController->m_action_result['preview'];
		//var_dump($lDocumentPreview);
		$lDocumentPreview = $this->FixDocumentPreview($lDocumentPreview);
		// var_dump($lDocumentPreview);
		// exit;
		$lVersionId = (int)$this->GetValueFromRequestWithoutChecks('version_id');
		$lDocumentId = (int)$this->GetValueFromRequestWithoutChecks('document_id');

		$lVersionModel = new mVersions();
		//$lDocumentId = $lVersionModel->GetVersionDocumentPjsId($lVersionId);

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
			'author_list_short' => $lDocumentData['author_list_short'],
			'document_type_name' => $lDocumentData['document_type_name'],
			'doi' => $lDocumentData['doi'],
			'idtext' => $lDocumentData['idtext'],
		));

		$this->m_pageView = new pGenerate_PDF_Page_View(array_merge($this->m_commonObjectsDefinitions, $lViewPageObjectsDataArray));
	}

	protected function FixDocumentPreview($pPreview){
		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$lPreview = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />


</head>
<body>
		<div id="PDF-Preview-Holder">' . $pPreview . '</div></body></html>';
		if(!@$lDom->loadHTML($lPreview)){
			return $pPreview;
		}
		$lXPath = new DOMXPath($lDom);
		$this->MoveFieldLabelNodes($lXPath);
		$this->StripUnnecessaryContent($lXPath);
		$lHolderNode = $lXPath->query('//div[@class="P-Article-Preview"]');
		if($lHolderNode->length){
			return $lDom->saveHTML($lHolderNode->item(0));
		}
		return $pPreview;
	}

	protected function MoveFieldLabelNodes($pXPath){
		$lXPath = $pXPath;
		$lNodesToMove = $lXPath->query('//span[@class="fieldLabel"]|//div[@class="fieldLabel"]');
		foreach ($lNodesToMove as $lLabel) {
			$lCurrentNode = $lLabel->nextSibling;
			while($lCurrentNode){
				if($lCurrentNode->nodeType == 1){
					break;
				}
				if($lCurrentNode->nodeType == 3 && trim($lCurrentNode->textContent)){
					break 2;
				}
				$lCurrentNode = $lCurrentNode->nextSibling;
			}
			if(!$lCurrentNode || $lCurrentNode->nodeType != 1
			 || $lCurrentNode->nodeName != 'div' || ($lCurrentNode->getAttribute('class') != 'P-Inline' && $lCurrentNode->getAttribute('class') != 'fieldValue')){
				continue;
			}
			 //var_dump('a');
			$lFirstParagraph = $lXPath->query('.//p', $lCurrentNode);
			$lReplacementNode = $lLabel->ownerDocument->createElement('span');
			$lReplacementNode->appendChild($lLabel->ownerDocument->createTextNode($lLabel->textContent));
			$lReplacementNode->setAttribute('class', 'inlineFieldLabel');
			$lLabel->parentNode->removeChild($lLabel);
			if(!$lFirstParagraph->length){
				$lCurrentNode->insertBefore($lReplacementNode, $lCurrentNode->firstChild);
				continue;
			}
			$lLabel->setAttribute('class', '');
			$lPNode = $lFirstParagraph->item(0);
			// var_dump($lPNode->ownerDocument->SaveHTML($lPNode));
			 $lPNode->insertBefore($lReplacementNode, $lPNode->firstChild);
		}
	}

	protected function StripUnnecessaryContent($pXPath){
		$this->StripReferenceSpans($pXPath);
		$this->StripXRefs($pXPath);
	}

	protected function StripReferenceSpans($pXPath){
		$this->StripNodes($pXPath, '//li[@class="ref-list-AOF-holder"]//span');
	}

	protected function StripXRefs($pXPath){
		$this->StripNodes($pXPath, '//xref');
	}

	protected function StripNodes($pXPath, $pXPathQuery){
		$lNodes = $pXPath->query($pXPathQuery);
		foreach ($lNodes as $lCurrentNode) {
			while($lCurrentNode->firstChild){
				$lCurrentNode->parentNode->insertBefore($lCurrentNode->firstChild, $lCurrentNode);
			}
			$lCurrentNode->parentNode->removeChild($lCurrentNode);
		}
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