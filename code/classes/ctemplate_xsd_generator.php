<?php
define('XSD_SCHEMA_LOCATION', 'http://www.w3.org/2001/XMLSchema');
define('UNBOUNDED_MAX_COUNT', 99999999);
/**
	Този клас ще реализира генерирането на xsd схема за подадения темплейт
*/
class ctemplate_xsd_generator extends csimple {
	var $m_templateId;
	// Dom Document-а на обекта
	var $m_documentXmlDom;
	var $m_schemaXmlNode;

	/*
	 * Тук ще пазим цялата информация за инстанциите на обектите. За всеки
	 * instance в ключа children ще пазим id-та на инстанциите на подобектите.
	 * За всеки instance в ключа fields ще пазим масив във формат id =>
	 * $lFieldDataArray с информация за всичките field-ове на instance-a
	 */
	var $m_templateObjectDetails;

	var $m_figureDetails;
	var $m_generatedObjectIds;
	var $m_dataSrcIds;

	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		$this->m_templateId = (int) $pFieldTempl['template_id'];
		$this->m_templateObjectDetails = array();
		$this->m_documentXmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$this->m_generatedObjectIds = array();

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
		$lSchemaXmlNode = $this->m_documentXmlDom->appendChild($this->xsdElem('schema'));

		$this->m_schemaXmlNode = $lSchemaXmlNode;

		$lDocumentXmlNode = $lSchemaXmlNode->appendChild($this->xsdElem('element'));
		$lDocumentXmlNode->setAttribute('name', 'document');
		$lDocumentXmlNode->setAttribute('type', 'documentType');

		// Id Checks for some of the elements
		$lFragment = $this->m_documentXmlDom->createDocumentFragment();
		$lElementIdsChecks = '
							<xsd:key xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="refUniqueID">
								<xsd:selector xpath="objects/references/reference"/>
								<xsd:field xpath="@id"/>
							</xsd:key>

							<xsd:key xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="figUniqueID">
								<xsd:selector xpath="figures/figure"/>
								<xsd:field xpath="@id"/>
							</xsd:key>

							<xsd:key xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="tblUniqueID">
								<xsd:selector xpath="tables/table"/>
								<xsd:field xpath="@id"/>
							</xsd:key>

							<xsd:keyref xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="refCitation" refer="refUniqueID">
								<xsd:selector xpath=".//reference_citation"/>
								<xsd:field xpath="@object_id"/>
							</xsd:keyref>

							<xsd:keyref xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="figCitation" refer="figUniqueID">
								<xsd:selector xpath=".//fig_citation"/>
								<xsd:field xpath="@object_id"/>
							</xsd:keyref>

							<xsd:keyref xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="tblCitation" refer="tblUniqueID">
								<xsd:selector xpath=".//tbls_citation"/>
								<xsd:field xpath="@object_id"/>
							</xsd:keyref>';

		$lFragment->appendXML($lElementIdsChecks);
		$lDocumentXmlNode->appendChild($lFragment);

		$lDocumentTypeNode = $lSchemaXmlNode->appendChild($this->xsdElem('complexType'));
		$lDocumentTypeNode->setAttribute('name', 'documentType');
		$lDocumentSeqNode = $lDocumentTypeNode->appendChild($this->xsdElem('sequence'));

		$lDocInfoNode = $lDocumentSeqNode->appendChild($this->xsdElem('element'));
		$lDocInfoNode->setAttribute('name', 'document_info');
		$lDocInfoComplexType = $lDocInfoNode->appendChild($this->xsdElem('complexType'));
		$lDocInfoSeqNode = $lDocInfoComplexType->appendChild($this->xsdElem('sequence'));

		$lDocTypeNode = $lDocInfoSeqNode->appendChild($this->xsdElem('element'));
		$lDocTypeNode->setAttribute('name', 'document_type');
		$lDocTypeComplexType = $lDocTypeNode->appendChild($this->xsdElem('complexType'));


