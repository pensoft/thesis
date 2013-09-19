<?php

/**@formatter:off
 *
 * A base view class.
 * All other view classes (if any) should extend (directly or not) this class.
 *
 * @author peterg
 *
 */
class epPage_View extends ebase {

	/**
	 * @formatter:off
	 * The metadata for all the objects
	 *
	 * The format of the object should be the following
	 * object_name => object_metadata
	 *
	 * ObjectMetadata is an array containing the metadata for the specific
	 * object
	 * This array contains the object templates under the templs key.
	 *
	 *
	 * The format of the object templates is the following
	 * template_key => template_path
	 *
	 * (e.g
	 * 		$this->m_objectsMetadata = array(
	 * 			'stories' => array(
	 * 				templs(
	 * 					G_HEADER => 'stories.head'
	 * 					G_ROWTEMPL => 'stories.row',
	 * 					G_FOOTER => 'stories.foot',
	 * 				)
	 * 			),
	 * 		)
	 * )
	 * @formatter:on
	 * @var array
	 */
	protected $m_objectsMetadata;

	/**
	 * @formatter:off
	 * The templates for the view object
	 *
	 * The format of the object templates is the following
	 * template_key => template_path
	 *
	 * (e.g
	 * 		$this->m_Templs = array(
	 * 			G_HEADER => 'stories.head'
	 * 			G_ROWTEMPL => 'stories.row',
	 * 			G_FOOTER => 'stories.foot',
	 * 		)
	 * )
	 *	@formatter:on
	 * @var array
	 */
	protected $m_Templs;

	/**
	 * This is an array containing the default templates to be used
	 * when there are no specific templates for any object or
	 * when we need a template with key which is not defined in the object
	 * templates
	 *
	 * @var array
	 */
	protected $m_defTempls;

	function __construct($pData) {
		$this->m_pubdata = $pData;
		$this->m_Templs = $pData['templs'];
		$this->m_objectsMetadata = $pData['objects_metadata'];
	}

	/**
	 * Sets the templates for the object with the specified name
	 *
	 * @param $pObjectName unknown_type
	 * @param $pObjectTempls unknown_type
	 */
	function setObjectTempls($pObjectName, $pObjectTempls) {
		if(is_array($pObjectTempls)){
			if(! is_array($this->m_objectsMetadata[$pObjectName])){
				$this->m_objectsMetadata[$pObjectName] = array();
			}
			$this->m_objectsMetadata[$pObjectName]['templs'] = $pObjectTempls;
		}

	}

	/**
	 * Changes the default templates to the ones passed as an argument
	 *
	 * @param $pDefTempls unknown_type
	 */
	function setDefaultTempls($pDefTempls) {
		if(is_array($pDefTempls)){
			$this->m_defTempls = $pDefTempls;
		}
	}

	function ReplaceHtmlFields($pStr, $pObject) {
		//~ $lCallBack = function($Matches){
			//~ return $this->HtmlPrepare($Matches[1], $pObject);
		//~ };
// 		return preg_replace_callback("/\{(.*?)\}/", $lCallBack, $pStr);
		return preg_replace("/\{(.*?)\}/e", "\$this->HtmlPrepare('\\1', \$pObject)", $pStr);
	}

	function ReplaceHtmlFormFields($pStr, $pObject) {
		//~ $lCallBack = function($Matches){
			//~ return $this->HtmlPrepareForm($Matches[1], $pObject);
		//~ };
		// 		return preg_replace_callback("/\{(.*?)\}/", $lCallBack, $pStr);
		return preg_replace("/\{(.*?)\}/e", "\$this->HtmlPrepareForm('\\1', \$pObject)", $pStr);
	}

	function DisplayObjectTemplate($pTemplId, $pObject) {
		$lTemplate = $this->getTemplate($pTemplId);
		return $this->ReplaceHtmlFields($lTemplate, $pObject);
	}

