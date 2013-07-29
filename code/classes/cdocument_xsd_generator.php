<?php
define('XSD_SCHEMA_LOCATION', 'http://www.w3.org/2001/XMLSchema');
/**
	Този клас ще реализира генерирането на xsd схема за подадения документ
*/
class cdocument_xsd_generator extends csimple {
	var $m_document_id;
	// Dom Document-а на обекта
	var $m_documentXmlDom;
	var $m_schemaXmlNode;
	var $m_mode;
	var $m_dataSrcIds;

	/*
	 * Тук ще пазим цялата информация за инстанциите на обектите. За всеки
	 * instance в ключа children ще пазим id-та на инстанциите на подобектите.
	 * За всеки instance в ключа fields ще пазим масив във формат id =>
	 * $lFieldDataArray с информация за всичките field-ове на instance-a
	 */
	var $m_templateObjectDetails;

	var $m_figureDetails;
	var $m_generatedObjectIds;

	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		$this->m_document_id = (int) $pFieldTempl['document_id'];
		$this->m_templateObjectDetails = array();
		$this->m_documentXmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$this->m_generatedObjectIds = array();
		$this->m_dataSrcIds = array();

		$this->m_mode = $pFieldTempl['mode'];
		if(!in_array($this->m_mode, array((int)SERIALIZE_INTERNAL_MODE, (int)SERIALIZE_INPUT_MODE))){
			$this->m_mode = (int)SERIALIZE_INTERNAL_MODE;

		}
	}

	function GetData() {
		$this->generateDocumentSchema();
		$this->m_pubdata['xml'] = $this->getXml();
		parent::GetData();
	}

	function getXml() {
		$this->m_documentXmlDom->formatOutput = TRUE;
		return $this->m_documentXmlDom->saveXML();
	}

	function xsdElem($elem)	{
		return $this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:'.$elem);
	}

	/*
	 * Първо запазваме информацията за всички обекти и всички field-ове. След
	 * това започваме рекурсивно да сериализираме обектите от 1-во ниво
	 */
	function generateDocumentSchema() {

		$lSchemaXmlNode = $this->m_documentXmlDom->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:schema'));

		$this->m_schemaXmlNode = $lSchemaXmlNode;

		$lDocumentXmlNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lDocumentXmlNode->setAttribute('name', 'document');
		$lDocumentXmlNode->setAttribute('type', 'documentType');
		// $lDocumentXmlNode->setAttribute('name', 'object_id');
		// $lDocumentXmlNode->setAttribute('type', 'xsd:integer');

		$lDocumentTypeNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
		$lDocumentTypeNode->setAttribute('name', 'documentType');
		$lDocumentSeqNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));

		$lDocInfoNode = $lDocumentSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lDocInfoNode->setAttribute('name', 'document_info');
// 		$lDocInfoNode->setAttribute('type', 'objectsType');

		$lObjectsXmlNode = $lDocumentSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lObjectsXmlNode->setAttribute('name', 'objects');
		$lObjectsXmlNode->setAttribute('type', 'objectsType');

		$lFiguresXmlNode = $lDocumentSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lFiguresXmlNode->setAttribute('name', 'figures');
		$lFiguresXmlNode->setAttribute('type', 'figuresType');

		$lTablesXmlNode = $lDocumentSeqNode->appendChild($this->xsdElem('element'));
		$lTablesXmlNode->setAttribute('name', 'tables');
		$lTablesXmlNode->setAttribute('type', 'tablesType');

		$lDocumentAttribNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
		$lDocumentAttribNode->setAttribute('name', 'id');
		$lDocumentAttribNode->setAttribute('type', 'xsd:integer');
		
		$lDocumentAttribNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
		$lDocumentAttribNode->setAttribute('name', 'journal_id');
		$lDocumentAttribNode->setAttribute('type', 'xsd:integer');


		$lCon = new DBCn();
		$lCon->Open();

		$lObjectsSql = '';
		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			$lObjectsSql = 'SELECT o.*, os.min_occurrence, os.max_occurrence, char_length(o.pos)/2 as level, o.parent_id real_parent_id
			FROM pwt.v_distinct_document_template_objects o
			LEFT JOIN pwt.document_template_objects p ON p.id = o.parent_id
			LEFT JOIN pwt.object_subobjects os ON os.object_id = p.object_id AND os.subobject_id = o.object_id
			WHERE o.document_id = ' . $this->m_document_id . '
			ORDER BY o.pos ASC';
		}else if ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
			$lObjectsSql = 'SELECT o.*, os.min_occurrence, os.max_occurrence, char_length(o.pos)/2 as level, rp.id as real_parent_id
			FROM pwt.v_distinct_document_template_objects o
			LEFT JOIN pwt.document_template_objects p ON p.id = o.parent_id
			LEFT JOIN pwt.object_subobjects os ON os.object_id = p.object_id AND os.subobject_id = o.object_id
			JOIN pwt.v_document_template_objects_xml_parent rp ON rp.child_doc_templ_object_id = o.id AND rp.real_doc_id = ' . $this->m_document_id . '
			WHERE o.document_id = ' . $this->m_document_id . ' AND o.display_object_in_xml = 1
				AND (o.parent_id IS NULL OR rp.id IS NOT NULL)
			ORDER BY o.pos ASC';
		}
		file_put_contents('/tmp/test.sql', $lObjectsSql);
		// Взимаме всичките обекти с една заявка.
		$lCon->Execute($lObjectsSql);
		$lCon->MoveFirst();
		// Тук ще пазим обектите от 1во ниво
		$lLevelOneTemplateObjects = array();