		$lDocTypeSimpleContent = $lDocTypeComplexType->appendChild($this->xsdElem('simpleContent'));
		$lDocTypeRestriction = $lDocTypeSimpleContent->appendChild($this->xsdElem('extension'));
		$lDocTypeRestriction->setAttribute('base', 'xsd:string');
		$lDocTypeIdAttributeNode = $lDocTypeRestriction->appendChild($this->xsdElem('attribute'));
		$lDocTypeIdAttributeNode->setAttribute('name', 'id');
		$lDocTypeIdAttributeNode->setAttribute('use', 'required');
		//$lDocTypeIdAttributeNode->setAttribute('type', 'xsd:integer');
		$jbXSDsimpleTypeNode = $lDocTypeIdAttributeNode->appendChild($this->xsdElem('simpleType'));
		$jbXSDrestrictnionNode = $jbXSDsimpleTypeNode->appendChild($this->xsdElem('restriction'));
		$jbXSDrestrictnionNode->setAttribute('base', 'xsd:integer');
		$jbXSDenumerationNode = $jbXSDrestrictnionNode->appendChild($this->xsdElem('enumeration'));
		$jbXSDenumerationNode->setAttribute('value', $this->m_templateId);

		$lJournalNameNode = $lDocInfoSeqNode->appendChild($this->xsdElem('element'));
		$lJournalNameNode->setAttribute('name', 'journal_name');
		$lJournalNameComplexType = $lJournalNameNode->appendChild($this->xsdElem('complexType'));

		$lJournalNameSimpleType = $lJournalNameComplexType->appendChild($this->xsdElem('simpleContent'));
		$lJournalNameRestriction = $lJournalNameSimpleType->appendChild($this->xsdElem('extension'));
		$lJournalNameRestriction->setAttribute('base', 'xsd:string');
		$lJournalIdAttributeNode = $lJournalNameRestriction->appendChild($this->xsdElem('attribute'));
		$lJournalIdAttributeNode->setAttribute('name', 'id');
		$lJournalIdAttributeNode->setAttribute('type', 'xsd:integer');


// 		$lDocInfoNode->setAttribute('type', 'objectsType');

		$lObjectsXmlNode = $lDocumentSeqNode->appendChild($this->xsdElem('element'));
		$lObjectsXmlNode->setAttribute('name', 'objects');
		$lObjectsXmlNode->setAttribute('type', 'objectsType');

		$lCon = new DBCn();
		$lCon->Open();

		$lObjectsSql = '
			SELECT *, coalesce(o.api_min_occurrence, (o.real_occurences).min_occurrence) as min_occurrence,
				coalesce(o.api_max_occurrence, (o.real_occurences).max_occurrence) as max_occurrence
			 FROM (
				SELECT DISTINCT ON (o.object_id, rp.id) o.*, os.api_min_occurrence, os.max_occurrence as api_max_occurrence, char_length(o.pos)/2 as level, rp.id as real_parent_id,
					CASE WHEN os.id IS NOT NULL THEN null ELSE spCalculateTemplateObjectOccurrences(o.template_id, o.object_id, rp.id) END as real_occurences
				FROM pwt.v_distinct_template_objects o
				JOIN pwt.v_template_objects_xml_parent rp ON rp.child_doc_templ_object_id = o.id
				LEFT JOIN pwt.object_subobjects os ON os.object_id = rp.object_id AND os.subobject_id = o.object_id
				WHERE o.template_id = ' . $this->m_templateId . ' AND o.display_object_in_xml = 1 AND rp.real_template_id = ' . (int)$this->m_templateId . '
					AND (o.parent_id = o.id OR rp.id IS NOT NULL)
				ORDER BY rp.id, o.object_id, o.pos ASC
			) o
			ORDER BY o.pos ASC';
  //var_dump($lObjectsSql);
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
   		//var_dump($this->m_templateObjectDetails);
		$lCon->CloseRs();
		// Взимаме всичките field-ове с една заявка.


