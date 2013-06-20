<?php
/**
	Този клас ще реализира апи-то
*/
class capi{
	var $m_xml;
	var $m_errCnt;
	var $m_errMsg;
	var $m_resultArray;
	var $m_getData;
	var $m_action;
	var $m_templateObjectDetails;
	var $m_templateId;
	var $m_con;
	var $m_documentId;
	var $m_uid;
	var $m_submittingAuthorUid;
	var $m_username;
	var $m_password;
	var $m_journalId;
	var $m_previousSessionUser;
	var $m_previuosUser;
	var $m_XmlDom;
	var $m_Xpath;
	var $m_documentName;
	function __construct($pFieldTempl){

		$this->m_xml = $pFieldTempl['xml'];
		$this->m_username = $pFieldTempl['username'];
		$this->m_password = $pFieldTempl['password'];
		$this->m_errCnt = 0;
		$this->m_errMsg = '';
		$this->m_resultArray = array();
		$this->m_getData = true;
		$this->m_action = $pFieldTempl['action'];
		$this->m_templateObjectDetails = array();
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_previousSessionUser = $_SESSION['suser'];

		global $user;
		$this->m_previuosUser = $user;

		$this->m_XmlDom = new DOMDocument('1.0', 'UTF-8');
		$this->m_Xpath = '';
		$this->m_documentName = DEFAULT_DOCUMENT_NAME;
	}

	function Authenticate(){
// 		$lSql = 'SELECT * FROM sitelogin(\'' . q($this->m_username)  . '\', \'' . q($this->m_password) . '\', \'' . q($_SERVER['REMOTE_ADDR']) . '\')';
// 		$this->ExecuteSqlStatement($lSql);
// 		$this->m_uid = (int)$this->m_con->mRs['id'];
		global $user;
		$user = new clogin($this->m_username, $this->m_password, $_SERVER['REMOTE_ADDR']);
// 		$_SESSION['suser'] = serialize($user);
		if ($user->state != 1) {
			$this->SetError(getstr('pwt.api.noSuchUser'));
		}
		$this->m_uid = $user->id;
		if(!$this->m_uid){
			$this->SetError(getstr('pwt.api.noSuchUser'));
		}

	}

	function ProcessDocumentSubmit(){
		$this->BaseValidateXml();
		$this->ImportDocument();
	}

