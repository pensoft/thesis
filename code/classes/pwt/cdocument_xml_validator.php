<?php
/**
	Този клас ще реализира валидирането на xml-а за подадения документ
*/
class cdocument_xml_validator extends csimple {
	var $m_document_id;
	var $m_documentXmlDom;
	var $m_documentGeneratedXML;
	var $m_documentGeneratedXSD;
	var $m_errorsCounter;
	var $m_warningsCounter;
	var $m_libxml_errors;
	var $m_Xml_data;
	var $m_docroot;
	var $m_error_lines;
	var $m_XML_errors;
	var $m_all_errors;
	var $m_instances_arr;
	var $m_GroupedErrArr;

	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		$this->m_document_id = (int) $pFieldTempl['document_id'];
		$this->m_documentXmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$this->m_documentGeneratedXML = $pFieldTempl['generated_xml'];
		$this->m_documentGeneratedXSD = $pFieldTempl['generated_xsd'];
		$this->m_docroot = getenv('DOCUMENT_ROOT');
		$this->m_errorsCounter = 0;
		$this->m_warningsCounter = 0;
		$this->m_libxml_errors = array();
		$this->m_all_errors = array();
		$this->m_instances_arr = array();
		// Enable user error handling
		libxml_use_internal_errors(true);
		$this->LoadXML();
	}

	function GetData() {
		//~ $this->GetXmlSchemaValidation();
		//~ $this->GetAllNodesErrsByLineNum();

		//~ $this->m_pubdata['xml_errors'] = $this->GetXmlSchemaValidation();
		//~ $this->m_pubdata['xml_errors'] = $this->m_XML_errors;
		$this->m_pubdata['errors_count'] = (int)$this->m_errorsCounter;
		$this->m_pubdata['xml_errors'] = $this->GetAllXMLErrors();
		//~ print_r($this->m_error_lines);
		//~ print_r($this->m_all_errors);
		parent::GetData();
	}

	function GetXmlErrors() {
		$this->GetXmlSchemaValidation();
		$this->GetAllNodesErrsByLineNum();
		$this->GroupXMLErrors();
		$this->GetCitationErrors();
		$this->GetCustomCheckErrors();
		return (int)$this->m_errorsCounter;
	}

	function GetCustomCheckErrors(){
		$lCheckPerformer = new cdocument_custom_checks(array(
			'document_id' => (int)$this->m_document_id,
			'mode' => (int) CUSTOM_CHECK_VALIDATION_MODE,
			'xml' => $this->m_documentXmlDom->saveXML(),
		));
		$lCheckPerformer->GetData();
		$lErrors = $lCheckPerformer->GetErrors();
		foreach ($lErrors as $lCurrentError){
			$this->m_errorsCounter++;
			$this->m_instances_arr[] = $lCurrentError['instance_id'];
			$this->m_GroupedErrArr[XML_OTHER_UNDEFINED_ERROR][] = array (
				'node_instance_id' => $lCurrentError['instance_id'],
				'node_instance_name' => $lCurrentError['instance_name'],
				'node_attribute_field_name' => $lCurrentError['msg'],
			);
		}
		if(count($lErrors)){
			$this->m_Xml_data = "";
		}
	}
	
	function GetCitationErrors() {
		$this->PrepareDocumentCitations();
		
		$lQuery = 'SELECT * FROM spGetDocumentCitationsForValidation(
				' . (int)$this->m_document_id . ',
				ARRAY[' . (int)CITATION_FIGURE_PLATE_TYPE_ID . ',' . (int)CITATION_TABLE_TYPE_ID . ', ' . (int)CITATION_REFERENCE_TYPE_ID . '])
				';
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lQuery);
		$lCon->MoveFirst();
		$lCitations = array();
		
		while(!$lCon->Eof()){
			$lPostgresStr = trim($lCon->mRs['citation_objects'], '{}');
			$lElements = explode(',', $lPostgresStr);
			
			$lCitations[$lCon->mRs['citation_id']] = array(
				'citation_type' => $lCon->mRs['citation_type'],
				'citation_objects' => $lElements,
				'is_plate' => $lCon->mRs['is_plate'],
			);
			$lCon->MoveNext();
		}
		$lCon->Close();
		
		$lQuery = 'SELECT * FROM spGetDocumentFiguresAndTables(' . (int)$this->m_document_id . ')';
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lQuery);
		$lCon->MoveFirst();
		$lFigsTables = array();
		
		while(!$lCon->Eof()){
			$lFigsTables[$lCon->mRs['object_id']] = array(
				'object_type' => $lCon->mRs['object_type'],
				'is_plate' => $lCon->mRs['is_plate'],
				'position' => $lCon->mRs['position'],
			);
			$lCon->MoveNext();
		}
		$lCon->Close();
		
		$lQuery = 'SELECT * FROM spGetDocumentReferences(' . (int)$this->m_document_id . ')';
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lQuery);
		$lCon->MoveFirst();
		$lReferences = array();
		
		while(!$lCon->Eof()){
			$lReferences[] = (int)$lCon->mRs['reference_instance_id'];
			$lCon->MoveNext();
		}
		$lCon->Close();
		
		if(count($lCitations)){ // Всички цитирани обекти
			foreach($lCitations as $lCitation) {
				if($lCitation['citation_type'] == (int)CITATION_TABLE_TYPE_ID) {
					$lCittTablesArr[] = $lCitation['citation_objects'];
				} elseif($lCitation['citation_type'] == (int)CITATION_REFERENCE_TYPE_ID) {
					$lCittReferencesArr[] = $lCitation['citation_objects'];
				} elseif($lCitation['citation_type'] == (int)CITATION_FIGURE_PLATE_TYPE_ID) {
					$lCittFiguresArr[] = $lCitation['citation_objects'];
				}
			}
		}
		
		if(count($lFigsTables)){ // Фигури и таблици на едно място
			foreach($lFigsTables as $key => $val) {
				if($val['object_type'] == (int)CITATION_TABLE_TYPE_ID) {
					$lTablesArr[] = $key;
				} elseif($val['object_type'] == (int)CITATION_FIGURE_PLATE_TYPE_ID) {
					$lFiguresArr[] = $key;
				}
			}
			foreach($lFiguresArr as $lFigure) {
				if(!$this->in_array_r($lFigure, $lCittFiguresArr)) {
					$this->m_GroupedErrArr[XML_UNCITED_FIGURES_ERROR][] = array (
						'node_instance_name' => 'figures',
						'cited_error_type' => (int)CITATION_FIGURE_PLATE_TYPE_ID,
						'document_id' => (int)$this->m_document_id,
						'node_attribute_field_name' => 'Fig ' . $lFigsTables[$lFigure]['position'] . ' is not cited',
					);
					$this->m_errorsCounter++;
				}
			}
			foreach($lTablesArr as $lTable) {
				if(!$this->in_array_r($lTable, $lCittTablesArr)) {
					$this->m_GroupedErrArr[XML_UNCITED_TABLES_ERROR][] = array (
						'node_instance_name' => 'tables',
						'cited_error_type' => (int)CITATION_TABLE_TYPE_ID,
						'document_id' => (int)$this->m_document_id,
						'node_attribute_field_name' => 'Table ' . $lFigsTables[$lTable]['position'] . ' is not cited',
					);
					$this->m_errorsCounter++;
				}
			}
		}
		
		if(count($lReferences)){ // Референции
			foreach($lReferences as $lReference) {
				if(!$this->in_array_r($lReference, $lCittReferencesArr)) {
					$this->m_GroupedErrArr[XML_UNCITED_REFERENCES_ERROR][] = array (
						'node_instance_name' => 'reference',
						'cited_error_type' => (int)CITATION_REFERENCE_TYPE_ID,
						'node_attribute_field_name' => 'References is not cited',
						'node_instance_id' => $lReference,
						'node_instance_name' => 'reference',
					);
					$this->m_errorsCounter++;
				}
			}
		}
	}
	
	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r($needle, $item, $strict))) {
				return true;
			}
		}

		return false;
	}
	
	function PrepareDocumentCitations() {
		static $lDocumentXmls = array();
		
		$lDocumentSerializer = new cdocument_serializer(array(
			'document_id' => $this->m_document_id,
			'mode' => (int)SERIALIZE_INTERNAL_MODE,
		));
		
		$lDocumentSerializer->GetData();
		$lDocumentXmls[(int)$this->m_document_id][(int)$pMode] = $lDocumentSerializer->getXml();
		
		prepareDocumentCitations((int)$this->m_document_id, $lDocumentXmls[(int)$this->m_document_id][(int)SERIALIZE_INTERNAL_MODE]);
		
	}
	
	function GetFieldsInstances() {
		return $this->m_instances_arr;
	}

	/**
	 * Loading XML
	 */

	function LoadXML() {
		if($this->m_documentGeneratedXML) {
			$this->m_documentXmlDom->load(SITE_URL . '/'. $this->m_documentGeneratedXML);
			$this->m_documentXmlDom->formatOutput = true;
			$this->m_documentXmlDom->loadXML($this->m_documentXmlDom->saveXML());
		}
	}

	/**
	 * Gets all errors info in $this->m_all_errors array
	 */

	function GetAllNodesErrsByLineNum() {
		
		$lAllNodeLineNumbers = $this->m_documentXmlDom->getElementsByTagName('*');
		if($lAllNodeLineNumbers->length) {
			foreach ($lAllNodeLineNumbers as $node) {
				//~ echo $node->getLineNo() ."\n";
				$lErrType = $this->GetErrType($node->getLineNo(), $this->m_error_lines);
				//~ echo $lErrType .':' . $node->getLineNo() ."\n";
				if($lErrType) {
					if($node->parentNode) {
						if ($node->parentNode->hasAttributes()) { 
							
							if($node->parentNode->getAttribute('id') && $node->parentNode->getAttribute('field_name')) {
								if($node->parentNode->parentNode && $node->parentNode->parentNode->hasAttributes()){
									if($node->parentNode->parentNode->getAttribute('instance_id') && $node->parentNode->parentNode->getAttribute('display_name')){
										$lInstance_id = (int)$node->parentNode->parentNode->getAttribute('instance_id');
										$lInstance_name = $node->parentNode->parentNode->getAttribute('display_name');
									}
								} elseif($node->parentNode->parentNode->parentNode && $node->parentNode->parentNode->parentNode->hasAttributes()) {
									if($node->parentNode->parentNode->parentNode->getAttribute('instance_id') && $node->parentNode->parentNode->parentNode->getAttribute('display_name')){
										$lInstance_id = (int)$node->parentNode->parentNode->parentNode->getAttribute('instance_id');
										$lInstance_name = $node->parentNode->parentNode->parentNode->getAttribute('display_name');
									}
								}

								$this->m_all_errors[] = array (
										'node_name' => $node->parentNode->nodeName,
										'node_instance_id' => $lInstance_id,
										'node_instance_name' => $lInstance_name,
										'node_attribute_id' => (int)$node->parentNode->getAttribute('id'),
										'node_attribute_field_name' => $node->parentNode->getAttribute('field_name'),
										'node_error_type' => $lErrType,
								);
								$this->m_errorsCounter++;
							} else {
							/*
								if($node->hasAttributes() && $node->getAttribute('instance_id') && $node->getAttribute('display_name')){
									$lInstance_id = (int)$node->getAttribute('instance_id');
									$lInstance_name = $node->getAttribute('display_name');
								}
								$this->m_all_errors[] = array (
										'node_name' => $node->parentNode->nodeName,
										'node_instance_id' => $lInstance_id,
										'node_instance_name' => $lInstance_name,
										'node_error_type' => XML_OTHER_UNDEFINED_ERROR,
								);
							*/
							}
							//~ echo $node->parentNode->getAttribute('id') . '-' . $node->parentNode->getAttribute('field_name') . '- Type:'. $in_array . "\n";
							
						} else {
							if($node->hasAttributes() && $node->getAttribute('instance_id') && $node->getAttribute('display_name')){
								$lInstance_id = (int)$node->getAttribute('instance_id');
								$lInstance_name = $node->getAttribute('display_name');
								$this->m_all_errors[] = array (
										'node_name' => $node->parentNode->nodeName,
										'node_instance_id' => $lInstance_id,
										'node_instance_name' => $lInstance_name,
										'node_error_type' => $lErrType,
								);
								$this->m_errorsCounter++;
							}
							
						}
					}
				}
			}
		}
	}

	function GetAllXMLErrors() {
		return prepareXMLErrors($this->m_GroupedErrArr);
	}

	/**
	 * Groups errors by type
	 */

	function GroupXMLErrors() {
		if(is_array($this->m_all_errors)) {
			foreach($this->m_all_errors as $err) {
				global $gXMLErrors;
				if($gXMLErrors[$err['node_error_type']]) {
					$this->m_instances_arr[] = $err['node_instance_id'];
					if($err['node_error_type'] == XML_OTHER_UNDEFINED_ERROR) {
						$this->m_GroupedErrArr[$err['node_error_type']][] = array (
											'node_instance_id' => $err['node_instance_id'],
											'node_instance_name' => $err['node_instance_name'],
											'node_attribute_field_name' => $err['node_name'],
										);
					} else {
						$this->m_GroupedErrArr[$err['node_error_type']][] = array (
											'node_instance_id' => $err['node_instance_id'],
											'node_instance_name' => $err['node_instance_name'],
											'node_attribute_field_name' => $err['node_attribute_field_name'],
										);
					}
				}
			}
			//~ print_r($this->m_GroupedErrArr);
			//~ return $this->m_GroupedErrArr;
		}

	}

	/**
	 * Gets errtype by line number
	 */

	function GetErrType($val, $arr) {
		
		foreach ($arr as $key){
			if(array_key_exists($val, $key)) {
				foreach ($key as $val){
					return $val;
				}
			}
		}
	}

	/**
	 *
	 */

	function GetXmlSchemaValidation() {
		if (!$this->m_documentXmlDom->schemaValidate(SITE_URL . '/'. $this->m_documentGeneratedXSD)) {
			$this->LibXMLGetAllErrors();
			//~ print_r($this->m_error_lines);
			//~ $this->m_Xml_data = $this->LibXMLGetAllErrors();
		} else {
			$this->m_Xml_data = "validated";
		}
		return $this->m_Xml_data;
	}

	/**
	 * Get single error
	 */

	function LibXMLGetError($error) {
		//~ $return = "<br/>\n";
		//~ echo $error->line ."\n";
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				//~ $this->m_warningsCounter++;
				$err_type = $this->GetErrorTypeByCode($error->code);
				//~ $return .= "<b>Warning $error->code</b>: ";
				break;
			case LIBXML_ERR_ERROR:
				//~ $this->m_errorsCounter++;
				$err_type = $this->GetErrorTypeByCode($error->code);
				//~ $return .= "<b>Error $error->code</b>: ";
				break;
			case LIBXML_ERR_FATAL:
				$err_type = $this->GetErrorTypeByCode($error->code);
				//~ $return .= "<b>Fatal Error $error->code</b>: ";
				break;
		}
		$return .= trim($error->message);
		if ($error->file) {
			//~ $return .=    " on line <b>$error->line</b>";
		}
		$this->m_error_lines[] = array (
						$error->line => $err_type
					);
		//~ $this->m_error_lines[] = $error->line;
		//~ return $return;
	}

	function GetErrorsCount() {
		return $this->m_errorsCounter;
	}

	/**
	 * Get all errors
	 */

	function LibXMLGetAllErrors() {
		$this->m_libxml_errors = libxml_get_errors();
		//~ print_r($this->m_libxml_errors);
		//~ exit;
		foreach ($this->m_libxml_errors as $error) {
			//~ $this->m_XML_errors .= $this->LibXMLGetError($error);
			$this->LibXMLGetError($error);
		}
		//~ $this->m_pubdata['errors_count'] = (int)$this->m_errorsCounter;
		//~ $this->m_pubdata['warnings_count'] = (int)$this->m_warningsCounter;
		libxml_clear_errors();
	}

	/**
	 * Get error type by code
	 */

	function GetErrorTypeByCode($errcode) {
		switch ((int)$errcode) {
			case 1824: // Invalid type
				$err_type = XML_INVALID_FIELD_TYPE_ERROR;
				break;
			case 1871: // Missing field
			case 1831:  // The value has a length of "0" that interrupts the allowed minimum length of "1"
				$err_type = XML_MISSING_FIELD_ERROR;
				break;
			case 1866: //Attribute is not allowed
				$err_type = XML_UNALLOWED_ATTRIBUTE_ERROR;
				break;
			case 1831: // The value has a length of "0" that interrupts the allowed minimum length of "1"
				$err_type = XML_INCORRECT_FIELD_LENGTH_ERROR;
				break;
			default:
				$err_type = XML_OTHER_UNDEFINED_ERROR;
				break;
		}
		return $err_type;
	}

}
?>