		$lFieldsSql = 'SELECT f.id as field_id, f.type, ft.value_column_name, of.label as field_name, ds.id as data_src_id, ds.query as data_src_query,
			of.control_type, of.xml_node_name, o.id as template_object_id,
			(CASE WHEN of.allow_nulls = true THEN 1 ELSE of.api_allow_null::int END) as allow_nulls,
			(CASE WHEN o.display_object_in_xml = 1 THEN o.id ELSE rp.id END) as real_template_object_id, o.display_object_in_xml
		FROM pwt.fields f
		JOIN pwt.field_types ft ON ft.id = f.type
		JOIN pwt.object_fields of ON of.field_id = f.id
		JOIN pwt.v_distinct_template_objects o ON o.object_id = of.object_id
		JOIN pwt.v_template_objects_xml_parent rp ON rp.child_doc_templ_object_id = o.id AND rp.real_template_id = ' . (int)$this->m_templateId . '
		LEFT JOIN pwt.data_src ds ON ds.id = of.data_src_id
		WHERE o.template_id = ' . (int)$this->m_templateId . ' AND of.display_in_xml = 1
			 AND (o.parent_id = o.id OR rp.id IS NOT NULL)
			 AND (o.display_object_in_xml = 1 OR (o.display_object_in_xml = 4))
		ORDER BY o.pos ASC, of.id
		';