	/**
	 * Тук ще импортираме получения xml в базата
	 */
	function ImportDocument(){

		//Правим всичко в 1 транзакция - ако стане проблем ще rollback-нем
		$this->ExecuteSqlStatement('BEGIN TRANSACTION;');

// 		$this->SetError(getstr('pwt.api.xmlIsValid'));

		if(!$this->m_XmlDom->loadXML($this->m_xml)){
			$this->SetError(getstr('pwt.api.xmlIsInvalid'));
		}
		$this->m_Xpath = new DOMXPath($this->m_XmlDom);

		// Подготвяме XML-а за импорт
		$this->PrepareXmlForImport();

		//Слагаме имената на възлите като техни атрибути понеже xml парсинга на Postgresql е непълен и не може автоматично да се вземе името на текущия възел
		//Слагаме и индексите понеже в postres-а няма релативни възли в xpath-а и не можем да го сметнем
		$lObjectsQuery = '/document/objects//*[count(ancestor-or-self::fields) = 0]';
		$lObjects = $this->m_Xpath->query($lObjectsQuery);
		for($i = 0; $i < $lObjects->length; ++$i){
			$lCurrentObject = $lObjects->item($i);
			$lCurrentObject->setAttribute('is_object', 1);
			$lPreviousObjectsOfTheSameType = $this->m_Xpath->evaluate('count(./preceding-sibling::' . $lCurrentObject->nodeName . ')', $lCurrentObject);
			$lCurrentObject->setAttribute('object_idx', $lPreviousObjectsOfTheSameType + 1);
			$lCurrentObject->setAttribute('node_name', $lCurrentObject->nodeName);
		}

		$lFieldsQuery = '/document/objects//fields/*';
		$lFields = $this->m_Xpath->query($lFieldsQuery);
		for($i = 0; $i < $lFields->length; ++$i){
			$lCurrentField = $lFields->item($i);
			$lCurrentField->setAttribute('is_field', 1);
			$lCurrentField->setAttribute('node_name', $lCurrentField->nodeName);
		}


// 		var_dump($lXPath->query('/document/objects//author[fields/submitting_author[value="1"]]')->length);
		$lAuthors = $this->m_Xpath->query('/document/objects//author');
		$lSubmittingAuthor = null;
		for($i = 0; $i < $lAuthors->length; ++$i){
			$lSubmittingAuthorValNode = $this->m_Xpath->query('./fields/submitting_author/value', $lAuthors->item($i));
			if($lSubmittingAuthorValNode->length && (int)$lSubmittingAuthorValNode->item(0)->nodeValue == 1){
				$lSubmittingAuthor = $lAuthors->item($i);
				break;
			}
		}

		if(!$lSubmittingAuthor){
			$this->SetError(getstr('pwt.api.xmlIsInvalid') . getstr('pwt.api.noSubmittingAuthor'));
		}
		$lSubmittingAuthorData = array();
		$lSubmittingAuthorFields = array(
			'email' => './fields/e-mail/value',
			'salutation' => './fields/salutation/value',
			'first_name' => './fields/first_name/value',
			'middle_name' => './fields/middle_name/value',
			'last_name' => './fields/last_name/value',
			'aff' => './fields/secondary_address[1]/fields/affiliation/value',
			'city' => './fields/secondary_address[1]/fields/city/value',
			'country' => './fields/secondary_address[1]/fields/country/value',
		);
		foreach ($lSubmittingAuthorFields as $lFieldName => $lXPathQuery) {
			$lNodes = $this->m_Xpath->query($lXPathQuery, $lSubmittingAuthor);
// 			var_dump($lNodes, $lXPathQuery);
			if($lNodes->length){
				$lSubmittingAuthorData[$lFieldName] = trim($lNodes->item(0)->nodeValue);
			}
		}

// 		var_dump($lSubmittingAuthorData);
		if(!$lSubmittingAuthorData['email']){
			$this->SetError(getstr('pwt.api.xmlIsInvalid') . getstr('pwt.api.submittingAuthorDoesNotHaveEmail'));
		}

		// Document Name
		$lDocumentNameNode = $this->m_Xpath->query('/document/objects/article_metadata/title_and_authors/fields/title/value');
		$this->m_documentName = $lDocumentNameNode->item(0)->textContent;

		//~ print_r($this->m_XmlDom->saveXML());
		//~ exit;


// 		var_dump('SELECT * FROM pwt.spCreateUsrForAuthorApi(
// 			\'' . q($lSubmittingAuthorData['email']) . '\',
// 			\'' . q($lSubmittingAuthorData['salutation']) . '\',
// 			\'' . q($lSubmittingAuthorData['first_name']) . '\',
// 			\'' . q($lSubmittingAuthorData['middle_name']) . '\',
// 			\'' . q($lSubmittingAuthorData['last_name']) . '\',
// 			\'' . q($lSubmittingAuthorData['aff']) . '\',
// 			\'' . q($lSubmittingAuthorData['city']) . '\',
// 			\'' . q($lSubmittingAuthorData['country']) . '\'
// 		)');
		$this->ExecuteSqlStatement('SELECT * FROM pwt.spCreateUsrForAuthorApi(
			\'' . q($lSubmittingAuthorData['email']) . '\',
			\'' . q($lSubmittingAuthorData['salutation']) . '\',
			\'' . q($lSubmittingAuthorData['first_name']) . '\',
			\'' . q($lSubmittingAuthorData['middle_name']) . '\',
			\'' . q($lSubmittingAuthorData['last_name']) . '\',
			\'' . q($lSubmittingAuthorData['aff']) . '\',
			\'' . q($lSubmittingAuthorData['city']) . '\',
			\'' . q($lSubmittingAuthorData['country']) . '\'
		)');

		$this->m_submittingAuthorUid = (int)$this->m_con->mRs['uid'];

		if(!$this->m_submittingAuthorUid){
			$this->SetError(getstr('pwt.api.couldNotCreateUserForSubmittingAuthor'));
		}
// 		var_dump($this->m_con->mRs['user_exists']);
		if(!(int)$this->m_con->mRs['user_exists']){
			//~ sendMailToAuthorApiRegister($this->m_con->mRs['email'], $this->m_con->mRs['upass'], $this->m_con->mRs['fullname']);
		}




		for($i = 0; $i < $lFields->length; ++$i){
			$lCurrentField = $lFields->item($i);
			$lCurrentField->setAttribute('is_field', 1);
			$lCurrentField->setAttribute('node_name', $lCurrentField->nodeName);
		}

		$this->ExecuteSqlStatement('SELECT * FROM pwt.spCreateDocumentFromApi(
			' . (int)$this->m_templateId . ',
			\'' . q($this->m_documentName) . '\',
			' . (int)DEFAULT_DOCUMENT_PAPER_TYPE . ',
			' . (int)$this->m_journalId . ',
			' . (int)$this->m_submittingAuthorUid . ',
			' . (int)$this->m_uid . '
		)');


		$this->m_documentId = (int)$this->m_con->mRs['id'];

		$this->ExecuteSqlStatement('SELECT * FROM spImportDocumentFromExternalXml(
			' . (int)$this->m_documentId . ',
			\'' . q($this->m_XmlDom->saveXML()) . '\',
			' . $this->m_submittingAuthorUid . '
		)');


		$this->m_resultArray['document_id'] = $this->m_documentId;
		$this->m_resultArray['document_link'] = DOCUMENT_VIEW_LINK . $this->m_documentId;

		//Трябва да импортнем фигурите
		$lFiguresQuery = '/document/figures/figure';
		$lFigures = $this->m_Xpath->query($lFiguresQuery);
		for($i = 0; $i < $lFigures->length; ++$i){
			$lCurrentFig = $lFigures->item($i);
			$this->ImportSingleFig($lCurrentFig, $this->m_Xpath);
		}

		//Трябва да импортнем таблиците
		$lTablesQuery = '/document/tables/table';
		$lTables = $this->m_Xpath->query($lTablesQuery);
		for($i = 0; $i < $lTables->length; ++$i){
			$lCurrentTable = $lTables->item($i);
			$this->ImportSingleTable($lCurrentTable, $this->m_Xpath);
		}

		$this->ExecuteSqlStatement('COMMIT TRANSACTION;');

		// Трябва да изпълним after save php екшъните
		$this->ExecuteSqlStatement('
			SELECT DISTINCT ON (i.pos, i.id) i.id
			FROM pwt.document_object_instances i
			JOIN pwt.object_actions oa ON oa.object_id = i.object_id
			WHERE i.document_id = ' . (int)$this->m_documentId . '
			AND oa.pos = ' . (int)ACTION_AFTER_SAVE_POS . '
			ORDER BY i.pos DESC
			');
		$lAdditionalParameters = array(
			'try_to_change_user_without_session_change' => 1,
			'username' => $this->m_username,
			'password' => $this->m_password,
		);
		while(!$this->m_con->Eof()){
			$lInstanceId = $this->m_con->mRs['id'];

			performInstanceSaveActions($lInstanceId, $lAdditionalParameters);
			$this->m_con->MoveNext();
		}
		//echo 2;
// 		$this->ExecuteSqlStatement('SELECT * FROM pwt.spUpdateDocumentSubmittingAuthor(' . $this->m_documentId . ')');
	}

	/**
	 *
	 * @param DomNode $pFigNode
	 * @param DOMXPath $pXPath
	 */
	private function ImportSingleFig(&$pFigNode, &$pXPath){
		$lCaption = '';
		$lUrl = '';
		$lCaptionNode = $pXPath->query('./caption', $pFigNode);
		if($lCaptionNode->length){
			$lCaption = $lCaptionNode->item(0)->nodeValue;
		}
		$lIsPlate = false;
		$lPlateId = 0;
		$lPlateType = 0;
		if($pFigNode->getAttribute('is_plate')){
			$lIsPlate = true;
			$lPlateType = $pFigNode->getAttribute('type');
		}

		$lUrlNode = $pXPath->query('./url', $pFigNode);

		for($i = 0; $i < $lUrlNode->length; ++$i){
			$lUrl = $lUrlNode->item($i)->textContent;
			$lFollowingSibling = $pXPath->query('./following-sibling::*[1]', $lUrlNode->item($i));
			$lPhotoDesc = '';
			if($lFollowingSibling->length){
				$lFollowingSiblingNode = $lFollowingSibling->item(0);
				if($lFollowingSiblingNode->nodeName == 'photo_description'){
					$lPhotoDesc = $lFollowingSiblingNode->textContent;
				}
			}

			$lFigSql = 'SELECT * FROM pwt.spuploadfigurephoto(1, null, ' . (int)$this->m_documentId . ', ' . (int)$lPlateId . ', \'' . q($lCaption) . '\', \'' . q($lPhotoDesc	) . '\'
			, ' . (int)$this->m_submittingAuthorUid . ', null, 0, ' . (int)$lPlateType . ')';
			$this->ExecuteSqlStatement($lFigSql);
			$lFigId = (int)$this->m_con->mRs['photo_id'];
			if($lPlateId == 0){
				$lPlateId = (int)$this->m_con->mRs['plate_id'];
			}
			if($lUrl){
				$lPicContent = file_get_contents($lUrl);
				$lPicFileName = PATH_PWT_DL . 'oo_' . $lFigId . '.jpg';
				$lBigPicFileName = PATH_PWT_DL . 'big_' . $lFigId . '.jpg';
				if($lPicContent === false || !file_put_contents($lPicFileName, $lPicContent)){
					$this->SetError(getstr('pwt.api.couldNotDownloadFigPhoto') . ' ' . $lUrl);
				}
				exec("convert -colorspace rgb -quality 80 -thumbnail " . escapeshellarg('1024x1024>') . " " . $lPicFileName . " " . $lBigPicFileName );
			}
		}
	}

