<?php
require_once PATH_CLASSES . 'comments.php';
/**
 * Този клас ще реализира запазването на документа след натискане
 * на сейв бутона под формата на някой инстанс.
 * Ще е направен събмит на тази пост форма и ще са подадени id на документа и id на инстанса, който събмитваме.
 * Всички полета ще са подадени в поста във формат instance_id__field_id.
 * Първо ще се конектнем към базата и ще вземем информацията за всички field-ове на instance-a (на всички по-долни нива вкл).
 * След това ще обработим получените от POST стойности и ще ъпдейтнем базата.
*/
class cdocument_saver{
	var $m_errorCount;
	var $m_errorMsg;
	var $m_validationErrorCount;

	var $m_dontGetData;
	var $m_documentId;
	var $m_rootInstanceId;

	/**
	 *
	 * Конекция към базата
	 * @var DBCn
	 */
	var $m_con;


	/**
	 * @formatter:off
	 * Тук ще пазим масив на всички field-ове групирани по . Формата ще е следния:
	 * 		instance_id => array(
	 * 			field_id => array(
	 * 				label => val,
	 * 				type => val,
	 * 				html_control_type => val,
	 * 				value_column_name => val,
	 * 				base_value => val,
	 * 				allow_nulls => val,
	 * 				value_is_null => val
	 * 				has_validation_error => val,
	 * 				validation_err_msg => val,
	 *				is_array => boolean,
	 *				is_html => boolean,
	 *				previous_value => val,
	 *				comments => array(
	 *					comment_id => array(
	 *						previous_start_offset => val,
	 *						previous_end_offset => val,
	 *						position_fix_type => val
	 *					)
	 *				)
	 * 			),
	 * 		)
	 *
	 * instance_id - id-то на instance-а, към който е полето
	 * field_id - id-то на полето
	 * label - label-а на полето за съответния обект
	 * type - тип на полето (int, string ...)
	 * html_control_type - html тип на полето (select, multiple select ...)
	 * value_column_name - колоната в която се пази стойността на полето в таблицата (value_int, value_str ...)
	 * base_value - стойността в $_REQUEST за това поле
	 * has_validation_error => дали има грешка при валидацията на стойността на полето
	 * validation_err_msg => съобщението за грешка при валидацията на стойността на полето
	 * is_array => whether the field should have an array value or not
	 * is_html => whether the fields should have an html value or not
	 * previous_value => the previous value of the field (in case we need to make a diff to fix teh
	 * 		comments positions - we will get it from the comments query
	 * comments => an array of all the comments which start/end in this field
	 * previous_start_offset, previous_end_offset => the offset position of the comment in the field (it
	 * 		is possible only one of previous_start_offset/previous_end_offset to be meaningful - i.e. if the
	 * 		comment only begins/ends in the specified field.
	 * position_fix_type => which of the positions we will correct (start/end/both) - a bitmask of COMMENTS_FIX_TYPE_START_POS and COMMENTS_FIX_TYPE_END_POS
	 * @formatter:on
	 */
	var $m_fields;

	/**
	 * Тук ще пазим ид-тата на всички инстанси. Това ни трябва за да не ъпдейтваме инстанси-те, които са във view mode.
	 */
	var $m_instanceIds;

	/**
	 * По това се ориентираме дали правим автосейв, ако правим не правим чекове на полетата
	 */
	var $m_autoSaveOn;

	/**
	 * Дали save-a е в попъпа или не
	 * @var unknown_type
	 */
	var $m_inPopup;

	function __construct($pFieldArr){

		$this->m_errorCount = 0;
		$this->m_errorMsg = '';
		$this->m_validationErrorCount = 0;
		$this->m_dontGetData = false;
		$this->m_documentId = $pFieldArr['document_id'];
		$this->m_rootInstanceId = $pFieldArr['root_instance_id'];
		$this->m_instanceIds = $pFieldArr['instance_ids'];
		$this->m_autoSaveOn = (int)$pFieldArr['auto_save_on'];
		$this->m_ExplicitFieldId = (int)$pFieldArr['explicit_field_id'];
		$this->m_inPopup = (int)$pFieldArr['in_popup'];

		if($this->m_ExplicitFieldId){
			$this->m_instanceIds = array($this->m_rootInstanceId);
		}
		if(!is_array($this->m_instanceIds)){
			$this->m_instanceIds = array($this->m_rootInstanceId);
		}
		$this->m_instanceIds = array_map('parseToInt', $this->m_instanceIds);
// 		var_dump($this->m_instanceIds);


		$this->m_fields = array();

// 		$this->m_con = new DBCn();
		$this->m_con = Con();
		$this->m_con->Close();
		$this->m_con->Open();

		/*
		 * Понеже ajax save-а на различните обекти събмитва формата на root-обекта на страницата,
		* а на практика на document saver-а като root instance id се подава id-то на обекта
		* който се запазва - трябва да филтрираме instanceIds да са само на обекти, които са подобекти на
		* запазвания обект (за да не изпълняваме save action-и на обекти, които реално не са били save-нати)
		*/
		$lSql = '
			SELECT string_agg(i.id::text, \',\') as ids
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances p ON p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos))
			WHERE p.id = ' . (int)$this->m_rootInstanceId . ' AND i.id IN (' . implode(',', $this->m_instanceIds) . ')';
		$this->m_con->Execute($lSql);
		$this->m_instanceIds = explode(',', $this->m_con->mRs['ids']);
		$this->m_instanceIds = array_map('parseToInt', $this->m_instanceIds);
// 		var_dump($this->m_instanceIds);
// 		var_dump($lSql);