	function EvalHtmlTemplateFunction($pName, $pObject, $pObjectIsForm = false) {
		if(! preg_match('/^\_(.*)\((.*)\)$/', $pName, $lMas)){
			return '';
		}
		$lFuncname = $lMas[1];

		//~ $lFuncParams = split(',', stripslashes($lMas[2]));
		$lFuncParams = preg_split('/,/', stripslashes($lMas[2]));


		$lStrFuncParams = '';
		$lNotExportedParamReferences = array();
		foreach($lFuncParams as $k => $v){
			$lParamName = trim($v);
			$lExportParam = true;
			if(! $pObjectIsForm){



				if($pObject->ValExists($lParamName)){
					$lObjectItem = $pObject->GetVal($lParamName);
					// 			var_dump($pName, $lObjectItem);
					if($lObjectItem){
						if(is_object($lObjectItem) && is_subclass_of($lObjectItem, 'epPage_View')){														
							$lExportParam = false;
							$lNotExportedParamReferences[$lParamName] = $lObjectItem;
						}elseif(is_object($lObjectItem) && is_subclass_of($lObjectItem, 'ebase')){

							// If the item is an object - we get its display result
							// If the item doesnt have a view object - we add this
							// viewing object
							if(! $lObjectItem->getViewObject()){
								$lObjectItem->setViewObject($this);
							}
							$lObjectParamValue = $lObjectItem->DisplayC();

						}elseif(is_array($lObjectItem)){
							// We treat arrays as object definitions
							if($lObjectItem['ctype']){
								if(! $lObjectItem['ob']){
									$lObjectItem['ob'] = new $lObjectItem['ctype']($lObjectItem);
								}// If the item doesnt have a view object - we add this
								// viewing object
								if(! $lObjectItem['ob']->getViewObject()){
									$lObjectItem['ob']->setViewObject($this);
								}
								$lObjectParamValue = $lObjectItem['ob']->DisplayC();
							}else{
								$lObjectParamValue = $lObjectItem;
							}

						}elseif(is_scalar($lObjectItem)){
							$lObjectParamValue = $lObjectItem;

						}else{
							$lObjectParamValue = '';
						}
					}else{
						$lObjectParamValue = $pObject->GetVal($lParamName);
					}



				}else{//If there is no such named value in the object use the name as value
					$lObjectParamValue = $lParamName;
				}

			}else{
				//First check in the fields
				if($pObject->CheckIfFieldExists($lParamName)){
					$lObjectParamValue = $pObject->GetFieldValue($lParamName);
				}elseif($pObject->ValExists($lParamName)){//After that check in the pubdata
					$lObjectParamValue = $pObject->GetVal($lParamName);
				}else{//If there is no such named value in the object use the name as value
					$lObjectParamValue = $lParamName;
				}
			}
// 			if($lFuncname == 'DisplayCommentAnswerForm'){
// 				$lForms = $pObject->GetVal('comment_reply_forms');
// 				var_dump($lForms[1]);
// // 				var_dump($lForms[1]->Display());
// 				exit;
// 			}
			if($lExportParam){
				$lStrFuncParams .= var_export($lObjectParamValue, true) . ',';
			}else{
				$lStrFuncParams .= '$lNotExportedParamReferences[\'' . $lParamName . '\'],';
			}
			
		}
		if($lStrFuncParams){
			$lStrFuncParams = substr($lStrFuncParams, 0, - 1);
		}
		$evalstr = 'return ' . $lFuncname . '(' . $lStrFuncParams . ');';	
// 		var_dump($evalstr);	
		return eval($evalstr);
	}

	function EvalHtmlTemplateMethod($pName, $pObject) {
		if(! preg_match('/^\$(.*)\((.*)\)$/', $pName, $lMas)){
			return '';
		}
		$lFuncname = $lMas[1];
		$lFuncParams = split(',', stripslashes($lMas[2]));
		$lStrFuncParams = '';

		foreach($lFuncParams as $k => $v){
			$lParamName = trim($v);
			$lObjectParamValue = $pObject->GetVal($lParamName);

			if($lObjectParamValue){
				$lStrFuncParams .= var_export($lObjectParamValue, true) . ',';
			}else{
				$lStrFuncParams .= var_export($lParamName, true) . ',';
			}
		}
		if($lStrFuncParams){
			$lStrFuncParams = substr($lStrFuncParams, 0, - 1);
		}
		$evalstr = 'return $pObject->' . $lFuncname . '(' . $lStrFuncParams . ');';
		return eval($evalstr);
	}

