<?php

/**
 * A base class that will display form.
 * It gets its data from a controller and is being displayed by a view
 * An output is generated for each row given by the controller
 *
 * @author peterg
 *
 */
class evForm_Display extends ebase_view {
	/**
	 * A reference to the controller instance which handles the form.
	 * It is used when performing actions
	 *
	 * @var ecBase_Controller
	 */
	var $m_controllerInstance;

	// @formatter:off
	/**
	 * An array containing the data after the controller method
	 * for the current action has been executed.
	 * The format should be the following:
	 * 		err_cnt => Whether there were errors or not
	 * 		err_msgs => an array containing the error messages (its format should be field_name => err_msg; if nor field_name is specified - global error)
	 * 		field_values => an array containing the new field values for the form (the format should be field_name => field_value)
	 * @var array
	 */
	// @formatter:on
	var $m_controllerData;

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
	 * @var unknown_type
	 */
	var $m_debug;


	/**
	 * Whether to use captcha (i.e. to check the captcha before executing the form action)
	 * @var unknown_type
	 */
	var $m_useCaptcha;

	var $m_addBackUrl;

	function __construct($pData) {
		parent::__construct($pData);
		$this->m_controllerInstance = $pData['controller_instance'];
		$this->m_formName = $pData['form_name'];
		$this->m_formMethod = $pData['form_method'];
		$this->m_debug = (int) $pData['debug'];
		$this->m_fieldsMetadata = $pData['fields_metadata'];
		$this->m_addBackUrl = $pData['add_back_url'];
		$this->m_useCaptcha = $pData['use_captcha'];

		$this->m_actionIsExecuted = false;

		$this->m_errCnt = 0;
		$this->m_fieldErrors = array();
		$this->m_globalErrors = array();

		if(! $this->m_formName){
			$gFormsCount = getglobalformnumber();
			$this->m_formName = "def" . $gFormsCount;
		}

		if(! in_array($this->m_formMethod, array(
			'GET',
			'POST'
		))){
			$this->m_formMethod = FORM_DEFAULT_METHOD;
		}
		$this->m_pubdata['form_method'] = $this->m_formMethod;

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

		if(!$_REQUEST['form_name'] || $_REQUEST['form_name'] == $this->m_formName){
			//We process the action only if this was the action for exactly this form
			$this->m_currentAction = $_REQUEST['tAction'];
		}


		if( !$this->m_currentAction){
			$this->m_currentAction = DEFAULT_FORM_ACTION;
		}



		if(! is_array($this->m_fieldsMetadata[$this->m_currentAction])){
			if($this->m_currentAction == DEFAULT_FORM_ACTION){
				// We create the default action ourselves!!!
				$this->m_fieldsMetadata[$this->m_currentAction] = array(
					"CType" => "action",
					"ActionMask" => ACTION_SHOW,
					"Hidden" => true
				);
			}else{
				// We search for the default action in the labels of the action
				// fields
				foreach($this->m_fieldsMetadata as $k => $v){
					if($this->m_currentAction == $v["DisplayName"] && $v["CType"] == "action"){
						$this->m_currentAction = $k;
						break;
					}
				}
				if(! is_array($this->m_fieldsMetadata[$this->m_currentAction])){
					trigger_error($this->m_currentAction . " is not valid action.", E_USER_ERROR);
				}
			}
		}else{
			if($this->m_fieldsMetadata[$this->m_currentAction]["CType"] != "action"){
				trigger_error($this->m_currentAction . " is not valid action.", E_USER_ERROR);
			}
		}
	}

	protected function AddBackUrl(){
		return $this->m_addBackUrl;
	}