// 		var_dump($lCon->mRs);
		while(! $lCon->Eof()){
			$lTemplateObjectId = (int) $lCon->mRs['id'];
			$this->m_templateObjectDetails[$lTemplateObjectId] = $lCon->mRs;
			$this->m_templateObjectDetails[$lTemplateObjectId]['children'] = array();
			$this->m_templateObjectDetails[$lTemplateObjectId]['fields'] = array();
			if((int) $lCon->mRs['level'] == 1){
				$lLevelOneTemplateObjects[] = $lTemplateObjectId;
			}

			if((int) $lCon->mRs['real_parent_id']){
				$this->m_templateObjectDetails[(int) $lCon->mRs['real_parent_id']]['children'][] = $lTemplateObjectId;
			}

			$lCon->MoveNext();
		}

		$lCon->CloseRs();
		// Взимаме всичките field-ове с една заявка.


		$lFieldsSql = '';
		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			$lFieldsSql = 'SELECT f.id as field_id, f.type, ft.value_column_name, of.label as field_name, ds.id as data_src_id, ds.query as data_src_query,
				of.control_type, of.xml_node_name, o.id as template_object_id, of.allow_nulls::int as allow_nulls, o.id as  template_object_id
			FROM pwt.fields f
			JOIN pwt.field_types ft ON ft.id = f.type
			JOIN pwt.object_fields of ON of.field_id = f.id
			JOIN pwt.v_distinct_document_template_objects o ON o.object_id = of.object_id
			LEFT JOIN pwt.data_src ds ON ds.id = of.data_src_id
			WHERE o.document_id = ' . (int)$this->m_document_id . '
			ORDER BY o.pos ASC, of.id
			';
		}elseif ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
			$lFieldsSql = 'SELECT f.id as field_id, f.type, ft.value_column_name, of.label as field_name, ds.id as data_src_id, ds.query as data_src_query,
				of.control_type, of.xml_node_name, o.id as template_object_id, of.allow_nulls::int as allow_nulls, rp.id as real_template_object_id, o.display_object_in_xml
			FROM pwt.fields f
			JOIN pwt.field_types ft ON ft.id = f.type
			JOIN pwt.object_fields of ON of.field_id = f.id
			JOIN pwt.v_distinct_document_template_objects o ON o.object_id = of.object_id
			JOIN pwt.v_document_template_objects_xml_parent rp ON rp.child_doc_templ_object_id = o.id AND rp.real_doc_id = ' . $this->m_document_id . '
			LEFT JOIN pwt.data_src ds ON ds.id = of.data_src_id
			WHERE o.document_id = ' . (int)$this->m_document_id . ' AND of.display_in_xml = 1
				 AND (o.parent_id IS NULL OR rp.id IS NOT NULL)
				 AND (o.display_object_in_xml = 1 OR o.display_object_in_xml = 4)
			ORDER BY o.pos ASC, of.id
			';
		}
		file_put_contents('/tmp/test123.sql', $lFieldsSql);
		$lCon->Execute($lFieldsSql);
		$lCon->MoveFirst();
		while(! $lCon->Eof()){
			/*
			 * Пазим field-овете към обекта, понеже id-то на field-овете не е
			 * уникално (може няколко обекта да имат field с едно и също id
			 */
			$lTempObjectId = (int) $lCon->mRs['template_object_id'];

			if ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
				if($lCon->mRs['display_object_in_xml'] == 4){
					$lTempObjectId = (int) $lCon->mRs['real_template_object_id'];
				}
			}

			$lDataSrcId = (int) $lCon->mRs['data_src_id'];
			if($lDataSrcId && !in_array($lDataSrcId, $this->m_dataSrcIds)){
				$this->m_dataSrcIds[$lDataSrcId] = $lDataSrcId;
			}

			$this->m_templateObjectDetails[$lTempObjectId]['fields'][$lCon->mRs['field_id']] = $lCon->mRs;
			$lCon->MoveNext();
		}

		foreach ($this->m_dataSrcIds as $lDataSrcId => $lData){
			$lSql = 'SELECT name, query, xml_node_name
				FROM pwt.data_src
				WHERE id = ' . $lDataSrcId;
			$lCon->Execute($lSql);
			$this->m_dataSrcIds[$lDataSrcId] = $lCon->mRs;
			$lQuery = $lCon->mRs['query'];
			$lValues = array();
			if($lQuery){
				$lCon->Execute($lQuery);
				while(!$lCon->Eof()){
					$lValues[$lCon->mRs['id']] = $lCon->mRs['name'];
					$lCon->MoveNext();
				}
			}
			$this->m_dataSrcIds[$lDataSrcId]['values'] = $lValues;

		}

		// Слагаме главните елементи в objects

		$lObjectsTypeNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
		$lObjectsTypeNode->setAttribute('name', 'objectsType');
		$lObjectsSeqNode = $lObjectsTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));
		foreach($lLevelOneTemplateObjects as $lTemplateObjectId){
			$lChildDetails = $this->m_templateObjectDetails[$lTemplateObjectId];
			$lChildElementXmlNode = $lObjectsSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));

			if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
