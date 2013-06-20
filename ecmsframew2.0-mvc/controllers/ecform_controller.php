<?php

class ecForm_Controller extends ecBase_Controller {
	/**
	 * The form model
	 *
	 * @var emForm_Model
	 */
	protected $m_formModel;
	/**
	 * The view object for the form
	 *
	 * @var evForm_View
	 */
	protected $m_formView;

	/**
	 * This is mainly for the view
	 * the name under which to look for templates in the page view
	 *
	 * @var unknown_type
	 */
	protected $m_nameInViewObject;

	// @formatter:off

	/**

	 * An array containing the data after the model method

	 * for the current action has been executed.

	 * The format should be the following:

	 * 		err_cnt => Whether there were errors or not

	 * 		err_msgs => an array containing the error messages (its format should be
	 * 			field_name => err_msg;	 if nor field_name is specified - global error)

	 * 		field_values => an array containing the new field values for the form (the format should be field_name => field_value)

	 * @var array

	 */

	// @formatter:on
	var $m_modelData;

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
	var $m_fieldsMetadata;

	/**
	 * An array containing the values of all the fields in the form
	 *
	 * @var array
	 */
	var $m_fieldsValues;

	var $m_currentAction;

	/**
	 * The name of the form (e.g.
	 * the name attribute in the html)
	 *
	 * @var unknown_type
	 */
	var $m_formName;

	/**
	 * Number of errors in the form
	 *
	 * @var int
	 */
	var $m_errCnt;
	/**
	 * An array which contains the errors for each field
	 *
	 * The format of the array is
	 * field_name => field_errors
	 * (The errors for each field are an array of error msgs).
	 *
	 * @var array
	 */
	var $m_fieldErrors;
	/**
	 * An array containing the errors for the whole form
	 * (e.g.
	 * when we have an error from the controller which is not specific for any
	 * field)
	 *
	 * @var unknown_type
	 */
	var $m_globalErrors;

	/**
	 * The form method (POST/GET)
	 *
	 * @var string
	 */
	var $m_formMethod;

	/**
	 * Whether the main action of the form is executed.
	 * It is changed in the GetData method
	 *
	 * @var boolean
	 */
	var $m_actionIsExecuted;

	/**
	 * Whether the form is in debug mode or not
	 * In debug mode some useful debugging data is displayed
	 *
	 * @var boolean
	 */
	var $m_debug;

	/**
	 * Whether to use captcha (i.e.
	 * to check the captcha before executing the form action)
	 *
	 * @var boolean
	 */
	var $m_useCaptcha;

	var $m_addBackUrl;
	
	/**
	 * Turn on/off ajax validation (0 - Off, 1 - On)
	 * 
	 * @var int 
	 */
	var $m_jsValidation;

	/**
	 * which field to perform the validation
	 * 
	 * @var string
	 */
	var $m_checkField;
	
	/**
	 * contains the form fields template key
	 * 
	 * @var string
	 */
	var $m_formFieldsTemplName;
	 
	/**
	 * A copy of the data passed to the controller
	 * This data should be passed to the form view
	 * @var array
	 */
	var $m_pubdata;

