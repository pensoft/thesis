<?php
/**
 * A base controller class.
 * All other controller class should extend (directly or not) this class.
 *
 * Every controller should at most communicate with the model and the view.
 * No direct db communication is allowed - this communication should be implemented by the model.
 * @author peterg
 *
 */
class ecBase_Controller {
	/**
	 * In this hash we will keep references to all the models
	 * the controller uses.
	 *
	 * @var hash
	 */
	var $m_models;
	/**
	 * A reference to the main view of the page
	 *
	 * @var epPage_View
	 */
	var $m_pageView;
	/**
	 * The definitions of the common objects
	 *
	 * @var unknown_type
	 */
	var $m_commonObjectsDefinitions;

	/**
	 * This variable tells us whether to perform session_write_close in the
	 * constructor or not;
	 *
	 * @var boolean
	 * @see ecBase_Controller::InitSessionCloseVariable()
	 */
	var $m_closeSession;

	/**
	 * Max file size in bytes (Used for checks when uploading files)
	 *
	 * @var int
	 */
	var $m_maxFileSize;

	function __construct() {
		$this->m_models = array();
		$this->InitSessionCloseVariable();
		if($this->m_closeSession){
			session_write_close();
		}
		// 5 MB
		$this->m_maxFileSize = 5 * 1024 * 1024;
	}

	/**
	 * This method inits the $m_closeSession variable.
	 * By default it sets it to true.
	 * If the variable is initted to true the session will be automatically
	 * closed for writing
	 * in the constructor of the controller.
	 * All controllers which don\'t want to close the session should overwrite
	 * this method
	 * and set the variable to false
	 */
	protected function InitSessionCloseVariable() {
		$this->m_closeSession = true;
	}

	function Display() {
		return $this->m_pageView->Display();
	}

	// @formatter->off
	/**
	 * Returns the value associated with key in the $_REQUEST/GET/POST variables
	 *
	 * First checks in the Prefered method. If the key is not found there - a
	 * new search is performed in the oposite method (POST => GET).
	 * If there is still no result, $_REQUEST is searched
	 *
	 * @param $pKey string
	 * @param $pPreferedMethod string
	 *       	 -> the prefered method (if none is given POST is the default)
	 * @param $pValueType -
	 *       	 if no value type is specified, no type checks will be
	 *       	 performed
	 *       	 if value type is specified - the function will return the
	 *       	 result of the CheckValueType method with
	 *       	 the value of the key and the specified type
	 * @param $pIsArray -
	 *       	 whether the value should be an array of fields of the
	 *       	 specified type (array of ints)
	 * @param $pAllowNulls -
	 *       	 whether the value could be null or not
	 * @param $pDateType -
	 *       	 if the type of the field is date what date it is (i.e. date
	 *        	with time, time only ...)
	 *
	 * @return an array in the specified format
	 *         err_cnt => number of errors (in the check type)
	 *         err_msgs => an array containing the error msgs
	 *         value => the value
	 */
	// @formatter->on
	function GetValueFromRequest($pKey, $pPreferedMethod = '', $pValueType = false, $pIsArray = false, $pAllowNulls = false, $pDateType = DATE_TYPE_ALL) {
		$pPreferedMethod = strtoupper($pPreferedMethod);
		if(! in_array($pPreferedMethod, array(
			'GET',
			'POST'
		))){
			$pPreferedMethod = 'POST';
		}
		$lValue = '';
		$lUseStripSlashes = true;
		switch ($pPreferedMethod) {
			case 'GET' :
				if(isset($_GET[$pKey])){
					$lValue = $_GET[$pKey];
					break;
				}
				if(isset($_POST[$pKey])){
					$lValue = $_POST[$pKey];
					break;
				}
				if(isset($_FILES[$pKey])){
					$lUseStripSlashes = false;
					$lValue = array();
					$lValue['FileUp'] = true;
					$lValue['FileName'] = $_FILES[$pKey]['name'];
					$lValue['FileType'] = $_FILES[$pKey]['type'];
					$lValue['FileSize'] = $_FILES[$pKey]['size'];
					$lValue['FileTmpName'] = $_FILES[$pKey]['tmp_name'];
					$lValue['FileError'] = $_FILES[$pKey]['error'];
					break;
				}
				$lValue = $_REQUEST[$pKey];
				break;
			case 'POST' :
				if(isset($_POST[$pKey])){
					$lValue = $_POST[$pKey];
					break;
				}
				if(isset($_GET[$pKey])){
					$lValue = $_GET[$pKey];
					break;
				}
				if(isset($_FILES[$pKey])){
					$lUseStripSlashes = false;
					$lValue = array();
					$lValue['name'] = $_FILES[$pKey]['name'];
					$lValue['type'] = $_FILES[$pKey]['type'];
					$lValue['size'] = $_FILES[$pKey]['size'];
					$lValue['tmp_name'] = $_FILES[$pKey]['tmp_name'];
					$lValue['error'] = $_FILES[$pKey]['error'];
					break;
				}
				$lValue = $_REQUEST[$pKey];
				break;
		}
		// if($pKey == 'test'){
		// var_dump($lValue);
		// }

		if($lUseStripSlashes){
			// Remove the unnecesarry slashes if needed
			if(is_array($lValue)){
				$lValue = array_map("s", $lValue);
			}else{
				$lValue = s($lValue);
			}
		}
		// if($pKey == 'test'){

		// var_dump($lValue);

		// }

		$lResult = array(
			'err_cnt' => 0,
			'value' => $lValue
		);
		if($pValueType == false){
			return $lResult;
		}
		return $this->CheckValueType($lValue, $pValueType, $pIsArray, $pAllowNulls, $pDateType);
	}