	/**
	 *
	 * @param DomNode $pFigNode
	 * @param DOMXPath $pXPath
	 */
	private function ImportSingleTable(&$pTableNode, &$pXPath){
		$lCaption = '';
		$lContent = '';
		$lTableId = 0;

		$lCaptionNode = $pXPath->query('./caption', $pTableNode);
		$lContentNode = $pXPath->query('./content/value', $pTableNode);

		if($lCaptionNode->length){

			$lHtml = new DOMDocument();

			foreach($lCaptionNode as $node) {

				foreach($node->childNodes as $child) {
					$lHtml->appendChild($lHtml->importNode($child, true));
				}
			}

			$lCaption = $lHtml->saveHTML();

		}
		if($lContentNode->length){

			$lHtml = new DOMDocument();

			foreach($lContentNode as $node) {

				foreach($node->childNodes as $child) {
					$lHtml->appendChild($lHtml->importNode($child, true));
				}
			}

			$lContent = $lHtml->saveHTML();
		}

		$lTableId = (int)$pTableNode->getAttribute('id');


		$lTableSql = 'SELECT * FROM pwt.spsavetabledata(1, null, ' . (int)$this->m_documentId . ', \'' . q($lCaption) . '\', \'' . q($lContent) . '\', ' . (int)$this->m_submittingAuthorUid . ')';

		$this->ExecuteSqlStatement($lTableSql);
	}


	private function ExecuteSqlStatement($pSql, $pSetError = true){
		if(!$this->m_con->Execute($pSql)){
			if($pSetError){
				$this->SetError($this->m_con->GetLastError());
			}
		}

	}

	private function SetError($pErrorMsg){
		$this->m_con->Execute('ROLLBACK;');
		throw new Exception($pErrorMsg);
	}

	private function LogAuthenticate($pLoginIsSuccessful = false, $pErrMsg = ''){
		$lSql = 'SELECT * FROM pwt.spApiLogAuthenticate(\''. $this->m_username . '\', \'' . q($this->m_password) . '\', '
		. (int)$pLoginIsSuccessful . ', \'' . q($pErrMsg) . '\', \'' . q($_SERVER['REMOTE_ADDR']) . '\')';
		$this->ExecuteSqlStatement($lSql, FALSE);
	}

	private function LogDocumentProcessing($pProcessingIsSuccessful = false, $pErrMsg = ''){
		$lSql = 'SELECT * FROM pwt.spApiLogDocumentProcessing('. (int) $this->m_uid . ', \'' . q($this->m_xml) . '\', '
				. (int)$pProcessingIsSuccessful . ', \'' . q($pErrMsg) . '\')';
		$this->ExecuteSqlStatement($lSql, FALSE);
	}