	function __construct($pData) {
		parent::__construct($pData);
		$this->m_pubdata = $pData;
		$this->m_formName = $pData['form_name'];
		$this->m_formMethod = strtolower($pData['form_method']);
		$this->m_debug = (int) $pData['debug'];
		$this->m_addBackUrl = $pData['add_back_url'];
		$this->m_useCaptcha = $pData['use_captcha'];
		$this->m_nameInViewObject = $pData['name_in_viewobject'];
		$this->m_jsValidation = (array_key_exists('js_validation', $pData) ? (int)$pData['js_validation'] : 0);
		$this->m_checkField = $_REQUEST['check_field'];
		
		if(is_array($pData['fields_metadata'])) {
			$this->m_fieldsMetadata = $pData['fields_metadata'];
		} else {
			$this->m_fieldsMetadata = $this->getTemplate($pData['fields_metadata']);
			$this->m_formFieldsTemplName = $pData['fields_metadata'];
		}
		
		$this->m_actionIsExecuted = false;

		$this->m_errCnt = 0;
		$this->m_fieldErrors = array();
		$this->m_globalErrors = array();

		if(! $this->m_formName){
			$gFormsCount = getglobalformnumber();
			$this->m_formName = "def" . $gFormsCount;
		}

		if(! in_array($this->m_formMethod, array(
			'get',
			'post'
		))){
			$this->m_formMethod = FORM_DEFAULT_METHOD;
		}

		$this->m_fieldsValues = array();
		$this->FillFieldsValuesFromRequest();

		// Here we explicitly add some fields like backurl and form name
		$this->m_fieldsMetadata['form_name'] = array(
			'CType' => 'hidden',
			'VType' => 'string',
			'DefValue' => $this->m_formName
		);
		$this->m_fieldsMetadata['backurl'] = array(
			'CType' => 'hidden',
			'VType' => 'string',
			'DefValue' => $this->m_formName
		);
		$this->m_fieldsMetadata['selfurl'] = array(
			'CType' => 'hidden',
			'VType' => 'string',
			'DefValue' => $this->m_formName
		);

		$this->Setselfurl();
		$this->m_fieldsValues['form_name'] = $this->m_formName;

		$this->InitCurrentAction();

		if(! is_array($this->m_fieldsMetadata[$this->m_currentAction])){
			if($this->m_currentAction == DEFAULT_FORM_ACTION){
				// We create the default action ourselves!!!
				$this->m_fieldsMetadata[$this->m_currentAction] = array(
					"CType" => "action",
					"ActionMask" => ACTION_SHOW,
					"Hidden" => true
				);
			} elseif($this->m_currentAction == DEFAULT_FORM_CHECK_ACTION){
				// We create the default action ourselves!!!
				$this->m_fieldsMetadata[$this->m_currentAction] = array(
					"CType" => "action",
					"SQL" => '{' . $this->m_checkField . '}',
					"ActionMask" => ACTION_CHECK | ACTION_CCHECK,
					"Hidden" => true,
				);
			} else{
				// We search for the default action in the labels of the action
				// fields
				foreach($this->m_fieldsMetadata as $k => $v){
					if($this->m_currentAction == $v["DisplayName"] && $v["CType"] == "action"){
						$this->m_currentAction = $k;
						break;
					}
				}
				if(! is_array($this->m_fieldsMetadata[$this->m_currentAction])){
					$this->SetError($this->m_currentAction . " is not valid action.");
				}
			}
		}else{
			if($this->m_fieldsMetadata[$this->m_currentAction]["CType"] != "action"){
				$this->SetError($this->m_currentAction . " is not valid action.");
			}
		}
	}

	function InitCurrentAction() {
		if(! $_REQUEST['form_name'] || $_REQUEST['form_name'] == $this->m_formName){
			// We process the action only if this was the action for exactly
			// this form
			$this->m_currentAction = $_REQUEST['tAction'];
		}

		if(!$this->m_currentAction && $this->m_checkField) {
			$this->m_currentAction = DEFAULT_FORM_CHECK_ACTION;
		}
		
		if(! $this->m_currentAction){
			$this->m_currentAction = DEFAULT_FORM_ACTION;
		}
	}

	/**
	 * Returns the name of the current action
	 * @return string
	 */
	function GetCurrentAction(){
		return $this->m_currentAction;
	}

	/**
	 * Set current action
	 * @return string
	 */
	function SetCurrentAction($pAction){
		$this->m_currentAction = $pAction;
	}


