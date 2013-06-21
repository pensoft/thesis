<?php
set_include_path(get_include_path() . PATH_SEPARATOR . PATH_CLASSES . 'excel_reader/');

include 'PHPExcel.php';
include 'PHPExcel/IOFactory.php';


class cimport_checklist_taxon extends csimple {
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
	var $m_materialsSpreadsheet;
	var $m_extLinksSpreadsheet;
	var $m_allowedMaterialTypes;

	var $m_taxonXmlFileName;
	var $m_materialXmlFileName;
	var $m_externalLinkXmlFileName;
	/**
	 * @formatter:off
	 * The array which will contain all the taxa data
	 *
	 * @var array The format ot the array will be the following
	 *      taxon_local_id => taxon_data
	 *      Where taxon_data is an array containing the data about the specific
	 *      taxon in the format $key => $val
	 *      Under the keys taxon_materials and taxon_ext_files we will store the
	 *      materials/external files data about the specific taxon
	 *      Each of these arrays will consist of arrays, each representing a
	 *      single material/ext file
	 *      The format of the data about the material/ext_files will be $key =>
	 *      $val
	 *      @formatter:on
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
	/**
	 * a mapping to the material fields.
	 * It is used if the fields in the excel file
	 * and the fields in the db object have different names. The format is the
	 * following
	 * db_field_name => excel_field_name
	 *
	 * @var array
	 */
	var $m_materialsFieldsMapping = array();
	/**
	 * a mapping to the ext link fields.
	 * It is used if the fields in the excel file
	 * and the fields in the db object have different names. The format is the
	 * following
	 * db_field_name => excel_field_name
	 *
	 * @var array
	 */
	var $m_extLinksFieldsMapping = array();

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
		$this->m_allowedMaterialTypes = array(
			'Holotype' => array(
				'Holotype',
				'Holotype(s)'
			),
			'Syntype' => array(
				'Syntype(s)',
				'Syntype'
			),
			'Hapantotype' => array(
				'Hapantotype'
			),
			'Paratype' => array(
				'Paratype(s)',
				'Paratype'
			),
			'Neotype' => array(
				'Neotype(s)',
				'Neotype'
			),
			'Lectotype' => array(
				'Lectotype(s)',
				'Lectotype'
			),
			'Paralectotype' => array(
				'Paralectotype(s)',
				'Paralectotype'
			),
			'Isotype' => array(
				'Isotype(s)',
				'Isotype'
			),
			'Isoparatype' => array(
				'Isoparatype(s)',
				'Isoparatype'
			),
			'Isolectotype' => array(
				'Isolectotype(s)',
				'Isolectotype'
			),
			'Isoneotype' => array(
				'Isoneotype(s)',
				'Isoneotype'
			),
			'Other material' => array(
				'Other material(s)',
				'Other material'
			)
		);

		$this->m_taxonFieldsMapping = array(
			$this->parseFieldName('taxon_authors_and_year') => $this->parseFieldName(CHECKLIST_TAXON_AUTHORSHIP_FIELD_NAME)
		);

		$this->m_materialsFieldsMapping = array();

		$this->m_extLinksFieldsMapping = array();

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
			FROM pwt.spGetObjectXMLTemplateFileName(' . (int) CHECKLIST_TAXON_OBJECT_ID . ',' . (int) $this->m_templateId . ')
			');
		$lObjectXmlName = $this->m_con->mRs['result'];

		$this->m_taxonXmlFileName = PATH_OBJECTS_XSL . 'template_' . (int) $this->m_templateId . '/' . $lObjectXmlName;

		// Material xml file
		$lTreatmentHabitatTypeId = (int) CHECKLIST_TAXON_HABITAT;
		$lTreatmentStatusTypeId = (int) CHECKLIST_TAXON_STATUS_TYPE;

		$this->m_con->Execute('SELECT * FROM spGetCustomCreateObject(
			' . (int) TT_MATERIAL_CUSTOM_CREATION_ID . ',
			ARRAY[' . (int) $lTreatmentStatusTypeId . ', ' . (int) $lTreatmentHabitatTypeId . ']
		)');

		$lObjectId = (int) $this->m_con->mRs['result'];