	/**
	 * Тук ще правим базова валидация на xml-a - ще гледаме само
	 * дали структурата на xml-а е ОК. Ще допускаме задължителни полета да са празни.
	 */
	protected function BaseValidateXml(){
		if(!$this->m_xml){
			throw new Exception(getstr('pwt.api.xmlIsRequired'));
		}
		function libxml_display_error($pError) {
			$lResult = "\n";
			switch ($pError->level) {
				case LIBXML_ERR_WARNING :
					$lResult .= "Warning $pError->code: ";
					break;
				case LIBXML_ERR_ERROR :
					$lResult .= "Error $pError->code on line $pError->line: ";
					break;
				case LIBXML_ERR_FATAL :
					$lResult .= "Fatal Error $pError->code: ";
					break;
			}
			$lResult .= trim($pError->message);
			$lResult .= " on line $pError->line";

			return $lResult;
		}

		function libxml_display_errors() {
			$lErrors = libxml_get_errors();
			$lResult = '';
			foreach($lErrors as $lCurrentError){
				$lResult .= libxml_display_error($lCurrentError);
			}
			libxml_clear_errors();
			return $lResult;
		}

		libxml_use_internal_errors(1);
		$lXmlDom = new DOMDocument('1.0', 'UTF-8');

		if(!$lXmlDom->loadXML($this->m_xml)){
			$this->m_resultArray['xml_error'] = libxml_display_errors();
			throw new Exception(getstr('pwt.api.xmlIsInvalid') . $this->m_resultArray['xml_error']);
		}

		$lXPath = new DOMXPath($lXmlDom);
		$lTemplateIdNode = $lXPath->query('/document/document_info/document_type/@id');

		if(!$lTemplateIdNode->length || !(int)$lTemplateIdNode->item(0)->textContent){
			throw new Exception(getstr('pwt.api.noDocumentTypeSpecified'));
		}
		$lTemplateId = (int)$lTemplateIdNode->item(0)->textContent;
		$this->m_templateId = $lTemplateId;

		$lJournalIdNode = $lXPath->query('/document/document_info/journal_name/@id');

		if(!$lJournalIdNode->length || !(int)$lJournalIdNode->item(0)->textContent){
			throw new Exception(getstr('pwt.api.noJoournalIdSpecified'));
		}
		$this->m_journalId = (int)$lJournalIdNode->item(0)->textContent;

		$lTemplateXsdGenerator = new ctemplate_xsd_generator(array(
			'template_id' => $lTemplateId,
			'mode' => 2,
		));

		$lTemplateXsdGenerator->GetData();
		$lTemplateXsd = $lTemplateXsdGenerator->getXml();
// 		file_put_contents('./1.xsd', $lTemplateXsd);
// 		var_dump($lTemplateXsd);//

 		//~ print_r($lTemplateXsd);
		//~ exit;
		if(!$lXmlDom->schemaValidateSource($lTemplateXsd)){

			$this->m_resultArray['xml_error'] = libxml_display_errors();
// 			var_dump($this->m_resultArray['xml_error']);
// 			var_dump($this->m_resultArray);
			throw new Exception(getstr('pwt.api.xmlIsInvalid') . $this->m_resultArray['xml_error']);
		}


// 		exit;

	}

	function GetData(){
		if(!$this->m_getData){
			return;
		}
		$this->m_getData = false;
		$lAction = 'authenticate';
		try{
			switch($this->m_action){
				default:
					throw new Exception(getstr('pwt.api.unknownAction'));
				case 'authenticate':{
					$this->Authenticate();
					$this->LogAuthenticate(true);
					break;
				}
				case 'process_document':{
					$this->Authenticate();
					$this->LogAuthenticate(true);
					$lAction = 'process_document';
					$this->ProcessDocumentSubmit();
					$this->LogDocumentProcessing(true);
					break;
				}

			}
		}catch(Exception $pException){
			$this->m_errCnt++;
			//var_dump($pException);
			switch($lAction){
				case 'authenticate':{
					$this->LogAuthenticate(false, $pException->getMessage());
					break;
				}
				case 'process_document':{
					$this->LogDocumentProcessing(false, $pException->getMessage());
					break;
				}

			}
			$this->m_errMsg .= $pException->getMessage();
		}

// 		$_SESSION['suser'] = $this->m_previousSessionUser;
		global $user;
		$user = $this->m_previuosUser;
		//var_dump($this->m_previousSessionUser, $_SESSION['suser']);
	}

	function GetResult(){
		$this->GetData();
		$lResult = new DOMDocument('1.0', 'UTF-8');
		$lRootNode = $lResult->appendChild($lResult->createElement('result'));
		libxml_use_internal_errors(0);
		if($this->m_errCnt){
			$lRootNode->appendChild($lResult->createElement('returnCode', 1));
			$lRootNode->appendChild($lResult->createElement('errorCount', $this->m_errCnt));
			$lRootNode->appendChild($lResult->createElement('errorMsg', $this->m_errMsg));
		}else{
			$lRootNode->appendChild($lResult->createElement('returnCode', 0));
			foreach ($this->m_resultArray as $lKey => $lValue){
				$lRootNode->appendChild($lResult->createElement($lKey, $lValue));
			}
		}
// 		var_dump($lResult->saveXML());
		return $lResult->saveXML();

	}

	/*
		Preparing xml for import
	*/

	function PrepareXmlForImport() {

		$this->PrepareReferencesForImport();
		$this->PrepareTaxonTreatmentsForImport();

	}

	/*
		Materials for import
	*/