// 				$lChildElementXmlNode->setAttribute('name', 'object');
				$lChildElementXmlNode->setAttribute('name', $lChildDetails['xml_node_name']);
			}else if ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
				$lChildElementXmlNode->setAttribute('name', $lChildDetails['xml_node_name']);
			}

			$lChildElementXmlNode->setAttribute('type', $lChildDetails['xml_node_name'] . 'Type');
			$lChildElementXmlNode->setAttribute('minOccurs', 1);
			$lChildElementXmlNode->setAttribute('maxOccurs', 1);

			$this->generateObject($lTemplateObjectId, $lObjectsXmlNode);
		}

		// Генерираме типовете на главните обекти
		foreach($lLevelOneTemplateObjects as $lTemplateObjectId){

			$this->generateObject($lTemplateObjectId, $lObjectsXmlNode);
		}

		//$this->generateFiguresDefinition();
		//$this->generateTablesDefinition();
		$this->generateFieldTypesDefinition();
		$this->generateDataSrcs();
	}

	/**
	 * Тук ще генерираме типа на 1 обект.
	 * За целта подаваме id-то на document_template_object-а
	 * на обекта
	 *
	 * @param $pTemplateObjectId
	 */
	protected function generateObject($pTemplateObjectId) {
		$lTemplateObjectDetails = $this->m_templateObjectDetails[$pTemplateObjectId];
		if(array_key_exists($lTemplateObjectDetails['object_id'], $this->m_generatedObjectIds) && in_array($lTemplateObjectDetails['xml_node_name'], $this->m_generatedObjectIds[$lTemplateObjectDetails['object_id']])){
			return;
		}

		if(!array_key_exists($lTemplateObjectDetails['object_id'], $this->m_generatedObjectIds)){
			$this->m_generatedObjectIds[$lTemplateObjectDetails['object_id']] = array();
		}
		$this->m_generatedObjectIds[$lTemplateObjectDetails['object_id']][] = $lTemplateObjectDetails['xml_node_name'];



		$lSchemaXmlNode = $this->m_schemaXmlNode;
		$lDocumentTypeNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
		$lDocumentTypeNode->setAttribute('name', $lTemplateObjectDetails['xml_node_name'] . 'Type');



		$lDocumentSeqNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));

		// Първо добавяме wrapper-a на field-овете, ако имаме field-ове
		if(count($lTemplateObjectDetails['fields'])){
			$lFieldsElementXmlNode = $lDocumentSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
			$lFieldsElementXmlNode->setAttribute('name', 'fields');
			$lFieldsElementXmlNode->setAttribute('type', $lTemplateObjectDetails['xml_node_name'] . 'FieldsType');
			$lFieldsElementXmlNode->setAttribute('minOccurs', 1);
			$lFieldsElementXmlNode->setAttribute('maxOccurs', 1);
		}


		// След това добавяме всички подобекти като елементи на главния обект
		foreach($lTemplateObjectDetails['children'] as $lChildInstanceId){
			$lChildDetails = $this->m_templateObjectDetails[$lChildInstanceId];
			$lChildElementXmlNode = $lDocumentSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));


			if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