		$this->m_con->Execute('
			SELECT *
			FROM pwt.spGetObjectXMLTemplateFileName(' . (int) $lObjectId . ',' . (int) $this->m_templateId . ')
		');
		$lObjectXmlName = $this->m_con->mRs['result'];

		$this->m_materialXmlFileName = PATH_OBJECTS_XSL . 'template_' . (int) $this->m_templateId . '/' . $lObjectXmlName;

		// External Link xml file name
		$this->m_con->Execute('
			SELECT *
			FROM pwt.spGetObjectXMLTemplateFileName(' . (int) TAXON_EXTERNAL_LINK_OBJECT_ID . ',' . (int) $this->m_templateId . ')
			');
		$lObjectXmlName = $this->m_con->mRs['result'];

		$this->m_externalLinkXmlFileName = PATH_OBJECTS_XSL . 'template_' . (int) $this->m_templateId . '/' . $lObjectXmlName;

		//~ var_dump($this->m_materialXmlFileName);
		//~ var_dump($this->m_taxonXmlFileName);
		//~ var_dump($this->m_externalLinkXmlFileName);

		if(! file_exists($this->m_materialXmlFileName) || ! file_exists($this->m_taxonXmlFileName) || ! file_exists($this->m_externalLinkXmlFileName)){
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
		$this->m_taxonObjectIdxId = $lXPath->evaluate('count(' . $lParentInstanceQuery . '/checklist_taxon)');
		$lParentNodes = $lXPath->query($lParentInstanceQuery);
		if(!$lParentNodes->length){
			$this->SetError(getstr('pwt.noSuchParent'));
			return;
		}
		$lParentNode = $lParentNodes->item(0);
		$lParentObjectId = (int)$lParentNode->getAttribute('object_id');
		$lNomenclatureQuery = $lParentInstanceQuery . '/fields/*[@id="' . CHECKLIST_TAXON_NOMENCLATURE_FIELD_ID . '"]/value[@value_id > 0]';
		if($lParentObjectId == CHECKLIST_SPECIES_LOCALITY_LOCALITY_OBJECT_ID){
			$lNomenclatureQuery = $lParentInstanceQuery . '/ancestor::*[@object_id=\'' . CHECKLIST_SPECIES_LOCALITY_LOCALITY_CHECKLIST_OBJECT_ID . '\']/fields/*[@id="' . CHECKLIST_TAXON_NOMENCLATURE_FIELD_ID . '"]/value[@value_id > 0]';
		}
		// var_dump($lNomenclatureQuery);
		$lNomenclatureFieldNode = $lXPath->query($lNomenclatureQuery);
		// var_dump($lNomenclatureFieldNode->length);
		if($lNomenclatureFieldNode->length){
			$lNomenclature = (int) $lNomenclatureFieldNode->item(0)->getAttribute('value_id');
		}
		// var_dump($lNomenclature);
		if(! $lNomenclature){
			$this->SetError(getstr('pwt.checklistShouldHaveNomenclature'));
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
			$this->m_taxaSpreadsheet = $this->m_excelReader->getSheetByName(CHECKLIST_TAXON_TAXA_SPREADSHEET_NAME);
			// var_dump($this->m_taxaSpreadsheet);
			if(! $this->m_taxaSpreadsheet){
				$this->m_taxaSpreadsheet = $this->m_excelReader->getSheet((int) CHECKLIST_TAXON_TAXA_SPREADSHEET_DEFAULT_IDX);
			}
			$this->m_materialsSpreadsheet = $this->m_excelReader->getSheetByName(CHECKLIST_TAXON_MATERIALS_SPREADSHEET_NAME);
			if(! $this->m_materialsSpreadsheet){
				$this->m_materialsSpreadsheet = $this->m_excelReader->getSheet((int) CHECKLIST_TAXON_MATERIALS_SPREADSHEET_DEFAULT_IDX);
			}
			$this->m_extLinksSpreadsheet = $this->m_excelReader->getSheetByName(CHECKLIST_TAXON_EXT_LINKS_SPREADSHEET_NAME);
			if(! $this->m_extLinksSpreadsheet){
				$this->m_extLinksSpreadsheet = $this->m_excelReader->getSheet((int) CHECKLIST_TAXON_EXT_LINKS_SPREADSHEET_DEFAULT_IDX);
			}
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
			$this->parseMaterialsData();
			$this->parseExternalLinksData();
			// var_dump($this->m_taxaData);
		}catch(Exception $pException){
			$this->SetError(getstr('pwt.couldNotProcessXmlFile'));
		}
	}

	protected function parseTaxaData() {
		global $gChecklistTaxonRankFields;
		$lTaxaArray = $this->m_taxaSpreadsheet->toArray(null, true, true, true);
		// var_dump($lTaxaArray);
		if(! is_array($lTaxaArray) || count($lTaxaArray) < 2){
			// No taxa specified
			return;
		}
		// Remove the field names from the taxa array
		$lTaxaFieldNames = array_shift($lTaxaArray);
		$lTaxonRankFieldNames = array_reverse($gChecklistTaxonRankFields);

		foreach($lTaxaFieldNames as $lColumnName => $lFieldName){
			$lTaxaFieldNames[$lColumnName] = $this->parseFieldName($lFieldName);
		}
		foreach($lTaxonRankFieldNames as $lKey => $lFieldName){
			$lTaxonRankFieldNames[$lKey] = $this->parseFieldName($lFieldName);
		}
		$lLocalIdFieldName = $this->parseFieldName(CHECKLIST_TAXON_LOCAL_ID_FIELD_NAME);
		$lMaterialsFieldName = $this->parseFieldName(CHECKLIST_TAXON_MATERIALS_KEY_NAME);
		$lRankFieldName = $this->parseFieldName(CHECKLIST_TAXON_RANK_FIELD_NAME);
		$lExternalLinksFieldName = $this->parseFieldName(CHECKLIST_TAXON_EXTERNAL_LINKS_KEY_NAME);
		foreach($lTaxaArray as $lCurrentTaxon){
			$lCurrentTaxonParsedData = array();
			foreach($lTaxaFieldNames as $lColumnName => $lFieldName){
				$lCurrentTaxonParsedData[$lFieldName] = trim($lCurrentTaxon[$lColumnName]);
			}
			$lTaxonLocalId = (int) $lCurrentTaxonParsedData[$lLocalIdFieldName];
			if(! $lTaxonLocalId){
				// If there is no local taxon id - don't process the taxon
				continue;
			}
			$lTaxonRank = '';
			foreach($lTaxonRankFieldNames as $lKey => $lFieldName){
				if($lCurrentTaxonParsedData[$lFieldName] != ''){
					$lTaxonRank = $lFieldName;
					break;
				}
			}

			if($lTaxonRank == ''){
				// If there is no local rank - don't process the taxon
				continue;
			}
			if(! $this->ValidateSingleTaxon($lCurrentTaxonParsedData)){
				continue;
			}
			$lCurrentTaxonParsedData[$lRankFieldName] = $lTaxonRank;
			$lCurrentTaxonParsedData[$lMaterialsFieldName] = array();
			$lCurrentTaxonParsedData[$lExternalLinksFieldName] = array();
			$this->m_taxaData[$lTaxonLocalId] = $lCurrentTaxonParsedData;
		}
	}

	protected function parseMaterialsData() {
		$lMaterialsArray = $this->m_materialsSpreadsheet->toArray(null, true, true, true);
		if(! is_array($lMaterialsArray) || count($lMaterialsArray) < 2){
			// No materials specified
			return;
		}
		// Remove the field names from the taxa array
		$lMaterialsFieldNames = array_shift($lMaterialsArray);
		foreach($lMaterialsFieldNames as $lColumnName => $lFieldName){
			$lMaterialsFieldNames[$lColumnName] = $this->parseFieldName($lFieldName);
		}
		$lLocalIdFieldName = $this->parseFieldName(CHECKLIST_TAXON_LOCAL_ID_FIELD_NAME);
		$lMaterialsFieldName = $this->parseFieldName(CHECKLIST_TAXON_MATERIALS_KEY_NAME);
		$lTypeStatusFieldName = $this->parseFieldName(CHECKLIST_TAXON_MATERIAL_TYPE_STATUS_FIELD_NAME);
		foreach($lMaterialsArray as $lCurrentMaterial){
			$lCurrentMaterialParsedData = array();
			foreach($lMaterialsFieldNames as $lColumnName => $lFieldName){
				$lCurrentMaterialParsedData[$lFieldName] = trim($lCurrentMaterial[$lColumnName]);
			}
			$lTaxonLocalId = (int) $lCurrentMaterialParsedData[$lLocalIdFieldName];
			$lTypeStatus = $lCurrentMaterialParsedData[$lTypeStatusFieldName];
			if(! $lTaxonLocalId || ! array_key_exists($lTaxonLocalId, $this->m_taxaData) || $lTypeStatus == ''){
				// If there is no local taxon id - don't process the taxon
				continue;
			}
			// var_dump($lTypeStatus);
			if(! $this->ValidateSingleMaterial($lCurrentMaterialParsedData)){
				continue;
			}
			$this->m_taxaData[$lTaxonLocalId][$lMaterialsFieldName][] = $lCurrentMaterialParsedData;
		}
	}

	protected function parseExternalLinksData() {
		$lExternalLinksArray = $this->m_extLinksSpreadsheet->toArray(null, true, true, true);
		if(! is_array($lExternalLinksArray) || count($lExternalLinksArray) < 2){
			// No materials specified
			return;
		}
		// Remove the field names from the taxa array
		$lLinksFieldNames = array_shift($lExternalLinksArray);
		foreach($lLinksFieldNames as $lColumnName => $lFieldName){
			$lLinksFieldNames[$lColumnName] = $this->parseFieldName($lFieldName);
		}
		$lLocalIdFieldName = $this->parseFieldName(CHECKLIST_TAXON_LOCAL_ID_FIELD_NAME);
		$lExternalLinksFieldName = $this->parseFieldName(CHECKLIST_TAXON_EXTERNAL_LINKS_KEY_NAME);
		$lLinkTypeFieldName = $this->parseFieldName(CHECKLIST_TAXON_EXTERNAL_LINK_LINK_TYPE_FIELD_NAME);
		$lLinkFieldName = $this->parseFieldName(CHECKLIST_TAXON_EXTERNAL_LINK_LINK_VALUE_FIELD_NAME);
		foreach($lExternalLinksArray as $lCurrentLink){
			$lCurrentLinkParsedData = array();
			foreach($lLinksFieldNames as $lColumnName => $lFieldName){
				$lCurrentLinkParsedData[$lFieldName] = trim($lCurrentLink[$lColumnName]);
			}
			$lTaxonLocalId = (int) $lCurrentLinkParsedData[$lLocalIdFieldName];
			if(! $lTaxonLocalId || ! array_key_exists($lTaxonLocalId, $this->m_taxaData)){
				// If there is no local taxon id - don't process the taxon
				continue;
			}
			$lLinkType = $lCurrentLinkParsedData[$lLinkTypeFieldName];
			$lLinkValue = $lCurrentLinkParsedData[$lLinkFieldName];
			if($lLinkType == '' || $lLinkValue == ''){ // Required fields
				continue;
			}

			if(! $this->ValidateSingleExternalLink($lCurrentLinkParsedData)){
				continue;
			}
			$this->m_taxaData[$lTaxonLocalId][$lExternalLinksFieldName][] = $lCurrentLinkParsedData;
		}
	}

	/**
	 * Returns true/false indicating whether the taxon consists of valid data
	 *
	 * @param $pTaxonData unknown_type
	 */
	protected function ValidateSingleTaxon(&$pTaxonData) {
		return true;
	}

	/**
	 * Returns true/false indicating whether the material consists of valid data
	 *
	 * @param $pMaterialData unknown_type
	 */
	protected function ValidateSingleMaterial(&$pMaterialData) {
		$lTypeStatusFieldName = $this->parseFieldName(CHECKLIST_TAXON_MATERIAL_TYPE_STATUS_FIELD_NAME);
		$lMaterialTypeStatus = $pMaterialData[$lTypeStatusFieldName];
		$lMaterialTypeStatusRealName = recursive_array_search(strtolower(trim($lMaterialTypeStatus)), $this->m_allowedMaterialTypes);
		if(! $lMaterialTypeStatusRealName){
			return false;
		}
		$pMaterialData[$lTypeStatusFieldName] = $lMaterialTypeStatusRealName;
		return true;
	}

	/**
	 * Returns true/false indicating whether the ext link consists of valid data
	 *
	 * @param $pLinkData unknown_type
	 */
	protected function ValidateSingleExternalLink(&$pLinkData) {
		return true;
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

		error_reporting(- 1);
		if(! $lDom->loadXML(file_get_contents($this->m_taxonXmlFileName))){
			$this->SetError(getstr('pwt.xmlIsInva1lid'));
			return;
		}
		// Fill the field values
		$this->FillObjectFieldValues($lDom->documentElement, $pTaxonData, $this->m_taxonFieldsMapping);
		// Create the xml for the materials
		$lMaterialsFieldName = $this->parseFieldName(CHECKLIST_TAXON_MATERIALS_KEY_NAME);
		$lExternalLinksFieldName = $this->parseFieldName(CHECKLIST_TAXON_EXTERNAL_LINKS_KEY_NAME);

		$lXPath = new DOMXPath($lDom);
		$lAdditionalElements = array(
			'materials' => array(
				'field_name' => $lMaterialsFieldName,
				'holder_xpath' => '//materials',
				'xml_file' => $this->m_materialXmlFileName,
				'mapping' => &$this->m_materialsFieldsMapping
			),
			'external_links' => array(
				'field_name' => $lExternalLinksFieldName,
				'holder_xpath' => '//external_links',
				'xml_file' => $this->m_externalLinkXmlFileName,
				'mapping' => &$this->m_extLinksFieldsMapping
			)
		);

		foreach($lAdditionalElements as $lCurrentAdditionalElement){
			$lHolderQuery = $lCurrentAdditionalElement['holder_xpath'];
			$lHolderNode = $lXPath->query($lHolderQuery);
			if($lHolderNode->length){
				$lHolder = $lHolderNode->item(0);
				$lFieldName = $lCurrentAdditionalElement['field_name'];
				$lXmlFile = $lCurrentAdditionalElement['xml_file'];
				foreach($pTaxonData[$lFieldName] as $lCurrentElementData){
					$lElementXmlNode = $this->GenerateSingleInnerElementXml($lCurrentElementData, $lXmlFile, $lCurrentAdditionalElement['mapping']);
					if($lElementXmlNode){
						$lImportedNode = $lDom->importNode($lElementXmlNode, true);
						if($lImportedNode){
							$lHolder->appendChild($lImportedNode);
						}
					}

				}
			}
		}

		$this->SetXmlInternalDetails($lDom->documentElement, $pTaxonObjectIdx);
		return $lDom->saveXML();
	}

	/**
	 * Generate the xml of a single element and return
	 * a reference to its root so that it can be appended to the xml of the
	 * taxon
	 *
	 * @param $pElementData array
	 * @param $pXmlFileName string
	 *       	 - the file name where the xml for the element is
	 * @param $pFieldMappingArr array
	 *       	 - a mapping of the fields for the specific element
	 * @return s DOMElement
	 */
	protected function GenerateSingleInnerElementXml(&$pElementData, $pXmlFileName, &$pFieldMappingArr) {
		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);

		if(! $lDom->loadXML(file_get_contents($pXmlFileName))){
			$this->SetError(getstr('pwt.xmlIsInvalid'));
			return;
		}
		// Fill the field values
		$this->FillObjectFieldValues($lDom->documentElement, $pElementData, $pFieldMappingArr);
		// Create the xml for the materials
		return $lDom->documentElement;
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
		for($i = 0; $i < $lFields->length; ++ $i){
			$lCurrentField = $lFields->item($i);
			$lFieldParsedName = $this->parseFieldName($lCurrentField->nodeName);
			if(array_key_exists($lFieldParsedName, $pFieldMappingArr)){
				$lFieldParsedName = $pFieldMappingArr[$lFieldParsedName];
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
			$lSql = 'SELECT * FROM spimportdocumentobjectfromxml(
					' . (int) $this->m_documentId . ',
					\'' . q($lTaxonXml) . '\',
					' . (int) $this->m_instanceId . ',
					' . (int) $this->m_userId . ')';

			// var_dump($lSql);
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