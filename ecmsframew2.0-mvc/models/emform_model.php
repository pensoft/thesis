<?php

/**
 * A base model for forms.
 * It should accept the form fields details and provide methods for executing action which interacts with the db,
 * @author peterg
 *
 */
class emForm_Model extends emBase_Model {
	// @formatter:off
	/**
	 * An array containing the metadata describing the fields for the form.
	 * Its format is the following
	 * field_name => field_metadata
	 * (e.g
	 * $this->m_fieldsMetadata = array(
	 * 		'title' => array(
	 * 			'CType' => 'text',
	 *			'VType' => 'string',
	 * 			'AllowNulls' => true,
	 * 			'DisplayName' => getstr('regprof.title'),
	 * 			'AddTags' => array(
	 * 				'class' => 'titleInput',
	 * 			),
	 * 		)
	 * );
	 *
	 * @var array
	 */
	// @formatter:on
	protected $m_fieldsMetadata;

	function __construct($pFieldsMetadata) {
		parent::__construct();
		$this->m_fieldsMetadata = $pFieldsMetadata;
		$this->InitSrcValues();
	}

	function GetFieldsMetadata() {
		return $this->m_fieldsMetadata;
	}

	/**
	 * This function should execute the specified action (i.e.
	 * execute the sql for this action)
	 *
	 * @param $pActionName string
	 * @param $pFieldValues array
	 */
	function ExecuteAction($pActionName, $pFieldValues) {
		if(! array_key_exists($pActionName, $this->m_fieldsMetadata)){
			return array(
				'err_cnt' => 1,
				'err_msgs' => array(
					array(
						'err_msg' => getstr(ERR_NO_SUCH_ACTION)
					)
				)
			);
		}
// 		var_dump($pFieldValues);
		$lActionSql = $this->m_fieldsMetadata[$pActionName]['SQL'];
		$lActionSql = preg_replace("/\{([a-z].*?)\}/e", "\$this->SqlPrepareField('\\1', \$pFieldValues[\\1])", $lActionSql);
// 		var_dump($lActionSql);

		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'field_values' => array()
		);
		if(! $this->m_con->Execute($lActionSql)){
			$lResult['err_cnt'] = 1;
			$lResult['err_msgs'][] = array(
				'err_msg' => $this->m_con->GetLastError()
			);
		}else{
			foreach($this->m_fieldsMetadata as $lFieldName => $lFieldData){
				if(array_key_exists($lFieldName, $this->m_con->mRs)){
					$lResult['field_values'][$lFieldName] = $this->SqlUnPrepareField($lFieldName, $this->m_con->mRs[$lFieldName]);
				}
			}
		}
// 		var_dump($lResult);
		return $lResult;
	}

	/**
	 * Escapes the passed field value so that it could be used in the action sql
	 *
	 * @param $pFieldName unknown_type
	 * @param $pFieldValue unknown_type
	 */
	function SqlPrepareField($pFieldName, $pFieldValue) {
		if(! array_key_exists($pFieldName, $this->m_fieldsMetadata)){
			return false;
		}
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lValueType = $lFieldMetadata["VType"];

		if(is_array($pFieldValue)){
			if($lFieldMetadata["TransType"] == MANY_TO_STRING){
				if($lValueType == "date"){
					$lTmpArr = array();
					foreach($pFieldValue as $k => $v){
						$lTmpArr[] = manageckdate($v, $lFieldMetadata['DateType'], 0);
					}
					$lRetStr = "'" . implode(DEF_SQLSTR_SEPARATOR, $lTmpArr) . DEF_SQLSTR_SEPARATOR . "'";
				}else{
					$lRetStr = "'" . implode(DEF_SQLSTR_SEPARATOR, array_map('q', $pFieldValue)) . DEF_SQLSTR_SEPARATOR . "'";
				}
			}elseif($lFieldMetadata["TransType"] == MANY_TO_SQL_ARRAY){
				if($lValueType == "date"){
					$lTmpArr = array();
					foreach($pFieldValue as $k => $v){
						$lTmpArr[] = manageckdate($v, $lFieldMetadata['DateType'], 0);
					}
					$lRetStr = "array[" . implode(DEF_SQLSTR_SEPARATOR, $lTmpArr) . "]";
				}else{
					$lRetStr = "array[" . implode(DEF_SQLSTR_SEPARATOR, array_map(((($lValueType == "string") || ($lValueType == "mlstring")) ? 'arrstr_q' : 'arrint_q'), $pFieldValue)) . "]";
				}
			}elseif($lFieldMetadata["TransType"] == MANY_TO_BIT){
				if($lValueType == "int"){
					$lRetStr = array2bitint($pFieldValue);
				}else{
					if($lFieldMetadata["TransType"] == MANY_TO_BIT_ONE_BOX)
						if(is_null($pFieldValue) || $pFieldValue === '')
							$lRetStr = 0;
						else
							$lRetStr = $pFieldValue;
					else{
						trigger_error("Cannot convert string or float values to bit value.", E_USER_ERROR);
						$lRetStr = 0;
					}
				}
			}
		}else{
			
			if($pFieldValue == '' || is_null($pFieldValue)){
				return ' NULL ';
			}
			if($lValueType == "int" || $lValueType == "float"){
				$lRetStr = $pFieldValue;
			}else if($lValueType == "date"){
				$lRetStr = "'" . q(manageckdate($pFieldValue, $lFieldMetadata['DateType'], 0)) . "'";
			}else{
				$lRetStr = "'" . q($pFieldValue) . "'";
			}
		}
		return $lRetStr;
	}

	/**
	 * Parses the sql value of the field and returns a valid php value(the form controller works with php values)
	 * @param $pFieldName unknown_type
	 * @param $pFieldValue unknown_type
	 */
	function SqlUnPrepareField($pFieldName, $pFieldValue) {
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lFieldControlType = $lFieldMetadata['CType'];
		$lFieldValueType = $lFieldMetadata['VType'];
		if($lFieldControlType == 'mselect' || $lFieldControlType == 'checkbox' || $lFieldValueType == 'mlstring' || $lFieldValueType == 'mlint'){
			switch ($lFieldMetadata["TransType"]) {
				case MANY_TO_STRING :
					return explode(DEF_SQLSTR_SEPARATOR, $pFieldValue);
				case MANY_TO_BIT :
					// checkbox fix (default value)
					if($lFieldControlType == 'checkbox' && $lFieldMetadata['DefValue'] && is_null($pFieldValue)) {
						return int2bitarray($lFieldMetadata['DefValue']);
					} else {
						return int2bitarray($pFieldValue);
					}
				case MANY_TO_BIT_ONE_BOX :
					return $pFieldValue;
				case MANY_TO_SQL_ARRAY :
					return pg_unescape_array($pFieldValue);
			}
		}else{
			if($lFieldValueType == 'date'){
				return formatformdate($pFieldValue);
			}else{
				return $pFieldValue;
			}
		}
	}

	/**
	 * Returns an array containing the names of the necessary fields for the execution of the specified action
	 * @param unknown_type $pActionName
	 */
	function GetActionNecessaryFields($pActionName){
		$lFieldMetadata = $this->m_fieldsMetadata[$pActionName];
		$lSql = $lFieldMetadata['SQL'];
		if(preg_match_all('/\{([a-z].*?)\}/i', $lSql, $lMatches)){
			//If there are any matches - return them
			return $lMatches[1];
		}else{
			return array();
		}
	}
	
	function InitSrcValues() {
		foreach($this->m_fieldsMetadata as $key => $val) {
			if(array_key_exists('SrcValues', $val)) {
				if(!is_array($val['SrcValues'])) {
					$this->m_con->Execute($val['SrcValues']);
					$this->m_con->MoveFirst();
					$lSrcValues = array();

					while(!$this->m_con->Eof()) {
						$lSrcValues[] = $this->m_con->mRs;
						$this->m_con->MoveNext();
					}
					
					$this->m_fieldsMetadata[$key]['SrcValues'] = $lSrcValues;
				}
			}
		}
	}

}

?>