	function PrepareTaxonTreatmentsForImport() {
		$lTreatmentsQuery = '/document/objects//systematics/treatment';

		$lNodes = $this->m_Xpath->query($lTreatmentsQuery);

		// Добавяме атрибути на field-овете понеже се ориентираме по тях по-нататък

		$lFieldsQuery = '/document/objects//systematics/treatment//fields/*';
		$lFields = $this->m_Xpath->query($lFieldsQuery);
		for($i = 0; $i < $lFields->length; ++$i){
			$lCurrentField = $lFields->item($i);
			$lCurrentField->setAttribute('is_field', 1);
			$lCurrentField->setAttribute('node_name', $lCurrentField->nodeName);
		}


		/* АКО ИМА TREATMENT-и */
		if($lNodes->length){
			//~ var_dump($lNodes->length);
			for($i = 0; $i < $lNodes->length; ++$i){

				$lCurrNode = $lNodes->item($i);

				// Обработваме материалите в таксона
				$lMaterialQuery = $this->m_Xpath->query('./materials/material', $lCurrNode);



				/* АКО ИМА МАТЕРИАЛИ */
				if($lMaterialQuery->length){
					//~ var_dump($lMaterialQuery->length);
					for($j = 0; $j < $lMaterialQuery->length; ++$j){
						//~ var_dump($lNodes->item($i)->textContent);
						$lMaterialCurrNode = $lMaterialQuery->item($j);

						/*
							ОПРЕДЕЛЯМЕ МАТЕРИАЛЪТ ОТ СЛЕДНИТЕ ПАРАМЕТРИ
							- "Taxon treatment status type" -> id 13 в таблицата data_src
							- "Taxon treatment habitat type" -> id 14 в таблицата data_src
						*/

						$lTreatmentNode = $this->m_Xpath->query('./ancestor::treatment', $lMaterialCurrNode);


						$lTreatmentStatusType = $this->m_Xpath->query('./fields/select_type/value', $lTreatmentNode->item(0));
						$lTreatmentHabitatType = $this->m_Xpath->query('./fields/habitat/value', $lTreatmentNode->item(0));

						$lTreatmentStatusTypeValue = trim($lTreatmentStatusType->item(0)->textContent);
						$lTreatmentHabitatTypeValue = trim($lTreatmentHabitatType->item(0)->textContent);

						$this->ExecuteSqlStatement('
							SELECT id
							FROM spGetDataSrcQuerySelectedId(\'' . q($lTreatmentStatusTypeValue) . '\', ' . (int)TT_STATUS_TYPES_SRC_ID  . ')
						');

						$lTreatmentStatusTypeId = (int)$this->m_con->mRs['id'];

						$this->ExecuteSqlStatement('
							SELECT id
							FROM spGetDataSrcQuerySelectedId(\'' . q($lTreatmentHabitatTypeValue) . '\', ' . (int)TT_HABITAT_TYPES_SRC_ID  . ')
						');


						$lTreatmentHabitatTypeId = (int)$this->m_con->mRs['id'];
						if(!(int)$lTreatmentHabitatTypeId){
							$lTreatmentHabitatTypeId = (int)DEFAULT_TT_HABITAT_TYPE_ID;
						}

						if(!(int)$lTreatmentStatusTypeId){
							$lTreatmentStatusTypeId = (int)DEFAULT_TT_STATUS_TYPE_ID;
						}

						$this->ExecuteSqlStatement('SELECT * FROM spGetCustomCreateObject(
							' . (int) TT_MATERIAL_CUSTOM_CREATION_ID . ',
							ARRAY[' . (int)$lTreatmentStatusTypeId . ', ' . (int)$lTreatmentHabitatTypeId . ']
						)');

						$lObjectId = (int)$this->m_con->mRs['result'];

						if(!$lObjectId){//No such material
							$lMaterialCurrNode->parentNode->removeChild($lMaterialCurrNode);
							continue;
						}

						$this->ExecuteSqlStatement('
							SELECT *
							FROM pwt.spGetObjectXMLTemplateFileName(' . (int) $lObjectId . ',' . (int) $this->m_templateId . ')
							');

						$lObjectXmlName = $this->m_con->mRs['result'];

						$lFilePath = PATH_OBJECTS_XSL . 'template_' . (int)$this->m_templateId . '/' . $lObjectXmlName; // Tuk Trqbva da se napravi da se vzima ot bazata

						$this->PrepareSingleMaterialForImport($lMaterialCurrNode, $lFilePath);
					}
				}


				$lClassificationTypeValue = $this->m_Xpath->query('./fields/classification/value', $lCurrNode);
				$lRankTypeValue = $this->m_Xpath->query('./fields/rank/value', $lCurrNode);
				$lTreatmentTypeValue = $this->m_Xpath->query('./fields/type_of_treatment/value', $lCurrNode);



				// Понеже материалите са подобект на treatment-ите
				// и вече сме ги обработили трябва да ги премахнем и след това да ги дoбавим отново
				//~ $lMaterials = $this->m_Xpath->query('./materials', $lCurrNode);

				//~ if((int)$lMaterials->length) {
					//~ $lMaterialsNode = $lMaterials->item(0);
					//~ $lMaterials->item(0)->parentNode->removeChild($lMaterials->item(0));
				//~ }



				$lClassificationTypeValue = trim($lClassificationTypeValue->item(0)->textContent);
				$lRankTypeValue = trim($lRankTypeValue->item(0)->textContent);
				$lTreatmentTypeValue = trim($lTreatmentTypeValue->item(0)->textContent);

				$this->ExecuteSqlStatement('
					SELECT id
					FROM spGetDataSrcQuerySelectedId(\'' . q($lRankTypeValue) . '\', ' . (int)TT_RANK_SRC_ID  . ')
					');
				$lRankTypeId = (int)$this->m_con->mRs['id'];

				$this->ExecuteSqlStatement('
					SELECT id
					FROM spGetDataSrcQuerySelectedId(\'' . q($lTreatmentTypeValue) . '\', ' . (int)TT_TYPES_SRC_ID  . ')
					');
				$lTreatmentTypeId = (int)$this->m_con->mRs['id'];

				$this->ExecuteSqlStatement('
					SELECT id
					FROM spGetTaxonClassificationSelectedRootId(\'' . q($lClassificationTypeValue) . '\')
					');
				$lRootClassificationId = (int)$this->m_con->mRs['id'];
				$lClassificationNomenclatureCode = (int)$this->m_con->mRs['nomenclature_code'];


// 				var_dump('SELECT * FROM spGetCustomCreateObject(
// 					' . (int) TT_CUSTOM_CREATION_ID . ',
// 					ARRAY[' . (int)$lRankTypeId . ', ' . (int)$lRootClassificationId . ', ' . (int)$lTreatmentTypeId . ']
// 				)', $lTreatmentTypeValue, $lClassificationTypeValue);
				$this->ExecuteSqlStatement('SELECT * FROM spGetCustomCreateObject(
					' . (int) TT_CUSTOM_CREATION_ID . ',
					ARRAY[' . (int)$lRankTypeId . ', ' . (int)$lClassificationNomenclatureCode . ', ' . (int)$lTreatmentTypeId . ']
				)');

				$lObjectId = (int)$this->m_con->mRs['result'];

				if(!$lObjectId){//No such treatment
					$lCurrNode->parentNode->removeChild($lCurrNode);
					continue;
				}

				$this->ExecuteSqlStatement('
					SELECT *
					FROM pwt.spGetObjectXMLTemplateFileName(' . (int) $lObjectId . ',' . (int) $this->m_templateId . ')
					');

				$lObjectXmlName = $this->m_con->mRs['result'];


				$lFilePath = PATH_OBJECTS_XSL . 'template_' . (int)$this->m_templateId . '/' . $lObjectXmlName; // Tuk Trqbva da se napravi da se vzima ot bazata
				$this->PrepareSingleTreatmentForImport($lCurrNode, $lFilePath);
			}
		}

	}

	/*
		Materials for import
	*/

	function PrepareMaterialsForImport() {


		$lTreatmentsQuery = '/document/objects//systematics/treatment';

		$lTreatmentNodes = $this->m_Xpath->query($lTreatmentsQuery);

		if($lTreatmentNodes->length){
			for($i = 0; $i < $lTreatmentNodes->length; ++$i){

				$lMaterialsQuery = '/document/objects//materials/material';

				$lNodes = $this->m_Xpath->query($lMaterialsQuery);

				/* АКО ИМА МАТЕРИАЛИ */
				if($lNodes->length){
					for($i = 0; $i < $lNodes->length; ++$i){
						//~ var_dump($lNodes->item($i)->textContent);
						$lCurrNode = $lNodes->item($i);

						/*
							ОПРЕДЕЛЯМЕ МАТЕРИАЛЪТ ОТ СЛЕДНИТЕ ПАРАМЕТРИ
							- "Taxon treatment status type" -> id 13 в таблицата data_src
							- "Taxon treatment habitat type" -> id 14 в таблицата data_src
						*/

						$lTreatmentNode = $this->m_Xpath->query('./ancestor::treatment', $lCurrNode);


						$lTreatmentStatusType = $this->m_Xpath->query('./fields/select_type/value', $lTreatmentNode->item(0));
						$lTreatmentHabitatType = $this->m_Xpath->query('./fields/habitat/value', $lTreatmentNode->item(0));

						$lTreatmentStatusTypeValue = trim($lTreatmentStatusType->item(0)->textContent);
						$lTreatmentHabitatTypeValue = trim($lTreatmentHabitatType->item(0)->textContent);

						$this->ExecuteSqlStatement('SELECT query FROM pwt.data_src WHERE id = ' . (int)TT_STATUS_TYPES_SRC_ID );

						$lTTStatusTypesQuery = $this->m_con->mRs['query'];

						$this->ExecuteSqlStatement('SELECT query FROM pwt.data_src WHERE id = ' . (int)TT_HABITAT_TYPES_SRC_ID );

						$lTTHabitatTypesQuery = $this->m_con->mRs['query'];

						$this->ExecuteSqlStatement('SELECT id FROM (' . $lTTStatusTypesQuery . ') a WHERE name = \'' . $lTreatmentStatusTypeValue . '\'');

						$lTreatmentStatusTypeId = (int)$this->m_con->mRs['id'];

						$this->ExecuteSqlStatement('SELECT id FROM (' . $lTTStatusTypesQuery . ') a WHERE name = \'' . $lTreatmentHabitatTypeValue . '\'');

						$lTreatmentHabitatTypeId = (int)$this->m_con->mRs['id'];
						if(!(int)$lTreatmentHabitatTypeId)
							$lTreatmentHabitatTypeId = (int)DEFAULT_TT_HABITAT_TYPE_ID;

						$this->ExecuteSqlStatement('SELECT * FROM spGetCustomCreateObject(
							' . (int) TT_MATERIAL_CUSTOM_CREATION_ID . ',
							ARRAY[' . (int)$lTreatmentStatusTypeId . ', ' . (int)$lTreatmentHabitatTypeId . ']
						)');

						$lObjectId = (int)$this->m_con->mRs['result'];

						$this->ExecuteSqlStatement('
							SELECT *
							FROM pwt.spGetObjectXMLTemplateFileName(' . (int) $lObjectId . ',' . (int) $this->m_templateId . ')
						');

						//~ print_r('SELECT * FROM pwt.spGetObjectXMLTemplateFileName(
							//~ ' . (int) TT_MATERIAL_CUSTOM_CREATION_ID . ',
							//~ ' . (int) $this->m_templateId . ',
							//~ ' . (int) $lTreatmentStatusTypeId. ',
							//~ ' . (int) $lTreatmentHabitatTypeId. '
						//~ )');
						//~ exit;

						$lObjectXmlName = $this->m_con->mRs['result'];


						$lFilePath = PATH_OBJECTS_XSL . 'template_' . (int)$this->m_templateId . '/' . $lObjectXmlName; // Tuk Trqbva da se napravi da se vzima ot bazata
						$this->PrepareSingleMaterialForImport($lCurrNode, $lFilePath);
					}
				}
			}
		}
	}

	/*
		References for import
	*/

	function PrepareReferencesForImport() {

		$lReferencesQuery = '/document/objects//references/reference';

		$lNodes = $this->m_Xpath->query($lReferencesQuery);

		/* AKO IMA REFERENCII */
		if($lNodes->length){
			for($i = 0; $i < $lNodes->length; ++$i){
				//~ var_dump($lNodes->item($i)->textContent);
				$lCurrNode = $lNodes->item($i);
				$lReferenceTypeValue = $this->m_Xpath->query('./fields/reference_type/value', $lCurrNode);

				if(!$lReferenceTypeValue->length){//Reference type empty
					$lCurrNode->parentNode->removeChild($lCurrNode);
					continue;
				}

				$lReferenceTypeString = trim($lReferenceTypeValue->item(0)->textContent);
				$this->ExecuteSqlStatement('
						SELECT id
						FROM spGetDataSrcQuerySelectedId(\'' . q($lReferenceTypeString) . '\', ' . (int)REFERENCE_TYPES_SRC_ID  . ')
				');
				$lReferenceTypeId = (int)$this->m_con->mRs['id'];

				$this->ExecuteSqlStatement('
					SELECT *
					FROM spGetCustomCreateObject(' . (int) REFERENCE_CUSTOM_CREATION_ID . ',ARRAY[' . (int)$lReferenceTypeId . '])
				');
				$lObjectId = (int) (int)$this->m_con->mRs['result'];

				if(!$lObjectId){//No such reference type
					$lCurrNode->parentNode->removeChild($lCurrNode);
					continue;
				}

				$this->ExecuteSqlStatement('
					SELECT *
					FROM pwt.spGetObjectXMLTemplateFileName(' . (int) $lObjectId . ',' . (int) $this->m_templateId . ')
				');
				$lObjectXmlName = $this->m_con->mRs['result'];
				$lFilePath = PATH_OBJECTS_XSL . 'template_' . (int)$this->m_templateId . '/' . $lObjectXmlName;
				$this->PrepareSingleReferenceForImport($lCurrNode, $lFilePath);
			}

		}
	}

	/*
		Обработваме референциите като за всеки тип минаваме през стойностите
		и ги слагаме на празните подготвени темплейти и след това ги replace-ваме
	*/

	function PrepareSingleReferenceForImport(&$pRefNode, $pReferenceXML) {


		if(is_file($pReferenceXML)){
			$lXmlDom = new DOMDocument('1.0', 'UTF-8');

			$lXML = file_get_contents($pReferenceXML);
			if(!$lXmlDom->loadXML($lXML)){
				$this->SetError(getstr('pwt.api.xmlIsInvalid'));
			}
			$lXPath = new DOMXPath($lXmlDom);

			$lReferenceNodes = $this->m_Xpath->query('./*/fields/*|./fields/*', $pRefNode); // elementi na xml-a za import
			$lEmptyReferenceNodes = $lXPath->query('.//*'); // elementi na xml-a ot prazniq template za obekta
			//~ $lEmptyReferenceNodes = $lXPath->query('.//descendant::*'); // elementi na xml-a ot prazniq template za obekta

			for($j = 0; $j < $lReferenceNodes->length; ++$j){ // ciklq prez elementite i gledam dali imam takuv node i v xml-a za import
				$lReferenceNodeValue = $this->m_Xpath->query('./value', $lReferenceNodes->item($j)); // Stoinostta na elementa

				if(trim($lReferenceNodeValue->item(0)->nodeValue) != '') { // Ако имаме стойност за слагане търсим елемента, за да го сложим на мястото му
					for($i = 0; $i < $lEmptyReferenceNodes->length; ++$i){
						if($lReferenceNodes->item($j)->nodeName == $lEmptyReferenceNodes->item($i)->nodeName && trim($lReferenceNodeValue->item(0)->textContent) != '' ) {

							$lEmptyReferenceNodeValue = $lXPath->query('./value', $lEmptyReferenceNodes->item($i)); // Kade da replace-nem
							$lEmptyReferenceNodeValue->item(0)->nodeValue = $lReferenceNodeValue->item(0)->nodeValue; // Slagame stoinostta na elementa v prazniq template

							//~ var_dump($lEmptyReferenceNodes->item($i));
							//~ var_dump($lReferenceNodeValue->item(0)->textContent);
						}
					}
				}
				//~ var_dump($lReferenceNodes->item($j)->nodeName);
			}

			// Insert и remove на елементите (replace)
			$lNodeToImport = $this->m_XmlDom->importNode($lXmlDom->documentElement, true);

			$pRefNode->parentNode->insertBefore($lNodeToImport, $pRefNode);
			$pRefNode->parentNode->removeChild($pRefNode);

		} else {
			$this->SetError(getstr('pwt.api.xmlFileDoNotExists'));
		}
	}


	function PrepareSingleMaterialForImport(&$pCurrNode, $pXMLFile) {

		if(is_file($pXMLFile)){
			$lXmlDom = new DOMDocument('1.0', 'UTF-8');

			$lXML = file_get_contents($pXMLFile);
			if(!$lXmlDom->loadXML($lXML)){
				$this->SetError(getstr('pwt.api.xmlIsInvalid'));
			}
			$lXPath = new DOMXPath($lXmlDom);

			$lMaterialNodes = $this->m_Xpath->query('./fields/*', $pCurrNode); // elementi na xml-a za import
			$lEmptyMaterialNodes = $lXPath->query('.//*'); // elementi na xml-a ot prazniq template za obekta

			for($j = 0; $j < $lMaterialNodes->length; ++$j){ // ciklq prez elementite i gledam dali imam takuv node i v xml-a za import
				$lMaterialNodeValue = $this->m_Xpath->query('./value', $lMaterialNodes->item($j)); // Stoinostta na elementa

				if(trim($lMaterialNodeValue->item(0)->nodeValue) != '') { // Ако имаме стойност за слагане търсим елемента, за да го сложим на мястото му
					for($i = 0; $i < $lEmptyMaterialNodes->length; ++$i){
						if($lMaterialNodes->item($j)->nodeName == $lEmptyMaterialNodes->item($i)->nodeName) {

							$lEmptyMaterialNodeValue = $lXPath->query('./value', $lEmptyMaterialNodes->item($i)); // Kade da replace-nem
							$lEmptyMaterialNodeValue->item(0)->nodeValue = $lMaterialNodeValue->item(0)->nodeValue; // Slagame stoinostta na elementa v prazniq template
							//~ var_dump($lMaterialNodes->item($j)->textContent);
							//~ var_dump($lEmptyMaterialNodeValue->item(0)->nodeName);
						}
						//~ var_dump($lMaterialNodes->item($j)->nodeName);
					}
				}
				//~ var_dump($lMaterialNodes->item($j)->nodeName);
			}

			// Insert и remove на елементите (replace)
			$lNodeToImport = $this->m_XmlDom->importNode($lXmlDom->documentElement, true);

			$pCurrNode->parentNode->insertBefore($lNodeToImport, $pCurrNode);
			$pCurrNode->parentNode->removeChild($pCurrNode);

		} else {
			$this->SetError(getstr('pwt.api.xmlFileDoNotExists'));
		}
	}

	function PrepareSingleTreatmentForImport(&$pCurrNode, $pXMLFile) {

		if(is_file($pXMLFile)){
			$lXmlDom = new DOMDocument('1.0', 'UTF-8');

			$lXML = file_get_contents($pXMLFile);
			if(!$lXmlDom->loadXML($lXML)){
				$this->SetError(getstr('pwt.api.xmlIsInvalid'));
			}
			$lXPath = new DOMXPath($lXmlDom);

			$lRealTreatmentFieldNodes = $this->m_Xpath->query('.//*[@is_field = "1"]', $pCurrNode); // само field-вете
			$lEmptyTreatmentNodes = $lXPath->query('.//*[@id]'); // elementi na xml-a ot prazniq template za obekta

			for($j = 0; $j < $lRealTreatmentFieldNodes->length; ++$j){ // ciklq prez elementite i gledam dali imam takuv node i v xml-a za import
				$lTreatmentNodeValue = $this->m_Xpath->query('./value', $lRealTreatmentFieldNodes->item($j)); // Stoinostta na elementa

				if(trim($lTreatmentNodeValue->item(0)->nodeValue) != '') { // Ако имаме стойност за слагане търсим елемента, за да го сложим на мястото му
					for($i = 0; $i < $lEmptyTreatmentNodes->length; ++$i){
						if($lRealTreatmentFieldNodes->item($j)->nodeName == $lEmptyTreatmentNodes->item($i)->nodeName) {

							$lEmptyTreatmentNodeValue = $lXPath->query('./value', $lEmptyTreatmentNodes->item($i)); // Kade da replace-nem
							$lEmptyTreatmentNodeValue->item(0)->nodeValue = $lTreatmentNodeValue->item(0)->nodeValue; // Slagame stoinostta na elementa v prazniq template

						}
					}

				}
				//~ var_dump($lMaterialNodes->item($j)->nodeName);
			}

			$lMaterials = $this->m_Xpath->query('./materials', $pCurrNode);

			// Слагаме материалите на правилното място
			if($lMaterials->length) {
				$lNodeToInsertBefore = $lXPath->query('.//materials');
				if($lNodeToInsertBefore->length){
					$lNodeToImport = $lXmlDom->importNode($lMaterials->item(0), true);
					$lNodeToInsertBefore->item(0)->parentNode->insertBefore($lNodeToImport, $lNodeToInsertBefore->item(0));
					$lNodeToInsertBefore->item(0)->parentNode->removeChild($lNodeToInsertBefore->item(0));
				}
			}

			// Insert и remove на елементите (replace)
			$lNodeToImport = $this->m_XmlDom->importNode($lXmlDom->documentElement, true);

			$pCurrNode->parentNode->insertBefore($lNodeToImport, $pCurrNode);
			$pCurrNode->parentNode->removeChild($pCurrNode);

		} else {
			$this->SetError(getstr('pwt.api.xmlFileDoNotExists'));
		}
	}
}