		$this->getFieldsData();

	}

	function HasErrors(){
		return $this->m_errorCount;
	}

	function GetErrorMsg(){
		return $this->m_errorMsg;
	}

	function HasValidationErrors(){
		return $this->m_validationErrorCount;
	}

	function SetFieldValidationError($pInstanceId, $pFieldId, $pErrorMsg){
		// Ако правим autosave не проверяваме полетата
		if(!$this->m_autoSaveOn){
			$this->m_validationErrorCount++;
			$pErrorMsg = $this->m_fields[$pInstanceId][$pFieldId]['label'] . ': ' . $pErrorMsg;
			$this->SetError($pErrorMsg, '<br/>');
			$this->m_fields[$pInstanceId][$pFieldId]['has_validation_error'] = true;
			if($this->m_fields[$pInstanceId][$pFieldId]['validation_err_msg'] != ''){
				$this->m_fields[$pInstanceId][$pFieldId]['validation_err_msg'] += '<br/>';
			}
			$this->m_fields[$pInstanceId][$pFieldId]['validation_err_msg'] += $pErrorMsg;
		}
	}

	function PerformInstanceCustomChecks($pDocumentXml){
		if(!$this->m_autoSaveOn){
			/*
			 * Тук задължително подаваме параметъра да се ползва конекция иначе ако се направи
			 * нов dbcn обект, той ще прецака текущата транзакция
			 */
			$lCheckPerformer = new cdocument_custom_checks(array(
				'instance_id' => (int)$this->m_rootInstanceId,
				'document_id' => (int)$this->m_documentId,
				'mode' => (int) CUSTOM_CHECK_AFTER_SAVE_MODE,
				'xml' => $pDocumentXml,
				'use_existing_db_connection' => 1,
			));
			$lCheckPerformer->GetData();
			$lErrors = $lCheckPerformer->GetErrors();
			foreach ($lErrors as $lCurrentError){
				if((int)$lCurrentError['instance_id'] && (int)$lCurrentError['field_id']){
					$this->SetFieldValidationError($lCurrentError['instance_id'], $lCurrentError['field_id'], $lCurrentError['msg']);
				}else{
					$this->SetError($lCurrentError['msg']);
				}
			}
			if(count($lErrors)){
				$this->m_con->Execute('ROLLBACK TRANSACTION;');
				throw new Exception(getstr('pwt.customCheckError'));
			}
		}
	}

	/**
	 * Зареждаме field-овете
	 */
	protected function getFieldsData(){
		if(!$this->m_documentId || !$this->m_rootInstanceId){
			$this->SetError(getstr('pwt.save.requiredParametersAreMissing'));
			return;
		}

		$lFieldCheck = (int)$this->m_ExplicitFieldId ? " AND f.id = $this->m_ExplicitFieldId" : '';
		//Взимаме field-овете на главния обект, както и на подобектите, които не се показват в лявото меню
		//Надолу по дървото, ако има обект който се показва от ляво - не взимаме неговите field-ове и подобекти
		$lSql = '
			SELECT i.id as instance_id, fv.field_id, f.type, of.control_type as html_control_type,
				ft.value_column_name, of.allow_nulls::int, of.label, ft.is_array::int as is_array,
				hct.is_html::int as is_html
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances pi ON pi.document_id = i.document_id AND
				char_length(pi.pos) <= char_length(i.pos) AND substring(i.pos, 1, char_length(pi.pos)) = pi.pos
			LEFT JOIN pwt.document_object_instances pi1 ON pi1.document_id = i.document_id AND
				char_length(pi1.pos) <= char_length(i.pos) AND substring(i.pos, 1, char_length(pi1.pos)) = pi1.pos
				AND pi1.display_in_tree = true AND char_length(pi1.pos) > char_length(pi.pos)
			JOIN pwt.instance_field_values fv ON fv.instance_id = i.id AND fv.is_read_only = false
			JOIN pwt.object_fields of ON of.object_id = i.object_id AND of.field_id = fv.field_id AND of.dont_save_value = false
			JOIN pwt.fields f ON f.id = fv.field_id
			JOIN pwt.field_types ft ON ft.id = f.type
			JOIN pwt.html_control_types hct ON hct.id = of.control_type
			WHERE i.document_id = ' . (int)$this->m_documentId . ' AND
				pi.id = ' . (int)$this->m_rootInstanceId . ' AND (pi.id = i.id OR i.display_in_tree = false)
				AND pi1.id IS NULL ' . $lFieldCheck . '
				AND i.id IN (' . implode(',', $this->m_instanceIds) . ')
			ORDER BY i.id
		';
	//var_dump($lSql);
		if(!$this->m_con->Execute($lSql)){
			$this->SetError('pwt.save.couldNotLoadDocumentInstanceFields');
			return;
		}
		$this->m_con->MoveFirst();
		while(!$this->m_con->Eof()){
			if(!is_array($this->m_fields[$this->m_con->mRs['instance_id']])){
				$this->m_fields[$this->m_con->mRs['instance_id']] = array();
			}

			$lBaseValue = $_REQUEST[$this->m_con->mRs['instance_id'] . INSTANCE_FIELD_NAME_SEPARATOR . (int)$this->m_con->mRs['field_id']];
			if(!is_array($lBaseValue)){
					$lBaseValue = stripslashes($lBaseValue);
			}

// 			if((int)$this->m_con->mRs['field_id'] == 20){
// 				trigger_error('SQL VAL  ' . var_export($lBaseValue, 1));
// 				// 						exit;
// 			}
			$this->m_fields[$this->m_con->mRs['instance_id']][(int)$this->m_con->mRs['field_id']] = array(
	 			'type' => (int)$this->m_con->mRs['type'],
				'label' => $this->m_con->mRs['label'],
	 			'html_control_type' => (int)$this->m_con->mRs['html_control_type'],
				'allow_nulls' => (int)$this->m_con->mRs['allow_nulls'],
				'value_column_name' => $this->m_con->mRs['value_column_name'],
				'base_value' => $lBaseValue,
				'value_is_null' => false,
				'has_validation_error' => false,
				'validation_err_msg' => '',
				'is_array' => (int)$this->m_con->mRs['is_array'],
				'is_html' => (int)$this->m_con->mRs['is_html'],
				'comments' => array(),
				'previous_value' => null,
			);

			$this->m_con->MoveNext();
		}
// 		trigger_error('SQL FIELDS BEF  ' . var_export($this->m_fields, 1));
		$this->PerformAllFieldsChecks();
	}

	function PerformAllFieldsChecks(){
		foreach ($this->m_fields as $lInstanceId => $lFields) {
			foreach ($lFields as $lFieldId => $lCurrentFieldData) {
				$this->PerformSingleFieldChecks($lInstanceId, $lFieldId);
			}
		}
	}

	/**
	 * Връща информация относно валидацията
	 *
	 * @return масив със следния формат
	 * 		instance_id => array(
	 * 			field_id => array(
	 * 				has_validation_error => val,
	 * 				validation_err_msg => val,
	 * 			),
	 * 		)
	 * 		instance_id - id-то на instance-а, към който е полето
	 * 		field_id - id-то на полето
	 * 		has_validation_error => дали има грешка при валидацията на стойността на полето
	 * 		validation_err_msg => съобщението за грешка при валидацията на стойността на полето
	 */
	function GetFieldValidationInfo(){
		$lValidationInfo = array();
		foreach ($this->m_fields as $lInstanceId => $lFields) {
			foreach ($lFields as $lFieldId => $lCurrentFieldData) {
				if(!is_array($lValidationInfo[$lInstanceId])){
					$lValidationInfo[$lInstanceId] = array();
				}
				$lValidationInfo[$lInstanceId][$lFieldId] = array(
					'has_validation_error' => $lCurrentFieldData['has_validation_error'],
					'validation_err_msg' => $lCurrentFieldData['validation_err_msg'],
				);
			}
		}
		return $lValidationInfo;
	}


	function GetData(){
		global $user;
		if($this->m_dontGetData){
			return;
		}
		if($this->m_errorCount){
			return;
		}
// 		return;
		try {
// 			trigger_error('SQL FIELDS BEF2  ' . var_export($this->m_fields, 1));
			//Стартираме транзакцията
			$this->ExecuteSqlQuery('BEGIN TRANSACTION;');

			$this->FixCommentsPositions();
// 			trigger_error('SQL FIELDS BEF3  ' . var_export($this->m_fields, 1));

			$lValueFieldNames = array('value_int', 'value_str', 'value_date', 'value_arr_int', 'value_arr_str', 'value_arr_date');
			// Изпълняваме SQL Save Action-ите на всички field-ове
			$lInstances = $this->m_instanceIds;
			if(!in_array($this->m_rootInstanceId, $lInstances)){
				$lInstances[] = $this->m_rootInstanceId;
			}

			$this->ExecuteSqlQuery('SELECT * FROM pwt.spSaveDocumentRevision(' . (int)$this->m_documentId . ', ' . (int)$user->id . ') as revision_id');

			if(!$this->m_autoSaveOn){
				$lSql = 'SELECT * FROM spPerformInstancesBeforeSqlSaveActions(' . (int)$user->id . ', ARRAY[' . implode(',', $lInstances) . ']::int[]);';
				$this->ExecuteSqlQuery($lSql);
			}else{
				$lSql = 'SELECT * FROM spPerformInstancesBeforeSqlAutoSaveActions(' . (int)$user->id . ', ARRAY[' . implode(',', $lInstances) . ']::int[]);';
				$this->ExecuteSqlQuery($lSql);
			}

			//Ъпдейтваме полетата 1 по 1
			foreach ($this->m_fields as $lInstanceId => $lFields) {
				foreach ($lFields as $lFieldId => $lCurrentFieldData) {
					$lParsedFieldValue = $this->getParsedFieldValue($lInstanceId, $lFieldId);
					$lSql .= 'UPDATE pwt.instance_field_values SET
						' . $lCurrentFieldData['value_column_name'] . ' = ' . $lParsedFieldValue;

					//Слагаме стойността в правилната колона. Всички останали колони сетваме на NULL
					foreach ($lValueFieldNames as $lFieldName) {
						if($lFieldName == $lCurrentFieldData['value_column_name']){
							continue;
						}
						$lSql .= ', ' . $lFieldName . ' = NULL ';
					}

					$lSql .= '
						WHERE document_id = ' . $this->m_documentId . ' AND instance_id = ' . $lInstanceId .
						' AND field_id = ' . $lFieldId . ';';
// 					if($lFieldId == 20){
// 						trigger_error('SQL POST  ' . var_export($_REQUEST, 1));
// 						trigger_error('SQL Fields  ' . var_export($this->m_fields, 1));
// 						trigger_error('SQL: ' . $lSql, E_USER_NOTICE);
// 						var_dump($lSql);
// 						exit;
// 					}
				}
			}
			if($lSql){
				$this->ExecuteSqlQuery($lSql);
			}



			if(!$this->m_autoSaveOn){
				$lSql = 'SELECT * FROM spPerformInstancesSqlSaveActions(' . (int)$user->id . ', ARRAY[' . implode(',', $lInstances) . ']::int[]);';
				$this->ExecuteSqlQuery($lSql);
			}else{
				$lSql = 'SELECT * FROM spPerformInstancesSqlAutoSaveActions(' . (int)$user->id . ', ARRAY[' . implode(',', $lInstances) . ']::int[]);';
				$this->ExecuteSqlQuery($lSql);
			}

			if($this->m_inPopup && !$this->m_autoSaveOn){//Confirm the instance
				$lSql = 'SELECT * FROM pwt.spMarkInstanceAsConfirmed(' . (int)$this->m_rootInstanceId . ', ' . (int)$user->id . ');';
// 				var_dump($lSql);
				$this->ExecuteSqlQuery($lSql);
			}

			$this->ExecuteSqlQuery('UPDATE pwt.documents d
									SET name = (
										SELECT (CASE WHEN (trim(value_str) IS NULL) THEN \'Untitled\' ELSE value_str END)
											FROM pwt.instance_field_values
											WHERE document_id = ' . (int)$this->m_documentId . ' AND field_id = ' . (int)DOCUMENT_TITLE_FIELD_ID . '
										),
										lastmoddate = now()
									WHERE d.id = ' . (int)$this->m_documentId);

			/*
			 * Тук задължително подаваме параметъра да се ползва конекция иначе ако се направи
			* нов dbcn обект, той ще прецака текущата транзакция
			*/
			/*$lDocumentSerializer = new cdocument_serializer(array(
				'document_id' => (int)$this->m_documentId,
				'mode' => (int)SERIALIZE_INTERNAL_MODE,
				'use_existing_db_connection' => 1,
			));
			$lDocumentSerializer->GetData();
			$lDocumentXml = $lDocumentSerializer->getXml();

			$lSql = 'UPDATE pwt.documents SET doc_xml = \'' . q($lDocumentXml) . '\'::xml, generated_doc_html = 0 WHERE id = ' . (int)$this->m_documentId . ';';
// 			var_dump($lSql);
			$this->ExecuteSqlQuery($lSql);

			$this->PerformInstanceCustomChecks($lDocumentXml);
			*/
			//trigger_error('SQL: SELECT * FROM pwt."XmlIsDirty"(1, ' . (int)$this->m_documentId . ', ' . (int)$this->m_rootInstanceId . ');', E_USER_NOTICE);
// 			$this->ExecuteSqlQuery('SELECT * FROM pwt."XmlIsDirty"(1, ' . (int)$this->m_documentId . ', ' . (int)$this->m_rootInstanceId . ');');
// 			$this->ExecuteSqlQuery('ASDasdsad assa');
			if(!$this->m_autoSaveOn){
				$lSqlActivity = 'INSERT INTO pwt.activity (usr_id, document_id, action_type) VALUES(' . (int)$user->id . ', ' . (int)$this->m_documentId . ', ' . (int)ACTION_SAVE_DOCUMENT . ')';
				$this->ExecuteSqlQuery($lSqlActivity);
			}
			//Ако всичко е ОК - къмитваме
			$this->ExecuteSqlQuery('COMMIT TRANSACTION;');
			//Ако някой от instance-ите има прикачен action - изпълняваме го
			if(!$this->m_autoSaveOn){

				foreach ($this->m_instanceIds as $lInstanceId) {
					performInstanceSaveActions($lInstanceId);
				}
				//Ако главния инстанс няма field-ове - все пак трябва да му изпълним екшъните
				if(!array_key_exists($this->m_rootInstanceId, $this->m_fields)){
					performInstanceSaveActions($this->m_rootInstanceId);
				}
			}
		} catch (Exception $e) {
			/*
			 * При грешка - спираме всичко надолу. Класа автоматично ще ролбекне
			 * Затова няма нужда да зачистваме нещо.
			 */
			return;
		}


		$this->m_dontGetData = true;
	}

	/**
	 * Правим базови проверки за полето - дали е от правилния тип,
	 * или дали може да има празна стойност. Аналогично на метод DoChecks в kfor-а
	 * @param unknown_type $pInstanceId
	 * @param unknown_type $pFieldId
	 */
	protected function PerformSingleFieldChecks($pInstanceId, $pFieldId){
		$lCurValue =& $this->m_fields[$pInstanceId][$pFieldId]['base_value'];
		$lFieldType = $this->m_fields[$pInstanceId][$pFieldId]['type'];


		if ((is_null($lCurValue) || $lCurValue === '')){
			$this->m_fields[$pInstanceId][$pFieldId]['value_is_null'] = true;
		}

		if ((is_array($lCurValue) && !strlen(implode("", $lCurValue)))) {
			$this->m_fields[$pInstanceId][$pFieldId]['value_is_null'] = true;
		}



		if($this->m_fields[$pInstanceId][$pFieldId]['value_is_null'] && $this->m_fields[$pInstanceId][$pFieldId]['allow_nulls'])
			return;


		switch ($lFieldType) {
			case FIELD_CHECKBOX_MANY_TO_BIT_ONE_BOX_TYPE:
			case FIELD_INT_TYPE:
				if ($this->m_fields[$pInstanceId][$pFieldId]['value_is_null']) {
					$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_EMPTY_NUMERIC);
					return;
				}
				if (!is_numeric($lCurValue)) {
					$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_NAN);
					return;
				}

				$this->m_fields[$pInstanceId][$pFieldId]['base_value'] = (int)$lCurValue;
				return;
			case FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE:
			case FIELD_CHECKBOX_MANY_TO_BIT_TYPE:
				if ($this->m_fields[$pInstanceId][$pFieldId]['value_is_null']) {
					$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_EMPTY_FIELD);
					return;
				}
				if (is_array($lCurValue)) {
					foreach($lCurValue as $k => $v) {
						if (is_null($v)  || $v === '') {
							if ($this->m_fields[$pInstanceId][$pFieldId]['allow_nulls'])
								continue;
							else {
								$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_EMPTY_NUMERIC);
								return;
							}
						}
						if (!is_numeric($v)) {
							$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_NAN);
							return;
						}
						$this->m_fields[$pInstanceId][$pFieldId]['base_value'][$k] = (int)$this->m_fields[$pInstanceId][$pFieldId]['base_value'][$k];
					}
				}
				return;
			case FIELD_DATE_TYPE:
				if ($this->m_fields[$pInstanceId][$pFieldId]['value_is_null']) {
					$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_EMPTY_DATE);
					return;
				}
				$lStrError = manageckdate($lCurValue, DATE_TYPE_DATE);
				if ($lStrError) {
					$this->SetFieldValidationError($pInstanceId, $pFieldId, $lStrError);
					return;
				}
				return;
			case FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE:
				if ($this->m_fields[$pInstanceId][$pFieldId]['value_is_null']) {
					$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_EMPTY_FIELD);
					return;
				}
				if (is_array($lCurValue)) {
					foreach ($lCurValue as $k => $v) {
						$lStrError = manageckdate($lCurValue[$k], DATE_TYPE_DATE);
						if ($lStrError) {
							$this->SetFieldValidationError($pInstanceId, $pFieldId, $lStrError);
							return;
						}
					}
				}
				return;
			case FIELD_STRING_TYPE:
				if (is_null($lCurValue) || $lCurValue == '') {
					$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_EMPTY_STRING);
					return;
				}
				return;
			case FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE:
			case FIELD_CHECKBOX_MANY_TO_STRING_TYPE:
				if (is_null($lCurValue) || !is_array($lCurValue) || !strlen(implode("",$lCurValue))) {
					$this->SetFieldValidationError($pInstanceId, $pFieldId, ERR_EMPTY_STRING);
					return;
				}
				return;
		}
	}

	/**
	 * Връща ескейпната sql репрезентация на стойността даденото поле.
	 * Тази стойност е готова да се ползва директно в UPDATE заявка без да се ескейпва.
	 * В кфор-а аналогична функционалност има в метода SqlPrepare
	 * @param unknown_type $pInstanceId
	 * @param unknown_type $pFieldId
	 */
	protected function GetParsedFieldValue($pInstanceId, $pFieldId) {
		$lBaseValue = $this->m_fields[$pInstanceId][$pFieldId]['base_value'];
		$lFieldType = $this->m_fields[$pInstanceId][$pFieldId]['type'];
// 		var_dump($pFieldId, $lBaseValue);
// 		echo "\n";
		if($this->m_fields [$pInstanceId] [$pFieldId] ['value_is_null']){
			return 'NULL';
		}
		if (is_array( $lBaseValue )) {
			if ($lFieldType == FIELD_CHECKBOX_MANY_TO_STRING_TYPE) {
				$lRetStr = "'" . implode( DEF_SQLSTR_SEPARATOR, array_map( 'q', $lBaseValue ) ) . DEF_SQLSTR_SEPARATOR . "'";
			} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE) {
				$lRetStr = "array[" . implode( DEF_SQLSTR_SEPARATOR, array_map( 'arrstr_q', $lBaseValue ) ) . "]::varchar[]";
			} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE) {
				$lRetStr = "array[" . implode( DEF_SQLSTR_SEPARATOR, array_map( 'arrint_q', $lBaseValue ) ) . "]::int[]";
			} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE) {
				$lTmpArr = array();
				foreach ( $lBaseValue as $k => $v ) {
					$lTmpArr [] = manageckdate( $v, DATE_TYPE_DATE, 0 );
				}
				$lRetStr = "array[" . implode( DEF_SQLSTR_SEPARATOR, $lTmpArr ) . "]::date[]";
			} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_BIT_TYPE) {
				$lRetStr = array2bitint( $lBaseValue );
			} elseif ($lFieldType == FIELD_CHECKBOX_MANY_TO_BIT_ONE_BOX_TYPE) {
				if (is_null( $lBaseValue ) || $lBaseValue === '') {
					$lRetStr = 0;
				} else
					$lRetStr = $lBaseValue;
			}
		} else {
			if ($lFieldType == FIELD_INT_TYPE) {
				$lRetStr = (int)$lBaseValue;
			} else if ($lFieldType == FIELD_DATE_TYPE) {
				$lRetStr = "'" . q( manageckdate( $lBaseValue, DATE_TYPE_DATE, 0 ) ) . "'";
			} else if (in_array($lFieldType,
				array(FIELD_CHECKBOX_MANY_TO_SQL_DATE_ARRAY_TYPE, FIELD_CHECKBOX_MANY_TO_SQL_INT_ARRAY_TYPE, FIELD_CHECKBOX_MANY_TO_SQL_STRING_ARRAY_TYPE ))) {//Невалидна array стойност
				$lRetStr = 'NULL';
			} else {
				$lRetStr = "'" . q( $lBaseValue ) . "'";
			}
		}

		return $lRetStr;
	}

	/**
	 * Here we will fix the positions of the comments
	 * which/start end any of the modified fields
	 */
	protected function FixCommentsPositions(){
		if($this->m_errorCount){
			return;
		}
		/**
		 * First get a list of all the comments in the fields
		 */
		$lFieldCheck = (int)$this->m_ExplicitFieldId ? " AND ifs.field_id = $this->m_ExplicitFieldId  AND ife.field_id = $this->m_ExplicitFieldId " : '';
		$lSql = 'SELECT ms.id as start_comment_id, ifs.instance_id as start_instance_id, ifs.field_id as start_field_id, ms.start_offset,
					ifs.value_str as start_value_str, ifs.value_int as start_value_int, ifs.value_date as start_value_date,
				me.id as end_comment_id, ife.instance_id as end_instance_id, ife.field_id as end_field_id, me.end_offset,
					ife.value_str as end_value_str, ife.value_int as end_value_int, ife.value_date as end_value_date
			FROM pwt.document_object_instances i
			JOIN pwt.document_object_instances pi ON pi.document_id = i.document_id AND
				char_length(pi.pos) <= char_length(i.pos) AND substring(i.pos, 1, char_length(pi.pos)) = pi.pos
			LEFT JOIN pwt.msg ms ON ms.start_object_instances_id = i.id AND coalesce(ms.start_object_field_id, 0) <> 0 AND ms.start_offset >= 0
			LEFT JOIN pwt.msg me ON me.end_object_instances_id = i.id AND coalesce(me.end_object_field_id, 0) <> 0 AND me.end_offset >= 0
			LEFT JOIN pwt.instance_field_values ifs ON ifs.instance_id = i.id AND ifs.field_id = ms.start_object_field_id
			LEFT JOIN pwt.instance_field_values ife ON ife.instance_id = i.id AND ife.field_id = me.end_object_field_id
			WHERE i.document_id = ' . (int)$this->m_documentId . ' AND
				pi.id = ' . (int)$this->m_rootInstanceId . ' AND (pi.id = i.id OR i.display_in_tree = false)
				' . $lFieldCheck . '
				AND i.id IN (' . implode(',', $this->m_instanceIds) . ')
			ORDER BY i.id
		';
// 		var_dump($lSql);
		$this->ExecuteSqlQuery($lSql);
// 		trigger_error('SQL FIELDS BEFComm  ' . var_export($this->m_fields, 1));
		while(!$this->m_con->Eof()){
			$lRes = $this->m_con->mRs;
			$lCommentPrefixTypes = array(
				COMMENTS_FIX_TYPE_START_POS => 'start_',
				COMMENTS_FIX_TYPE_END_POS => 'end_',
			);

			foreach ($lCommentPrefixTypes as $lFixType => $lPrefix) {
				$lInstanceId = (int)$lRes[$lPrefix . 'instance_id'];
				$lFieldId = (int)$lRes[$lPrefix . 'field_id'];
				$lCommentId = (int)$lRes[$lPrefix . 'comment_id'];
// 				var_dump($lInstanceId, $lFieldId, $lCommentId);
				if($lInstanceId && $lFieldId && $lCommentId && array_key_exists($lInstanceId, $this->m_fields)&& array_key_exists($lFieldId, $this->m_fields[$lInstanceId])){
					$lFieldDataRef = &$this->m_fields[$lInstanceId][$lFieldId];
					$lFieldDataRef['previous_value'] = $lRes[$lPrefix . $lFieldDataRef['value_column_name']];
// 					var_dump($lFieldData['previous_value']);
					if(!array_key_exists($lCommentId, $lFieldDataRef['comments'])){
						$lFieldDataRef['comments'][$lCommentId] = array(
							'previous_' . $lPrefix . 'offset' => $lRes[$lPrefix . 'offset'],
							'position_fix_type' => $lFixType,
						);
						$lFieldDataRef['previous_value'] = parseFieldValue($lRes[$lPrefix . $lFieldDataRef['value_column_name']], $lFieldDataRef['type']);
					}else{
						$lFieldDataRef['comments'][$lCommentId]['previous_' . $lPrefix . 'offset'] = $lRes[$lPrefix . 'offset'];
						$lFieldDataRef['comments'][$lCommentId]['position_fix_type'] = $lFieldDataRef['comments'][$lCommentId]['position_fix_type'] | $lFixType;
					}
				}
			}

			$this->m_con->MoveNext();
		}
// 		trigger_error('SQL FIELDS Aftcom  ' . var_export($this->m_fields, 1));
		//Recalculate the positions and update the positions in the db
		foreach ($this->m_fields as $lInstanceId => $lInstanceFields) {
			foreach ($lInstanceFields as $lFieldId => $lFieldData) {
// 				trigger_error('SQL FIELDS Aftcom Before UPD ' . var_export($this->m_fields, 1));
				if(count($lFieldData['comments'])){
					$lInlineComments = array();
					if($lFieldData['is_html']){
						$lInlineComments = GetCommentNodesPosition($lFieldData['base_value']);
					}
// 					var_dump($lFieldData['base_value'], $lInlineComments);
					$lCommentsToRecalculateWithDiff = $lFieldData['comments'];
					foreach ($lInlineComments as $lCommentId => $lCommentData) {
						if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
							if($lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
								$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] -= COMMENTS_FIX_TYPE_START_POS;
							}
						}
						if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
							if($lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
								$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] -= COMMENTS_FIX_TYPE_END_POS;
							}
						}
						if(!$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type']){
							//This comment's position has already been commented by the comment node
							//do not recalculate it with diff
							unset($lCommentsToRecalculateWithDiff[$lCommentId]);
						}
					}

					$lModifiedComments = $lInlineComments;
