<?php

class evForm_View extends evbase_view {
	/**
	 * The name of the form (e.g.
	 * the name attribute in the html)
	 *
	 * @var unknown_type
	 */
	var $m_formName;

	/**
	 * The form method (POST/GET)
	 *
	 * @var string
	 */
	var $m_formMethod;

	/**
	 * Whether to use captcha (i.e.
	 * whether to display the captcha)
	 *
	 * @var boolean
	 */
	var $m_useCaptcha;

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

	var $m_addBackUrl;
	
	/**
	 * Turn on/off ajax validation (0 - Off, 1 - On)
	 * 
	 * @var int 
	 */
	var $m_jsValidation;

	/**
	 * which field to perform the validation
	 */
	var $m_checkField;
	
	/**
	 * contains the form fields template key
	 * 
	 * @var string
	 */
	var $m_formFieldsTemplName;
	
	function __construct($pData){
		parent::__construct($pData);
		$this->m_fieldsMetadata = $pData['fields_metadata'];
		$this->m_fieldsValues = $pData['fields_values'];
		$this->m_errCnt = $pData['err_cnt'];
		$this->m_globalErrors = $pData['global_errors'];
		$this->m_fieldErrors = $pData['fields_errors'];

		$this->m_formName = $pData['form_name'];
		$this->m_formMethod = $pData['form_method'];
		$this->m_useCaptcha = $pData['use_captcha'];
		$this->m_addBackUrl = $pData['add_back_url'];
		$this->m_jsValidation = $pData['js_validation'];
		$this->m_checkField = $pData['check_field'];
		$this->m_formFieldsTemplName = $pData['fields_templ_name'];

	}
	/**
	 * Returns an array with the errors which are global for the form
	 * (The array consists of error msgs)
	 */
	function GetGlobalErrorsArr() {
		return $this->m_globalErrors;
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
	 * Returns the number of errors in the form
	 */
	function GetErrorCount() {
		return $this->m_errCnt;
	}

	/**
	 * Returns a representation of the errors for the specifield field, which is
	 * to be used in the form template
	 *
	 * @param $pFieldName string
	 */
	function GetFieldErrorsTemplate($pFieldName) {
		$lErrors = $this->GetFieldErrorsArr($pFieldName);
		$lResult = '';
		$this->m_pubdata['field_errors_count'] = count($lErrors);
		$this->m_pubdata['field_label'] = $this->m_fieldsMetadata[$pFieldName]['DisplayName'];
		$this->m_pubdata['field_error_templ'] = (array_key_exists('error_templ', $this->m_fieldsMetadata[$pFieldName]) ? $this->m_fieldsMetadata[$pFieldName]['error_templ'] : 'span');
		$this->m_pubdata['field_error_templ_id'] = DEF_ERROR_ID_HOLDER . $pFieldName;
		if((int)$this->m_pubdata['field_errors_count']) {
			$this->m_pubdata['field_show_hide_err_holder'] = 'block';
		} else {
			$this->m_pubdata['field_show_hide_err_holder'] = 'none';
		}
		$this->m_pubdata['field_error_templ_id'] = DEF_ERROR_ID_HOLDER . $pFieldName;
		
		$lCurrentErrorIdx = 1;
		
		$lResult .= $this->GetReplacedObjTemplate(G_FORM_FIELD_ERROR_HEADER);
		foreach($lErrors as $lCurrentError){
			$this->m_pubdata['field_err_msg'] = $lCurrentError;
			$this->m_pubdata['current_field_err_idx'] = $lCurrentErrorIdx ++;
			$lResult .= $this->GetReplacedObjTemplate(G_FORM_FIELD_ERROR_ROW);
		}
		$lResult .= $this->GetReplacedObjTemplate(G_FORM_FIELD_ERROR_FOOTER);
		
		return $lResult;
	}

	/**
	 * Returns a representation of the errors for all fields which have errors
	 * (i.e.
	 * all errors except the global ones)
	 * which is to be used in the form template
	 */
	function GetAllFieldsErrorsTemplate() {
		$lResult = '';
		foreach($this->m_fieldsMetadata as $lFieldName => $lFieldData){
			$lResult .= $this->GetFieldErrorsTemplate($lFieldName);
		}
		return $lResult;
	}

	/**
	 * Returns a representation of the global errors for the form, which is to
	 * be used in the form template
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
	function GetCaptchaTemplate() {
		if(! $this->m_useCaptcha){
			return;
		}
		$lResult = $this->GetReplacedObjTemplate(G_FORM_CAPTCHA_ROW);
		return $lResult;
	}

	/**
	 * Returns the title of the specified field
	 *
	 * @param $pFieldName string
	 */
	function GetFieldTitle($pFieldName) {
		return $this->m_fieldsMetadata[$pFieldName]['DisplayName'];
	}

	/**
	 * Returns the value of the specified field
	 *
	 * @param $pFieldName string
	 */
	function GetFieldValue($pFieldName) {
		return $this->m_fieldsValues[$pFieldName];
	}

	/**
	 * Checks if the specified field exists in the form
	 *
	 * @param $pFieldName string
	 */
	function CheckIfFieldExists($pFieldName) {
		return array_key_exists($pFieldName, $this->m_fieldsMetadata);
	}

	/**
	 * Returns the template representation of the passed field
	 *
	 * @param $pFieldName string
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
	 * @param $pFieldName string
	 */
	protected function GetFieldViewmodeTemplate($pFieldName) {
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lFieldCurValue = $this->GetFieldValue($pFieldName);

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
				return nl2br($lFieldCurValue);
		}
	}