	/**
	 * Returns the string which will replace the passed named text
	 * in a template in context of the passed object
	 *
	 * @param $pName string
	 *       	 - the text that should be replaced
	 * @param $pObject cbase
	 *       	 - the context object
	 */
	function HtmlPrepare($pName, $pObject) {
		global $gAntetsArr; // tva e nai-globalnia masiv s antetkite
		$lRetStr = '';
		if($pName[0] == '_'){
			$lRetStr = $this->EvalHtmlTemplateFunction($pName, $pObject, false);
		}elseif($pName[0] == '$'){
			$lRetStr = $this->EvalHtmlTemplateMethod($pName, $pObject);
		}else if($pName[0] == '*'){
			$lRetStr = $this->DisplayObjectTemplate(mb_substr($pName, 1), $pObject);
		}else if($pName[0] == '%'){ // antetka
			if(! $_SESSION['glang'])
				$_SESSION['glang'] = 1; // ako se polzva ot
					                        // drug site(naprimer ot
					                        // administraciata) da ne
					                        // predavame ezik
			$lRetStr = $gAntetsArr[substr($pName, 1)];
		}else{

			$lObjectItem = $pObject->GetVal($pName);

			if($pName == 'form'){
				$lDebug = 1;
// 				var_dump($lObjectItem);
			}
			if(isset($lObjectItem)){
				if(is_object($lObjectItem) && is_subclass_of($lObjectItem, 'ebase')){
					// If the item is an object - we get its display result
					// If the item doesnt have a view object - we add this
					// viewing object
					if(! $lObjectItem->getViewObject()){
						$lObjectItem->setViewObject($this);
					}

					if(isset($lDebug)){
// 						var_dump($lObjectItem->getViewObject());
					}
					$lRetStr = $lObjectItem->DisplayC();
				}elseif(is_array($lObjectItem)){
					// We treat arrays as object definitions
					if(! isset($lObjectItem['ob'])){
						var_dump($lObjectItem, $pName);
						$lObjectItem['ob'] = new $lObjectItem['ctype']($lObjectItem);
					}
						// If the item doesnt have a view object - we add this
					// viewing object
					if(! $lObjectItem['ob']->getViewObject()){
						$lObjectItem['ob']->setViewObject($this);
					}
// 					if($lDebug){
// 						var_dump($lObjectItem);
// 					}
					$lRetStr = $lObjectItem['ob']->DisplayC();

				}elseif(is_scalar($lObjectItem)){

					$lRetStr = $lObjectItem;

				}else{

					$lRetStr = '';
				}
			}
		}

		return $lRetStr;
	}

	/**
	 * Returns the string which will replace the passed field text
	 * in a form template in context of the passed object
	 *
	 * Here only 3 types replacement is performed
	 * 1 -> function with the field
	 *
	 * @param $pName string
	 *       	 - the text that should be replaced
	 * @param $pObject cbase
	 *       	 - the context object
	 */
	function HtmlPrepareForm($pName, evForm_View $pObject) {
		global $gAntetsArr; // tva e nai-globalnia masiv s antetkite
		$lRetStr = '';
		if($pName[0] == '_'){ // Function eval (the parameters are the form field values)
			$lRetStr = $this->EvalHtmlTemplateFunction($pName, $pObject, true);
		}else if($pName[0] == '*'){ // Returns the title of the field
			$lRetStr = $pObject->GetFieldTitle(mb_substr($pName, 1));
		}else if($pName[0] == '@'){ // Returns the viewmode template for the field
			$lRetStr = $pObject->GetFieldTemplate(mb_substr($pName, 1), 1);
		}else if($pName[0] == '!'){ // Returns the errors for the field
			$lRetStr = $pObject->GetFieldErrorsTemplate(mb_substr($pName, 1));
		}else if($pName[0] == '~'){ // Returns all the errors for the form
			if($pName == '~~'){//All the fields errors
				$lRetStr = $pObject->GetAllFieldsErrorsTemplate();
			}else{//Return all the global errors
				$lRetStr = $pObject->GetGlobalErrorsTemplate();
			}
		}else if($pName == '&captcha&'){ // Returns the captcha
			$lRetStr = $pObject->GetCaptchaTemplate();
		}else if($pName[0] == '^'){ // Replace with the item from the pubdata - this is no field
			$pName = mb_substr($pName, 1);

			$lObjectItem = $pObject->GetVal($pName);
// 			var_dump($pName, $lObjectItem);

			if($lObjectItem){
				if(is_object($lObjectItem) && is_subclass_of($lObjectItem, 'epPage_View')){				
					$lRetStr = $lObjectItem;
				}else if(is_object($lObjectItem) && is_subclass_of($lObjectItem, 'ebase')){

					// If the item is an object - we get its display result
					// If the item doesnt have a view object - we add this
					// viewing object
					if(! $lObjectItem->getViewObject()){
						$lObjectItem->setViewObject($this);
					}
					$lRetStr = $lObjectItem->DisplayC();
				}elseif(is_array($lObjectItem)){
					// We treat arrays as object definitions
					if(! $lObjectItem['ob'])
						$lObjectItem['ob'] = new $lObjectItem['ctype']($lObjectItem);
						// If the item doesnt have a view object - we add this
					// viewing object
					if(! $lObjectItem['ob']->getViewObject()){
						$lObjectItem['ob']->setViewObject($this);
					}
					$lRetStr = $lObjectItem['ob']->DisplayC();

				}elseif(is_scalar($lObjectItem)){
					$lRetStr = $lObjectItem;

				}else{
					$lRetStr = '';
				}
			}
		}else{ // Get field template
			$lRetStr = $pObject->GetFieldTemplate($pName);
		}

		return $lRetStr;
	}