	/**
	 * Returns the number of errors in the form
	 */
	function GetErrorCount() {
		return $this->m_errCnt;
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
	 * Returns a representation of the errors for the specifield field, which is
	 * to be used in the form template
	 *
	 * @param $pFieldName unknown_type
	 */
	function GetFieldErrorsTemplate($pFieldName) {
		$lErrors = $this->GetFieldErrorsArr($pFieldName);
		$lResult = '';
		$this->m_pubdata['field_errors_count'] = count($lErrors);
		$this->m_pubdata['field_label'] = $this->m_fieldsMetadata[$pFieldName]['DisplayName'];
		$lCurrentErrorIdx = 1;
		foreach($lErrors as $lCurrentError){
			$this->m_pubdata['field_err_msg'] = $lCurrentError;
			$this->m_pubdata['current_field_err_idx'] = $lCurrentErrorIdx ++;
			$lResult .= $this->GetReplacedObjTemplate(G_FORM_FIELD_ERROR_ROW);
		}
		return $lResult;
	}

	/**
	 * Returns a representation of the errors for all fields which have errors (i.e. all errors except the global ones)
	 * which is to be used in the form template
	 */
	function GetAllFieldsErrorsTemplate(){
		$lResult = '';
		foreach($this->m_fieldsMetadata as $lFieldName => $lFieldData){
			$lResult .= $this->GetFieldErrorsTemplate($lFieldName);
		}
		return $lResult;
	}

	/**
	 * Returns an array with the errors which are global for the form
	 * (The array consists of error msgs)
	 */
	function GetGlobalErrorsArr() {
		return $this->m_globalErrors;
	}

	/**
	 * Returns a representation of the global errors for the form, which is to
	 * be used in the form template
	 *
	 * @param $pFieldName unknown_type
	 */
	function GetGlobalErrorsTemplate() {
		$lErrors = $this->GetGlobalErrorsArr();
		$lResult = '';
		$this->m_pubdata['global_errors_count'] = count($lErrors);
		$lCurrentErrorIdx = 1;
		foreach($lErrors as $lCurrentError){
			$this->m_pubdata['current_global_err_idx'] = $lCurrentErrorIdx ++;
			$this->m_pubdata['global_err_msg'] = $lCurrentError;
			$lResult .= $this->GetReplacedObjTemplate(G_FORM_GLOBAL_ERROR_ROW);
		}
		return $lResult;
	}

	/**
	 * Returns the captcha template (if the form uses captcha) which
	 * is to be used in the form edit template
	 */
	function GetCaptchaTemplate(){
		if(!$this->m_useCaptcha){
			return;
		}
		$lResult = $this->GetReplacedObjTemplate(G_FORM_CAPTCHA_ROW);
		return $lResult;
	}

	function Setselfurl() {
		global $forwardurl, $selfurl;
		if($_POST["selfurl"])
			$selfurl = $_POST["selfurl"];
		else if($_REQUEST["selfurl"])
			$selfurl = $_REQUEST["selfurl"];

		if($this->lFormMethod == 'POST'){
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
	 * Checks whether the series of actions which should be performed by the
	 * current action
	 * are logically correct (e.g.
	 * you cannot display the form and redirect in the same time)
	 *
	 * @return boolean
	 */
	function CheckActionMask() {
		$lMask = $this->lFieldArr[$this->lCurAction]["ActionMask"];

		if(($lMask & ACTION_FETCH) && ! ($lMask & ACTION_EXEC)){
			trigger_error("Cannot ACTION_FETCH if there is no ACTION_EXEC for action $this->m_currentAction", E_USER_ERROR);
			return false;
		}

		if(($lMask & ACTION_EXEC) && ! ($lMask & ACTION_CHECK)){
			trigger_error("Cannot ACTION_EXEC if there is no ACTION_CHECK for action $this->m_currentAction", E_USER_ERROR);
			return false;
		}

		if(($lMask & ACTION_SHOW) && ($lMask & ACTION_REDIRECT)){
			trigger_error("Cannot use both ACTION_SHOW and ACTION_REDIRECT for action $this->m_currentAction", E_USER_ERROR);
			return false;
		}

		if(($lMask & ACTION_CCHECK) && ! ($lMask & ACTION_CHECK)){
			trigger_error("Cannot use ACTION_CCHECK without ACTION_CHECK for action $this->m_currentAction", E_USER_ERROR);
			return false;
		}
	}

	/**
	 * Fills the fields values from the controller data which has been returned
	 * after the
	 * controller has executed the current action.
	 *
	 * @param $pControllerData array
	 */
	protected function FillFieldsValuesFromControllerData() {
		foreach($this->m_controllerData['field_values'] as $lFieldName => $lFieldValue){
			$lFieldInfo = $this->m_fieldsMetadata[$lFieldName];
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

	/**
	 * Fills the values of the fields from the $_POST/GET (depending on the form
	 * method)
	 */
	protected function FillFieldsValuesFromRequest() {
		foreach($this->m_fieldsMetadata as $lFieldName => $lFieldInfo){
			$lFieldValue = '';
			switch ($this->m_formMethod) {
				case "GET" :
					$lFieldValue = $_REQUEST[$lFieldName];
					break;
				case "POST" :
					$lFieldValue = $_POST[$lFieldName];
					if(! isset($t))
						$lFieldValue = $_REQUEST[$lFieldName];
					break;
				default :
					$lFieldValue = $_POST[$lFieldName];
					if(! isset($t))
						$lFieldValue = $_REQUEST[$lFieldName];
			}
			switch ($lFieldInfo['VType']) {
				case "int" :
				case "float" :
				case "string" :
				case "mlstring" :
				case "mlint" :
				case "date" :
					// Remove the extra slashes if necessary
					if(is_array($lFieldValue)){
						return array_map("s", $lFieldValue);
					}else{
						$lFieldValue = s($lFieldValue);
					}
					$this->m_fieldsValues[$lFieldName] = $lFieldValue;
					break;
				case "file" :
					break;
				default :
					break;
			}
		}
	}

	/**
	 * Sets the name of the form
	 *
	 * @param $pNewFormName unknown_type
	 */
	function SetFormName($pNewFormName) {
		$this->m_formName = $pNewFormName;
		$this->m_fieldsValues['form_name'] = $pNewFormName;
	}

	function GetFieldTitle($pFieldName) {
		return $this->m_fieldsMetadata[$pFieldName]['DisplayName'];
	}

	/**
	 * Execs the controller functions which is associated with the current
	 * action
	 */
	function ExecControllerAction() {

		$lCurrentActionMetadata = $this->m_fieldsMetadata[$this->m_currentAction];
		$lControllerMethodName = $lCurrentActionMetadata['ControllerMethodName'];
		if(! $lControllerMethodName){
			$this->SetError(ERR_NO_CONTROLLER_METHOD_FOR_ACTION);
			return;
		}
		if(! is_object($this->m_controllerInstance) && ! is_subclass_of($this->m_controllerInstance, ecBase_Controller)){
			$this->SetError(ERR_NO_CONTROLLER);
			return;
		}
		/*
		 * The callback function should accept 1 parameter - an array with the
		 * format field_name => field_value We will pass as an argument all the
		 * fields in the ['FieldsToCheck'] of the metadata of the current action
		 */
		$lFieldValues = array();
		if(is_array($lCurrentActionMetadata['FieldsToCheck'])){
			foreach($lCurrentActionMetadata['FieldsToCheck'] as $lFieldName){
				$lFieldValues[$lFieldName] = $this->GetFieldValue($lFieldName);
			}
		}

		if(! is_callable(array(
			$this->m_controllerInstance,
			$lCurrentActionMetadata['ControllerMethodName']
		))){
			$this->SetError(ERR_CONTROLLER_METHOD_IS_NOT_CALLABLE);
			return;
		}

		$this->LogDebugMsg('Executing controller "' . $lCurrentActionMetadata['ControllerMethodName'] . '" method with ' . var_export($lFieldValues, 1));



		$lCallResult = call_user_func(array(
			$this->m_controllerInstance,
			$lCurrentActionMetadata['ControllerMethodName']
		), $lFieldValues);
		if($lCallResult === false){ // Could not execute the callback
			$this->SetError(ERR_COULD_NOT_EXECUTE_CONTROLLER_METHOD);
			return;
		}
		/**
		 * The controller method should return a result array in the following
		 * format
		 * err_cnt => Whether there were errors or not
		 * err_msgs => an array containing the error messages (
		 * it should consist of arrays with the following format
		 * field_name => The name of the field in which there was an error (If
		 * no field name is specified - a global error is assumed)
		 * err_msg => The error msg
		 * r)
		 * field_values => an array containing the new field values for the form
		 * (the format should be field_name => field_value)
		 */
		$this->m_controllerData = $lCallResult;
		$this->FillErrorsFromControllerData();
	}

	/**
	 * Checks if there have been errors in the
	 * execution of the controller method of the current action
	 * and if any sets them to the form
	 */
	protected function FillErrorsFromControllerData() {
		if(! (int) $this->m_controllerData['err_cnt']){
			return;
		}
		if(! is_array($this->m_controllerData['err_msgs']) || ! count($this->m_controllerData['err_msgs'])){
			$this->SetError(ERR_CONTROLLER_STATES_THERE_ARE_ERRORS_BUT_DOESNT_PROVIDE_THEM);
			return;
		}
		foreach($this->m_controllerData['err_msgs'] as $lError){
			$this->SetError($lError['err_msg'], $lError['field_name']);
		}
	}

	function GetData() {
		if(! $this->m_actionIsExecuted){
			$this->m_actionIsExecuted = true;
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
				$this->FillFieldsValuesFromControllerData();
			}
		}
	}

	/**
	 * Returns the template representation of the passed field
	 *
	 * @param $pFieldName unknown_type
	 * @param $pInViewmode unknown_type
	 */
	function GetFieldTemplate($pFieldName, $pInViewmode = 0) {
		if((int) $pInViewmode){
			return $this->GetFieldViewmodeTemplate($pFieldName);
		}
		return $this->GetFieldEditmodeTemplate($pFieldName);
	}

	/**
	 * Returns a viewmode representation of the field
	 *
	 * @param $pFieldName unknown_type
	 */
	protected function GetFieldViewmodeTemplate($pFieldName) {
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lFieldCurValue = $this->m_fieldsValues[$pFieldName];

		if(isset($lFieldMetadata["DefValue"]) && (is_null($lFieldCurValue))){
			$lFieldCurValue = $lFieldMetadata["DefValue"];
		}
		$lResult = '';
		switch ($lFieldMetadata["CType"]) {
			case "select" :
			case "mselect" :
			case "radio" :
			case "checkbox" :
				// The representation here is a string containing all the
				// selected values;
				$lSrcValues = $lFieldMetadata['ScrValues'];
				if(! is_array($lSrcValues)){
					$lSrcValues = array();
				}
				if(! is_array($lFieldCurValue)){
					$lFieldCurValue = array(
						$lFieldCurValue
					);
				}
				$lValuesDisplayed = 0;
				foreach($lSrcValues as $lKey => $lValue){
					if(! in_array($lKey, $lFieldCurValue))
						continue;
					if(! $lValuesDisplayed > 0){
						$lResult .= FORM_DEFAULT_VALUES_IN_VIEWMODE_SEPARATOR;
					}
					$lLabel = $lValue;
					if(is_array($lValue)){
						$lLabel = $lValue['label'];
					}
					$lResult .= $lLabel;
					$lValuesDisplayed ++;
				}
				return $lResult;
			case "file" :
			case "text" :
			case "hidden" :
				$lResult = h($lFieldCurValue);
				return $lResult;
			case "textarea" :
				return nl2br(h($lFieldCurValue));
		}
	}

	/**
	 * Returns an editmode representation of the field
	 *
	 * @param $pFieldName unknown_type
	 */
	protected function GetFieldEditmodeTemplate($pFieldName) {
// 		var_dump($pFieldName);
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		switch ($lFieldMetadata["CType"]) {
			case "select" :
			case "mselect" :
			case "radio" :
			case "checkbox" :
				return $this->GetSelectFieldEditmodeTemplate($pFieldName);
			case "text" :
			case "password" :
			case "textarea" :
			case "hidden" :
			case "mlfield" :
			case "file" :
				return $this->GetInputFieldEditmodeTemplate($pFieldName);
			case "action" :
				return $this->GetActionFieldEditmodeTemplate($pFieldName);
		}
	}

	/**
	 * Returns the viewmode template for fields which are of type
	 * select, mselect, radio or checkbox
	 *
	 * @param $pFieldName unknown_type
	 */
	protected function GetSelectFieldEditmodeTemplate($pFieldName) {
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lFieldCurValue = $this->m_fieldsValues[$pFieldName];
		$lSrcValues = $lFieldMetadata['SrcValues'];
		$lFieldControlType = $lFieldMetadata["CType"];

		if(! in_array($lFieldControlType, array(
			'select',
			'mselect',
			'radio',
			'checkbox'
		))){
			return;
		}

		if(! is_array($lSrcValues)){
			$lSrcValues = array();
		}

		if(isset($lFieldMetadata["DefValue"]) && (is_null($lFieldCurValue))){
			$lFieldCurValue = $lFieldMetadata["DefValue"];
		}

		$lAdditionalTags = $lFieldMetadata['AddTags'];
		$lAdditionalTagsString = '';
		if(is_array($lAdditionalTags)){
			foreach($lAdditionalTags as $k => $v){
				$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
			}
		}

		if(! is_array($lFieldCurValue)){
			$lFieldCurValue = array(
				$lFieldCurValue
			);
		}

		$lRes = '';
		$this->m_pubdata['additional_tags_string'] = $lAdditionalTagsString;
		switch ($lFieldControlType) {
			case "select" :
				$lRes .= $this->GetReplacedObjTemplate(G_FORM_SELECT_START);
				break;
			case "mselect" :
				$lRes .= $this->GetReplacedObjTemplate(G_FORM_MSELECT_START);
				break;
			case "radio" :
				$lRes .= $this->GetReplacedObjTemplate(G_FORM_RADIO_START);
				break;
			case "checkbox" :
				$lRes .= $this->GetReplacedObjTemplate(G_FORM_CHECKBOX_START);
				break;
		}

		foreach($lSrcValues as $lKey => $lValueData){
			$lCurrentRowAdditionalTagsString = $lAdditionalTagsString;
			if(is_array($lValueData)){
				$lLabel = $lValueData['label'];
				if(is_array($lValueData['AddTags'])){
					foreach($lValueData['AddTags'] as $lAddKey => $lAddValue){
						$lCurrentRowAdditionalTagsString .= ' ' . htmlspecialchars($lAddKey) . '="' . htmlspecialchars($lAddValue) . '"';
					}
				}
			}else{
				$lLabel = $lValueData;
			}
			$this->m_pubdata['row_additional_tags_string'] = $lCurrentRowAdditionalTagsString;
			$this->m_pubdata['value_label'] = $lLabel;
			$this->m_pubdata['value_is_selected'] = in_array($lKey, $lFieldCurValue);
			$this->m_pubdata['value_key'] = $lKey;

			switch ($lFieldControlType) {
				case "select" :
					$lRes .= $this->GetReplacedObjTemplate(G_FORM_SELECT_ROW);
					break;
				case "mselect" :
					$lRes .= $this->GetReplacedObjTemplate(G_FORM_MSELECT_ROW);
					break;
				case "radio" :
					$lRes .= $this->GetReplacedObjTemplate(G_FORM_RADIO_ROW);
					break;
				case "checkbox" :
					$lRes .= $this->GetReplacedObjTemplate(G_FORM_CHECKBOX_ROW);
					break;
			}
		}

		switch ($lFieldControlType) {
			case "select" :
				$lRes .= $this->GetReplacedObjTemplate(G_FORM_SELECT_END);
				break;
			case "mselect" :
				$lRes .= $this->GetReplacedObjTemplate(G_FORM_MSELECT_END);
				break;
			case "radio" :
				$lRes .= $this->GetReplacedObjTemplate(G_FORM_RADIO_END);
				break;
			case "checkbox" :
				$lRes .= $this->GetReplacedObjTemplate(G_FORM_CHECKBOX_END);
				break;
		}
		return $lRes;
	}

	/**
	 * Returns the viewmode template for fields which are of type
	 * text, password, textarea, hidden, mlfield, or file
	 *
	 * @param $pFieldName unknown_type
	 */
	protected function GetInputFieldEditmodeTemplate($pFieldName) {
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lFieldCurValue = $this->m_fieldsValues[$pFieldName];
		$lSrcValues = $lFieldMetadata['SrcValues'];
		$lFieldControlType = $lFieldMetadata["CType"];
		$lFieldValueType = $lFieldMetadata['VType'];
		$lFieldIsRichtext = $lFieldMetadata['RichText'];

		if(! in_array($lFieldControlType, array(
			'text',
			'password',
			'textarea',
			'hidden',
			'mlfield',
			'file'
		))){
			return;
		}

		if(! is_array($lSrcValues)){
			$lSrcValues = array();
		}

		if(isset($lFieldMetadata["DefValue"]) && (is_null($lFieldCurValue))){
			$lFieldCurValue = $lFieldMetadata["DefValue"];
		}

		$this->m_pubdata['field_name'] = $pFieldName;
		$this->m_pubdata['field_cur_value'] = $lFieldCurValue;

		$lAdditionalTags = $lFieldMetadata['AddTags'];
		$lAdditionalTagsString = '';
		$lCalendarIcon = '';
		if(is_array($lAdditionalTags)){
			foreach($lAdditionalTags as $k => $v){
				if($k == 'addcalico'){
					$this->m_pubdata['calendar_data'] = $v;
					$lCalendarIcon = $this->GetReplacedObjTemplate(G_FORM_CALENDAR_ROW);

				}else{
					$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
				}
			}
		}

		$lResult = '';
		$this->m_pubdata['calendar_icon'] = $lCalendarIcon;
		$this->m_pubdata['additional_tags_string'] = $lAdditionalTagsString;

		// We put the metadata in the pubdata so that it can be used in the
		// templates
		foreach($lFieldMetadata as $lKey => $lValue){
			$this->m_pubdata['field_' . $lKey] = $lValue;
		}

		if((int) $lFieldIsRichtext && $lFieldValueType != 'mlstring'){
			$lResult .= $this->GetReplacedObjTemplate(G_FORM_RICHTEXT_EDITOR_ROW);
		}

		if($lFieldControlType == "textarea"){
			if(! (int) $lFieldIsRichtext){
				$lResult .= $this->GetReplacedObjTemplate(G_FORM_TEXTAREA_ROW);
			}
		}else{
			if(($lFieldValueType == "mlstring") || ($lFieldValueType == "mlint")){
				// Multi language string/int

				checkSessionLangs();
				$lResult .= $this->GetReplacedObjTemplate(G_FORM_MULTILANGUAGE_FIELD_HEAD);
				foreach($_SESSION["langs"] as $k => $v){
					$this->m_pubdata['lang_id'] = $k;
					$this->m_pubdata['lang_data'] = $v;
					$lFieldLabel = $lFieldMetadata['DisplayName'] ? $lFieldMetadata['DisplayName'] : $pFieldName;
					$lFieldLabel .= '(' . $v["code"] . ')';

					$this->m_pubdata['field_label'] = $lFieldLabel;
					$lResult .= $this->GetReplacedObjTemplate(G_FORM_MULTILANGUAGE_FIELD_ROW_HEAD);
					if((int) $lFieldIsRichtext){
						$lResult .= $this->GetReplacedObjTemplate(G_FORM_MULTILANGUAGE_RICHTEXT_EDITOR_ROW);
					}else{
						$lResult .= $this->GetReplacedObjTemplate(G_FORM_MULTILANGUAGE_INPUT_ROW);
					}

					$lResult .= $this->GetReplacedObjTemplate(G_FORM_MULTILANGUAGE_FIELD_ROW_FOOT);
				}

				$lResult .= $this->GetReplacedObjTemplate(G_FORM_MULTILANGUAGE_FIELD_FOOT);
			}else{
				if(! (int) $lFieldIsRichtext){
					switch ($lFieldControlType) {
						case 'text' :
							$lResult .= $this->GetReplacedObjTemplate(G_FORM_TEXT_INPUT_ROW);
							break;
						case 'password' :
							$lResult .= $this->GetReplacedObjTemplate(G_FORM_PASSWORD_INPUT_ROW);
							break;
						case 'hidden' :
							$lResult .= $this->GetReplacedObjTemplate(G_FORM_HIDDEN_INPUT_ROW);
							break;
						case 'file' :
							$lResult .= $this->GetReplacedObjTemplate(G_FORM_FILE_INPUT_ROW);
							break;
					}
				}
			}
		}
		return $lResult;
	}

	/**
	 * Returns the viewmode template for fields which are of type
	 * action (the submit btns)
	 *
	 * @param $pFieldName unknown_type
	 */
	protected function GetActionFieldEditmodeTemplate($pFieldName) {
		global $selfurl, $forwardurl;

		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lFieldControlType = $lFieldMetadata["CType"];

		if(! in_array($lFieldControlType, array(
			'action'
		))){
			return;
		}

		$lAdditionalTags = $lFieldMetadata['AddTags'];
		$lAdditionalTagsString = '';

		$this->m_pubdata['field_name'] = $pFieldName;
		foreach($lFieldMetadata as $lKey => $lValue){
			$this->m_pubdata['field_' . strtolower($lKey)] = $lValue;
		}

		$lRedirUrl = $lFieldMetadata['RedirUrl'];
		$lBackUrl = 0;
		if($lRedirUrl == "{#backurl}" || $lRedirUrl == "{#selfurl}"){
			$lBackUrl = 1;
			$lRedirUrl = $this->GetRedirUrl($pFieldName);
		}
		if($lRedirUrl == ''){
			$lRedirUrl = getenv('REQUEST_URI');
			$lPattern = "/tAction=([^\&\?]*)/";
			if(preg_match($lPattern, $lRedirUrl)){
				$lRedirUrl = preg_replace($lPattern, "tAction=" . $pFieldName, $lRedirUrl);
			}else{
				$lRedirUrl = AddParamtoURL($lRedirUrl, 'tAction=' . $pFieldName);
			}
		}

		if($this->AddBackUrl() || ! $lBackUrl){
			$lRedirUrl = ClearParaminURL($lRedirUrl, "backurl");
			$lRedirUrl = ClearParaminURL($lRedirUrl, "selfurl");
			$lRedirUrl = AddParamtoURL($lRedirUrl, 'backurl=' . urlencode($forwardurl));
			$lRedirUrl .= AddParamtoURL($lRedirUrl, 'selfurl=' . urlencode($selfurl));
		}

		$lRedirectOnclickFound = false;
		if(is_array($lAdditionalTags)){
			foreach($lAdditionalTags as $k => $v){
				if(preg_match('/onclick/i', $k) && (strlen($k) == 7)){
					$v = str_replace('{loc}', 'window.location=\'' . $lRedirUrl . '\';return false;', $v);
				}
				$lRedirectOnclickFound = true;
				$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
			}
		}

		if(! $lRedirectOnclickFound){
			$lAdditionalTagsString .= ' onclick="javascript:window.location=\'' . $lRedirUrl . '\';return false;"';
		}

		$this->m_pubdata['additional_tags_string'] = $lAdditionalTagsString;

		if($lFieldMetadata['ButtonHtml']){
			return $this->GetReplacedObjTemplate($lFieldMetadata['ButtonHtml']);
		}
		if($lFieldMetadata['IsLink']){
			return $this->GetReplacedObjTemplate(G_FORM_ACTION_LINK_ROW);
		}
		if($lFieldMetadata['IsImage']){
			return $this->GetReplacedObjTemplate(G_FORM_ACTION_IMAGE_ROW);
		}
		return $this->GetReplacedObjTemplate(G_FORM_ACTION_DEFAULT_ROW);
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
			foreach($this->m_fieldsMetadata[$this->m_currentAction]['FieldsToCheck'] as $lFieldName){
				$lFieldsToCheck[] = $lFieldName;
			}
		}else{
			$lFieldsToCheck[] = $pFieldName;
		}


		foreach($lFieldsToCheck as $lFieldName){
			$lFieldMetadata = $this->m_fieldsMetadata[$lFieldName];
			$lCurValue = &$this->m_fieldsValues[$lFieldName];
			$lValueType = $lFieldMetadata['VType'];


			if((is_null($lCurValue) || $lCurValue === '') && $lFieldMetadata['AllowNulls']){
				continue;
			}

			if((is_array($lCurValue) && ! strlen(implode("", $lCurValue))) && $lFieldMetadata['AllowNulls']){
				continue;
			}

			switch ($lValueType) {
				case "float" :
				case "int" :
				case "mlint" :
					if(is_array($lCurValue)){
						foreach($lCurValue as $k => $v){
							if(is_null($v) || $v === ''){
								if($lFieldMetadata['AllowNulls'])
									continue;
								else{
									$this->SetError(ERR_EMPTY_NUMERIC, $lFieldName);
									continue 2; // minavame na sledvashtiat euement
								}
							}
							if(! is_numeric($v)){
								$this->SetError(ERR_NAN, $lFieldName);
								continue 2; // minavame na sledvashtiat euement
							}
							$lCurValue[$k] = (($lValueType == "float") ? (float) $lCurValue[$k] : (int) $lCurValue[$k]);
						}
					}else{
						if(is_null($lCurValue) || $lCurValue === ''){
							$this->SetError(ERR_EMPTY_NUMERIC, $lFieldName);
							continue;
						}
						if(! is_numeric($lCurValue)){
							$this->SetError(ERR_NAN, $lFieldName);
							continue;
						}

						$lCurValue = (($lValueType == "float") ? (float) $lCurValue : (int) $lCurValue);
					}
					break;
				case "date" :
					if(is_array($lCurValue)){
						foreach($lCurValue as $k => $v){
							$lstrError = manageckdate($lCurValue[$k], $lFieldMetadata['DateType']);
							if($lstrError){
								$this->SetError($lstrError, $lFieldName);
								continue 2; // minavame na sledvashtiat euement
							}
						}
					}else{
						$lstrError = manageckdate($lCurValue, $lFieldMetadata['DateType']);
						if($lstrError){
							$this->SetError($lstrError, $lFieldName);
							continue;
						}
					}
					break;
				case "string" :
					// var_dump($lFname);
					// echo "<br>";
					if(is_null($lCurValue) || $lCurValue == ''){
						$this->SetError(ERR_EMPTY_STRING, $lFieldName);
						continue;
					}
					break;
				case "mlstring" :
					if(is_null($lCurValue) || ! is_array($lCurValue) || ! strlen(implode("", $lCurValue))){
						$this->SetError(ERR_EMPTY_STRING, $lFieldName);
						continue;
					}
					break;
				default :
					break;
			}
		}


		/**
		 * We check the captcha code if the form uses one
		 * and the current action requires captcha check
		 */
		if($pFieldName == '' && $this->m_useCaptcha && $this->m_fieldsMetadata[$this->m_currentAction]['CheckCaptcha']){
			//If the value is correct - remove this value from future captcha codes
			if (in_array(strtolower($_GET['captcha']), $_SESSION['frmcapt'])) {
				foreach ($_SESSION['frmcapt'] as $captkey => $captval) {
					if ($captval == strtolower($_POST['captcha']))
						unset($_SESSION['frmcapt'][$captkey]);
				}
			} else {
				//Report error
				$this->SetError(ERR_CAPTCHA_WRONG_CODE);
			}
		}

// 		var_dump($this->m_fieldErrors);
// 		var_dump($this->m_globalErrors);

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
			foreach($this->m_fieldsMetadata[$this->m_currentAction]['FieldsToCheck'] as $lFieldName){
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
	 * Returns the exported value of the specified field.
	 * This value may after that be used in eval expressions
	 *
	 * @param $pFieldName unknown_type
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

	/**
	 * Returns the current value of the specified field
	 *
	 * @param $pFieldName unknown_type
	 * @return unknown_type
	 */
	function GetFieldValue($pFieldName) {
		return $this->m_fieldsValues[$pFieldName];
	}

	/**
	 * Checks if the specified field exists in the form
	 *
	 * @param $pFieldName unknown_type
	 */
	function CheckIfFieldExists($pFieldName) {
		return array_key_exists($pFieldName, $this->m_fieldsMetadata);
	}

	/**
	 *
	 * @see cbase_view::Display()
	 */
	function Display() {
		$this->GetData();

		$lRet .= $this->GetReplacedFormObjTemplate(G_FORM_HEADER);
		$lRet .= $this->GetReplacedFormObjTemplate(G_FORM_TEMPLATE);
		$lRet .= $this->GetReplacedFormObjTemplate(G_FORM_FOOTER);

		return $lRet;
	}

	/**
	 * Asks the view to replace all the form fields in the
	 * passed string
	 *
	 * @param $pStr unknown_type
	 */
	protected function ReplaceHtmlFormFields($pStr) {
		if($this->m_viewObject){
			return $this->m_viewObject->ReplaceHtmlFormFields($pStr, $this);
		}
	}

	protected function GetReplacedObjTemplate($pTemplate){
		if($this->m_viewObject){
			return $this->m_viewObject->ReplaceHtmlFields($this->getObjTemplate($pTemplate), $this);
		}
	}

	protected function GetReplacedFormObjTemplate($pTemplate){
		if($this->m_viewObject){
			return $this->m_viewObject->ReplaceHtmlFormFields($this->getObjTemplate($pTemplate), $this);
		}
	}

}

?>