	/**
	 * Returns directly the value from the result of the GetValueFromRequest
	 * method without performing checks
	 *
	 * @param $pKey unknown_type
	 * @param $pPreferedMethod unknown_type
	 */
	function GetValueFromRequestWithoutChecks($pKey, $pPreferedMethod = '') {
		$lResult = $this->GetValueFromRequest($pKey, $pPreferedMethod);
		return $lResult['value'];
	}

	// @formatter->off
	/**
	 * Checks whether the specified value is of the specified type
	 *
	 * @param $pValue -
	 *       	 the value to be checked
	 * @param $pValueType -
	 *       	 the type to check for
	 * @param $pIsArray -
	 *       	 whether the value should be an array of fields of the
	 *       	 specified type (array of ints)
	 * @param $pAllowNulls -
	 *       	 whether the value could be null or not
	 * @param $pDateType -
	 *       	 if the type of the field is date what date it is (i.e. date
	 *        	with time, time only ...)
	 *
	 * @return an array in the specified format
	 *         err_cnt => number of errors (in the check type)
	 *         err_msgs => an array containing the error msgs
	 *         value => the value
	 */
	// @formatter->on
	function CheckValueType($pValue, $pType, $pIsArray = false, $pAllowNulls = false, $pDateType = DATE_TYPE_ALL) {

		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'value' => $pValue
		);
		if((is_null($pValue) || $pValue === '') && $pAllowNulls){
			return $lResult;
		}