	function Setselfurl() {
		global $forwardurl, $selfurl;
		if($_POST["selfurl"])
			$selfurl = $_POST["selfurl"];
		else if($_REQUEST["selfurl"])
			$selfurl = $_REQUEST["selfurl"];

		if($this->lFormMethod == 'post'){
			$forwardurl = ClearParaminURL($forwardurl, "tAction");

			$forwardurl = AddParamtoURL($forwardurl, 'tAction=' . $this->lCurAction);
			foreach($this->m_fieldsValues as $kk => $vv){
				if($vv['PK']){
					$forwardurl = ClearParaminURL($forwardurl, $kk);
					$forwardurl = AddParamtoURL($forwardurl, $kk . '=' . $this->lFieldArr[$kk]['CurValue']);
				}
			}
			// echo "<p>POST - forlardurl - $forwardurl<P>";
		}
		if(! CheckSameUrl($forwardurl, $selfurl) || ($selfurl == '')){
			if(! ($this->m_fieldsMetadata[$this->m_currentAction]['ActionMask'] & ACTION_VIEW))
				$selfurl = '';
			else
				$selfurl = $forwardurl;

		}
		$forwardurl = ClearParaminURL($forwardurl, "selfurl");
		$selfurl = ClearParaminURL($selfurl, "backurl");
		$selfurl = ClearParaminURL($selfurl, "selfurl");
		$forwardurl = AddParamtoURL($forwardurl, 'selfurl=' . urlencode($selfurl));
		$this->m_fieldsValues['selfurl'] = $selfurl;

		if(! $this->m_fieldsValues['backurl'])
			$this->m_fieldsValues['backurl'] = $forwardurl;
	}

	/**
	 * If in debug mode logs the specified msg (outputs it with echo)
	 *
	 * @param $pMsg unknown_type
	 */
	protected function LogDebugMsg($pMsg) {
		if(! $this->m_debug){
			return;
		}
		echo $pMsg;
	}

	function AddBackUrl() {
		return $this->m_addBackUrl;
	}

	/**
	 * Returns the form name
	 */
	function GetFormName() {
		return $this->m_formName;
	}

	/**
	 * Returns the form method
	 *
	 * @return string
	 */
	function GetFormMethod() {
		return $this->m_formMethod;
	}

	/**
	 * Returns whether the form uses captcha
	 */
	function UseCaptcha() {
		return $this->m_useCaptcha;
	}

	/**
	 * Returns the fields metadata
	 *
	 * @return multitype:
	 */
	function GetFieldsMetadata() {
		return $this->m_fieldsMetadata;
	}

	/**
	* Sets the metadata of the fields (Usually after the model has filled the src values)
	*/
	function SetFieldsMetadata($pFieldsMetadata) {
		return $this->m_fieldsMetadata = $pFieldsMetadata;
	}

	/**
	 * Sets the form model.
	 *
	 * @param $pModel unknown_type
	 */
	function SetFormModel($pModel) {
		$this->m_formModel = $pModel;
	}

	/**
	 * Creates the form view (if it hasn't been created before
	 * and returns a reference to it
	 *
	 * @param $pModel unknown_type
	 */
	function CreateFormView() {
		if(! $this->m_formView){

			$lViewMetadata = $this->m_pubdata;

			$lViewMetadata = array_merge($lViewMetadata, array(
				'name_in_viewobject' => $this->m_nameInViewObject,
				'fields_metadata' => $this->m_fieldsMetadata,
				'err_cnt' => $this->m_errCnt,
				'global_errors' => $this->m_globalErrors,
				'fields_errors' => $this->m_fieldErrors,
				'fields_values' => $this->m_fieldsValues,
				'form_name' => $this->m_formName,
				'form_method' => $this->m_formMethod,
				'use_captcha' => $this->m_useCaptcha,
				'add_back_url' => $this->AddBackUrl(),
				'js_validation' => (int)$this->m_jsValidation,
				'check_field' => $this->m_checkField,
				'fields_templ_name' => $this->m_formFieldsTemplName,
			));

			$this->m_formView = new evForm_View($lViewMetadata);
		}
		return $this->m_formView;
	}

	/**
	 * Returns the number of errors in the form
	 */
	function GetErrorCount() {
		return $this->m_errCnt;
	}

