<?php
/**
	Този клас ще реализира десериализацията на подаден xml
	в нова head версия на статията в базата.
*/
class cdocument_deserializer{
	var $m_errorCount;
	var $m_errorMsg;	
	var $m_xml;
	var $m_xmlDom;
	var $m_domXPath;
	var $m_dontGetData;
	var $m_templateId;
	var $m_documentId;
	
	var $m_nextInstanceId;
	var $m_nextFieldId;
	
	var $m_temp_instances_table_name;
	var $m_temp_fields_table_name;
	
	/**
	 * 
	 * Конекция към базата
	 * @var unknown_type
	 */
	var $m_con;
	
	/**
	 * Тук ще пазим масив на всички инстанции. Формата ще е следния
	 * 		id => array(
	 * 			parent_instance_id => val,
	 * 			object_id => val, 
	 * 			display_in_tree => val,
	 * 			level => val,
	 * 		)
	 * 	където id ще е поредния номер на срещане на обекта в xml дървото. Позицията ще се изчислява автоматично при вкарването
	 *  на истинските резултати в реалните таблици.
	 *  parent_instance_id - id-то на parent възела(поредния номер на срещане на parent възела в xml дървото)
	 *  level - нивото на instance-a в йерархията. Почваме от 1во.
	 */ 		
	var $m_object_instances;
	/**
	 * Тук ще пазим масив на всички field-ове. Формата ще е следния:
	 * 		id => array(
	 * 			instance_id => val,
	 * 			field_id => val,
	 * 			value => val,
	 * 		)
	 * Тук instance_id отговаря на полето id от масива с инстанциите.
	 */
	var $m_fields;
	
	function __construct($pXml, $pUid){
		$this->m_errorCount = 0;
		$this->m_errorMsg = '';
		$this->m_xml = $pXml;
		$this->m_dontGetData = false;
		$this->initXmlDocument();
		
		$this->m_nextInstanceId = 1;
		$this->m_nextFieldId = 1;
		
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_temp_fields_table_name  = 'temp_instance_field_values_' . $pUid;
		$this->m_temp_instances_table_name  = 'temp_document_object_instances' . $pUid;
	}
	
	
	/** 
	 * Зарежда xml-a в дом документа. 
	 */
	protected function initXmlDocument() {
		$this->m_xmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		if(!$this->m_xmlDom->loadXML($this->m_xml)){
			$this->setError(getstr('pwt.couldNotLoadDocumentXml'));
			return;
		}
		$this->m_domXPath = new DOMXPath($this->m_xmlDom);	
	}
	
	function GetData(){
		if($this->m_dontGetData)
			return;
		$this->validateXmlStructure();
		if(!$this->m_errorCount){
			$this->importDocumentToDb();
		}
		$this->m_dontGetData = true;
	}
	
