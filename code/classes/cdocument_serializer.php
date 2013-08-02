<?php
/**
	Този клас ще реализира сериализацията на текущата версия на
	даден документ в xml
*/
ini_set('memory_limit', '500M');
class cdocument_serializer extends csimple {
	var $m_document_id;
	// Dom Document-а на обекта
	var $m_documentXmlDom;
	/**
	 * Типа на генериране на xml-a
	 * 1 - вътрешен xml (за нас)
	 * 2 - input xml (за американците)
	 */
	var $m_mode;

	/*
	 * Тук ще пазим цялата информация за инстанциите на обектите. За всеки
	 * instance в ключа children ще пазим id-та на инстанциите на подобектите.
	 * За всеки instance в ключа fields ще пазим масив във формат id =>
	 * $lFieldDataArray с информация за всичките field-ове на instance-a
	 */
	var $m_instanceDetails;


	var $m_figureDetails;

	var $m_tablesDetails;

	var $m_instance_id;

	var $m_useExistingDbConnection;
	var $m_objectsHolderNode;
	var $m_con;
	var $m_modifiedInstances = array();
	var $m_instancesWithInvalidCache = array();
	var $m_timeStart;
	var $m_currentDbTime;
	var $m_documentXmlCached = '';
	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		$this->m_document_id = (int) $pFieldTempl['document_id'];
		$this->m_instanceDetails = array();
		$this->m_figureDetails = array();
		$this->m_tablesDetails = array();
		$this->m_documentXmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$this->m_documentXmlDom->formatOutput = false;
		$this->m_mode = $pFieldTempl['mode'];
		$this->m_instance_id = (int)$pFieldTempl['instance_id'];
		if(!in_array($this->m_mode, array((int)SERIALIZE_INTERNAL_MODE, (int)SERIALIZE_INPUT_MODE))){
			$this->m_mode = (int)SERIALIZE_INTERNAL_MODE;

		}
		$this->m_useExistingDbConnection = (int)$pFieldTempl['use_existing_db_connection'];
		$this->m_timeStart = $this->getCurrentTime();
	}

	function GetData() {
		$this->serializeDocument();
		$this->m_pubdata['xml'] = $this->getXml();
		parent::GetData();
// 		exit;
	}

	function getXml() {
		if(!$this->m_documentXmlCached){
			$this->m_documentXmlCached = $this->m_documentXmlDom->saveXML();
		}
		return $this->m_documentXmlCached;
	}

	private function DumpMemory($pMsg = '', $pPeak = false){
		return;
		if($pPeak){
			$pMsg .= ' ' . number_format(memory_get_peak_usage(1) / (1024*1024), 4)  . ' MB';
		}else{
			$pMsg .= ' ' . number_format(memory_get_usage(1) / (1024*1024), 4)  . ' MB';
		}
// 		trigger_error($pMsg , E_USER_NOTICE);
	}
	
	private function DumpTimeLog($pMsg = '', $pStartTime = 0, $pEndTime = 0){
		if($pEndTime == 0){
			$pEndTime = $this->getCurrentTime();
		}
		if($pStartTime == 0){
			$pStartTime = $this->m_timeStart;
		}
		$pMsg .= ' ' . number_format(($pEndTime - $pStartTime), 4) . ' s';
		trigger_error($pMsg, E_USER_NOTICE);
	}
	
	private function getCurrentTime(){
		return mktime() + substr((string)microtime(), 1, 6);
	}

	protected function initDBCon(){
		if(!$this->m_useExistingDbConnection){
			$this->m_con = new DBCn();
			$this->m_con->Open();
		}else{
			$this->m_con = Con();
			$this->m_con->CloseRs();
		}
		$this->m_con->SetFetchReturnType(PGSQL_ASSOC);
		if(defined('POSTGRESQL_DATESTYLE')){
			//So that updates and deletes of lastmod columns work correctly
			$this->m_con->Execute('SET datestyle = "' . q(POSTGRESQL_DATESTYLE) . '";');
		}
		
	}

	/*
	 * Първо запазваме информацията за всички обекти и всички field-ове. След
	 * това започваме рекурсивно да сериализираме обектите от 1-во ниво
	 */
	function serializeDocument() {
		$this->DumpTimeLog('START SERIALIZING TIME');
		$lDocumentXmlNode = $this->m_documentXmlDom->appendChild($this->m_documentXmlDom->createElement('document'));

		if($this->m_mode == 1){
			$lDocumentXmlNode->setAttribute('id', $this->m_document_id);
		}
		$this->initDBCon();
		
		$this->serializeDocumentInfo($lDocumentXmlNode);

		$lObjectsXmlNode = $lDocumentXmlNode->appendChild($this->m_documentXmlDom->createElement('objects'));
		$this->m_objectsHolderNode = $lObjectsXmlNode;
		$this->DumpTimeLog('START SERIALIZING INSTANCES');
		$this->serializeInstances();
		$this->DumpTimeLog('END SERIALIZING INSTANCES');
		$this->processInstancesWithInvalidCache();
		$this->DumpTimeLog('END PROCESSING INVALID CACHE SERIALIZING');
		$this->serializeFields();
		$this->DumpTimeLog('END SERIALIZING FIELDS');
		//$this->serializeFigures();
		$this->DumpTimeLog('END SERIALIZING FIGURES');
		//$this->serializeTables();
		$this->DumpTimeLog('END SERIALIZING TABLES');		
		
// 		$lInstanceId = 234969;
// 		$lNode = $this->m_instanceDetails[$lInstanceId]['instance_node'];
// 		$lNode2 = $this->m_instanceDetails[$lInstanceId]['fields_wrapper_node'];
// 		var_dump($lNode, $lNode2);
// 		exit;
		
		$this->updateModifiedInstancesCache();
		$this->DumpTimeLog('END UPDATING CACHE SERIALIZING');
		
		$this->storeDocumentXml();
		$this->DumpTimeLog('END SERIALIZING DOC XML');
		

		$lXML = $this->getXml();
		$this->DumpMemory('Mem XML content');
		$this->DumpMemory('Mem XML content max', 1);
		file_put_contents('/tmp/doc_' . $this->m_document_id . '.xml', $lXML);
// 		exit;
	}

	protected function serializeInstances(){		
		$lCon = $this->m_con;
		$lObjectsSql = '';

		//Ако ще сериализираме само 1 инстанс
		$lInstanceWhere = '';
		if((int)$this->m_instance_id){
			$lInstanceJoin = ' JOIN pwt.document_object_instances ip ON ip.document_id = i.document_id AND substring(i.pos, 1, char_length(ip.pos)) = ip.pos ';
			$lInstanceWhere .= ' AND ip.id = ' . (int)$this->m_instance_id . ' ';
		}
		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			$lObjectsSql = 'SELECT i.*, i.is_modified::int as instance_is_modified, char_length(i.pos)/2 as level, o.xml_node_name, o.generate_xml_id, o.cached_xml_type
			FROM pwt.document_object_instances i
			JOIN pwt.document_template_objects o ON o.id = i.document_template_object_id
			-- LEFT JOIN pwt.document_object_instances p ON p.id = i.parent_id
			-- LEFT JOIN pwt.document_template_objects po ON po.id = p.document_template_object_id
			' . $lInstanceJoin . '
			WHERE i.document_id = ' . $this->m_document_id . $lInstanceWhere . ' AND i.is_confirmed = true
				-- AND (p.id IS NULL OR p.is_modified = true OR p.cached_xml_type = ' . (int) OBJECTS_CACHED_XML_TYPE_ONLY_FIELDS . ') 
			ORDER BY o.pos ASC, i.pos ASC ';
		}else if ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
			$lObjectsSql = 'SELECT i.*, p.id as parent_id, char_length(i.pos)/2 as level, o.xml_node_name, o.generate_xml_id
			FROM pwt.document_object_instances i
			JOIN pwt.document_template_objects o ON o.id = i.document_template_object_id
			JOIN pwt.v_document_template_objects_xml_parent rp ON rp.child_doc_templ_object_id = o.id AND rp.real_doc_id = ' . $this->m_document_id  . '
			LEFT JOIN pwt.document_object_instances p ON p.document_template_object_id = rp.id AND
			p.pos = substring(i.pos, 1, char_length(p.pos))
			' . $lInstanceJoin . '
			WHERE i.document_id = ' . $this->m_document_id . $lInstanceWhere . ' AND i.is_confirmed = true AND (i.parent_id IS NULL OR p.id IS NOT NULL) AND o.display_object_in_xml = 1
			ORDER BY o.pos ASC, i.pos ASC ';
		}