	/**
	 * Returns an array with the errors for the specified field.
	 * (The array consists of error msgs)
	 */
	function GetFieldErrorsArr($pFieldName) {
		$lResult = $this->m_fieldErrors[$pFieldName];
		if(! is_array($lResult)){
			$lResult = array();
		}
		return $lResult;
	}

	/**
	 * Sets a form error.
	 * If a field name is passed the error is treated as a field error.
	 * Otherwise the error is treated as global error
	 *
	 * @param $pErrMsg unknown_type
	 * @param $pFieldName unknown_type
	 */
	function SetError($pErrMsg, $pFieldName = '') {
		$pErrMsg = getstr($pErrMsg);
		$this->m_errCnt ++;
		if($pFieldName){
			if(! is_array($this->m_fieldErrors[$pFieldName])){
				$this->m_fieldErrors[$pFieldName] = array();
			}
			$this->m_fieldErrors[$pFieldName][] = $pErrMsg;
		}else{
			$this->m_globalErrors[] = $pErrMsg;
		}
	}
	
	function SetPubdataValue($pKey, $pValue){
		$this->m_pubdata[$pKey] = $pValue;
	}

	/**
	 * Returns the metadata for the specific field
	 * @param unknown_type $pFieldName
	 * @return multitype:
	 */
	function GetFieldMetadata($pFieldName){
		return $this->m_fieldsMetadata[$pFieldName];
	}

	/**
	 * Returns the value of the specified field
	 *
	 * @param $pFieldName unknown_type
	 */
	function GetFieldValue($pFieldName) {
		return $this->m_fieldsValues[$pFieldName];
	}

	/**
	 * Set value of the specified field
	 *
	 * @param $pFieldName  unknown_type
	 * @param $pFieldValue unknown_type
	 */
	function SetFieldValue($pFieldName, $pFieldValue) {
		return $this->m_fieldsValues[$pFieldName] = $pFieldValue;
	}
	
	/**
	 * Set property of the specified field
	 *
	 * @param $pFieldName      unknown_type
	 * @param $pFieldPropName  unknown_type
	 * @param $pFieldPropValue unknown_type
	 */
	function SetFieldProp($pFieldName, $pFieldPropName, $pFieldPropValue) {
		return $this->m_fieldsMetadata[$pFieldName][$pFieldPropName] = $pFieldPropValue;
	}

	function PerformAction() {
		
		if(! $this->m_actionIsExecuted){
			$this->m_actionIsExecuted = true;
			$this->CheckActionMask();
			if($this->m_errCnt > 0){
				return;
			}

			$lActMask = $this->m_fieldsMetadata[$this->m_currentAction]["ActionMask"];

			// Performs basic checks on the fields for errors
			if($lActMask & ACTION_CHECK){
				$this->PerformFieldChecks();
			}
			if($this->m_errCnt > 0){
				return;
			}

			// Performs custom checks on the fields for errors
			if($lActMask & ACTION_CCHECK){
				$this->PerformFieldCustomChecks();
			}

			if($this->m_errCnt > 0){
				return;
			}

			// If there are no errors - contact the controller to perform the
			// action (e.g. Save a new story to the db through the model)
			if($lActMask & ACTION_EXEC){
				$this->ExecControllerAction();
			}

			// Fetch the data from the controller (the field values)
			if(($lActMask & ACTION_FETCH) && $this->m_errCnt == 0){
				$this->FillFieldsValuesFromModelData();
			}

			// Fetch the data from the controller (the field values)
			if(($lActMask & ACTION_REDIRECT) && $this->m_errCnt == 0){
				$this->Redirect();
			}
		}
	}