	/** 
	 * Тук вкарваме документа в базата. 
	 * 
	 * За целта, ако документа е нов, си копираме структурата на темплейта(стандартно) или ако е нова версия
	 * правим копие на текущата версия в базата. След това просто наливаме данните от 
	 * xml-а в базата чрез temp таблици. Сигурни сме, че структурата на xml-а е вярна, 
	 * понеже иначе validateXmlStructure е щяла да гръмне, и затова не правим проверка на обектите - директно
	 * ще вкарваме instance-и и стойности на field-овете.
	 */
	protected function importDocumentToDb(){
		if($this->m_errorCount)
			return;
		$this->storePrevData();	
		if($this->m_errorCount)
			return;
		$lObjects = array();
		
		//Взимаме структурата в масиви, за да може по-лесно да я вкараме в базата
		$lRootObjectsQuery = '/document/objects/object';
		$lRootObjectsResult = $this->m_domXPath->query($lRootObjectsQuery);
		if($lRootObjectsResult->length){
			for($i = 0; $i < $lRootObjectsResult->length; ++$i){
				$lCurrentObjectInstance = $lRootObjectsResult->item($i);
				$this->processObjectInstance($lCurrentObjectInstance);
			}
		}
		//Вече пишем в базата. Първо вкарваме данните в темп таблици, а после в истинските таблици
		try {
			//Стартираме транзакцията
			$this->executeSqlQuery('BEGIN TRANSACTION;');
			//Създаваме темп таблиците
			$lCreateTableSql = '
				CREATE TEMP TABLE ' . $this->m_temp_instances_table_name . '(
					id bigint NOT NULL,
					pos varchar,
					object_id bigint,
					real_id bigint,
					document_id int,
					parent_instance_id bigint,
					display_in_tree int,
					level int
				);
				CREATE TEMP TABLE ' . $this->m_temp_fields_table_name . '(
					id bigint NOT NULL,
					value varchar,
					field_id bigint NOT NULL,
					document_id int,
					instance_id bigint
				);			
			';
			
			//Вкарваме данните. Работим с prepared statement-и понеже са еднотипни и са по-бързи от единични insert-и.
			//Първо вкарваме instance-ите.			
			$this->prepareSqlStatement('TempObjectInstancesInsertStatement', 'INSERT INTO ' . $this->m_temp_instances_table_name . '(id, object_id, parent_instance_id, document_id, display_in_tree, level) VALUES ($1, $2, $3, $4, $5, $6);');
			foreach ($this->m_object_instances as $lId => $lObjectData) {
				$this->executeSqlPreparedStatement('TempObjectInstancesInsertStatement', array($lId, $lObjectData['object_id'], $lObjectData['parent_instance_id'], $this->m_documentId, $lObjectData['display_in_tree'], $lObjectData['level']));
			}
			
			//вкарваме field-овете
			$this->prepareSqlStatement('TempFieldsInsertStatement', 'INSERT INTO ' . $this->m_temp_fields_table_name . '(id, field_id, value, document_id, instance_id) VALUES ($1, $2, $3, $4, $5);');
			foreach ($this->m_fields as $lId => $lFieldData) {
				$this->executeSqlPreparedStatement('TempFieldsInsertStatement', array($lId, $lFieldData['field_id'], $lFieldData['value'], $this->m_documentId, $lFieldData['instance_id']));
			}
			
			//Сега трябва да изчислим позицииите на обектите
			$this->executeSqlQuery('SELECT * FROM spCalculateTempObjectInstancesPos(' . $this->m_temp_instances_table_name . ')');
			//Вкарваме реалните instance-и в базата
			$this->executeSqlQuery('SELECT * FROM spInsertTempObjectInstances(' . $this->m_temp_instances_table_name . ')');
			//Вкарваме и field-овете
			$this->executeSqlQuery('SELECT * FROM spInsertTempFields(' . $this->m_temp_instances_table_name . ', ' . $this->m_temp_fields_table_name . ')');
			
			//Ако всичко е ОК - къмитваме
			$this->executeSqlQuery('COMMIT TRANSACTION;');
		} catch (Exception $e) {
			/*
			 * При грешка - спираме всичко надолу. Класа автоматично ще ролбекне
			 * Затова няма нужда да зачистваме нещо.
			 */
			return;
		}
		
		
		
	}
	
	/**
	 * Изпълняваме sql заявка. Ще използваме член променливата за конекция към базата.
	 * Ако гръмне - ролбек-ваме.
	 * При грешка ще хвърляме exception, за да може да не правим след всяка команда проверка дали всичко е минало успешно,
	 * а наведнъж да обработваме грешка при коя да е заявка.
	 * @param unknown_type $lQuery - заявката, която ще се опитаме да изпълним.
	 */
	protected function executeSqlQuery($lQuery){
		if(!$this->m_con->Execute($lQuery)){
			$this->setSqlError($this->m_con->GetLastError());			
		}
	}
	
	/**
	 * Изпълняваме sql prepared statement. Ще използваме член променливата за конекция към базата.
	 * Ако гръмне - ролбек-ваме.
	 * При грешка ще хвърляме exception, за да може да не правим след всяка команда проверка дали всичко е минало успешно,
	 * а наведнъж да обработваме грешка при коя да е заявка.
	 * @param unknown_type $lStatement - името на statement-а, който ще изпълняваме
	 * @param $lParams - масив с параметрите, които се ползват от prepared statement-а
	 */
	protected function executeSqlPreparedStatement($lStatementName, $lParams){		
		if(pg_execute($this->m_con->mhCn, $lStatementName, $lParams) === false){			
			$this->setSqlError(getstr('pwt.sqlCouldNotExecutePreparedStatementError'));
		}
	}
	
	/**
	 * Сигнализираме за sql грешка. 
	 * 
	 * За целта първо сетваме грешка. След това ролбекваме и хвърляме exception,
	 * за да може да го обработим на 1 място
	 * @param unknown_type $lErrorMsg - съобщението за грешката
	 */
	protected function setSqlError($lErrorMsg){
		$this->setError($lErrorMsg);
		$this->m_con->Execute('ROLLBACK TRANSACTION;');
		throw new Exception(getstr('pwt.sqlError'));
	}
	