// 		var_dump($lObjectsSql);

		$this->DumpMemory('Mem Before ');
		// Взимаме всичките обекти с една заявка.

		$lCon->Execute($lObjectsSql);

		$lCon->MoveFirst();
		// Тук ще пазим обектите от 1во ниво
		$lLevelOneInstances = array();

		while(! $lCon->Eof()){

			$lInstanceId = (int) $lCon->mRs['id'];
			$lInstanceDetails = $lCon->mRs;
			$this->m_instanceDetails[$lInstanceId] = array();
			$this->m_instanceDetails[$lInstanceId]['children'] = array();
			$this->m_instanceDetails[$lInstanceId]['cached_xml_type'] = (int)$lCon->mRs['cached_xml_type'];
			$this->m_instanceDetails[$lInstanceId]['object_id'] = $lInstanceDetails['object_id'];
			$this->m_instanceDetails[$lInstanceId]['is_modified'] = $lInstanceDetails['instance_is_modified'];
			if($lCon->mRs['instance_is_modified']){
				$this->m_modifiedInstances[] = $lInstanceId;
			}
			$lInstanceNode = $this->serializeInstance($lInstanceId, $lInstanceDetails);
			// 			$this->m_objectDetails[$lInstanceId]['fields'] = array();
			if((int) $lCon->mRs['level'] == 1 || $this->m_instance_id == $lInstanceId){
				$lLevelOneInstances[] = $lInstanceId;
			}
			if((int) $lCon->mRs['parent_id']){
				$this->m_instanceDetails[(int) $lCon->mRs['parent_id']]['children'][] = $lInstanceId;
			}
			$lCon->MoveNext();
		}
		// 		trigger_error('Mem ESC1 ' . memory_get_usage() , E_USER_NOTICE);
		$lCon->CloseRs();
		// 		trigger_error('Mem ESC ' . memory_get_usage() , E_USER_NOTICE);
		// 		var_dump($this->m_objectDetails);
		// 		exit;
		$this->DumpMemory('Mem Objects ');
		// Взимаме всичките field-ове с една заявка.


		// 		exit;

		// Сериализираме само главните обекти. Ще имаме рекурсия, която ще се
		// грижи за дървото надолу
		foreach($lLevelOneInstances as $lInstanceId){
			$lInstanceNode = $this->m_instanceDetails[$lInstanceId]['instance_node'];
			if($lInstanceNode){
				$lInstanceNode = $this->m_objectsHolderNode->appendChild($lInstanceNode);
				$this->m_instanceDetails[$lInstanceId]['instance_node'] = $lInstanceNode;
			}
		}		
	}

	protected function serializeFields(){
		$this->initDBCon();
		$lCon = $this->m_con;

		$lFieldsSql = '';
		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			$lFieldsSql = 'SELECT f.id, f.type, fv.*, ft.value_column_name, of.label as field_name, ds.id as data_src_id, ds.query as data_src_query,
			of.control_type, of.xml_node_name
			FROM pwt.fields f
			JOIN pwt.field_types ft ON ft.id = f.type
			JOIN pwt.instance_field_values fv ON fv.field_id = f.id AND fv.document_id = ' . $this->m_document_id . '
			JOIN pwt.document_object_instances i ON i.id = fv.instance_id AND i.is_confirmed = true
			JOIN pwt.object_fields of ON of.field_id = f.id AND of.object_id = i.object_id
			LEFT JOIN pwt.data_src ds ON ds.id = fv.data_src_id
			WHERE i.is_modified = true AND i.is_confirmed = true
			ORDER BY i.pos ASC, of.id
			';
		}elseif ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
			$lFieldsSql = 'SELECT f.id, f.type, fv.*, ft.value_column_name, of.label as field_name, ds.id as data_src_id, ds.query as data_src_query,
			of.control_type, of.xml_node_name, o.display_object_in_xml, p.id as real_parent_id
			FROM pwt.fields f
			JOIN pwt.field_types ft ON ft.id = f.type
			JOIN pwt.instance_field_values fv ON fv.field_id = f.id AND fv.document_id = ' . $this->m_document_id . '
			JOIN pwt.document_object_instances i ON i.id = fv.instance_id AND i.is_confirmed = true
			JOIN pwt.object_fields of ON of.field_id = f.id AND of.object_id = i.object_id
			LEFT JOIN pwt.data_src ds ON ds.id = fv.data_src_id
			JOIN pwt.document_template_objects o ON o.id = i.document_template_object_id
			JOIN pwt.v_document_template_objects_xml_parent rp ON rp.child_doc_templ_object_id = o.id AND rp.real_doc_id = ' . $this->m_document_id . '
			LEFT JOIN pwt.document_object_instances p ON p.document_template_object_id = rp.id AND
			p.pos = substring(i.pos, 1, char_length(p.pos))
			WHERE (o.display_object_in_xml = 1 OR (o.display_object_in_xml = 4))  AND (i.parent_id IS NULL OR p.id IS NOT NULL)
			AND of.display_in_xml = 1
			ORDER BY i.pos ASC, p.pos, of.id
			';
		}
		//За обектите от тип 4 (показване на field-овете в директния parent) се допуска максимално 1 ниво нагоре

		$this->DumpMemory('Mem before fields exec');
		$this->DumpMemory('Mem before fields exec max', 1);
		$lCon->Execute($lFieldsSql);
		$this->DumpMemory('Mem after fields exec');
		$this->DumpMemory('Mem after fields exec max', 1);