	/**
	 * Execs the controller functions which is associated with the current
	 * action
	 */
	function ExecControllerAction() {

		$lCurrentActionMetadata = $this->m_fieldsMetadata[$this->m_currentAction];
		/*
		 * The callback function should accept 1 parameter - an array with the
		 * format field_name => field_value We will pass as an argument all the
		 * fields in needed for the current action of the metadata of the
		 * current action
		 */
		$lFieldValues = array();
		$lNeededFields = $this->m_formModel->GetActionNecessaryFields($this->m_currentAction);
		if(is_array($lNeededFields)){
			foreach($lNeededFields as $lFieldName){
				$lCurValue = $this->GetFieldValue($lFieldName);
				// var_dump($lCurValue, $lFieldName);
				// echo "<br/>\n";
				// If the current value is null and there is default value - set
				// the field value to the default value
				if(is_null($lCurValue) && isset($this->m_fieldsMetadata[$lFieldName]['DefValue'])){
					$this->m_fieldsValues[$lFieldName] = $this->m_fieldsMetadata[$lFieldName]['DefValue'];
					$lCurValue = $this->GetFieldValue($lFieldName);
				}
				$lFieldValues[$lFieldName] = $lCurValue;
			}
		}
		$this->LogDebugMsg('Executing action ' . $this->m_currentAction . ' model action with ' . var_export($lFieldValues, 1));
		$lCallResult = $this->m_formModel->ExecuteAction($this->m_currentAction, $lFieldValues);

		/**
		 * The model method should return a result array in the following
		 * format
		 * err_cnt => Whether there were errors or not
		 * err_msgs => an array containing the error messages (
		 * it should consist of arrays with the following format
		 * field_name => The name of the field in which there was an error (If
		 * no field name is specified - a global error is assumed)
		 * err_msg => The error msg
		 * ),
		 * field_values => an array containing the new field values for the form
		 * (the format should be field_name => field_value)
		 */
		// @formatter->on
		$this->m_modelData = $lCallResult;
		$this->FillErrorsFromModelData();
	}

	/**
	 * Checks whether the series of actions which should be performed by the
	 * current action
	 * are logically correct (e.g.
	 * you cannot display the form and redirect in the same time)
	 *
	 * @return boolean
	 */
	function CheckActionMask() {
		$lMask = $this->lFieldArr[$this->m_currentAction]["ActionMask"];

		if(($lMask & ACTION_FETCH) && ! ($lMask & ACTION_EXEC)){
			$this->SetError("Cannot ACTION_FETCH if there is no ACTION_EXEC for action $this->m_currentAction", E_USER_ERROR);
			return false;
		}

		if(($lMask & ACTION_EXEC) && ! ($lMask & ACTION_CHECK)){
			$this->SetError("Cannot ACTION_EXEC if there is no ACTION_CHECK for action $this->m_currentAction", E_USER_ERROR);
			return false;
		}

		if(($lMask & ACTION_SHOW) && ($lMask & ACTION_REDIRECT)){
			$this->SetError("Cannot use both ACTION_SHOW and ACTION_REDIRECT for action $this->m_currentAction", E_USER_ERROR);
			return false;
		}

		if(($lMask & ACTION_CCHECK) && ! ($lMask & ACTION_CHECK)){
			$this->SetError("Cannot use ACTION_CCHECK without ACTION_CHECK for action $this->m_currentAction", E_USER_ERROR);
			return false;
		}
	}