// 					var_dump($lCommentsToRecalculateWithDiff);
					if(count($lCommentsToRecalculateWithDiff)){
						$lDiffModifiedComments = RecalculateCommentsPositions($lFieldData['previous_value'], $lFieldData['base_value'], $lCommentsToRecalculateWithDiff);
						foreach ($lDiffModifiedComments as $lCommentId => $lDiffCommentData){
							if(!array_key_exists($lCommentId, $lModifiedComments)){
								$lModifiedComments[$lCommentId] = $lDiffCommentData;
							}else{
								if($lDiffCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
									$lModifiedComments[$lCommentId]['new_start_offset'] = $lDiffCommentData['new_start_offset'];
									$lModifiedComments[$lCommentId]['position_fix_type'] = $lModifiedComments[$lCommentId]['position_fix_type'] | COMMENTS_FIX_TYPE_START_POS;
								}
								if($lDiffCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
									$lModifiedComments[$lCommentId]['new_end_offset'] = $lDiffCommentData['new_end_offset'];
									$lModifiedComments[$lCommentId]['position_fix_type'] = $lModifiedComments[$lCommentId]['position_fix_type'] | COMMENTS_FIX_TYPE_END_POS;
								}
							}
						}
// 						var_dump($lDiffModifiedComments);
// 						$lModifiedComments = $lModifiedComments + $lDiffModifiedComments;
					}