// 		~ var_dump($lFieldsSql);
		$lCon->MoveFirst();
		while(! $lCon->Eof()){
			/*
			 * Пазим field-овете към обекта, понеже id-то на field-овете не е
			* уникално (може няколко обекта да имат field с едно и също id
				*/

			if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
				$lInstanceId = (int) $lCon->mRs['instance_id'];
			}elseif ($this->m_mode == (int) SERIALIZE_INPUT_MODE){
				if($lCon->mRs['display_object_in_xml'] == 1){
					$lInstanceId = (int) $lCon->mRs['instance_id'];
				}else{
					$lInstanceId = (int) $lCon->mRs['real_parent_id'];
					// 				var_dump($lInstanceId);
				}
			}
			//If the instance is not modified - skip
			if(!array_key_exists($lInstanceId, $this->m_instanceDetails) || !$this->m_instanceDetails[$lInstanceId]['is_modified']){
				continue;
			}
			$lFieldData = $lCon->mRs;
			$lFieldData['value'] = $lFieldData[$lFieldData['value_column_name']];
			$lFieldXMLNode = $this->serializeField($lFieldData);
			$lInstanceFieldsWrapper = $this->m_instanceDetails[$lInstanceId]['fields_wrapper_node'];
			if(!$lInstanceFieldsWrapper){
				$lInstanceNode = $this->m_instanceDetails[$lInstanceId]['instance_node'];
				if($lInstanceNode){
					$lInstanceFieldsWrapper = $this->m_documentXmlDom->createElement('fields');
					if($lInstanceNode->firstChild){
						$lInstanceFieldsWrapper = $lInstanceNode->insertBefore($lInstanceFieldsWrapper, $lInstanceNode->firstChild);
					}else{
						$lInstanceFieldsWrapper = $lInstanceNode->appendChild($lInstanceFieldsWrapper);
					}
					if($lInstanceId == 234969){
						var_dump($lInstanceFieldsWrapper);
					}
					$this->m_instanceDetails[$lInstanceId]['fields_wrapper_node'] = $lInstanceFieldsWrapper;
				}
			}
			if($lInstanceFieldsWrapper){
				$lInstanceFieldsWrapper->appendChild($lFieldXMLNode);
			}
			$lCon->MoveNext();
		}
		$this->DumpMemory('Mem fields');

		$this->DumpMemory('Mem xml');
	}

	protected function serializeFigures(){
		$this->initDBCon();
		$lCon = $this->m_con;
		// Взимаме всичките фигури с една заявка.
		$lFiguresSql = '(SELECT
					m.id as photo_id,
					m.document_id,
					m.plate_id,
					null as format_type,
					null as photo_ids_arr,
					null as photo_positions_arr,
					m.title as photo_title,
					m.description as photo_desc,
					m.position,
					m.move_position,
					null as plate_desc,
					null as plate_title,
					m.lastmod,
					m.ftype as ftype,
					m.link as link
				FROM pwt.media m
				WHERE m.plate_id IS NULL AND m.document_id = ' . (int)$this->m_document_id . ' AND m.ftype IN (0,2)
			UNION
				SELECT
					null as photo_id,
					max(m.document_id) as document_id,
					m.plate_id,
					max(p.format_type) as format_type,
					array_agg(m.id) as photo_ids_arr,
					array_agg(m.position) as photo_positions_arr,
					null as photo_title,
					null as photo_desc,
					null as position,
					max(m.move_position),
					max(p.description) as plate_desc,
					max(p.title) as plate_title,
					max(p.lastmod) as lastmod,
					null as ftype,
					null as link
				FROM pwt.media m
				JOIN pwt.plates p ON p.id = m.plate_id
				WHERE m.document_id = ' . (int)$this->m_document_id . ' AND m.ftype IN (0,2)
				GROUP BY m.plate_id
		)
		ORDER BY move_position';

		$lCon->Execute($lFiguresSql);
		$lCon->MoveFirst();
		$lFiguresNode = $this->m_documentXmlDom->documentElement->appendChild($this->m_documentXmlDom->createElement('figures'));
		$lFigNumber = 1;
		while(! $lCon->Eof()){
			$lCurrentFig = $lCon->mRs;
			$lCurrentFig['fig_number'] = $lFigNumber++;
			/**
			 * Тук сериализираме всички фигури - за целта създаваме root възел за фигурите
			 * и в него сериализираме 1 по 1 фигурите
			 */
			if((int)$lCurrentFig['ftype'] == 2) { // Video
				$this->serializeVideo($lFiguresNode, $lCurrentFig);
			} else {
				if((int)$lCurrentFig['plate_id']) {
					$this->serializePlate($lFiguresNode, $lCurrentFig);
				} else {
					$this->serializeFigure($lFiguresNode, $lCurrentFig);
				}
			}
			$lCon->MoveNext();
		}
		// 		$lCon->Close();

		$this->DumpMemory('Mem figures');
	}

	protected function serializeTables(){
		$this->initDBCon();
		$lCon = $this->m_con;
		// Взимаме всичките таблици с една заявка.

		$lTablesSql = 'SELECT t.id,
				t.title as table_title,
				t.description as table_desc,
				t.move_position as position,
				t.lastmod
			FROM pwt.tables t
			WHERE t.document_id = ' . (int)$this->m_document_id . '
			ORDER BY t.move_position ASC
		';





		$lCon->Execute($lTablesSql);
		$lCon->MoveFirst();
		$lTablesNode = $this->m_documentXmlDom->documentElement->appendChild($this->m_documentXmlDom->createElement('tables'));
		$lTableNumber = 1;
		while(! $lCon->Eof()){
			$lCurrentTable = $lCon->mRs;
			$lCurrentTable['table_number'] = $lTableNumber++;
			$this->serializeTable($lTablesNode, $lCurrentTable);
			$lCon->MoveNext();
		}
		$this->DumpMemory('Mem tables');
	}

	protected function serializeDocumentInfo($pDocumentXmlNode){

		$lDocumentInfo = 'SELECT p.name as document_type, j.name as journal_name, t.id as template_id, p.id as papertype_id, j.id as journal_id,
								now() as db_current_time
							FROM pwt.document_template_objects o
							LEFT JOIN pwt.templates t ON t.id = o.template_id
							LEFT JOIN public.journals j ON j.id = t.journal_id
							JOIN pwt.documents d ON d.id = o.document_id
							LEFT JOIN pwt.papertypes p ON p.id = d.papertype_id

							WHERE o.document_id = ' . (int)$this->m_document_id . '
							LIMIT 1';

		$lCon = $this->m_con;

		$lCon->Execute($lDocumentInfo);
		$lCon->MoveFirst();
		$this->m_currentDbTime = $lCon->mRs['db_current_time'];

		// set journal_id attr
		if($this->m_mode == 1){
			$pDocumentXmlNode->setAttribute('journal_id', (int)$lCon->mRs['journal_id']);
		}

		$lInfoNode = $this->m_documentXmlDom->documentElement->appendChild($this->m_documentXmlDom->createElement('document_info'));
		$lDocumentType = $lInfoNode->appendChild($this->m_documentXmlDom->createElement('document_type', $lCon->mRs['document_type']));

		if($this->m_mode == 1){
			if((int)$lCon->mRs['papertype_id']){
				$lDocumentType->setAttribute('id', $lCon->mRs['papertype_id']);
			}
		} elseif($this->m_mode == 2) {
			if((int)$lCon->mRs['template_id']){
				$lDocumentType->setAttribute('id', $lCon->mRs['template_id']);
			}
		}

		$lDocumentJournal = $lInfoNode->appendChild($this->m_documentXmlDom->createElement('journal_name', $lCon->mRs['journal_name']));

		if((int)$lCon->mRs['journal_id']){
			$lDocumentJournal->setAttribute('id', $lCon->mRs['journal_id']);
		}

		$lUser = $lInfoNode->appendChild($this->m_documentXmlDom->createElement('user'));
	}

	/**
	 * Тук ще сериализираме 1 инстанс обект.
	 * За целта подаваме id-то на инстанса
	 * на обекта, както и данните за този инстанс
	 *
	 * @param
	 *       	 $pInstanceId
	 * @param
	 *       	 $pInstanceDetails
	 */
	protected function serializeInstance($pInstanceId, $pInstanceDetails) {
// 		$lObjectData = $this->m_objectDetails[$pObjectInstanceId];
		$lObjectData = $pInstanceDetails;
		$lParentId = (int) $lObjectData['parent_id'];
		$lParentNode = $this->m_instanceDetails[$lParentId]['instance_node'];
		$lParentIsModified = $this->m_instanceDetails[$lParentId]['is_modified'];
		$lParentCachedXmlType = $this->m_instanceDetails[$lParentId]['cached_xml_type'];
		
		if($lParentId && !$lParentNode){
			//No parent node - we have nothing to do. This branch of the tree wont be added to the xml
			return;
		}
		
		$lObjectXmlNode = null;
		$lDataHasBeenTakenFromCache = true;
		if(!$lObjectData['instance_is_modified'] && $lObjectData["cached_xml_type"] == OBJECTS_CACHED_XML_TYPE_WHOLE_TREE){
// 			var_dump($pInstanceId);
			$lObjectXmlNode = $this->parseInstanceCache($pInstanceId, $lObjectData['cached_xml']);			
		}
		
		if($lObjectXmlNode == null){
			//If the node has not been created by any chance
			$lObjectXmlNode = $this->m_documentXmlDom->createElement($lObjectData['xml_node_name']);
			$lDataHasBeenTakenFromCache = false;
		}
		
		if($lParentNode && ($lParentIsModified || $lParentCachedXmlType != OBJECTS_CACHED_XML_TYPE_WHOLE_TREE)){
			//Add to parent only if the parent is modified or the parent doesnt cache the whole tree
			$lObjectXmlNode = $lParentNode->appendChild($lObjectXmlNode);
		}
		
		$this->m_instanceDetails[$pInstanceId]['instance_node'] = $lObjectXmlNode;
		
		if($lDataHasBeenTakenFromCache){
			//If the data has been taken from the cache - we have nothing more to do
			return;
		}

		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
// 			$lObjectXmlNode = $pParentXmlNode->appendChild($this->m_documentXmlDom->createElement('object'));
			$lObjectXmlNode->setAttribute('object_id', $lObjectData['object_id']);
			$lObjectXmlNode->setAttribute('instance_id', $lObjectData['id']);
			$lObjectXmlNode->setAttribute('display_name', $lObjectData['display_name']);
			$lObjectXmlNode->setAttribute('pos', $lObjectData['pos']);
		}
		
		if($this->m_mode == (int)SERIALIZE_INPUT_MODE && (int)$lObjectData['generate_xml_id']){
			$lIdx = 1;
			foreach ($this->m_instanceDetails[$lObjectData['parent_id']]['children'] as $lChildInstanceId) {
				if($lChildInstanceId == $pInstanceId)
					break;

				if($this->m_instanceDetails[$lChildInstanceId]['object_id'] == $lObjectData['object_id']){
					$lIdx++;
				}
			}
			$lObjectXmlNode->SetAttribute('id', $lIdx);
		}		
		
		//Add the fields wrapper from the cache
		if(!$lObjectData['instance_is_modified'] && $lObjectData["cached_xml_type"] == OBJECTS_CACHED_XML_TYPE_ONLY_FIELDS){
			$lFieldsWrapperXmlNode = $this->parseInstanceCache($pInstanceId, $lObjectData['cached_xml']);
			if($lFieldsWrapperXmlNode){
				$this->m_instanceDetails[$pInstanceDetails]['fields_wrapper_node'] = $lObjectXmlNode->appendChild($lFieldsWrapperXmlNode);
			}
		}
	}

	function serializeField($pFieldData){
		$lFieldXmlNode = $this->m_documentXmlDom->createElement($pFieldData['xml_node_name']);
		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			// 			$lFieldXmlNode = $pParentXmlNode->appendChild($this->m_documentXmlDom->createElement('field'));

			$lFieldXmlNode->setAttribute('id', $pFieldData['id']);
			$lFieldXmlNode->setAttribute('field_name', $pFieldData['field_name']);
		}




		// 		$lFieldValueColumn = $pFieldData['value_column_name'];
		// 		$lFieldValue = $pFieldData[$lFieldValueColumn];
		$lFieldValue = $pFieldData['value'];

		// За да мине валидацията на датата при SERIALIZE_INTERNAL_MODE
		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			if($pFieldData['type'] == (int)FIELD_DATE_TYPE) {
				$lParsedValue = prepareDateFieldForXSDValidation($lFieldValue);
			} else {
				$lParsedValue = parseFieldValue($lFieldValue, $pFieldData['type']);
			}
		} else {
			$lParsedValue = parseFieldValue($lFieldValue, $pFieldData['type']);
		}



		$lRealValue = getFieldValueForSerialization($lParsedValue, $pFieldData['control_type'], $pFieldData['data_src_id'], $pFieldData['data_src_query'], $this->m_document_id, $pFieldData['instance_id'], (int)$this->m_useExistingDbConnection);

		if(is_array($lRealValue)){
			if(! count($lRealValue)){
				$lFieldValueNode = $lFieldXmlNode->appendChild($this->m_documentXmlDom->createElement('value'));
			}else{
				// 				var_dump($lParsedValue);

				foreach($lRealValue as $lKey => $lValue){
					// 					$lValue = '<i>dsadas&asd</i>'		;

					$lFieldValueNode = $lFieldXmlNode->appendChild($this->m_documentXmlDom->createElement('value'));

					if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
						$lFieldValueNode->setAttribute('value_id', $lKey);
					}

					$lFragment = $this->m_documentXmlDom->createDocumentFragment();
					$lPreparedValue = prepareXmlValue($lValue);
					if(@$lFragment->appendXML($lPreparedValue)){
						$lFieldValueNode->appendChild($lFragment);
					}else{
						$lFieldValueNode->appendChild($this->m_documentXmlDom->createTextNode($lValue));
					}
				}
			}
		}else{
			// 			$lRealValue = '111<i>a<b>s</b>d</i>';

			$lFieldValueNode = $lFieldXmlNode->appendChild($this->m_documentXmlDom->createElement('value'));
			$lFragment = $this->m_documentXmlDom->createDocumentFragment();
			$lPreparedValue = prepareXmlValue($lRealValue);


			if(@$lFragment->appendXML($lPreparedValue)){
				// 				var_dump($lPreparedValue);
				$lFieldValueNode->appendChild($lFragment);
			}else{
				// 				var_dump($lRealValue);
				$lFieldValueNode->appendChild($this->m_documentXmlDom->createTextNode($lRealValue));
			}
		}
		return $lFieldXmlNode;
	}




	function serializeFigure(&$pParentXmlNode, $pFigData) {
		$lFigureXmlNode = $pParentXmlNode->appendChild($this->m_documentXmlDom->createElement('figure'));

		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			$lFigureXmlNode->setAttribute('id', $pFigData['photo_id']);
		}else{
			$lFigureXmlNode->setAttribute('id', $pFigData['fig_number']);
		}

		$lFigureCaption = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('caption'));
		$lCaption = prepareXmlValue($pFigData['photo_desc']);
		$lFragment = $this->m_documentXmlDom->createDocumentFragment();
		if(@$lFragment->appendXML($lCaption)){
			$lFigureCaption->appendChild($lFragment);
		}else{
			$lFigureCaption->appendChild($this->m_documentXmlDom->createTextNode($lCaption));
		}

		$lFigureUrl = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('url'));
		$lUrl = SITE_URL . SHOWFIGURE_URL . 'big_' . $pFigData['photo_id'] . '.jpg';
		$lFigureUrl->appendChild($this->m_documentXmlDom->createTextNode($lUrl));
	}


	function serializeVideo(&$pParentXmlNode, $pFigData) {
		$lFigureXmlNode = $pParentXmlNode->appendChild($this->m_documentXmlDom->createElement('figure'));

		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			$lFigureXmlNode->setAttribute('id', $pFigData['photo_id']);
		}else{
			$lFigureXmlNode->setAttribute('id', $pFigData['fig_number']);
		}

		$lFigureXmlNode->setAttribute('is_video', '1');

		$lFigureCaption = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('caption'));
		$lCaption = prepareXmlValue($pFigData['photo_title']);
		$lFragment = $this->m_documentXmlDom->createDocumentFragment();
		if(@$lFragment->appendXML($lCaption)){
			$lFigureCaption->appendChild($lFragment);
		}else{
			$lFigureCaption->appendChild($this->m_documentXmlDom->createTextNode($lCaption));
		}

		$lFigureUrl = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('url'));
		$lUrl = $pFigData['link'];
		$lFigureUrl->appendChild($this->m_documentXmlDom->createTextNode($lUrl));
	}

	/**
	 * Тук сериализираме един plate
	 */
	function serializePlate(&$pParentXmlNode, $pFigData) {
		$lFigureXmlNode = $pParentXmlNode->appendChild($this->m_documentXmlDom->createElement('figure'));
		$lFigureXmlNode->setAttribute('is_plate', '1');
		$lPlateFiguresIds = explode(',', ereg_replace("[{-}]", '', $pFigData['photo_ids_arr']));

		$lSql = 'SELECT m.description,
						m.id
				FROM pwt.media m
				WHERE m.plate_id = ' . (int) $pFigData['plate_id'] . ' AND m.document_id = ' . $this->m_document_id;
		if(!$this->m_useExistingDbConnection){
			$lCon = new DBCn();
			$lCon->Open();
		}else{
			$lCon = Con();
			$lCon->CloseRs();
		}

		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lPlatePhotosDesc = array();
		while(! $lCon->Eof()){
			$lPlatePhotosDesc[$lCon->mRs['id']] = $lCon->mRs['description'];
			$lCon->MoveNext();
		}
		$lCon->CloseRs();

		$lPlateFiguresPositions = explode(',', ereg_replace("[{-}]", '', $pFigData['photo_positions_arr']));
		$lCombinePlateFigures = array_combine((array)$lPlateFiguresIds, (array)$lPlateFiguresPositions);
		asort($lCombinePlateFigures);

		//~ $lCombinePlateFiguresDesc = array_combine((array)$lPlateFiguresIds, (array)$lPlatePhotosDesc);

		$lPlateFiguresInfoArr[$pFigData['format_type']] = $lCombinePlateFigures;

		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			$lFigureXmlNode->setAttribute('id', $pFigData['plate_id']);
			$lFigureXmlNode->setAttribute('type', $pFigData['format_type']);
		}else{
			$lFigureXmlNode->setAttribute('id', $pFigData['fig_number']);
			$lFigureXmlNode->setAttribute('type', $pFigData['format_type']);
		}

		$lFigureCaption = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('caption'));
		//$lCaption = $this->xmlEscape($pFigData['plate_desc']);
		$lCaption = $pFigData['plate_desc'];

		$lFragment = $this->m_documentXmlDom->createDocumentFragment();
		if(@$lFragment->appendXML($lCaption)){
			$lFigureCaption->appendChild($lFragment);
		}else{
			$lFigureCaption->appendChild($this->m_documentXmlDom->createTextNode($lCaption));
		}

		foreach($lPlateFiguresInfoArr as $key=>$val){
			foreach($val as $k=>$v){
				$lFigureUrl = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('url'));
				$lUrl = SITE_URL . SHOWFIGURE_URL . 'big_' . $k . '.jpg';
				$lFigureUrl->appendChild($this->m_documentXmlDom->createTextNode($lUrl));
				$lFigureUrl->setAttribute('id', $k);

				$lFigureDesc = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('photo_description'));
				$lDesc = $lPlatePhotosDesc[$k];
				$lFigureDesc->appendChild($this->m_documentXmlDom->createTextNode($lDesc));
			}
		}

	}


	function serializeTable(&$pParentXmlNode, $pFigData) {
		$lFigureXmlNode = $pParentXmlNode->appendChild($this->m_documentXmlDom->createElement('table'));

		if($this->m_mode == (int)SERIALIZE_INTERNAL_MODE){
			$lFigureXmlNode->setAttribute('id', $pFigData['id']);
			$lFigureXmlNode->setAttribute('position', $pFigData['position']);
		}else{
			$lFigureXmlNode->setAttribute('id', $pFigData['table_number']);
		}

		$lFigureTitle = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('title'));
		$lFigureCaption = $lFigureXmlNode->appendChild($this->m_documentXmlDom->createElement('description'));
		$lTitle = prepareXmlValue($pFigData['table_title']);
		//$lCaption = $this->xmlEscape(prepareXmlValue($pFigData['table_desc']));
		$lCaption = prepareXmlValue($pFigData['table_desc']);
		$lFragment = $this->m_documentXmlDom->createDocumentFragment();
		if(@$lFragment->appendXML($lTitle)){
			$lFigureTitle->appendChild($lFragment);
		}else{
			$lFigureTitle->appendChild($this->m_documentXmlDom->createTextNode($lTitle));
		}
		if(@$lFragment->appendXML($lCaption)){
			$lFigureCaption->appendChild($lFragment);
		}else{
			$lFigureCaption->appendChild($this->m_documentXmlDom->createTextNode($lCaption));
		}
	}
	/**
	 * Try to import the cached xml from the instance
	 * in the document xml.
	 * On success returns a xml fragment.
	 * On failure returns null
	 * @param xml $pCachedXml
	 */
	protected function parseInstanceCache($pInstanceId, $pCachedXml){
		if($pCachedXml){
			$lFragment = $this->m_documentXmlDom->createDocumentFragment();
	// 		var_dump($pCachedXml);
			if(@$lFragment->appendXML($pCachedXml)){
				return $lFragment;
			}
		}
		$this->markInstanceAsModified($pInstanceId);
		return null;
	}
	
	/** Mark the instance as modified when there is an error in its cache(i.e. when the cache cannot be parsed)
	 * 	so that its cache can be regenerated. We will accumulate a list with their ids
	 * 	so that we can update them in the db with the current connection
	 * 	before the fields sql is executed 
	 */  
	protected function markInstanceAsModified($pInstanceId){
		$this->m_modifiedInstances[] = $pInstanceId;
		$this->m_instancesWithInvalidCache[] = $pInstanceId;
		$this->m_instanceDetails[$pInstanceId]['is_modified'] = true;
	}
	
	/**
	 * Mark the instances with invalid xml in the cache
	 * so that their cache can be regenerated
	 */
	protected function processInstancesWithInvalidCache(){
		if(!is_array($this->m_instancesWithInvalidCache) || !count($this->m_instancesWithInvalidCache)){
			return;
		}		
		$this->m_instancesWithInvalidCache = array_map('intval', $this->m_instancesWithInvalidCache);
// 		var_dump($this->m_instancesWithInvalidCache);
		$lSql = 'UPDATE pwt.document_object_instances SET					
						is_modified = true,
						lastmod_date = \'' . q($this->m_currentDbTime) . '\'
				WHERE document_id = ' . (int)$this->m_document_id . ' AND id IN (' . implode(',', $this->m_instancesWithInvalidCache) . ')';
		$this->m_con->Execute($lSql);
	}
	
	/**
	 * Update the cache for all the modified instances
	 */
	protected function updateModifiedInstancesCache(){
		if(!is_array($this->m_modifiedInstances ) || !count($this->m_modifiedInstances )){
			return;
		}
// 		sleep(30);
// 		return;
// 		var_dump($this->m_modifiedInstances);
		//Execute 1 big sql
		$lSql = '';
		foreach ($this->m_modifiedInstances as $lInstanceId) {
			$lSql .= $this->updateModifiedInstanceCache($lInstanceId);
// 			$this->m_con->Execute($lSql);
		}
		$this->DumpTimeLog('END GENERATING CACHE SERIALIZING');
		$this->m_con->Execute($lSql);
// 		var_dump($this->m_con->GetLastError());
// 		var_dump($this->m_modifiedInstances);
// 		var_dump($lSql);
// 		exit;
		
		
	}
	
	protected function storeDocumentXml(){
		$lSql = 'SELECT * FROM pwt.spStoreDocumentXml(' . (int) $this->m_document_id . ', \'' . q($this->getXml())  . '\')';
		$this->m_con->Execute($lSql);
	}
	
	protected function updateModifiedInstanceCache($pInstanceId){
		$lSql = '';
		$lXml = '';
		$lNode = null;
		
// 		var_dump($pInstanceId);
		switch($this->m_instanceDetails[$pInstanceId]['cached_xml_type']){
			default:
				//Unrecognized cache type - just mark the field as unmodified
				break;
			case (int)OBJECTS_CACHED_XML_TYPE_ONLY_FIELDS:
				$lNode = $this->m_instanceDetails[$pInstanceId]['fields_wrapper_node'];
				break;
			case (int)OBJECTS_CACHED_XML_TYPE_WHOLE_TREE:
				$lNode = $this->m_instanceDetails[$pInstanceId]['instance_node'];
				break;
		}
		if(!$lNode){
			$lUpdatedVal = 'null';
		}else{
			$lXml = $this->m_documentXmlDom->saveXML($lNode);
			$lUpdatedVal = '\'' . q($lXml) . '\'';
		}
		
		
// 		var_dump($this->m_documentXmlDom->saveXML($lNode));
		$lSql = 'UPDATE pwt.document_object_instances SET
					cached_xml = ' . ($lUpdatedVal) . ',
					is_modified = false
				WHERE document_id = ' . (int)$this->m_document_id . ' AND id = ' . (int)$pInstanceId . ' AND lastmod_date <= \'' . q($this->m_currentDbTime) . '\'::timestamp;
		';
		
// 		if($pInstanceId == 252633){
// 			trigger_error('SERIALIZING 252633 ' . $lSql, E_USER_NOTICE);			
// 		}
		return $lSql;
// 		$lResult = $this->m_con->Execute($lSql);
// 		if($pInstanceId == 230389){
// 			var_dump($lResult);
// 			var_dump($this->m_con->GetLastError());
// 		}
	}

	function xmlEscape($pStringToEscape) {
		//~ return $pStringToEscape;
 		return str_replace(array('&nbsp;', '&', '\''), array(' ', '&amp;', '&apos;'), $pStringToEscape);
	}
	
	


}
?>