	/**
	 * Perform the checks for the specified field
	 * If no field is given the fields for the current action will be checked
	 *
	 * @param $pFieldName unknown_type
	 */
	protected function PerformFieldChecks($pFieldName = '') {
		$lFieldsToCheck = array();
		if($pFieldName == ''){
			$lNeededFields = $this->m_formModel->GetActionNecessaryFields($this->m_currentAction);
			foreach($lNeededFields as $lFieldName){
				$lFieldsToCheck[] = $lFieldName;
			}
		}else{
			$lFieldsToCheck[] = $pFieldName;
		}

		foreach($lFieldsToCheck as $lFieldName){
			$lFieldMetadata = $this->m_fieldsMetadata[$lFieldName];
			$lValueType = $lFieldMetadata['VType'];
			$lControlType = $lFieldMetadata['CType'];

			$lIsArray = false;
			if($lValueType == 'mlint' || $lValueType == 'mlstring' || $lControlType == 'checkbox'){
				$lIsArray = true;
			}
			// var_dump($this->m_fieldsValues[$lFieldName], $lFieldName,
			// $lValueType, $lFieldMetadata['AllowNulls']);
			// echo "\n<br />";
			$lCheckResult = $this->CheckValueType($this->m_fieldsValues[$lFieldName], $lValueType, $lIsArray, $lFieldMetadata['AllowNulls'], $lFieldMetadata['DateType']);
			// var_dump($lCheckResult);
			// echo "\n<br />";
			if($lCheckResult['err_cnt']){
				foreach($lCheckResult['err_msgs'] as $lCurrentErrorMsg){
					$this->SetError($lCurrentErrorMsg['err_msg'], $lFieldName);
				}
			}else{
				$this->m_fieldsValues[$lFieldName] = $lCheckResult['value'];
			}
		}

		/**
		 * We check the captcha code if the form uses one
		 * and the current action requires captcha check
		 */
		if($pFieldName == '' && $this->m_useCaptcha && $this->m_fieldsMetadata[$this->m_currentAction]['CheckCaptcha']){
			// If the value is correct - remove this value from future captcha
			// codes
			if(in_array(strtolower($this->GetValueFromRequestWithoutChecks('captcha')), $_SESSION['frmcapt'])){
				foreach($_SESSION['frmcapt'] as $captkey => $captval){
					if($captval == strtolower($this->GetValueFromRequestWithoutChecks('captcha')))
						unset($_SESSION['frmcapt'][$captkey]);
				}
			}else{
				// Report error
				$this->SetError(ERR_CAPTCHA_WRONG_CODE);
			}
		}

		// var_dump($this->m_fieldErrors);
		// var_dump($this->m_globalErrors);

	}

	/**
	 * Perform the custom checks for the specified field
	 * If no field is given the fields for the current action will be checked
	 *
	 * @param $pFieldName unknown_type
	 */
	protected function PerformFieldCustomChecks($pFieldName = '') {
		$lFieldsToCheck = array();
		if($pFieldName == ''){
			$lNeededFields = $this->m_formModel->GetActionNecessaryFields($this->m_currentAction);
			foreach($lNeededFields as $lFieldName){
				$lFieldsToCheck[] = $lFieldName;
			}
		}else{
			$lFieldsToCheck[] = $pFieldName;
		}

		foreach($lFieldsToCheck as $lFieldName){
			$lFieldMetadata = $this->m_fieldsMetadata[$lFieldName];

			if(is_array($lFieldMetadata["Checks"])){
				foreach($lFieldMetadata["Checks"] as $lCkN => $lCurrentCheck){
					$lEvalStr = 'return ' . preg_replace("/\{([a-z].*?)\}/e", "\$this->PrepareFieldValueForEval('\\1')", $lCurrentCheck) . ';';

					// ~ var_dump($lEvalStr);
					$lRes = eval($lEvalStr);
					if($lRes){
						$this->SetError($lRes, $lFieldName);
						break;
					}
				}
			}
		}
	}

	/**
	 * Fills the values of the fields from the $_POST/GET (depending on the form
	 * method)
	 */
	protected function FillFieldsValuesFromRequest() {
		foreach($this->m_fieldsMetadata as $lFieldName => $lFieldInfo){
			$lFieldValue = $this->GetValueFromRequest($lFieldName, $this->m_formMethod);
			// var_dump('REQ', $lFieldName, $lFieldValue['value'],
			// $_REQUEST[$lFieldName]);
			// echo "\n <br/>";
			$this->m_fieldsValues[$lFieldName] = $lFieldValue['value'];
		}
	}