// 				$lChildElementXmlNode->setAttribute('name', 'object');
				$lChildElementXmlNode->setAttribute('name', $lChildDetails['xml_node_name']);
			}else if ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
				$lChildElementXmlNode->setAttribute('name', $lChildDetails['xml_node_name']);
			}

			$lChildElementXmlNode->setAttribute('type', $lChildDetails['xml_node_name'] . 'Type');
			$lChildElementXmlNode->setAttribute('minOccurs', (int)$lChildDetails['min_occurrence']);

			$lMaxOccurs = (int)$lChildDetails['max_occurrence'];
			if($lMaxOccurs >= 99999999){
				$lMaxOccurs = 'unbounded';
			}
			$lChildElementXmlNode->setAttribute('maxOccurs', $lMaxOccurs);
		}

		if ($this->m_mode == (int) SERIALIZE_INTERNAL_MODE){
			$lObjectIdAttributeNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
			$lObjectIdAttributeNode->setAttribute('name', 'object_id');
			$lObjectIdAttributeNode->setAttribute('type', 'xsd:integer');
			$lObjectIdAttributeNode->setAttribute('fixed', $lTemplateObjectDetails['object_id']);

			$lInstanceIdAttributeNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
			$lInstanceIdAttributeNode->setAttribute('name', 'instance_id');
			$lInstanceIdAttributeNode->setAttribute('type', 'xsd:integer');

			$lDisplayNameAttributeNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
			$lDisplayNameAttributeNode->setAttribute('name', 'display_name');
			$lDisplayNameAttributeNode->setAttribute('type', 'xsd:string');
// 			$lDisplayNameAttributeNode->setAttribute('fixed', $lTemplateObjectDetails['xml_node_name']);

			$lPosAttributeNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
			$lPosAttributeNode->setAttribute('name', 'pos');
			$lPosAttributeNode->setAttribute('type', 'xsd:string');
		}

		if ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
			if($lTemplateObjectDetails['generate_xml_id']){
				$lAttributeNode = $lDocumentTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
				$lAttributeNode->setAttribute('name', 'id');
				$lAttributeNode->setAttribute('type', 'xsd:integer');
			}
		}



		//След това генерираме дефиницията на field wrapper-a
		if(count($lTemplateObjectDetails['fields'])){
			$lFieldsWrapperTypeNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
			$lFieldsWrapperTypeNode->setAttribute('name', $lTemplateObjectDetails['xml_node_name'] . 'FieldsType');
			$lFieldsWrapperSeqNode = $lFieldsWrapperTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));
			foreach($lTemplateObjectDetails['fields'] as $lFieldId => $pFieldData){

				$this->generateField($lFieldsWrapperSeqNode, $pFieldData);
			}
		}

		//След това генерираме типовете на всички подобекти
		foreach($lTemplateObjectDetails['children'] as $lChildInstanceId){
			$this->generateObject($lChildInstanceId);
		}

	}

	/**
	 * Тук генерираме схемата на 1 field. Типовете на field-овете ще генерираме по надолу
	 *
	 * @param lExtensionNode- xml типа на обекта, към който е field-a
	 * @param lExtensionNodey - информацията за field-а
	 */
	protected function generateField(&$pParentXmlNode, $pFieldData) {
		$lFieldElementXmlNode = $pParentXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));

		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
// 			$lFieldElementXmlNode->setAttribute('name', 'field');
			$lFieldElementXmlNode->setAttribute('name', $pFieldData['xml_node_name']);

			$lComplexTypeNode = $lFieldElementXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
			$lComplexContentNode = $lComplexTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexContent'));
			$lExtensionNode = $lComplexContentNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:extension'));

			$lExtensionNode->setAttribute('base', $this->getFieldTypeName($pFieldData['type'], $pFieldData['data_src_id'], $pFieldData['allow_nulls'], $pFieldData['control_type']));
			$lIdAttributeNode = $lExtensionNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
			$lIdAttributeNode->setAttribute('name', 'id');
			$lIdAttributeNode->setAttribute('type', 'xsd:integer');
			$lIdAttributeNode->setAttribute('fixed', $pFieldData['field_id']);


			$lFieldNameAttributeNode = $lExtensionNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
			$lFieldNameAttributeNode->setAttribute('name', 'field_name');
			$lFieldNameAttributeNode->setAttribute('type', 'xsd:string');