		$lCon->Execute($lFieldsSql);
		$lCon->MoveFirst();
		while(! $lCon->Eof()){
			/*
			 * Пазим field-овете към обекта, понеже id-то на field-овете не е
			 * уникално (може няколко обекта да имат field с едно и също id
			 */
			$lTempObjectId = (int) $lCon->mRs['real_template_object_id'];


			$this->m_templateObjectDetails[$lTempObjectId]['fields'][$lCon->mRs['field_id']] = $lCon->mRs;
			$lDataSrcId = (int) $lCon->mRs['data_src_id'];
			if($lDataSrcId && !in_array($lDataSrcId, $this->m_dataSrcIds)){
				$this->m_dataSrcIds[$lDataSrcId] = $lDataSrcId;
			}

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

		$lObjectsTypeNode = $lSchemaXmlNode->appendChild($this->xsdElem('complexType'));
		$lObjectsTypeNode->setAttribute('name', 'objectsType');
		$lObjectsSeqNode = $lObjectsTypeNode->appendChild($this->xsdElem('sequence'));
		//var_dump($lLevelOneTemplateObjects);
		foreach($lLevelOneTemplateObjects as $lTemplateObjectId){
			$lChildDetails = $this->m_templateObjectDetails[$lTemplateObjectId];
			$lChildElementXmlNode = $lObjectsSeqNode->appendChild($this->xsdElem('element'));
			$lChildElementXmlNode->setAttribute('name', $lChildDetails['xml_node_name']);

			$lChildElementXmlNode->setAttribute('type', $lChildDetails['xml_node_name'] . 'Type');
			$lChildElementXmlNode->setAttribute('minOccurs', 1);
			$lChildElementXmlNode->setAttribute('maxOccurs', 1);

			$this->generateObject($lTemplateObjectId, $lObjectsXmlNode);
		}

		// Генерираме типовете на главните обекти
		foreach($lLevelOneTemplateObjects as $lTemplateObjectId){

			$this->generateObject($lTemplateObjectId, $lObjectsXmlNode);
		}

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
		$lDocumentTypeNode = $lSchemaXmlNode->appendChild($this->xsdElem('complexType'));
		$lDocumentTypeNode->setAttribute('name', prepareValueForXmlNodeName($lTemplateObjectDetails['xml_node_name']) . 'Type');


		$lDocumentSeqNode = $lDocumentTypeNode->appendChild($this->xsdElem('sequence'));

		// Първо добавяме wrapper-a на field-овете, ако имаме field-ове
		if(count($lTemplateObjectDetails['fields'])){
			$lFieldsElementXmlNode = $lDocumentSeqNode->appendChild($this->xsdElem('element'));
			$lFieldsElementXmlNode->setAttribute('name', 'fields');
			$lFieldsElementXmlNode->setAttribute('type', $lTemplateObjectDetails['xml_node_name'] . 'FieldsType');
			$lFieldsElementXmlNode->setAttribute('minOccurs', 0);
			$lFieldsElementXmlNode->setAttribute('maxOccurs', 1);
		}


		// След това добавяме всички подобекти като елементи на главния обект
		foreach($lTemplateObjectDetails['children'] as $lChildInstanceId){
			$lChildDetails = $this->m_templateObjectDetails[$lChildInstanceId];
			$lChildElementXmlNode = $lDocumentSeqNode->appendChild($this->xsdElem('element'));
			$lChildElementXmlNode->setAttribute('name', prepareValueForXmlNodeName($lChildDetails['xml_node_name']));

			$lChildElementXmlNode->setAttribute('type', prepareValueForXmlNodeName($lChildDetails['xml_node_name']) . 'Type');
			$lChildElementXmlNode->setAttribute('minOccurs', (int)$lChildDetails['min_occurrence']);

			$lMaxOccurs = (int)$lChildDetails['max_occurrence'];
			if($lMaxOccurs >= (int)UNBOUNDED_MAX_COUNT){
				$lMaxOccurs = 'unbounded';
			}
			$lChildElementXmlNode->setAttribute('maxOccurs', $lMaxOccurs);
		}

		if($lTemplateObjectDetails['generate_xml_id']){
			$lAttributeNode = $lDocumentTypeNode->appendChild($this->xsdElem('attribute'));
			$lAttributeNode->setAttribute('name', 'id');
			$lAttributeNode->setAttribute('type', 'xsd:integer');
			$lAttributeNode->setAttribute('use', 'required');
		}



		//След това генерираме дефиницията на field wrapper-a
		if(count($lTemplateObjectDetails['fields'])){
			$lFieldsWrapperTypeNode = $lSchemaXmlNode->appendChild($this->xsdElem('complexType'));
			$lFieldsWrapperTypeNode->setAttribute('name', prepareValueForXmlNodeName($lTemplateObjectDetails['xml_node_name']) . 'FieldsType');
			$lFieldsWrapperSeqNode = $lFieldsWrapperTypeNode->appendChild($this->xsdElem('sequence'));
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
		$lFieldElementXmlNode = $pParentXmlNode->appendChild($this->xsdElem('element'));
		$lFieldElementXmlNode->setAttribute('name', prepareValueForXmlNodeName($pFieldData['xml_node_name']));
		$lFieldType = $this->getFieldTypeName($pFieldData['type'], $pFieldData['data_src_id'], $pFieldData['allow_nulls'], $pFieldData['control_type']);
		if($lFieldType != ''){
			$lFieldElementXmlNode->setAttribute('type', $lFieldType);
		}
		if($pFieldData['allow_nulls']){
			$lFieldElementXmlNode->setAttribute('minOccurs', 0);
		}

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

		$lBaseTypesXml .= '
			<xsd:complexType xmlns:xsd="' . XSD_SCHEMA_LOCATION . '" name="fieldNotEmpty">
				<xsd:sequence>
					<xsd:element name="value" minOccurs="1" maxOccurs="1">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:any namespace="##any" processContents="skip"/>
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

		$lFragment = $this->m_documentXmlDom->createDocumentFragment();
		$lFragment->appendXML($lBaseTypesXml);
		$this->m_schemaXmlNode->appendChild($lFragment);
	}

	/**
	 * Here we will generate the field types of the fields which have data src (a.k.a enums) - we
	 * will limit the possible values for the fields to the one available in the data src
	 */
	function generateDataSrcs(){
		foreach ($this->m_dataSrcIds as $lSrcId => $lSrcData){
			//First generate the base type with the enums
			$lSrcName = $lSrcData['xml_node_name'];

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
				if(!$pAllowNull){
					return 'fieldNotEmpty';
				}
				return 'fieldEmpty';
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
			$lSrcName = $this->m_dataSrcIds[$pDataSrc]['xml_node_name'];
			$lName = 'field' . $lSrcName;
			switch ($pFieldType) {
				case FIELD_CHECKBOX_MANY_TO_STRING_TYPE:
				case FIELD_CHECKBOX_MANY_TO_BIT_TYPE:
				case FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE:
				case FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE:
				case FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE:
					$lName .= 'Arr';
			}
			if($pAllowNull){
				return $lName . 'Empty';
			}
			return $lName;

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