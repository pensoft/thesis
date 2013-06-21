<?php
/**
 * Materials - batch import from XLS/XLSX/ODS
 */
class cimport_materials extends csimple {
	var $m_materials = array();

	var $m_documentId;
	var $m_templateId;
	var $m_instanceId;
	var $m_userId;
	var $m_FilePath;
	var $m_ImportFile;
	var $m_XmlDom;
	var $m_Xpath;
	var $m_MatchArr;
	var $m_con;
	var $m_MaterialsObjectIdx;
	var $m_EmptyXMLpath;
	var $m_AllFieldValues;
	var $m_errors;
	var $m_errorCount;

	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		$this->m_documentId = $pFieldTempl['document_id'];
		$this->m_templateId = $pFieldTempl['template_id'];
		$this->m_instanceId = $pFieldTempl['instance_id'];
		$this->m_FilePath = $pFieldTempl['file_path'];
		//~ $this->m_FilePath = PATH_PWT_UPLOADED_FILES . '1636_material_2013_01_22_15_59_24.xls';
		//~ $this->m_FilePath = PATH_PWT_UPLOADED_FILES . '1636_material_2013_01_22_17_26_01.xls';


		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_XmlDom = new DOMDocument('1.0', 'UTF-8');
		$this->m_Xpath = '';

		/* Тук се описват елементите от upload-вания файл, които съответстват на тези от xml-a (ако има евентуално разминавания)
		*	key - от xls-a
		*	value - от xml-a
		*/
		$this->m_MatchArr = array(
				'typeStatus' => 'type_status'
			);

		$this->m_AllFieldValues = array();
		$this->m_errors = array();
		$this->m_errorCount = 0;