	/**
	 * Създаваме sql prepared statement.
	 * Създаваме го във функция за да може по лесно да обработим грешката, ако стане нещо. 
	 * 
	 * @param unknown_type $lStatementName - името на statement-a
	 * @param unknown_type $lStatementQuery - параметризираната sql заявка
	 */
	protected function prepareSqlStatement($lStatementName, $lStatementQuery){
		if(pg_prepare($this->m_con->mhCn, $lStatementName, $lStatementQuery) === false){
			$this->setSqlError(getstr('pwt.sqlCouldNotPrepareSqlPreparedStatementError'));
		}
	}
	
	/**
	 * Обработва подадения възел на instance и го записва в $this->m_object_instances. 
	 * @param unknown_type $pInstanceNode - възела, който обработваме
	 * @param unknown_type $pParentId - id на parent възела
	 * @param unknown_type $pLevel - нивото на instance-a в йерархията
	 */
	protected function processObjectInstance(&$pInstanceNode, $pParentId = 0, $pLevel = 1){
		$lInstanceId = $this->m_nextInstanceId++;
		
		$this->m_object_instances[$lInstanceId] = array(
			'object_id' => $pInstanceNode->getAttribute('object_id'),
			'parent_instance_id' => $pParentId,
			'display_in_tree' => (int)$pInstanceNode->getAttribute('display_in_tree'),
			'level' => $pLevel,
		);
		
		//Първо взимаме всички field-ове
		$lFieldsQuery = './field';
		$lFieldsQueryResult = $this->m_domXPath->query($lFieldsQuery, $pInstanceNode);
		for($i = 0; $i < $lFieldsQueryResult->length; ++$i){
			$this->processInstanceField($lFieldsQueryResult->item($i), $lInstanceId);
		}
		//После обикаляме всички подобекти
		$lSubobjectsQuery = './object';
		$lSubobjectsQueryResult = $this->m_domXPath->query($lSubobjectsQuery, $pInstanceNode);
		for($i = 0; $i < $lSubobjectsQueryResult->length; ++$i){
			$this->processObjectInstance($lSubobjectsQueryResult->item($i), $lInstanceId, $pLevel + 1);
		}
	}
	
	/**
	 * Обработва подадения възел на field-a и го записва в $this->m_fields. 
	 * @param unknown_type $pFieldNode - възела, който обработваме
	 * @param unknown_type $pParentInstanceId - id на instance-a, към който е field-a
	 */
	protected function processInstanceField(&$pFieldNode, $pParentInstanceId){
		$lFieldId = $this->m_nextFieldId++;
		$this->m_fields[$lFieldId] = array(
			'instance_id' => $pParentInstanceId,
 			'field_id' => $pFieldNode->getAttribute('id'),
			'value' => $pFieldNode->textContent,
		);
	}
	
	/**
	 * Тук запазваме предишната версия на статията или структурата на темплейта, ако правим нова статия
	 */
	protected function storePrevData() {
		if($this->m_errorCount)
			return;
		if($this->m_documentId){//Правим нова версия
			
		}else{//Правим нов документ
					
		}
	}
	
	/**
	 * Валидация на структурата на xml-а спрямо структурата на обектите в базата
	 * 
	 * Взима id-то на документа/темплейта. Ако те съществуват се валидира на структурата на документа
	 * спрямо структурата на темплейта/документа за да сме сигурни, че няма
	 * да си вкараме грешна структура в базата
	 */
	protected function validateXmlStructure(){
		$lDocumentQuery = '/document';
		$lDocumentQueryResult = $this->m_domXPath->query($lDocumentQuery);
		if($lDocumentQueryResult->length){
			$lDocumentNode = $lDocumentQueryResult->item(0);
			//Нова версия
			$this->m_documentId = (int)$lDocumentNode->getAttribute('id');
			if(!$this->m_documentId){//Нов документ
				$this->m_templateId = (int)$lDocumentNode->getAttribute('template_id');
			}
		}
		if(!$this->m_templateId && !$this->m_documentId){
			$this->setError(getstr('pwt.wrongXmlFormat'));
			return;
		}
	} 
	
	function setError($pErrorMsg){
		$this->m_errorCount++;
		$this->m_errorMsg .= $pErrorMsg;
	}
}