	/**
	 * Fills the fields values from the controller data which has been returned
	 * after the
	 * controller has executed the current action.
	 *
	 * @param $pControllerData array
	 */
	protected function FillFieldsValuesFromModelData() {
		foreach ($this->m_fieldsMetadata as $lFieldName => $lFieldInfo) {
			if (array_key_exists($lFieldName, $this->m_modelData['field_values'])) {
				
				$lFieldValue = $this->m_modelData['field_values'][$lFieldName];
				switch ($lFieldInfo['VType']) {
					case "int" :
					case "float" :
					case "string" :
					case "mlstring" :
					case "mlint" :
					case "date" :
						$this->m_fieldsValues[$lFieldName] = $lFieldValue;
						break;
					case "file" :
						if($lFieldValue){
							$this->m_fieldsValues[$lFieldName]['FileUp'] = true;
							$this->m_fieldsValues[$lFieldName]['FileName'] = $lFieldValue['name'];
							$this->m_fieldsValues[$lFieldName]['FileType'] = $lFieldValue['type'];
							$this->m_fieldsValues[$lFieldName]['FileSize'] = $lFieldValue['size'];
							$this->m_fieldsValues[$lFieldName]['FileTmpName'] = $lFieldValue['tmp_name'];
							$this->m_fieldsValues[$lFieldName]['FileError'] = $lFieldValue['error'];
						}
						break;
					default :
						break;
				}
			}
		}
	}
	

	/**
	 * Checks if there have been errors in the
	 * execution of the model method of the current action
	 * and if any sets them to the form
	 */
	protected function FillErrorsFromModelData() {
		if(! (int) $this->m_modelData['err_cnt']){
			return;
		}
		if(! is_array($this->m_modelData['err_msgs']) || ! count($this->m_modelData['err_msgs'])){
			$this->SetError(ERR_CONTROLLER_STATES_THERE_ARE_ERRORS_BUT_DOESNT_PROVIDE_THEM);
			return;
		}
		foreach($this->m_modelData['err_msgs'] as $lError){
			$this->SetError($lError['err_msg'], $lError['field_name']);
		}
	}

	/**
	 * Returns the exported value of the specified field.
	 * This value may after that be used in eval expressions
	 *
	 * @param $pFieldName string
	 */
	protected function PrepareFieldValueForEval($pFieldName) {
		return var_export($this->m_fieldsValues[$pFieldName], true);
	}

	function GetRedirUrl($pAction) {
		global $backurl;
		$lFieldMetadata = $this->m_fieldsMetadata[$pAction];
		$lRedirUrl = $lFieldMetadata['RedirUrl'];

		if($lRedirUrl == "{#selfurl}"){
			if($lFieldMetadata['ActionMask'] & ACTION_REDIRVIEW){
				$lViewActionExists = 0;
				foreach($this->m_fieldsMetadata as $k => $v){
					if(($v['CType'] == "action") && ($v['ActionMask'] & ACTION_VIEW)){
						$this->m_fieldsValues['selfurl'] = getenv("SCRIPT_NAME") . "?tAction=" . $k;

						foreach($this->m_fieldsMetadata as $kk => $vv){
							if($vv['PK']){
								$this->m_fieldsValues['selfurl'] .= "&" . $kk . "=" . $this->m_fieldsValues[$kk];
							}
						}

						// $this->lFieldArr['backurl']['CurValue'] .=
						// "&@backurl=" .
						// urlencode($this->lFieldArr['backurl']['CurValue']);
						$this->m_fieldsValues['selfurl'] = AddParamtoURL($this->m_fieldsValues['selfurl'], 'backurl=' . urlencode($backurl));

						if(($pAction == $this->lCurAction) && $this->m_fieldsMetadata[$pAction]["ErrorString"] && ($this->m_fieldsMetadata[$pAction]["ActionMask"] & ACTION_REDIRERROR)){
							$this->m_fieldsValues['selfurl'] = AddParamtoURL($this->lFieldArr['selfurl']['CurValue'], 'frameerrstr=' . urlencode($this->lFieldArr[$pAction]["ErrorString"]));
						}

						$lViewActionExists = 1;
						break;
					}
				}
				if(! $lViewActionExists){
					$lRedirUrl = "";
				}
			}else{
				if(! $this->m_fieldsValues['selfurl']){
					$lRedirUrl = "";
				}else{
					if(preg_match("/tAction=([^\&\?]+)/", $this->m_fieldsValues['selfurl'], $match)){
						if($this->m_fieldsMetadata[$match[1]]['ActionMask'] & ACTION_VIEW){
							$this->m_fieldsValues['selfurl'] = AddParamtoURL($this->m_fieldsValues['selfurl'], 'backurl=' . urlencode($backurl));
						}else{
							$lRedirUrl = "";
						}
					}else{
						if(! ($this->m_fieldsMetadata['new']['ActionMask'] & ACTION_VIEW))
							$lRedirUrl = "";
						else
							$this->m_fieldsValues['selfurl'] = AddParamtoURL($this->m_fieldsValues['selfurl'], 'backurl=' . urlencode($backurl));
					}
				}
			}
		}

		if(! $lRedirUrl || ($lRedirUrl == "{#backurl}")){
			$lRedirUrl = $backurl;
		}else{ // Fill the field values in the url (e.g. if you have to redirect
		       // to a newly created story you need its id)
			$lRedirUrl = preg_replace("/\{([^\#].*?)\}/e", "urlencode(\$this->GetFieldValue('\\1'))", $lRedirUrl);
			$lRedirUrl = preg_replace("/\{\#(.*?)\}/e", "\$this->GetFieldValue('\\1')", $lRedirUrl);
		}

		return $lRedirUrl;
	}

