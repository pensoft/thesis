<?php

class cOai extends cBase_Controller {
	var $m_oaiModel;
	var $m_resultObject;
	var $m_tempView;
	var $m_currentResumptionToken;
	var $m_resumptionTokenParsedParts;
	var $m_errCode;
	
	function __construct() {
		global $rewrite;
		parent::__construct();
		$this->m_oaiModel = new mOai();
		$lVerb = $this->GetValueFromRequestWithoutChecks('verb');
		$this->m_tempView = new pOai(array ());
		$this->m_resultObject = '';
		$this->m_currentResumptionToken = $this->GetValueFromRequestWithoutChecks('resumptionToken');
		$this->m_resumptionTokenParsedParts = $this->ParseResumptionToken($this->m_currentResumptionToken);
		$this->m_errCode = '';
		try {
			switch ($lVerb) {
				default :
				case OAI_VERB_GET_IDENTIFY :
					$this->GetIdentity();
					break;
				case OAI_VERB_GET_LIST_METADATA_FORMATS :
					$this->GetMetadataFormats();
					break;
				case OAI_VERB_GET_LIST_SETS :
					$this->GetSets();
					break;
				case OAI_VERB_GET_LIST_IDENTIFIERS :
					$this->GetRecords(1);
					break;
				case OAI_VERB_GET_LIST_RECORDS :
					$this->GetRecords();
					break;
				case OAI_VERB_GET_RECORD :
					$this->GetSingleRecord();
					break;
			}
		} catch ( Exception $pException ) {
			$this->m_resultObject = new evSimple_Block_Display(array (
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => 'errors',
				'view_object' => $this->m_tempView,
				'oai_url' => OAI_URL,	
				'err_code' => $pException->getCode(),
				'err_msg' => $pException->getMessage()		
			));
		}
		
		$pViewPageObjectsDataArray = array (
			'result_object' => $this->m_resultObject 
		);
		
		$this->m_pageView = new pOai(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	/**
	 * For additional info visit
	 * http://www.openarchives.org/OAI/openarchivesprotocol.html#Identify
	 */
	function GetIdentity() {
		$lFirstPubdate = $this->m_oaiModel->GetFirstArticlePubdate();
		$lResult = new evSimple_Block_Display(array (
			'ctype' => 'evSimple_Block_Display',
			'min_date' => $lFirstPubdate,
			'name_in_viewobject' => 'identity',
			'view_object' => $this->m_tempView,
			'repository_name' => OAI_REPOSITORY_NAME,
			'oai_url' => OAI_URL,
			'protocol_version' => OAI_PROTOCOL_VERSION,
			'date_format' => OAI_DATE_TEXT_FORMAT,
			'admin_email' => OAI_ADMIN_EMAIL,
			'resumption_token' => $this->m_currentResumptionToken 
		));
		$this->m_resultObject = $lResult->Display();
	}

	/**
	 * For additional info visit
	 * http://www.openarchives.org/OAI/openarchivesprotocol.html#ListSets
	 */
	function GetSets() {
		$lCurrentPage = (int) $this->m_resumptionTokenParsedParts['page'];
		$lNewResumptionTokenData = $this->m_resumptionTokenParsedParts;
		$lNewResumptionTokenData['page'] = (int) $lCurrentPage + 1;
		$lNewResumptionToken = $this->CreateResumptionToken($lNewResumptionTokenData);
		$lSets = $this->m_oaiModel->GetSets($lCurrentPage, (int) OAI_PAGE_SIZE);
		if(!$lSets->m_RecordCount){
			throw new Exception(getstr('oai.noSetHierarchy'), OAI_ERR_CODE_NO_SET_HEIRARCHY);
		}
		$lResult = new evList_Display(array (
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'sets',
			'view_object' => $this->m_tempView,
			'controller_data' => $lSets,
			'oai_url' => OAI_URL,
			'resumption_token_label' => OAI_RESUMPTION_TOKEN_LABEL,
			'resumption_token_in_request' => $this->m_currentResumptionToken,
			'new_resumption_token' => $lNewResumptionToken,
			'usefirstlast' => 1 
		));
		
		$this->m_resultObject = $lResult->Display();
	}
	
	/**
	 * For additional info visit
	 * http://www.openarchives.org/OAI/openarchivesprotocol.html#ListRecords
	 * http://www.openarchives.org/OAI/openarchivesprotocol.html#ListIdentifiers
	 */
	function GetRecords($pReturnIdentifiers = false) {
		$lSet = $this->GetValueFromRequestWithoutChecks('set');
		$lFromDate = $this->GetValueFromRequestWithoutChecks('from');
		$lUntilDate = $this->GetValueFromRequestWithoutChecks('until');
		$lMetadataPrefix = $this->GetValueFromRequestWithoutChecks('metadataPrefix');
		$lCurrentPage = (int) $this->m_resumptionTokenParsedParts['page'];
		$lNewResumptionTokenData = $this->m_resumptionTokenParsedParts;

		if($this->m_currentResumptionToken){
			$lSet = $this->m_resumptionTokenParsedParts['set'];
			$lFromDate = $this->m_resumptionTokenParsedParts['from'];
			$lUntilDate = $this->m_resumptionTokenParsedParts['until'];
			$lMetadataPrefix = $this->m_resumptionTokenParsedParts['metadata_prefix'];
			if( !$lMetadataPrefix || !$this->m_oaiModel->CheckIfMetadataPrefixIsAllowed($lMetadataPrefix)){
				throw new Exception(getstr('oai.badResumptionToken'), OAI_ERR_CODE_BAD_RESUMPTION_TOKEN);				
			}
		}
		
		if( !$lMetadataPrefix ){
			throw new Exception(getstr('oai.missingMetadataPrefixParam'), OAI_ERR_CODE_BAD_ARGUMENT);
		}
		
		if( !$this->m_oaiModel->CheckIfMetadataPrefixIsAllowed($lMetadataPrefix) ){
			throw new Exception(getstr('oai.wrongMetadataPrefixParam'), OAI_ERR_CODE_BAD_ARGUMENT);
		}
				
		$lNewResumptionTokenData['page'] = (int) $lCurrentPage + 1;
		$lNewResumptionTokenData['set'] = $lSet;
		$lNewResumptionTokenData['from'] = $lFromDate;
		$lNewResumptionTokenData['until'] = $lUntilDate;
		$lNewResumptionTokenData['metadata_prefix'] = $lMetadataPrefix;
		
		$lNewResumptionToken = $this->CreateResumptionToken($lNewResumptionTokenData);
		
		
		$lRecords = $this->m_oaiModel->GetRecords($lSet, $lFromDate, $lUntilDate, $lCurrentPage, (int) OAI_PAGE_SIZE);
		if(!$lRecords->m_RecordCount){			
			throw new Exception(getstr('oai.noRecordsMatch'), OAI_ERR_CODE_NO_RECORDS);
		}
		$lResult = new evList_Display(array (
			'ctype' => 'evList_Display',
			'name_in_viewobject' => ($pReturnIdentifiers ? 'identifiers' : ('records_' . $lMetadataPrefix)) ,
			'metadata_prefix' => $lMetadataPrefix,
			'view_object' => $this->m_tempView,
			'controller_data' => $lRecords,
			'oai_url' => OAI_URL,
			'new_resumption_token' => $lNewResumptionToken,
			'usefirstlast' => 1,
			'resumption_token_label' => OAI_RESUMPTION_TOKEN_LABEL,
			'resumption_token_in_request' => $this->m_currentResumptionToken,
			'set_label' => OAI_SET_LABEL, 
			'set_in_request' => $this->GetValueFromRequestWithoutChecks('set'),
			'from_label' => OAI_FROM_LABEL,
			'from_in_request' => $this->GetValueFromRequestWithoutChecks('from'),
			'until_label' => OAI_UNTIL_LABEL,
			'until_in_request' => $this->GetValueFromRequestWithoutChecks('until'),
			'metadata_label' => OAI_METADATA_PREFIX_LABEL,
			'metadata_prefix_in_request' => $this->GetValueFromRequestWithoutChecks('metadataPrefix'),
		));
	
		$this->m_resultObject = $lResult->Display();
	}
	
	/**
	 * For additional info visit
	 * http://www.openarchives.org/OAI/openarchivesprotocol.html#GetRecord
	 */
	function GetSingleRecord() {
		$lMetadataPrefix = $this->GetValueFromRequestWithoutChecks('metadataPrefix');
		$lIdentifier = $this->GetValueFromRequestWithoutChecks('identifier');		
	
		if( !$lMetadataPrefix ){
			throw new Exception(getstr('oai.missingMetadataPrefixParam'), OAI_ERR_CODE_BAD_ARGUMENT);
		}
	
		if( !$this->m_oaiModel->CheckIfMetadataPrefixIsAllowed($lMetadataPrefix) ){
			throw new Exception(getstr('oai.wrongMetadataPrefixParam'), OAI_ERR_CODE_BAD_ARGUMENT);
		}
	
		if( !$lIdentifier ){
			throw new Exception(getstr('oai.missingIdentifierParam'), OAI_ERR_CODE_BAD_ARGUMENT);
		}
	
	
		$lRecords = $this->m_oaiModel->GetSingleRecord($lIdentifier);
		if(!$lRecords->m_RecordCount){
			throw new Exception(getstr('oai.idDoesNotExist'), OAI_ERR_CODE_ID_DOES_NOT_EXIST);
		}
		$lResult = new evList_Display(array (
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'single_record_' . $lMetadataPrefix,
			'metadata_prefix' => $lMetadataPrefix,
			'view_object' => $this->m_tempView,
			'controller_data' => $lRecords,
			'oai_url' => OAI_URL,
			'metadata_label' => OAI_METADATA_PREFIX_LABEL,
			'metadata_prefix_in_request' => $lMetadataPrefix,
			'identifier_label' => OAI_IDENTIFIER_LABEL,
			'identifier_in_request' => $lIdentifier,
		));
	
		$this->m_resultObject = $lResult->Display();
	}
	
	function GetMetadataFormats(){
		$lIdentifier = $this->GetValueFromRequestWithoutChecks('identifier');
		if($lIdentifier){
			$lRecords = $this->m_oaiModel->GetSingleRecord($lIdentifier);
			if(!$lRecords->m_RecordCount){
				throw new Exception(getstr('oai.idDoesNotExist'), OAI_ERR_CODE_ID_DOES_NOT_EXIST);
			}
		}
		$lResult = new evList_Display(array (
			'ctype' => 'evList_Display',
			'name_in_viewobject' => 'metadata_formats',
			'view_object' => $this->m_tempView,
			'controller_data' => $this->m_oaiModel->GetMetadataFormats(),
			'oai_url' => OAI_URL,
			'identifier_label' => OAI_IDENTIFIER_LABEL,
			'identifier_in_request' => $lIdentifier,
		));
		$this->m_resultObject = $lResult->Display();
	}

	/**
	 * В тази функция обработваме подадения ResumptionToken.
	 * Очакваме това да е base64_encode-нат стринг във следния формат
	 * page=PAGE
	 */
	protected function ParseResumptionToken($pToken) {
		$lToken = trim($pToken);
		
		if ($lToken == '')
			return;
		
		$lToken = base64_decode($lToken);
		
		// Сплитваме различните параметри
		$lTokenParts = explode('&', $lToken);
		$lTokenParsedParts = array ();
		// Разделяме параметрите в масив с ключ име на параметър => стойност
		foreach ( $lTokenParts as $lCurrentPart ) {
			$lParsedPart = explode('=', $lCurrentPart);
			if (count($lParsedPart) > 1) {
				$lTokenParsedParts[$lParsedPart[0]] = $lParsedPart[1];
			}
		}
		return $lTokenParsedParts;
	}

	protected function CreateResumptionToken($pTokenData, $pUrlEncode = false) {
		$lResult = '';
		foreach ( $pTokenData as $lPartName => $lPartVal ) {
			if ($lResult != '') {
				$lResult .= '&';
			}
			$lResult .= $lPartName . '=' . $lPartVal;
		}
		$lResult = base64_encode($lResult);
		if ((int) $pUrlEncode)
			$lResult = urlencode($lResult);
		return $lResult;
	}
}
?>