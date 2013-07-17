<?php
set_include_path(get_include_path() . PATH_SEPARATOR . PATH_CLASSES . 'excel_reader/');

include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';


class cimport_data_paper_taxonomic_coverage extends csimple {
	var $m_materials = array();

	var $m_documentId;
	var $m_templateId;

	/**
	 * The id of the instance in which we will import the
	 * taxa (i.e.
	 * the parent instance id).
	 *
	 * @var int
	 */
	var $m_instanceId;

	var $m_userId;
	var $m_XmlDom;
	var $m_Xpath;
	var $m_con;
	var $m_errors;
	var $m_errorCount;
	var $m_filePath;
	var $m_excelReader;	
	
	var $m_taxaSpreadsheet;
	var $m_taxonXmlFileName;
	/**
	 * @formatter:off
	 * The array which will contain all the taxa data
	 *
	 * @var array The format ot the array will be the following
	 *      key => taxon_data
	 *      Where taxon_data is an array containing the data about the specific
	 *      taxon in the format $key => $val
	 *      
	 * @formatter:on
	 */
	var $m_taxaData;
	// An array containing the generated xmls of the taxa
	var $m_taxaXML;
	var $m_taxonObjectIdxId = 0;

	/**
	 * a mapping to the taxon fields.
	 * It is used if the fields in the excel file
	 * and the fields in the db object have different names. The format is the
	 * following
	 * db_field_name => excel_field_name
	 *
	 * @var array
	 */
	var $m_taxonFieldsMapping = array();
	

	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		// var_dump($pFieldTempl['file_path']);
		// var_dump(file_exists($pFieldTempl['file_path']));
		$this->m_instanceId = $pFieldTempl['instance_id'];
		$this->m_filePath = $pFieldTempl['file_path'];

		$this->m_con = new DBCn();
		$this->m_con->Open();

		$this->m_XmlDom = new DOMDocument('1.0', 'UTF-8');
		$this->m_Xpath = '';

		$this->m_taxaData = array();
		$this->m_taxaXML = array();

		$this->m_errors = array();
		$this->m_errorCount = 0;

		global $user;
		$this->m_userId = $user->id;		