	/**
	 * Redirects to the specified url for the current action
	 */
	function Redirect() {
		$lRedirUrl = $this->GetRedirUrl($this->m_currentAction);

		$this->LogDebugMsg('Redirecting to: <a href="' . $lRedirUrl . '">' . $lRedirUrl . '</a>');
		if($this->m_debug){
			exit();
		}

		$lRedirUrl = "Location: " . $lRedirUrl;

		Header($lRedirUrl);
		exit();
	}

	/**
	 * Returns the content of a form fields template with the parsed name
	 *
	 * @param $pFieldsTemplName unknown_type
	 */
	function getTemplate($pFieldsTemplName) {
		global $gFormFieldsArray;
		if($gFormFieldsArray){
			if(array_key_exists($pFieldsTemplName, $gFormFieldsArray)){
				return $gFormFieldsArray[$pFieldsTemplName];
			}
		}

		$lTmp = '';
		$lArr = array();
		if(strstr($pFieldsTemplName, '.')){
			$lArr = explode('.', $pFieldsTemplName);
		}

		$lExtSite = '';

		if(count($lArr) == 3){
			$lSiteName = $lArr[0];
			$lTmp = $lArr[1];
			$lExtSite = $lSiteName . '.';
		}elseif(count($lArr) == 2){
			$lSiteName = SITE_NAME;
			$lTmp = $lArr[0];
		}else{
			$lSiteName = SITE_NAME;
			$lTmp = 'tcpage';
		}

		$lFileName = PATH_CLASSES . $lSiteName . '/forms/' . $lTmp . '.php';

		if(file_exists($lFileName)){
			require_once ($lFileName);
		}elseif(file_exists(PATH_CLASSES . '/forms/' . $lTmp . '.php')){
			require_once (PATH_CLASSES . '/forms/' . $lTmp . '.php');
		}else{
			trigger_error("The file <b>\"$lFileName\"</b> does not exist [$pFieldsTemplName] !!!" . "\n", E_USER_NOTICE);
		}

		if(! is_array($gFormFieldsArr)){
			$gFormFieldsArr = array();
		}

		foreach($gFormFieldsArr as $k => $v){
			$gFormFieldsArray[$lExtSite . $k] = $v;
		}

		if(! array_key_exists($pFieldsTemplName, $gFormFieldsArray)){
			trigger_error("<b>\"$pFieldsTemplName\"</b> not found !!!" . "\n", E_USER_NOTICE);
		}

		return $gFormFieldsArray[$pFieldsTemplName];
	}

	function Display() {
		$this->PerformAction();
		$lView = $this->CreateFormView();
		return $lView->Display();
	}
	
}

?>