<?php
class cpjs_document_importer{
	var $m_xml;
	var $m_errCnt;
	var $m_errMsg;
	var $m_documentId;
	var $m_XmlDom;
	var $m_Xpath;
	var $m_metadataIsFetched = false;
	var $m_con;
	var $m_uid;
	var $m_xsd;
	var $m_commentsXml;


	function __construct($pDataArr){
		$this->m_documentId = $pDataArr['document_id'];
		$this->m_xml = $pDataArr['xml'];
		$this->m_uid = $pDataArr['uid'];

		$this->m_con = new DBCn();
		$this->m_con->Open();

// 		error_reporting(-1);
		if(!$this->m_documentId){
			$this->SetError(getstr('pwt.pjsImportNoDocumentId'));
			return;
		}

		$this->m_XmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		if(!$this->m_XmlDom->loadXML($this->m_xml)){
			$this->SetError(getstr('pwt.pjsImportWrongXml'));
		}

	}

	/**
	 * Fetch metadata from the xml (e.g. the document title)
	 */
	function FetchMetadata(){
		if($this->m_metadataIsFetched || $this->m_errCnt){
			return;
		}
		//Get the comments xml and remove them from the xml
		$lXPath = new DOMXPath($this->m_XmlDom);
		$lCommentsNodeQuery = '/document/comments';
		$lCommentsHolderNodes = $lXPath->query($lCommentsNodeQuery);
		if($lCommentsHolderNodes->length){
			$lCommentsHolderNode = $lCommentsHolderNodes->item(0);
// 			trigger_error('IMP:' . $this->m_XmlDom->saveXML($lCommentsHolderNode), E_USER_NOTICE);
			$this->m_commentsXml = $this->m_XmlDom->saveXML($lCommentsHolderNode);
			$lCommentsHolderNode->parentNode->removeChild($lCommentsHolderNode);
		}
// 		trigger_error('IMP:' . $this->m_commentsXml, E_USER_NOTICE);

		$this->m_metadataIsFetched = true;
	}



	/**
	 * Import the document in the database.
	 *
	 * First the document is validated. If the validation is successful then
	 * the xml is passed to a db function that creates a new version and updates the fields of
	 * the objects.
	 */
	function ImportDocument(){
		$this->FetchMetadata();
		$this->GenerateXSD();

		if($this->m_errCnt){
			return false;
		}

// 		if(!$this->m_XmlDom->schemaValidateSource($this->m_xsd)){
// 			$this->SetError(getstr('pwt.pjsImportInvalidXml'));
// 			return false;
// 		}

		$lSql = 'SELECT *
			FROM spImportPjsDocumentVersion(' . (int) $this->m_documentId . ', \'' . q($this->m_xml) . '\', \'' . q($this->m_commentsXml) . '\', ' . (int)$this->m_uid . ' );';
// 		var_dump($lSql);
		if(!$this->m_con->Execute($lSql)){
			$this->SetError(getstr($this->m_con->GetLastError()));
			return false;
		}
		return true;
	}

	function GenerateXSD(){
		if($this->m_errCnt){
			return;
		}
		$lXsdGenerator = new cdocument_xsd_generator(array(
			'document_id' => $this->m_documentId,
		));
		$lXsdGenerator->generateDocumentSchema();
		$this->m_xsd = $lXsdGenerator->getXml();
	}

	function SetError($pErrorMsg){
		$this->m_errCnt++;
		$this->m_errMsg = $pErrorMsg;
	}

	function GetErrCnt(){
		return $this->m_errCnt;
	}

	function GetErrMsg(){
		return $this->m_errMsg;
	}
}

?>