	/**
	 * Returns the content of a template with the parsed name
	 *
	 * @param $pTemplName unknown_type
	 */
	function getTemplate($pTemplName) {
		global $gTemplatesArray;
		if($gTemplatesArray){
			if(array_key_exists($pTemplName, $gTemplatesArray)){
				return $gTemplatesArray[$pTemplName];
			}
		}

		$lTmp = '';
		$lArr = array();
		if(strstr($pTemplName, '.')){
			$lArr = explode('.', $pTemplName);
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

		$lFileName = PATH_CLASSES . $lSiteName . '/templates/' . $lTmp . '.php';

		if(file_exists($lFileName)){
			require_once ($lFileName);
		}elseif(file_exists(PATH_CLASSES . '/templates/' . $lTmp . '.php')){
			require_once (PATH_CLASSES . '/templates/' . $lTmp . '.php');
		}else{
			trigger_error("The file <b>\"$lFileName\"</b> does not exist [$pTemplName] !!!" . "\n", E_USER_NOTICE);
		}

		if(! is_array($gTemplArr)){
			$gTemplArr = array();
		}

		foreach($gTemplArr as $k => $v){
			$gTemplatesArray[$lExtSite . $k] = $v;
		}

		if(! array_key_exists($pTemplName, $gTemplatesArray)){
			trigger_error("<b>\"$pTemplName\"</b> not found !!!" . "\n", E_USER_NOTICE);
		}

		return $gTemplatesArray[$pTemplName];
	}

	/**
	 * Returns the content of the template which is associated under name
	 * $pTemplId for the object with the specified name
	 * If no name is provided - the view object is assumed
	 * for the specified object
	 *
	 * @param $pTemplId unknown_type
	 * @param $templadd unknown_type
	 * @param $pObject unknown_type
	 */
	function getObjectTemplate($pTemplId, $templadd = "", $pObjectName = '') {
		if($pObjectName != ''){
			$lObjectTemplates = $this->m_objectsMetadata[$pObjectName]['templs'];
		}else{
			$lObjectTemplates = $this->m_Templs;
		}
		$lObjectDefaultTemplates = $this->m_defTempls;
		if(! is_array($lObjectTemplates) || ! array_key_exists($pTemplId, $lObjectTemplates)){
			if(is_array($lObjectDefaultTemplates)){
				if(! array_key_exists($pTemplId, $lObjectDefaultTemplates)){
// 					var_dump($this->m_Templs[G_BROWSE_TEMPLATE], $pObjectName, $lObjectTemplates, $pTemplId);
// 					echo "\n<br/>";
					trigger_error("Template " . $pTemplId . " does not exist in the object.");
				}else{
					return $this->getTemplate($lObjectDefaultTemplates[$pTemplId] . $templadd);
				}
			}
		}else{
			return $this->getTemplate($lObjectTemplates[$pTemplId] . $templadd);
		}
	}

	public function ProcessData() {
		// TODO Auto-generated method stub

	}

	/**
	 * Sets the content type

	 * @param $pContentType text - content type
	 */
	public function SetPageContentType($pContentType = 'text/html') {
		header("Content-Type: " . $pContentType, true);
	}

	/*
	 * (non-PHPdoc) @see cbase::Display()
	 */
	public function Display() {
		return $this->ReplaceHtmlFields($this->getObjectTemplate(G_DEFAULT), $this);
	}

}

?>