// 			$lFieldNameAttributeNode->setAttribute('fixed', $pFieldData['xml_node_name']);

		}else if ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
			$lFieldElementXmlNode->setAttribute('name', $pFieldData['xml_node_name']);
			$lFieldType = $this->getFieldTypeName($pFieldData['type'], $pFieldData['data_src_id'], $pFieldData['allow_nulls'], $pFieldData['control_type']);
			if($lFieldType != ''){
				$lFieldElementXmlNode->setAttribute('type', $lFieldType);
			}
		}


	}

	/**
	 * Тук генерираме дефиницията на обектите на фигурите
	 */
	function generateFiguresDefinition(){
		//Дефиниция на всички фигури
		$lSchemaXmlNode = $this->m_schemaXmlNode;
		$lFiguresTypeNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
		$lFiguresTypeNode->setAttribute('name', 'figuresType');

		$lFiguresSeqNode = $lFiguresTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));

		$lFigureXmlNode = $lFiguresSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lFigureXmlNode->setAttribute('name', 'figure');
		$lFigureXmlNode->setAttribute('type', 'figureType');
		$lFigureXmlNode->setAttribute('minOccurs', '0');
		$lFigureXmlNode->setAttribute('maxOccurs', 'unbounded');

		//Дефиниция на една фигура
		$lFigureTypeNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
		$lFigureTypeNode->setAttribute('name', 'figureType');

		$lFigureSeqNode = $lFigureTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));
		//Caption
		$lCaptionXmlNode = $lFigureSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lCaptionXmlNode->setAttribute('name', 'caption');
		$lCaptionXmlNode->setAttribute('type', 'xsd:string');
		$lCaptionXmlNode->setAttribute('minOccurs', '0');
		$lCaptionXmlNode->setAttribute('maxOccurs', '1');

		//Url
		$lUrlSeq = $lFigureSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));
		$lUrlSeq->setAttribute('minOccurs', '0');
		$lUrlSeq->setAttribute('maxOccurs', 'unbounded');

		$lUrlXmlNode = $lUrlSeq->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lUrlXmlNode->setAttribute('name', 'url');
		$lUrlXmlNode->setAttribute('minOccurs', '0');
		$lUrlXmlNode->setAttribute('maxOccurs', '1');
		$lUrlComplexType = $lUrlXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));

		$lUrlTypeSimpleContent = $lUrlComplexType->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:simpleContent'));
		$lUrlTypeRestriction = $lUrlTypeSimpleContent->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:extension'));
		$lUrlTypeRestriction->setAttribute('base', 'xsd:string');
		$lUrlIdAttributeNode = $lUrlTypeRestriction->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
		$lUrlIdAttributeNode->setAttribute('name', 'id');
		$lUrlIdAttributeNode->setAttribute('type', 'xsd:integer');

		//PhotoDescription
		$lPhotoDesc = $lUrlSeq->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lPhotoDesc->setAttribute('name', 'photo_description');
		$lPhotoDesc->setAttribute('type', 'xsd:string');
		$lPhotoDesc->setAttribute('minOccurs', '0');
		$lPhotoDesc->setAttribute('maxOccurs', '1');



		//Fig node attributes
		$lIdAttributeNode = $lFigureTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
		$lIdAttributeNode->setAttribute('name', 'id');
		$lIdAttributeNode->setAttribute('type', 'xsd:integer');

		$lIsPlateAttributeNode = $lFigureTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
		$lIsPlateAttributeNode->setAttribute('name', 'is_plate');
		$lIsPlateAttributeNode->setAttribute('type', 'xsd:integer');

		$lPlateTypeAttributeNode = $lFigureTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
		$lPlateTypeAttributeNode->setAttribute('name', 'type');
		$lPlateTypeAttributeNode->setAttribute('type', 'xsd:integer');
	}

	function generateTablesDefinition(){
		//Дефиниция на всички фигури
		$lSchemaXmlNode = $this->m_schemaXmlNode;
		$lTablesTypeNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
		$lTablesTypeNode->setAttribute('name', 'tablesType');

		$lTablesSeqNode = $lTablesTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));

		$lTableXmlNode = $lTablesSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lTableXmlNode->setAttribute('name', 'table');
		$lTableXmlNode->setAttribute('type', 'tableType');
		$lTableXmlNode->setAttribute('minOccurs', '0');
		$lTableXmlNode->setAttribute('maxOccurs', 'unbounded');
		
		//Дефиниция на една таблица
		$lTableTypeNode = $lSchemaXmlNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:complexType'));
		$lTableTypeNode->setAttribute('name', 'tableType');

		$lTableSeqNode = $lTableTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:sequence'));
		//Caption
		$lCaptionXmlNode = $lTableSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lCaptionXmlNode->setAttribute('name', 'title');
		$lCaptionXmlNode->setAttribute('minOccurs', '0');
		$lCaptionXmlNode->setAttribute('maxOccurs', '1');
		
		//Content
		$lContentXmlNode = $lTableSeqNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:element'));
		$lContentXmlNode->setAttribute('name', 'description');
		$lContentXmlNode->setAttribute('type', 'fieldNotEmpty');
		
		//Table node attributes
		$lIdAttributeNode = $lTableTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
		$lIdAttributeNode->setAttribute('name', 'id');
		$lIdAttributeNode->setAttribute('type', 'xsd:integer');
		$lIdAttributeNode->setAttribute('use', 'required');
		
		$lTableAttribNode = $lTableTypeNode->appendChild($this->m_documentXmlDom->createElementNS(XSD_SCHEMA_LOCATION, 'xsd:attribute'));
		$lTableAttribNode->setAttribute('name', 'position');
		$lTableAttribNode->setAttribute('type', 'xsd:integer');
	
	}

	/**
	 * Генерираме типовете на полетата
	 */
	function generateFieldTypesDefinition(){

		$lBaseTypesXml = '
			<xsd:simpleType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="str_not_empty">
				<xsd:restriction base="xsd:string">
					<xsd:whiteSpace value="collapse"/>
					<xsd:minLength value="1"/>
				</xsd:restriction>
			</xsd:simpleType>

			<xsd:simpleType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="empty_str">
				<xsd:restriction base="xsd:string">
					<xsd:whiteSpace value="collapse"/>
					<xsd:maxLength value="0" />
				</xsd:restriction>
			</xsd:simpleType>

			<xsd:simpleType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="tstamp_not_empty">
				<xsd:restriction base="xsd:dateTime" />
			</xsd:simpleType>
			<xsd:simpleType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="tstamp_empty">
				<xsd:union memberTypes="empty_str tstamp_not_empty" />
			</xsd:simpleType>

			<xsd:simpleType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="int_empty">
				<xsd:union memberTypes="empty_str xsd:integer" />
			</xsd:simpleType>

			 <xsd:simpleType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="int_not_empty">
                <xsd:restriction base="xsd:string">
                        <xsd:minLength value="1" />
                        <xsd:pattern value="[\-]?([0-9])+" />
                </xsd:restriction>
			</xsd:simpleType>

		';
		if($this->m_mode == (int)SERIALIZE_INPUT_MODE){
			$lBaseTypesXml .= '
			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldNotEmpty">
				<xsd:choice minOccurs="1">
					<xsd:element name="value" type="str_not_empty"></xsd:element>
					<xsd:any/>
				</xsd:choice>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldString">
				<xsd:sequence><xsd:element name="value" type="str_not_empty" minOccurs="1" maxOccurs="1"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldStringEmpty">
				<xsd:sequence><xsd:element name="value" type="xsd:string" minOccurs="1" maxOccurs="1"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldStringArr">
				<xsd:sequence><xsd:element name="value" type="str_not_empty" minOccurs="1" maxOccurs="unbounded"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldStringArrEmpty">
				<xsd:sequence><xsd:element name="value" type="xsd:string" minOccurs="1" maxOccurs="unbounded"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldInt">
				<xsd:sequence><xsd:element name="value" type="int_not_empty" minOccurs="1" maxOccurs="1"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldIntEmpty">
				<xsd:sequence><xsd:element name="value" type="int_empty" minOccurs="1" maxOccurs="1"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldIntArr">
				<xsd:sequence><xsd:element name="value" type="int_not_empty" minOccurs="1" maxOccurs="unbounded"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldIntArrEmpty">
				<xsd:sequence><xsd:element name="value" type="int_empty" minOccurs="1" maxOccurs="unbounded"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldDate">
				<xsd:sequence><xsd:element name="value" type="tstamp_not_empty" minOccurs="1" maxOccurs="1"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldDateEmpty">
				<xsd:sequence><xsd:element name="value" type="tstamp_empty" minOccurs="1" maxOccurs="1"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldDateArr">
				<xsd:sequence><xsd:element name="value" type="tstamp_not_empty" minOccurs="1" maxOccurs="unbounded"></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldDateArrEmpty">
				<xsd:sequence><xsd:element name="value" type="tstamp_empty" minOccurs="1" maxOccurs="unbounded"></xsd:element></xsd:sequence>
			</xsd:complexType>
		';
		}elseif($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){

			$lIdAttributeXml = '<xsd:attribute name="value_id" type="int_empty" />';

			$lBaseTypesXml .= '

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldNotEmpty">
				<xsd:sequence>
					<xsd:element name="value" minOccurs="1" maxOccurs="1">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:any namespace="##any" maxOccurs="unbounded" processContents="skip"/>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:sequence>
			</xsd:complexType>
			
			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldEmpty">
				<xsd:sequence>
					<xsd:element name="value" minOccurs="1" maxOccurs="1">
					</xsd:element>
				</xsd:sequence>
			</xsd:complexType>
			
			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldString">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="1"><xsd:complexType><xsd:simpleContent><xsd:extension base="str_not_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldStringEmpty">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="1"><xsd:complexType><xsd:simpleContent><xsd:extension base="xsd:string">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldStringArr">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="unbounded"><xsd:complexType><xsd:simpleContent><xsd:extension base="str_not_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldStringArrEmpty">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="unbounded"><xsd:complexType><xsd:simpleContent><xsd:extension base="xsd:string">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldInt">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="1"><xsd:complexType><xsd:simpleContent><xsd:extension base="int_not_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldIntEmpty">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="1"><xsd:complexType><xsd:simpleContent><xsd:extension base="int_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldIntArr">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="unbounded"><xsd:complexType><xsd:simpleContent><xsd:extension base="int_not_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldIntArrEmpty">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="unbounded"><xsd:complexType><xsd:simpleContent><xsd:extension base="int_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldDate">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="1"><xsd:complexType><xsd:simpleContent><xsd:extension base="tstamp_not_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldDateEmpty">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="1"><xsd:complexType><xsd:simpleContent><xsd:extension base="tstamp_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldDateArr">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="unbounded"><xsd:complexType><xsd:simpleContent><xsd:extension base="tstamp_not_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldDateArrEmpty">
			<xsd:sequence><xsd:element name="value" minOccurs="1" maxOccurs="unbounded"><xsd:complexType><xsd:simpleContent><xsd:extension base="tstamp_empty">' . $lIdAttributeXml . '</xsd:extension></xsd:simpleContent></xsd:complexType></xsd:element></xsd:sequence>
			</xsd:complexType>

			';
		}

		$lFragment = $this->m_documentXmlDom->createDocumentFragment();
		$lFragment->appendXML($lBaseTypesXml);
		$this->m_schemaXmlNode->appendChild($lFragment);
	}

	/**
	 * Here we will generate the field types of the fields which have data src (a.k.a enums) - we
	 * will limit the possible values for the fields to the one available in the data src
	 */
	function generateDataSrcs(){
		unlink('/tmp/srcname.txt');
		foreach ($this->m_dataSrcIds as $lSrcId => $lSrcData){
			//First generate the base type with the enums
			$lSrcName = $lSrcData['xml_node_name'];
			file_put_contents('/tmp/srcname.txt', $lSrcName . "\n", FILE_APPEND);
			$lSimpleTypeNode = $this->m_schemaXmlNode->appendChild($this->xsdElem('simpleType'));
			$lSimpleTypeNode->setAttribute('name', $lSrcName);
			$lRestriction = $lSimpleTypeNode->appendChild($this->xsdElem('restriction'));
			$lRestriction->setAttribute('base', 'xsd:string');
			foreach ($lSrcData['values'] as $lId => $lName) {
				$lEnumerationNode = $lRestriction->appendChild($this->xsdElem('enumeration'));;
				$lEnumerationNode->setAttribute('value', $lName);
			}
			//Create a type which allows nulls
			$lAllowNullSimpleTypeNode = $this->m_schemaXmlNode->appendChild($this->xsdElem('simpleType'));
			$lAllowNullSimpleTypeNode->setAttribute('name', $lSrcName . 'Empty');
			$lAllowNullUnionNode = $lAllowNullSimpleTypeNode->appendChild($this->xsdElem('union'));
			$lAllowNullUnionNode->setAttribute('memberTypes', 'empty_str ' . $lSrcData['xml_node_name']);

			//Create the field types for this src id
			//The field which is not array and doesnt allow nulls
			$lFieldTypeComplexTypeNode = $this->m_schemaXmlNode->appendChild($this->xsdElem('complexType'));
			$lFieldTypeComplexTypeNode->setAttribute('name', 'field' . $lSrcName);
			$lFieldTypeSeq = $lFieldTypeComplexTypeNode->appendChild($this->xsdElem('sequence'));
			$lValueElementNode = $lFieldTypeSeq->appendChild($this->xsdElem('element'));
			$lValueElementNode->setAttribute('name', value);
			$lValueElementNode->setAttribute('minOccurs', 1);
			$lValueElementNode->setAttribute('maxOccurs', 1);
			$lValueElementNode->setAttribute('type', $lSrcName);

			//The field which is not array and allows nulls
			$lFieldTypeComplexTypeNode = $this->m_schemaXmlNode->appendChild($this->xsdElem('complexType'));
			$lFieldTypeComplexTypeNode->setAttribute('name', 'field' . $lSrcName . 'Empty');
			$lFieldTypeSeq = $lFieldTypeComplexTypeNode->appendChild($this->xsdElem('sequence'));
			$lValueElementNode = $lFieldTypeSeq->appendChild($this->xsdElem('element'));
			$lValueElementNode->setAttribute('name', value);
			$lValueElementNode->setAttribute('minOccurs', 1);
			$lValueElementNode->setAttribute('maxOccurs', 1);
			$lValueElementNode->setAttribute('type', $lSrcName . 'Empty');

			//The field which is array and doesnt allow nulls
			$lFieldTypeComplexTypeNode = $this->m_schemaXmlNode->appendChild($this->xsdElem('complexType'));
			$lFieldTypeComplexTypeNode->setAttribute('name', 'field' . $lSrcName . 'Arr');
			$lFieldTypeSeq = $lFieldTypeComplexTypeNode->appendChild($this->xsdElem('sequence'));
			$lValueElementNode = $lFieldTypeSeq->appendChild($this->xsdElem('element'));
			$lValueElementNode->setAttribute('name', value);
			$lValueElementNode->setAttribute('minOccurs', 1);
			$lValueElementNode->setAttribute('maxOccurs', 'unbounded');
			$lValueElementNode->setAttribute('type', $lSrcName);

			//The field which is array and allows nulls
			$lFieldTypeComplexTypeNode = $this->m_schemaXmlNode->appendChild($this->xsdElem('complexType'));
			$lFieldTypeComplexTypeNode->setAttribute('name', 'field' . $lSrcName . 'ArrEmpty');
			$lFieldTypeSeq = $lFieldTypeComplexTypeNode->appendChild($this->xsdElem('sequence'));
			$lValueElementNode = $lFieldTypeSeq->appendChild($this->xsdElem('element'));
			$lValueElementNode->setAttribute('name', value);
			$lValueElementNode->setAttribute('minOccurs', 1);
			$lValueElementNode->setAttribute('maxOccurs', 'unbounded');
			$lValueElementNode->setAttribute('type', $lSrcName . 'Empty');

		}
	}

	function getFieldTypeName($pFieldType, $pDataSrc, $pAllowNull, $pControlType){

		//Не слагаме тип на полетата с html контролка.
		switch($pControlType){
			case (int)FIELD_HTML_TEXTAREA_TYPE:
			case (int)FIELD_HTML_TEXTAREA_THESIS_TYPE:
			case (int)FIELD_HTML_TEXTAREA_ANTITHESIS_TYPE:
			case (int)FIELD_HTML_TEXTAREA_THESIS_NEXT_COUPLET_TYPE:
			case (int)FIELD_HTML_TEXTAREA_THESIS_TAXON_NAME_TYPE:
			case (int)FIELD_HTML_EDITOR_TYPE:
			case (int)FIELD_HTML_EDITOR_TYPE_NO_CITATIONS:
			case (int)FIELD_HTML_EDITOR_TYPE_ONLY_REFERENCE_CITATIONS:
				if($pAllowNull){
					return 'fieldEmpty';
				}else{
					return 'fieldNotEmpty';
				}
			case (int)FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_TYPE:
			case (int)FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_TYPE:
			case (int)FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE:
			case (int)FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE:
				return 'fieldStringArrEmpty';
			case (int)FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
			case FIELD_HTML_TAXON_TREATMENT_CLASSIFICATION:
			case (int)FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
			case (int)FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
			case (int)FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
				if(!$pAllowNull){
					return 'fieldStringArrEmpty';
				}
				return 'fieldStringArr';
		}

		if($pDataSrc){
			switch ($pFieldType) {
				default:
					if($pAllowNull){
						return 'fieldStringEmpty';
					}
					return 'fieldString';
				case FIELD_CHECKBOX_MANY_TO_STRING_TYPE:
				case FIELD_CHECKBOX_MANY_TO_BIT_TYPE:
				case FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE:
				case FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE:
				case FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE:
					if($pAllowNull){
						return 'fieldStringArrEmpty';
					}
					return 'fieldStringArr';
			}
		}
		switch ($pFieldType) {
			case FIELD_CHECKBOX_MANY_TO_BIT_TYPE:
			case FIELD_CHECKBOX_MANY_TO_BIT_ONE_BOX_TYPE:
			case FIELD_INT_TYPE:
				if($pAllowNull){
					return 'fieldIntEmpty';
				}
				return 'fieldInt';
			default:
			case FIELD_STRING_TYPE:
			case FIELD_CHECKBOX_MANY_TO_STRING_TYPE:
				if($pAllowNull){
					return 'fieldStringEmpty';
				}
				return 'fieldString';
			case FIELD_DATE_TYPE:
				if($pAllowNull){
					return 'fieldDateEmpty';
				}
				return 'fieldDate';
			case FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE:
				if($pAllowNull){
					return 'fieldIntArrEmpty';
				}
				return 'fieldIntArr';
			case FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE:
				if($pAllowNull){
					return 'fieldStringArrEmpty';
				}
				return 'fieldStringArr';
			case FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE:
				if($pAllowNull){
					return 'fieldDateArrEmpty';
				}
				return 'fieldDateArr';
		}
	}
}
?>