		global $user;
		$this->m_userId = $user->id;
		$this->m_MaterialsObjectIdx = 0;
	}

	function LoadDefTempls() {

	}

	function GetUploadedFile() {
		if (!file_exists($this->m_FilePath))
			throw new Exception('Spreadsheet: File ('.$this->m_FilePath.') not found');

		$Spreadsheet = new cspreadsheetreade_proxy();
		$this->m_ImportFile = $Spreadsheet->getSpreadSheetObject($this->m_FilePath);
	}

	function GetData(){
		$this->GetUploadedFile();
		$this->ValidateUploadedFile();
		$this->GetXMLByDocumentType();
		$this->importMaterials();
	}

	function GetXMLByDocumentType() {

		$lDocumentSerializer = new cdocument_serializer(array(
			'document_id' => (int)$this->m_documentId,
			'mode' => SERIALIZE_INTERNAL_MODE,
		));
		$lDocumentSerializer->GetData();
		$lDocumentXML = $lDocumentSerializer->getXml();


		$lXMLDom = new DOMDocument('1.0', 'UTF-8');

		if(!$lXMLDom->loadXML($lDocumentXML)){
			$this->SetError(getstr('pwt.api.xmlIsInvalid'));
		}

		$lXPath = new DOMXPath($lXMLDom);

		// Взимаме броя на добавените вече материали, за да знаем поредния номер на материала, който импортваме(object_idx)
		$this->m_MaterialsObjectIdx = $lXPath->evaluate('count(.//*[@instance_id = ' . (int)$this->m_instanceId . ']/material)');


		$lMaterialsNode = $lXPath->query('.//*[@instance_id = ' . (int)$this->m_instanceId . ']');

		/*
			ОПРЕДЕЛЯМЕ МАТЕРИАЛА ОТ СЛЕДНИТЕ ПАРАМЕТРИ
			- "Taxon treatment status type" -> id 13 в таблицата data_src
			- "Taxon treatment habitat type" -> id 14 в таблицата data_src
		*/



		$this->ExecuteSqlStatement('SELECT * FROM pwt.spGetMaterialImportDetails(' . (int)$this->m_instanceId . ')');

		$lTreatmentHabitatTypeId = (int)$this->m_con->mRs['habitat_type'];
		$lTreatmentStatusTypeId = (int)$this->m_con->mRs['status_type'];

		$this->ExecuteSqlStatement('SELECT * FROM pwt.spGetObjectXMLTemplateFileName(
			' . (int) TT_MATERIAL_CUSTOM_CREATION_ID . ',
			' . (int) $this->m_templateId . ',
			' . (int) $lTreatmentStatusTypeId. ',
			' . (int) $lTreatmentHabitatTypeId. '
		)');

		$lObjectXmlName = $this->m_con->mRs['result'];
		
		$this->m_EmptyXMLpath = PATH_OBJECTS_XSL . 'template_' . (int)$this->m_templateId . '/' . $lObjectXmlName;
		//~ trigger_error($this->m_EmptyXMLpath, E_USER_NOTICE);
	}

	function ValidateUploadedFile() {

		$lFieldNames = array();
		$lFieldValues = array();


		$this->m_MatchMaterialTypes = array(
			'Holotype'		=> array('Holotype', 'holotype'),
			'Syntype' 		=> array('Syntype', 'syntype', 'Syntype', 'syntypes', 'Syntype(s)', 'syntype(s)'),
			'Hapantotype' => array('Hapantotype', 'hapantotype', 'Hapantotypes', 'hapantotypes', 'Hapantotype(s)', 'hapantotype(s)'),
			'Paratype' => array('Paratype(s)', 'paratype(s)', 'Paratype', 'paratype', 'Paratypes', 'paratypes'),
			'Isotype' 	=> array('Isotype', 'isotype', 'Isotypes', 'isotypes', 'Isotype(s)', 'isotype(s)'),
			'Other material' 	=> array('other material', 'Other material', 'Other Material', 'Other materials', 'Other Materials'),
			'Neotype' 	=> array('Neotype', 'neotype'),
			'Lectotype' 	=> array('Lectotype', 'lectotype'),
			'Paralectotype' 	=> array('Paralectotype', 'paralectotype'),
			'Isoparatype' 	=> array('Isoparatype', 'isoparatype'),
			'Isolectotype' 	=> array('Isolectotype', 'isolectotype'),
			'Isoneotype' 	=> array('Isoneotype', 'isoneotype'),
			'Isosyntype' 	=> array('Isosyntype', 'isosyntype'),
		);


		foreach ($this->m_ImportFile as $Key => $Row){
			if ($Key == 1){ // името на полето
				$lFieldNames = $Row;
			} else { // стойността на полето
				if(!array_empty($Row)) {
					$this->m_AllFieldValues[] = array_combine($lFieldNames, $Row);
				}
			}
		}

		if(count($this->m_AllFieldValues) > (int)PWT_MAX_ALLOWED_UPLOAD_FILE_MATERIAL_COUNT)
			$this->SetError(getstr('pwt.error.uploadedFileContainsMoreThanMaxAllowedMaterials'));
		if(count($this->m_AllFieldValues) == 0)
			$this->SetError(getstr('pwt.error.uploadedFileIsEmpty'));

		// Търсим за грешка според type-a и слагаме правилния type според масива $this->m_MatchMaterialTypes
		foreach($this->m_AllFieldValues as $k => $value) {
			foreach($value as $key => $val) {
				if($key == "typeStatus" && ($val == '' || !recursive_array_search(strtolower(trim($val)), $this->m_MatchMaterialTypes))) {
					if($val == '')
						$this->SetError(getstr('pwt.error.uploadedFileHasEmptyTypeStatus'));
					else
						$this->SetError(getstr('pwt.error.uploadedFileHasError') . ' in ' . $val . ' StatusType');
				} elseif($key == "typeStatus") {
					$this->m_AllFieldValues[$k][$key] = recursive_array_search(strtolower(trim($val)), $this->m_MatchMaterialTypes);
				}
			}
		}
	}

	function prepareXML($pMaterialData, $pMaterialObjectIdx) {

		//~ $this->m_EmptyXMLpath = PATH_OBJECTS_XSL . 'template_' . (int)$this->m_templateId . '/material_ttm_extant_na_extended_dc.xml';
		if(is_file($this->m_EmptyXMLpath)){

			$lXML = file_get_contents($this->m_EmptyXMLpath);
			if(!$this->m_XmlDom->loadXML($lXML)){
				$this->SetError(getstr('pwt.api.xmlIsInvalid'));
			}

			$this->m_Xpath = new DOMXPath($this->m_XmlDom);

			$lEmptyMaterialNodes = $this->m_Xpath->query('.//*'); // elementi na xml-a ot prazniq template za obekta

			foreach($pMaterialData as $key => $val) {
				if(trim($val != '')) { // Ако имаме стойност за слагане търсим елемента, за да го сложим на мястото му
					$match_val = $this->m_MatchArr[$key]; // Ако имаме съвпадение в масива за мачване
					
					for($i = 0; $i < $lEmptyMaterialNodes->length; ++$i){
						if(strtolower($key) == $lEmptyMaterialNodes->item($i)->nodeName || $match_val == $lEmptyMaterialNodes->item($i)->nodeName) {
							$lEmptyMaterialNodeValue = $this->m_Xpath->query('./value', $lEmptyMaterialNodes->item($i)); // Kade da replace-nem
							$lNode = $lEmptyMaterialNodeValue->item(0);
							$lNode->nodeValue = '';
							$lNode->appendChild($lNode->ownerDocument->createTextNode($val));
							//~ $lEmptyMaterialNodeValue->item(0)->nodeValue = $val; // Slagame stoinostta na elementa v prazniq template
						}
					}
				}
			}
		}

		//Слагаме имената на възлите като техни атрибути понеже xml парсинга на Postgresql е непълен и не може автоматично да се вземе името на текущия възел
		//Слагаме и индексите понеже в postres-а няма релативни възли в xpath-а и не можем да го сметнем
		$lObjectsQuery = './/*[count(ancestor-or-self::fields) = 0]';

		$lObjects = $this->m_Xpath->query($lObjectsQuery);

		$lObjects->item(0)->parentNode->setAttribute('is_object', 1);
		$lObjects->item(0)->parentNode->setAttribute('node_name', $lObjects->item(0)->parentNode->nodeName);
		$lObjects->item(0)->parentNode->setAttribute('object_idx', (int)$pMaterialObjectIdx + 1);

		for($i = 0; $i < $lObjects->length; ++$i){
			$lCurrentObject = $lObjects->item($i);
			$lCurrentObject->setAttribute('is_object', 1);
			$lPreviousObjectsOfTheSameType = $this->m_Xpath->evaluate('count(./preceding-sibling::' . $lCurrentObject->nodeName . ')', $lCurrentObject);
			$lCurrentObject->setAttribute('object_idx', $lPreviousObjectsOfTheSameType + 1);
			$lCurrentObject->setAttribute('node_name', $lCurrentObject->nodeName);
		}

		$lFieldsQuery = './/fields/*';
		$lFields = $this->m_Xpath->query($lFieldsQuery);
		for($i = 0; $i < $lFields->length; ++$i){
			$lCurrentField = $lFields->item($i);
			$lCurrentField->setAttribute('is_field', 1);
			$lCurrentField->setAttribute('node_name', $lCurrentField->nodeName);
		}

		//~ echo $this->m_XmlDom->saveXML();
		//~ echo $lXMLDom->saveXML();

	}

	function importMaterials() {
		// Ако няма грешка -> import
		if(!(int)$this->m_errorCount) {
			// За всеки ред от файла подготвяме XML-a
			$i = $this->m_MaterialsObjectIdx;
			foreach($this->m_AllFieldValues as $lMaterialData) {
				$this->prepareXML($lMaterialData, $i);
				//~ trigger_error(
				//~ 'SELECT * FROM spimportdocumentobjectfromxml(
					//~ ' . (int)$this->m_documentId . ',
					//~ \'' . q($this->m_XmlDom->saveXML()) . '\',
					//~ ' . (int)$this->m_instanceId . ',
					//~ ' . (int)$this->m_userId . ')'
				//~ , E_USER_NOTICE);
				
				file_put_contents('/var/www/pensoft/viktorp.pmt/items/messaging/test.txt', 'SELECT * FROM spimportdocumentobjectfromxml(
					' . (int)$this->m_documentId . ',
					\'' . q($this->m_XmlDom->saveXML()) . '\',
					' . (int)$this->m_instanceId . ',
					' . (int)$this->m_userId . ')');
				
// 				var_dump('SELECT * FROM spimportdocumentobjectfromxml(
// 					' . (int)$this->m_documentId . ',
// 					\'' . q($this->m_XmlDom->saveXML()) . '\',
// 					' . (int)$this->m_instanceId . ',
// 					' . (int)$this->m_userId . ')'
// 				);

				$this->ExecuteSqlStatement('SELECT * FROM spimportdocumentobjectfromxml(
					' . (int)$this->m_documentId . ',
					\'' . q($this->m_XmlDom->saveXML()) . '\',
					' . (int)$this->m_instanceId . ',
					' . (int)$this->m_userId . ')'
				);

				$i++;
			}
			$this->ExecuteSqlStatement($lSql = 'SELECT * FROM pwt."XmlIsDirty"(1, ' . $this->m_documentId . ', ' . $this->m_instanceId . ')');

		}

	}

	private function SetError($pErrorMsg){
		//~ throw new Exception($pErrorMsg);
		$this->m_errors = (array(
			'error' => $pErrorMsg
		));
		$this->m_errorCount++;
	}

	private function ExecuteSqlStatement($pSql, $pSetError = true){
		if(!$this->m_con->Execute($pSql)){
			if($pSetError){
				$this->SetError($this->m_con->GetLastError());
			}
		}

	}

	function Display(){
		$this->GetData();

		return json_encode($this->m_errors);
		//~ var_dump( json_encode($this->m_errors));
	}

}
?>