	/**
	 * Returns an editmode representation of the field
	 *
	 * @param $pFieldName string
	 */
	protected function GetFieldEditmodeTemplate($pFieldName) {
		// var_dump($pFieldName);
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

	protected function SetCurrentFieldJsValidation($pFieldName, $pElemId){
		$this->m_pubdata['field_additional_js'] = '';
				
		$lAdditionalAddJs = '';
		$lAdditionalJs = '';
		
		if((int)$this->m_jsValidation && $this->m_fieldsMetadata[$pFieldName]['check_event']) {
			$this->m_pubdata['field_id'] = $pElemId;
			$this->m_pubdata['field_check_event'] = $this->m_fieldsMetadata[$pFieldName]['check_event']; 
			$this->m_pubdata['field_name'] = $pFieldName; 
			$this->m_pubdata['field_templ_name'] = $this->m_formFieldsTemplName;
			
			$lFieldErrorsCount = count($this->GetFieldErrorsArr($pFieldName));
			$lFieldCurValue = $this->GetFieldValue($pFieldName);

			// added additional check for defvalue, because cur value will be empty string here, even if defvalue is set !!!! 
			if($lFieldErrorsCount || ($this->m_fieldsMetadata[$pFieldName]['AllowNulls'] === false &&  ( $lFieldCurValue == '' && $this->m_fieldsMetadata[$pFieldName]['DefValue'] == null ))) {
				if(!$lFieldErrorsCount) {
					$lAdditionalAddJs = ($this->m_fieldsMetadata[$pFieldName]['req_js'] ? $this->m_fieldsMetadata[$pFieldName]['req_js'] : ($this->m_fieldsMetadata[$pFieldName]['error_js'] ? $this->m_fieldsMetadata[$pFieldName]['error_js'] : ''));
				} else {
					$lAdditionalAddJs = ($this->m_fieldsMetadata[$pFieldName]['error_js'] ? $this->m_fieldsMetadata[$pFieldName]['error_js'] : '');
				}
			} else {
				$lAdditionalAddJs = ($this->m_fieldsMetadata[$pFieldName]['valid_js'] ? $this->m_fieldsMetadata[$pFieldName]['valid_js'] : '');
			}
			
			$this->m_pubdata['field_additional_js'] = $lAdditionalAddJs; 
			$lAdditionalJs = $this->GetReplacedObjTemplate(G_FORM_JS_VALIDATION);
		} else {
			$this->m_pubdata['field_additional_js'] = $lAdditionalAddJs;
			if($this->m_pubdata['field_additional_js']) {
				$lAdditionalJs = $this->GetReplacedObjTemplate(G_FORM_JS_ONLY);
			}
		}
		
		return $lAdditionalJs;
	}

	/**
	 * Returns the viewmode template for fields which are of type
	 * select, mselect, radio or checkbox
	 *
	 * @param $pFieldName string
	 */
	protected function GetSelectFieldEditmodeTemplate($pFieldName) {
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lFieldCurValue = $this->GetFieldValue($pFieldName);
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

		$this->m_pubdata['field_name'] = $pFieldName;

		$lAdditionalTags = $lFieldMetadata['AddTags'];
		$lAdditionalTagsString = '';
		if(is_array($lAdditionalTags)){
			foreach($lAdditionalTags as $k => $v){
				$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
				
				if($k == 'class') {
					if((int)$this->m_jsValidation && $lFieldMetadata['ErrorString']) {
						$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . ' ' . htmlspecialchars($lFieldMetadata['error_class']) . '"';
					} else {
						$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
					}
				}
				
				if($k == 'id') {
					$lElemId = $v;
				}
			}
			
			if(!array_key_exists('id', $lAdditionalTags) && (int)$this->m_jsValidation) {
				$lAdditionalTagsString .= ' id="' . DEF_FORM_FIELD_ID . $pFieldName . '"';
				$lElemId = DEF_FORM_FIELD_ID . $pFieldName;
			}
			
		} else {
			if((int)$this->m_jsValidation && $lFieldMetadata['ErrorString']) {
				$lAdditionalTagsString .= ' class="' . $lFieldMetadata['error_class'] . '"';
			}
			if((int)$this->m_jsValidation) {
				$lAdditionalTagsString .= ' id="' . DEF_FORM_FIELD_ID . $pFieldName . '"';
				$lElemId = DEF_FORM_FIELD_ID . $pFieldName;
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
			$lCurrentRowAdditionalTagsString = '';//$lAdditionalTagsString;
			if(is_array($lValueData)){
				foreach($lValueData as $key => $lValue) {
					if(!is_int($key)) {
						$this->m_pubdata['field_select_' . strtolower($key)] = $lValue;
					}
				}
				$lLabel = $lValueData['name'];
				if(isset($lValueData['AddTags']) && is_array($lValueData['AddTags'])){
					foreach($lValueData['AddTags'] as $lAddKey => $lAddValue){
						$lCurrentRowAdditionalTagsString .= ' ' . htmlspecialchars($lAddKey) . '="' . htmlspecialchars($lAddValue) . '"';
					}
				}
				if($lValueData['id']){
					$lKey = $lValueData['id'];
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
		return $lRes . $this->SetCurrentFieldJsValidation($pFieldName, $lElemId);
	}

	/**
	 * Returns the viewmode template for fields which are of type
	 * text, password, textarea, hidden, mlfield, or file
	 *
	 * @param $pFieldName string
	 */
	protected function GetInputFieldEditmodeTemplate($pFieldName) {
		$lFieldMetadata = $this->m_fieldsMetadata[$pFieldName];
		$lFieldCurValue = $this->GetFieldValue($pFieldName);
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
		$lAdditionalTags = isset($lFieldMetadata['AddTags']) ? $lFieldMetadata['AddTags'] : '';
		$lAdditionalTagsString = '';
		$lCalendarIcon = '';
		
		if(is_array($lAdditionalTags)){
			foreach($lAdditionalTags as $k => $v){
				if($k == 'addcalico'){
					$this->m_pubdata['calendar_data'] = $v;
					$lCalendarIcon = $this->GetReplacedObjTemplate(G_FORM_CALENDAR_ROW);

				} elseif($k == 'class') {
					if((int)$this->m_jsValidation && $lFieldMetadata['ErrorString']) {
						$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . ' ' . $lFieldMetadata['error_class'] . '"';
					} else {
						$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
					}
				} elseif($k == 'id' && (int)$this->m_jsValidation) {
					$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
					$lElemId = $v;
				} else{
					$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
				}
			}
			
			if(!array_key_exists('id', $lAdditionalTags) && (int)$this->m_jsValidation) {
				$lAdditionalTagsString .= ' id="' . DEF_FORM_FIELD_ID . $pFieldName . '"';
				$lElemId = DEF_FORM_FIELD_ID . $pFieldName;
			}

		} else {
			if((int)$this->m_jsValidation && $lFieldMetadata['ErrorString']) {
				$lAdditionalTagsString .= ' class="' . $lFieldMetadata['error_class'] . '"';
			}
			if((int)$this->m_jsValidation) {
				$lAdditionalTagsString .= ' id="' . DEF_FORM_FIELD_ID . $pFieldName . '"';
				$lElemId = DEF_FORM_FIELD_ID . $pFieldName;
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
		return $lResult . $this->SetCurrentFieldJsValidation($pFieldName, $lElemId);
	}

	/**
	 * Returns the viewmode template for fields which are of type
	 * action (the submit btns)
	 *
	 * @param $pFieldName string
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
		$lAdditionalTags = isset($lFieldMetadata['AddTags']) ? $lFieldMetadata['AddTags'] : '';
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

		//~ $lRedirectOnclickFound = false;
		if(is_array($lAdditionalTags)){
			foreach($lAdditionalTags as $k => $v){
				if(preg_match('/onclick/i', $k) && (strlen($k) == 7)){
					$v = str_replace('{loc}', 'window.location=\'' . $lRedirUrl . '\';return false;', $v);
				}
				//~ $lRedirectOnclickFound = true;
				$lAdditionalTagsString .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
			}
		}

		//~ if(! $lRedirectOnclickFound){
			//~ $lAdditionalTagsString .= ' onclick="javascript:window.location=\'' . $lRedirUrl . '\';return false;"';
		//~ }

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

	function AddBackUrl(){
		return $this->m_addBackUrl;
	}

	/**
	 *
	 * @see ebase_view::Display()
	 */
	function Display() {

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

	protected function GetReplacedObjTemplate($pTemplate) {
		if($this->m_viewObject){
			return $this->m_viewObject->ReplaceHtmlFields($this->getObjTemplate($pTemplate), $this);
		}
	}

	protected function GetReplacedFormObjTemplate($pTemplate) {
		if($this->m_viewObject){
			return $this->m_viewObject->ReplaceHtmlFormFields($this->getObjTemplate($pTemplate), $this);
		}
	}

}

?>