// 					var_dump('M', $lModifiedComments);
					foreach ($lModifiedComments as $lCommentId => $lCommentData) {
						$lSql = 'UPDATE pwt.msg SET ';

						if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
							$lSql .= 'start_offset = ' . (int)$lCommentData['new_start_offset'];
						}
						if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
							if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
								$lSql .= ', ';
							}
							$lSql .= 'end_offset = ' . (int)$lCommentData['new_end_offset'];
						}

						$lSql .= 'WHERE id = ' . (int)$lCommentId;
						$this->ExecuteSqlQuery($lSql);
					}

				}
				if($lFieldData['is_html']){
					//Remove the comment start and end nodes
					$this->m_fields[$lInstanceId][$lFieldId]['base_value'] = RemoveFieldCommentNodes($this->m_fields[$lInstanceId][$lFieldId]['base_value']);
				}
			}
		}

	}


	/**
	 * Изпълняваме sql заявка. Ще използваме член променливата за конекция към базата.
	 * Ако гръмне - ролбек-ваме.
	 * При грешка ще хвърляме exception, за да може да не правим след всяка команда проверка дали всичко е минало успешно,
	 * а наведнъж да обработваме грешка при коя да е заявка.
	 * @param unknown_type $lQuery - заявката, която ще се опитаме да изпълним.
	 */
	protected function ExecuteSqlQuery($lQuery){
		if(!$this->m_con->Execute($lQuery)){
			$this->setSqlError($this->m_con->GetLastError());
		}
	}

	/**
	 * Сигнализираме за sql грешка.
	 *
	 * За целта първо сетваме грешка. След това ролбекваме и хвърляме exception,
	 * за да може да го обработим на 1 място
	 * @param unknown_type $lErrorMsg - съобщението за грешката
	 */
	protected function SetSqlError($lErrorMsg){
		$this->SetError($lErrorMsg);
		$this->m_con->Execute('ROLLBACK TRANSACTION;');
		throw new Exception(getstr('pwt.sqlError'));
	}


	function SetError($pErrorMsg, $pErrorDelimiter = "\n"){
		if($this->m_errorCount){
			$this->m_errorMsg .= $pErrorDelimiter;
		}
		$this->m_errorCount++;
		$this->m_errorMsg .= $pErrorMsg;
	}
}