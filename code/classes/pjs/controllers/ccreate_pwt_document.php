<?php
/**
 * A controller used to create documents from pwt documents
 * @author peterg
 *
 */
class cCreate_Pwt_Document extends cBase_Controller {
	var $m_errCnt = 0;
	var $m_errMsgs = array();
	var $m_documentId;
	var $m_documentState;
	var $m_eventId;
	function __construct() {
		parent::__construct();
		$this->RedirectIfNotLogged();
		$pViewPageObjectsDataArray = array();
		$this->ProcessXml();


		if(!$this->m_errCnt){
			/*
			 * If there are no errors- redirect to the page with the form for
			 * the document payment/permissions
			 */
			//~ header('Location: /document_pwt_permissions.php?document_id=' . (int)$this->m_documentId);

			if($this->m_documentState == DOCUMENT_INCOMPLETE_STATE){
				$this->Redirect('/document_pwt_submission.php?document_id=' . (int)$this->m_documentId);
			}else{
				$lTaskObj = new cTask_Manager(array(
					'event_id' => (int)(int)$this->m_eventId,
				));
				$lTaskObj->Display();
				$lUrl = '/view_document.php?id=' . (int)$this->m_documentId . '&view_role=' . (int)AUTHOR_ROLE . ((int)$this->m_eventId ? '&event_id[]=' . (int)$this->m_eventId : '');
// 				var_dump($lUrl);
// 				exit;
				$this->Redirect($lUrl);
			}
			exit;
		}
		// If there are errors - we display them
		$pViewPageObjectsDataArray['contents'] = new evList_Display(array(
			'controller_data' => $this->m_errMsgs,
			'name_in_viewobject' => 'create_document_errors',
		));

		$this->m_pageView = new pCreate_Pwt_Document(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	function getDocumentXML() {
		$lDocumentId = $this->GetValueFromRequest('document_id', 'GET', 'int', false, false);
		$lDocumentXml = file_get_contents(GET_PWT_DOCUMENT_XML_URL . (int)$lDocumentId['value']);
		//var_dump($lDocumentXml);
		return $lDocumentXml;
	}

	function ProcessXml() {
		/*$lDocumentData = $this->GetValueFromRequest('document_xml', 'POST', 'xml', false, false);

		if($lDocumentData['err_cnt']){
			$this->m_errCnt = $lDocumentData['err_cnt'];
			$this->m_errMsgs = $lDocumentData['err_msgs'];
			return;
		}*/

		$lDocumentData['value'] = $this->getDocumentXML();

		// checking for errors
		if(in_array($lDocumentData['value'], array('1', '2'))) {
			$this->m_errCnt ++;

			switch($lDocumentData['value']) {
				case '1':
					$lErrorMsg = getstr('pjs.no_such_document');
					break;
				case '2':
					$lErrorMsg = getstr('pjs.empty_document');
					break;
			}

			$this->m_errMsgs[] = array(
				'err_msg' => $lErrorMsg
			);
			return;
		}

		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$lDocumentXml = $lDocumentData['value'];
		if(! $lDom->loadXML($lDocumentXml)){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => ERR_WRONG_XML
			);
			return;
		}

		$lXPath = new DOMXPath($lDom);
		$lIdQuery = '/document/@id';
		$lIdNode = $lXPath->query($lIdQuery);

		if(! $lIdNode->length){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr('pjs.xmlDoesntContainDocumentId')
			);
			return;
		}

		$lJournalIdQuery = '/document/@journal_id';
		$lJournalIdNode = $lXPath->query($lJournalIdQuery);

		$lCommentsQuery = '/document/comments';
		$lCommentsNode = $lXPath->query($lCommentsQuery);
		$lCommentsXML = '';
		if($lCommentsNode){
			$lCommentHolderNode = $lCommentsNode->item(0);
			$lCommentsXML = $lDom->saveXML($lCommentHolderNode);
			$lCommentHolderNode->parentNode->removeChild($lCommentHolderNode);
			$lDom->encoding = DEFAULT_XML_ENCODING;
			$lDocumentXml = $lDom->saveXML();
		}


		if(! $lJournalIdNode->length){
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr('pjs.xmlDoesntContainJournalId')
			);
			return;
		}
		$lJournalId = $lJournalIdNode->item(0)->nodeValue;

		$lDocumentTitle = '';
		$lDocumentTitleNode = $lXPath->query('//*[@object_id="9"]/fields/*[@id="3"]/value');
		if($lDocumentTitleNode->length){
			$lDocumentTitle = $lDocumentTitleNode->item(0)->textContent;
		}

		$lDocumentAuthorsNode = $lXPath->query('//*[@object_id="8"]');
		
		
		foreach ($lDocumentAuthorsNode as $key => $value) {
			$lDocumentAuthorsSubmissionNodeValue = $lXPath->query('./fields/*[@id="248"]/value', $value);
			if((int)$lDocumentAuthorsSubmissionNodeValue->item(0)->nodeValue == 1) {
				$lDocumentSubmissionAuthorIdNodeValue = $lXPath->query('./fields/*[@id="13"]/value', $value);
				$lDocumentSubmissionAuthorId = (int)$lDocumentSubmissionAuthorIdNodeValue->item(0)->nodeValue;
				break;
			}
		}
		
		if($lDocumentSubmissionAuthorId != $this->GetUserId()) {
			$this->m_errCnt ++;
			$this->m_errMsgs[] = array(
				'err_msg' => getstr('pjs.currentAuthorIsNotDocumentSubmissionAuthor')
			);
			return;
		}

 		//~ var_dump($lDocumentTitle);
 		//~ exit;

		$lModel = new mDocuments_Model();

		$lModelResponse = $lModel->CreateNewDocumentFromPwtDocument($lIdNode->item(0)->nodeValue, $lJournalId, $lDocumentXml, $this->GetUserId(), $lCommentsXML);

//		var_dump($lModelResponse);

		if($lModelResponse['err_cnt']){
			$this->m_errCnt ++;
			$this->m_errMsgs = $lModelResponse['err_msgs'];
			return;
		}

		$this->m_documentState = $lModelResponse['document_state'];
		$this->m_documentId = (int) $lModelResponse['document_id'];
		$this->m_eventId = (int) $lModelResponse['event_id'];
	}
}

?>