		$this->m_taxonFieldsMapping = array(
// 			$this->parseFieldName(DATA_PAPER_TAXONOMIC_COVERAGE_TAXA_SPECIFIC_NAME_FIELD_NAME) => $this->parseFieldName('scientific_name'),
		);
		$this->LoadDocumentData();
	}

	/**
	 * Get data about the document in which we will import the taxa
	 */
	protected function LoadDocumentData() {
		$lSql = 'SELECT d.id, d.template_id
			FROM pwt.document_object_instances i
			JOIN pwt.documents d ON d.id = i.document_id
			WHERE i.id = ' . (int) $this->m_instanceId . '
		';
		$this->m_con->Execute($lSql);
		$this->m_documentId = (int) $this->m_con->mRs['id'];
		$this->m_templateId = (int) $this->m_con->mRs['template_id'];

		if(! $this->m_documentId){
			$this->SetError(getstr('pwt.noSuchDocument'));
			return;
		}

		// Taxon xml file
		$this->m_con->Execute('
			SELECT *
			FROM pwt.spGetObjectXMLTemplateFileName(' . (int) DATA_PAPER_TAXONOMIC_COVERAGE_TAXA_OBJECT_ID . ',' . (int) $this->m_templateId . ')
			');
		$lObjectXmlName = $this->m_con->mRs['result'];

		$this->m_taxonXmlFileName = PATH_OBJECTS_XSL . 'template_' . (int) $this->m_templateId . '/' . $lObjectXmlName;
		
// 		var_dump($this->m_taxonXmlFileName);
		
		if(! file_exists($this->m_taxonXmlFileName)){
			$this->SetError(getstr('pwt.couldNotGetXmlFiles'));
			return;
		}

		$lDocumentXml = getDocumentXml($this->m_documentId);
		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		if(! $lDom->loadXML($lDocumentXml)){
			$this->SetError(getstr('pwt.couldNotLoadDocumentXml'));
			return;
		}
		$lXPath = new DOMXPath($lDom);

		$lParentInstanceQuery = './/*[@instance_id = ' . (int) $this->m_instanceId . ']';
		$this->m_taxonObjectIdxId = $lXPath->evaluate('count(' . $lParentInstanceQuery . '/taxa)');
		$lParentNodes = $lXPath->query($lParentInstanceQuery);
		if(!$lParentNodes->length){
			$this->SetError(getstr('pwt.noSuchParent'));
			return;
		}
	}

	function LoadDefTempls() {

	}

	/**
	 * Init the excel reader and get the spreadsheets for the taxon data
	 */
	protected function LoadFile() {
		// var_dump(file_exists($this->m_filePath));
		if(! file_exists($this->m_filePath)){
			$this->SetError(getstr('pwt.noSuchFile'));
			return;
		}
		try{
			$this->m_excelReader = PHPExcel_IOFactory::load($this->m_filePath);
			$this->m_taxaSpreadsheet = $this->m_excelReader->getSheet(0);			
		}catch(Exception $pException){
			$this->SetError(getstr('pwt.couldNotProcessXmlFile'));
		}

	}

	/**
	 * Parse the contents of the passed file (i.e.
	 * init taxa/materials/ext links structure)
	 */
	protected function ParseFileContents() {
		if($this->m_errorCount){
			return;
		}
		try{
			$this->parseTaxaData();			
			// var_dump($this->m_taxaData);
		}catch(Exception $pException){
			$this->SetError(getstr('pwt.couldNotProcessXmlFile'));
		}
	}

	protected function parseTaxaData() {
		$lTaxaArray = $this->m_taxaSpreadsheet->toArray(null, true, true, true);
// 		var_dump($lTaxaArray);
		if(! is_array($lTaxaArray) || count($lTaxaArray) < 2){
			// No taxa specified
			return;
		}
		// Remove the field names from the taxa array
		$lTaxaFieldNames = array_shift($lTaxaArray);		
				
		foreach($lTaxaFieldNames as $lColumnName => $lFieldName){
			$lTaxaFieldNames[$lColumnName] = $this->parseFieldName($lFieldName);
		}		
		$lTaxonLocalId = 1;
		foreach($lTaxaArray as $lCurrentTaxon){
			$lCurrentTaxonParsedData = array();
			foreach($lTaxaFieldNames as $lColumnName => $lFieldName){
				$lCurrentTaxonParsedData[$lFieldName] = trim($lCurrentTaxon[$lColumnName]);
			}			
			if(! $this->ValidateSingleTaxon($lCurrentTaxonParsedData)){
				continue;
			}			
			
// 			var_dump($lCurrentTaxonParsedData);
			$this->m_taxaData[$lTaxonLocalId++] = $lCurrentTaxonParsedData;
		}
	}

	

	/**
	 * Returns true/false indicating whether the taxon consists of valid data
	 * For now all non empty taxa are valid if they have a known rank
	 *
	 * @param $pTaxonData array
	 */
	protected function ValidateSingleTaxon(&$pTaxonData) {
		global $gChecklistTaxonRankFields;
		$lRankParsedFieldName = $this->parseFieldName(DATA_PAPER_TAXONOMIC_COVERAGE_TAXA_RANK_FIELD_NAME);
		$lTaxonRank = strtolower($pTaxonData[$lRankParsedFieldName]);
		$lRankIsValid = false;
		foreach ($gChecklistTaxonRankFields as $lRank){
			if(strtolower($lRank) == $lTaxonRank){
				$lRankIsValid = true;
				break;
			}
		}
		if(!$lRankIsValid){
			return false;
		}
		$pTaxonData[$lRankParsedFieldName] = $lTaxonRank;
		foreach ($pTaxonData as $lFieldName => $lFieldData){
			if($lFieldData != ''){
				return true;
			}
		}
		return false;
	}	

	protected function parseFieldName($pFieldName) {
		$lResult = strtolower($pFieldName);
		$lSearch = array(
			',',
			'-',
			' ',
			'_',
			'&',
			'/',
			'(',
			')'
		);
		$lReplace = array(
			'',
			'',
			'',
			'',
			'and',
			'',
			'',
			''
		);
		$lResult = str_replace($lSearch, $lReplace, $lResult);
		return $lResult;
	}

	function GetData() {
		$this->LoadFile();
		$this->ParseFileContents();		
		$this->GenerateXmls();
		$this->ImportData();		
	}

	protected function GenerateXmls() {
		if($this->m_errorCount){
			return;
		}
		$lTaxonObjectIdx = $this->m_taxonObjectIdxId;
		foreach($this->m_taxaData as $lTaxonLocalId => $lTaxonData){
			$lTaxonXml = $this->GenerateSingleTaxonXml($lTaxonData, $lTaxonObjectIdx ++);
			$this->m_taxaXML[$lTaxonLocalId] = $lTaxonXml;
		}
	}

	/**
	 * Generate single taxon
	 *
	 * @param $pTaxonData array
	 * @return s the xml serialized value of the taxon
	 */
	protected function GenerateSingleTaxonXml(&$pTaxonData, $pTaxonObjectIdx) {
		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);

// 		error_reporting(- 1);
		if(! $lDom->loadXML(file_get_contents($this->m_taxonXmlFileName))){
			$this->SetError(getstr('pwt.xmlIsInva1lid'));
			return;
		}
		// Fill the field values
		$this->FillObjectFieldValues($lDom->documentElement, $pTaxonData, $this->m_taxonFieldsMapping);			
		$this->SetXmlInternalDetails($lDom->documentElement, $pTaxonObjectIdx);
		return $lDom->saveXML();
	}

	
	/**
	 * Fill the values of the specified object with the values from the data
	 * array
	 * A field is considered equal if its name matches the name of the key in
	 * $pObjectData
	 *
	 * @param $pParentNode DOMNode
	 * @param $pObjectData array
	 * @param $pFieldMappingArr array
	 */
	protected function FillObjectFieldValues(&$pParentNode, &$pObjectData, &$pFieldMappingArr) {
		$lXPath = new DOMXPath($pParentNode->ownerDocument);
		$lFieldsQuery = './/fields/*';
		$lFields = $lXPath->query($lFieldsQuery, $pParentNode);
// 		var_dump($pFieldMappingArr);
		
		for($i = 0; $i < $lFields->length; ++ $i){
			$lCurrentField = $lFields->item($i);
			$lFieldParsedName = $this->parseFieldName($lCurrentField->nodeName);
// 			var_dump($lFieldParsedName);
			if(array_key_exists($lFieldParsedName, $pFieldMappingArr)){
				$lFieldParsedName = $pFieldMappingArr[$lFieldParsedName];
// 				exit;
			}
			if(array_key_exists($lFieldParsedName, $pObjectData) && $pObjectData[$lFieldParsedName] != ''){
				$lFieldValueNode = $lXPath->query('./value', $lCurrentField); // Kade da replace-nem
				if($lFieldValueNode->length){
					$lNode = $lFieldValueNode->item(0);
					//$lValue = mb_convert_encoding($pObjectData[$lFieldParsedName], "UTF-8");
					$lValue = $pObjectData[$lFieldParsedName];
					$lNode->nodeValue = '';
					$lNode->appendChild($lNode->ownerDocument->createTextNode($lValue));
					//$lFieldValueNode->item(0)->nodeValue = $pObjectData[$lFieldParsedName];
				}
			}
		}
	}

	/**
	 * Set the internal details for postgres in order
	 * the import to work as expected (i.e.
	 * node name attributes, field name attributes, etc..
	 *
	 * @param $pXmlNode DOMNode
	 */
	function SetXmlInternalDetails(&$pXmlNode, $pObjectIdxId) {
		$lXPath = new DOMXPath($pXmlNode->ownerDocument);
		$lObjectsQuery = './/*[count(ancestor-or-self::fields) = 0]';
		// No fields node parent

		$lObjects = $lXPath->query($lObjectsQuery, $pXmlNode);

		$pXmlNode->setAttribute('is_object', 1);
		$pXmlNode->setAttribute('node_name', $pXmlNode->nodeName);
		$pXmlNode->setAttribute('object_idx', (int) $pObjectIdxId + 1);

		for($i = 0; $i < $lObjects->length; ++ $i){
			$lCurrentObject = $lObjects->item($i);
			$lCurrentObject->setAttribute('is_object', 1);
			$lPreviousObjectsOfTheSameType = $lXPath->evaluate('count(./preceding-sibling::' . $lCurrentObject->nodeName . ')', $lCurrentObject);
			$lCurrentObject->setAttribute('object_idx', $lPreviousObjectsOfTheSameType + 1);
			$lCurrentObject->setAttribute('node_name', $lCurrentObject->nodeName);
		}

		$lFieldsQuery = './/fields/*';
		$lFields = $lXPath->query($lFieldsQuery, $pXmlNode);
		for($i = 0; $i < $lFields->length; ++ $i){
			$lCurrentField = $lFields->item($i);
			$lCurrentField->setAttribute('is_field', 1);
			$lCurrentField->setAttribute('node_name', $lCurrentField->nodeName);
		}
	}

	function ImportData() {
		if((int) $this->m_errorCount){
			return;
		}
		if(! $this->m_con->Execute('BEGIN;')){
			$this->SetError(getstr('pwt.couldNotBeginTransaction'));
		}
		// Ако няма грешка -> import
		foreach($this->m_taxaXML as $lTaxonLocalId => $lTaxonXml){
// 			var_dump($lTaxonXml);
			$lSql = 'SELECT * FROM spimportdocumentobjectfromxml(
					' . (int) $this->m_documentId . ',
					\'' . q($lTaxonXml) . '\',
					' . (int) $this->m_instanceId . ',
					' . (int) $this->m_userId . ')';

// 			var_dump($lSql);
// 			exit;
			if(! $this->m_con->Execute($lSql)){
				$this->m_con->Execute('ROLLBACK;');
				$this->SetError(getstr('pwt.couldNotImportData'));
			}			
		}
		$lSql = 'SELECT * FROM pwt."XmlIsDirty"(1, ' . $this->m_documentId . ', ' . $this->m_instanceId . ')';
		if(! $this->m_con->Execute($lSql)){
			$this->m_con->Execute('ROLLBACK;');
			$this->SetError(getstr('pwt.couldNotImportData'));
		}

		if(! $this->m_con->Execute('COMMIT;')){
			$this->m_con->Execute('ROLLBACK;');
			$this->SetError(getstr('pwt.couldNotCommitTransaction'));
		}

	}

	protected function SetError($pErrorMsg) {
		// ~ throw new Exception($pErrorMsg);
		$this->m_errors = (array(
			'error' => $pErrorMsg
		));
		$this->m_errorCount ++;
	}

	function Display() {
		$this->GetData();

		return json_encode($this->m_errors);
		// ~ var_dump( json_encode($this->m_errors));
	}

}
?>