		if(($pIsArray && is_array($pValue) && ! strlen(implode("", $pValue))) && $pAllowNulls){
			return $lResult;
		}
		// var_dump($pValue);
		try{
			switch ($pType) {
				case "float" :
				case "int" :
				case "mlint" :
					if(is_array($pValue) && $pIsArray){
						foreach($pValue as $k => $v){
							if(is_null($v) || $v === ''){
								if($pAllowNulls)
									continue;
								else{
									throw new Exception(ERR_EMPTY_NUMERIC);
								}
							}
							if(! is_numeric($v)){
								throw new Exception(ERR_NAN);
							}
							$pValue[$k] = (($pType == "float") ? (float) $pValue[$k] : (int) $pValue[$k]);
						}
					}else{
						if(is_null($pValue) || $pValue === ''){
							throw new Exception(ERR_EMPTY_NUMERIC);
						}
						if(! is_numeric($pValue)){
							throw new Exception(ERR_NAN);
						}

						$pValue = (($pType == "float") ? (float) $pValue : (int) $pValue);
					}
					break;
				case "date" :
					if(is_array($pValue) && $pIsArray){
						foreach($pValue as $k => $v){
							$lstrError = manageckdate($pValue[$k], $pDateType);
							if($lstrError){
								throw new Exception($lstrError);
							}
						}
					}else{
						$lstrError = manageckdate($pValue, $pDateType);
						if($lstrError){
							throw new Exception($lstrError);
						}
					}
					break;
				case "xml" :

					if($pIsArray && (is_null($pValue) || ! is_array($pValue) || ! strlen(implode("", $pValue)))){

						throw new Exception(ERR_EMPTY_XML);

					}elseif(! $pIsArray && (is_null($pValue) || $pValue == '')){
						throw new Exception(ERR_EMPTY_XML);
					}
					$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
					if($pIsArray){
						foreach($pValue as $lCurrentXml){
							if(!$lDom->loadXML($lCurrentXml)){
								throw new Exception(ERR_WRONG_XML);
							}
						}

					}else{
						error_reporting(-1);
						if(!$lDom->loadXML($pValue)){
							throw new Exception(ERR_WRONG_XML);
						}
						error_reporting(0);
					}
					break;
				case "string" :
				case "mlstring" :
					if($pIsArray && (is_null($pValue) || ! is_array($pValue) || ! strlen(implode("", $pValue)))){

						throw new Exception(ERR_EMPTY_STRING);

					}elseif(! $pIsArray && (is_null($pValue) || $pValue == '')){
						throw new Exception(ERR_EMPTY_STRING);
					}
					break;
				default :
					break;
			}
			$lResult['value'] = $pValue;
		}catch(Exception $pException){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => $pException->getMessage()
			);
		}
		return $lResult;
	}

	// @formatter->off
	/**
	 * Upload a pic
	 *
	 * @param $pFileKey string
	 *       	 - the key under which to search for the file in $_FILES
	 *
	 * @return s an array in the following format
	 *         err_cnt => number of errors (if any)
	 *         err_msgs => an array containing the error msgs
	 *         photo_id => the id
	 */
	// @formatter->on
	function UploadPhoto($pFileKey, $pTitle = '', $pDescription = '') {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array(),
			'photo_id' => 0
		);
		$lFilesModel = new mFiles_Model();
		$lPicId = 0;
		$lUploadHasErrors = true;
		try{
			$lFileData = $_FILES[$pFileKey];
			$lFileExt = substr($lFileData['name'], strrpos($lFileData['name'], '.'));
			$lFileSize = $lFileData['size'];
			$lFileName = $lFileData['name'];
			if($pTitle){
				$pTitle = $lFileName;
			}

			if(! $lFileData['name']){
				throw new Exception(getstr('global.noFileSelected'));
			}

			if($lFileSize > $this->m_maxFileSize){
				throw new Exception(getstr('global.fileTooBig'));
			}
			if(! $lFileSize){
				throw new Exception(getstr('global.invalidFileWithoutSize'));
			}
			if($lFileData['error'] != UPLOAD_ERR_OK){
				throw new Exception(getstr('global.fileCouldNotBeUploaded'));
			}

			$lDbRes = $lFilesModel->GetNewPicId($pTitle, $pDescription, $lFileName, $lFileSize);
			if($lDbRes['err_cnt']){
				throw new Exception($lDbRes['err_msgs'][0]['err_msg']);
			}
			$lPicId = $lDbRes['id'];
			if(! move_uploaded_file($lFileData['tmp_name'], PATH_DL . $lPicId . $lFileExt)){
				var_dump(PATH_DL . $lPicId . $lFileExt);
				throw new Exception(getstr('global.couldNotMoveUploadedFile'));
			}
			
			$f =  PATH_DL . $lPicId . $lFileExt;
			$f_big = PATH_DL . 'big_' . $lPicId;
			$f_oo =  PATH_DL . 'oo_' . $lPicId;
			$lCommand = "-colorspace sRGB -thumbnail " . escapeshellarg('1024x1024>');
			$lCommand_oo = "-colorspace sRGB";
			
			//~ exec("convert -colorspace rgb -quality 100 -thumbnail " . escapeshellarg('1024x1024>') . " " . PATH_DL . $lPicId . $lFileExt . " " . PATH_DL . 'big_' . $lPicId . '.png');
			//~ exec("convert -colorspace rgb -quality 100 -thumbnail " . escapeshellarg('1024x1024>') . " " . PATH_DL . $lPicId . $lFileExt . " " . PATH_DL . 'big_' . $lPicId . '.jpg');
			//~ exec(escapeshellcmd("convert -colorspace rgb -quality 100 " . PATH_DL . $lPicId . $lFileExt . " " . PATH_DL . 'oo_' . $lPicId . '.png'));
			//~ exec(escapeshellcmd("convert -colorspace rgb -quality 100 " . PATH_DL . $lPicId . $lFileExt . " " . PATH_DL . 'oo_' . $lPicId . '.jpg'));
			
			//~ trigger_error("AAAAAAAA". $lCommand, E_USER_NOTICE);

			
			executeConsoleCommand('convert', array_merge(array($f), array($lCommand), array($f_big . ".jpg")));
			executeConsoleCommand('convert', array_merge(array($f), array($lCommand), array($f_big . ".png")));
			executeConsoleCommand('convert', array_merge(array($f), array($lCommand_oo), array($f_oo . ".jpg")));
			executeConsoleCommand('convert', array_merge(array($f), array($lCommand_oo), array($f_oo . ".png")));
			
			unlink(PATH_DL . $lPicId . $lFileExt);
			$lUploadHasErrors = false;
			$lResult['photo_id'] = $lPicId;

		}catch(Exception $pException){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => $pException->getMessage()
			);
		}
		if($lUploadHasErrors && $lPicId){
			$lFilesModel->DeletePic($lPicId);
		}

		return $lResult;
	}

	// @formatter->off
	/**
	 * Deletes the specified photo from the db
	 *
	 * returns an array with the following format (
	 * err_cnt => number of errors
	 * err_msgs => an array containing the error msgs (an array containing
	 * arrays with the following format
	 * err_msg => the msg of the current error
	 * )
	 * )
	 */
	function DeletePic($pPicId) {
		$lResult = array(
			'err_cnt' => 0,
			'err_msgs' => array()
		);
		$lCon = $this->m_con;
		$lSql = 'SELECT PicsUpload(3, ' . (int) $pPicId . ', null, null, null, null);';

		if(! $lCon->Execute($lSql)){
			$lResult['err_cnt'] ++;
			$lResult['err_msgs'][] = array(
				'err_msg' => getstr($lCon->GetLastError())
			);
		}
		return $lResult;
	}

	function Redirect($pUrl = '/index.php', $pSilent = false, $pContentType = '', $pContentDisposition = ''){
		if($pSilent){			
			if($pContentType){
				header('Content-Type: ' . $pContentType);
			}
			if($pContentDisposition){
				header('Content-Disposition: ' . $pContentDisposition);
			}
			echo file_get_contents($pUrl);
		}else{
			header('Location: ' . $pUrl);
		}
		exit;
	}

}

?>