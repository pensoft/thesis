<?php //CVS: Opraveno vyv VIEW Mode da se display-va None vmesto -- None -- i podobnite; kakto i pipano po History-to. Triabva da se dooptimiazira

require_once(PATH_ECMSFRCLASSES . 'static.php');
require_once(PATH_ECMSFRCLASSES . 'lang.php');
require_once(PATH_ECMSFRCLASSES . 'adodb-time.inc.php');
require_once(PATH_ECMSFRCLASSES . 'kforhist.php');
require_once(PATH_ECMSFRCLASSES . 'fckeditor.php') ;


define("ACTION_CHECK", 1);		// checkva samo dali tipovete na promenlivite sa si takiva kakvito trjabva
define("ACTION_CCHECK", 2);		// checkva i ostanalite custom checkove
define("ACTION_EXEC", 4);		// execute-va sql-a
define("ACTION_FETCH", 8);		// fetch-va resultatite ot sql-a
define("ACTION_SHOW", 16);		// pokajva formata
define("ACTION_REDIRECT", 32);	// redirect-va kum urlto na action-a
define("ACTION_REDIR", 32);	// redirect-va kum urlto na action-a (alias na REDIRECT)
define("ACTION_VIEW", 64);	// display-va formata vyv view mode
define("ACTION_REDIRVIEW", 128);	// display-va formata vyv view mode
define("ACTION_REDIRERROR", 256);	// redirva-va formata vyv view mode ako e imalo error na actiona i displayva errora

//Date Type 1 = wrong time, 2 wrong date, 3 wrong datetime
define("DATE_TYPE_ALL", 0); //Date zaduljitelno + time (ne zaduljitelno)
define("DATE_TYPE_TIME", 1); //Samo time puska
define("DATE_TYPE_DATE", 2); //Samo date puska
define("DATE_TYPE_DATETIME", 3); //Trqbva zaduljitelno i date i time


// define za greshkite
/*define("ERR_WRONG_TIME", getstr('kfor.wrongTimeErr') );
define("ERR_WRONG_DATE", getstr('kfor.wrongDateErr'));
define("ERR_WRONG_DATETIME", getstr('kfor.wrongDateTimeErr'));

define("ERR_EMPTY_NUMERIC",getstr('kfor.emptyNumericErr'));
define("ERR_EMPTY_STRING", getstr('kfor.emptyStringErr'));
define("ERR_NAN", getstr('kfor.nanErr'));
*/
define("ERR_WRONG_TIME", getstr('kfor.errRequiredField'));
define("ERR_WRONG_DATE", getstr('kfor.wrongDateErr'));
define("ERR_WRONG_DATETIME", getstr('kfor.errRequiredField'));

define("ERR_EMPTY_NUMERIC", getstr('kfor.emptyNumericErr'));
define("ERR_EMPTY_STRING", getstr('kfor.emptyStringErr'));
define("ERR_NAN", getstr('kfor.nanErr'));

define("PRIMARY_KEY", 1);	// display-va formata vyv view mode


define("MANY_TO_MANY", 1);
define("MANY_TO_STRING", 2);
define("MANY_TO_BIT", 3);
define("MANY_TO_BIT_ONE_BOX", 4);
define("MANY_TO_SQL_ARRAY", 5);

// tva e DEFAULTEN separator mejdu radio i checkbox
define("DEF_CONTR_SEP", '<br />');

// tva e defaulten separator mejdu valuetata pri MANY_TO_STRING conversiata
define("DEF_SQLSTR_SEPARATOR", ",");
define("DEF_EXT_ERROR_STRING", 'style="background-image: url(/images/req/20x20_red_non-req.gif);"');

// tva e za mlstring DisplayFormat
define('MLSTR_D_NOTABLE',1);

// za FCK editor
define('FCK_BASIC_TOOLS', 1);
define('FCK_ALL_TOOLS', 2);

function AddParamtoURL($url,$param) {
        if (!strlen($param)) return $url;
	if (substr($param,-1)=='=') return $url;
        if (strpos($url,'?')===FALSE)
		return $url.'?'.$param;
	else return $url.'&'.$param;
}

function CKMAXSTRLEN($pFld, $pMax) {
	return array('Expr' => 'mb_strlen(' . $pFld . ', "UTF-8") > ' . $pMax . '', 'ErrStr' => getstr('CKMAXSTRLEN') . $pMax . getstr('SYMB'));
}

function CKMINSTRLEN($pFld, $pMax) {
	return array('Expr' => 'mb_strlen(' . $pFld . ', "UTF-8") < ' . $pMax . '', 'ErrStr' => getstr('CKMINSTRLEN') . $pMax . getstr('SYMB'));
}

//~ function CKEMAILADDR($pFld) {
	//~ return array("Expr" => '!ereg("^[A-Za-z0-9_\.-\+]+@([A-Za-z0-9_\-])+(\.([A-Za-z0-9_\-])+)+$", ' . $pFld . ')', 'ErrStr' => 'CKEMAILADDR');
//~ }

function CKEMAILADDR($pFld) {
	return array("Expr" => '!preg_match("/^[A-Za-z0-9_\.-]+@([A-Za-z0-9_\.-])+\.[A-Za-z]{2,6}$/", ' . $pFld . ')', 'ErrStr' => 'CKEMAILADDR');
}

function CKPASSWORD($pFld) {
	return array('Expr' => 'preg_match("/[^A-Za-z0-9\_\.\-\|\~\!\@\#\$\%\^\&\*\(\)\+\=\\\[\]\;\:\,\/\<\>\?]/", ' . $pFld . ')', 'ErrStr' => 'CKPASSWORD');
}

function CKUSERNAME($pFld) {
	return array('Expr' => 'preg_match("/[^A-Za-z0-9\_\.\-\|]/", ' . $pFld . ')', 'ErrStr' => 'CKUSERNAME');
}

class kfor {
	var $lFieldArr;
	var $lFormName;
	var $lFormHtml;
    var $lvFormHtml;
	var $lFormMethod;
	var $lErrorCount;
	var $lCurAction;
	var $lExecFlag;
	var $debug;
	var $StopErrDisplay;
	var $RetErrStr;

	var $lAddbackurl;
	var $lExtErrorString;
	var $lFormAction;
	var $lJsValidate;
	var $lPathFields;
	var $lKeyFields;

	function kfor($pFieldTempl, $pFormHtml = null, $pMethod = "POST", $pvFormHtml = null, $pAddbackurl = 1, $pSetFormName = null, $pJsValidation = 0, $pPathFields = array()) {

		if (!$pSetFormName) {
		$globalformcount = getglobalformnumber();
		$this->lFormName = "def" . $globalformcount;
		$globalformcount++;
		} else {
			$this->lFormName = $pSetFormName;
		}

		$this->lFieldArr = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->lFormMethod = $pMethod;
		$this->lFormHtml = $pFormHtml;
		$this->lvFormHtml = $pvFormHtml;
		$this->lErrorCount = 0;
		$this->StopErrDisplay = 0;
		$this->RetErrStr = '';
		$this->lJsValidate = (int)$pJsValidation;
		$this->lPathFields = (array_key_exists('path', $pPathFields) ? $pPathFields['path'] : '');
		$this->lKeyFields = (array_key_exists('key', $pPathFields) ? $pPathFields['key'] : '');

		$this->lExtErrorString=DEF_EXT_ERROR_STRING;
		$this->lAddbackurl=$pAddbackurl;

		if (!$this->GetFormValue('kfor_name') || ($this->GetFormValue('kfor_name') == $this->lFormName)) {
			$gCurAction = $this->GetFormValue('tAction');
		}
		if ($gCurAction == '') {
			$gCurAction = 'new';
		}

		if (!is_array($this->lFieldArr[$gCurAction])) {
			if ($gCurAction == 'new') {
					// XXX : tuk sami si praim defaultniat action!!!
					$this->lFieldArr["new"] = array( "CType" => "action", "ActionMask" => ACTION_SHOW, "Hidden" => true );
			} else {
					foreach ($this->lFieldArr as $k => $v) {
						if ($gCurAction == $v["DisplayName"] && $v["CType"] == "action") {
								$gCurAction = $k;
								break;
						}
					}
					if (!is_array($this->lFieldArr[$gCurAction])) trigger_error("$gCurAction is not valid action.", E_USER_ERROR);
			}
		} else {
			if ($this->lFieldArr[$gCurAction]["CType"] != "action") {
				trigger_error("$gCurAction is not valid action.", E_USER_ERROR);
			}
		}

		$this->lFieldArr['kfor_name'] = array('CType' => 'hidden', 'VType' => 'string', 'DefValue' => $this->lFormName);

		$this->lCurAction = $gCurAction;

		if ($this->lAddbackurl) {
			if (!is_array($this->lFieldArr["backurl"]))
				$this->lFieldArr["backurl"]=array("VType" => "string","CType" => "hidden");
			else {
				$this->lFieldArr["backurl"]["VType"] = "string";
				$this->lFieldArr["backurl"]["CType"] = "hidden";
			}
			if (!is_array($this->lFieldArr["selfurl"]))
				$this->lFieldArr["selfurl"]=array("VType" => "string","CType" => "hidden");
			else {
				$this->lFieldArr["selfurl"]["VType"] = "string";
				$this->lFieldArr["selfurl"]["CType"] = "hidden";
			}
		}

		$this->CheckActionMask();
		$this->FillFormValues();
		$this->lFieldArr['kfor_name']['CurValue'] = $this->lFormName;

		if ($this->lAddbackurl) $this->Setselfurl();
	}

	function Setselfurl() {
		global $forwardurl,$selfurl;
		 if  ($_POST["selfurl"]) $selfurl=$_POST["selfurl"];
		 else if  ($_REQUEST["selfurl"]) $selfurl=$_REQUEST["selfurl"];
		 if ($this->lFormMethod=='POST') {
//				 var_dump($forwardurl);
			 $forwardurl=ClearParaminURL($forwardurl,"tAction");

			 $forwardurl=AddParamtoURL($forwardurl,'tAction='. $this->lCurAction);
			 foreach ($this->lFieldArr as $kk => $vv) {
					if ($vv['PK']) {
						   $forwardurl=ClearParaminURL($forwardurl,$kk);
						   $forwardurl=AddParamtoURL($forwardurl,$kk.'='.$this->lFieldArr[$kk]['CurValue']);
					}
			}
			//echo "<p>POST - forlardurl - $forwardurl<P>";
		 }
		 if (!CheckSameUrl($forwardurl,$selfurl) || ($selfurl==''))  {

			   //tozi if mozhe da se maha kogato ne e neobhodim zashtoto izliza ot koncepciata no trebe da se opravi i Redirect
			   //if (preg_match("/tAction=([^\&\?]+)/",$forwardurl,$match)) {

			if (!($this->lFieldArr[$this->lCurAction]['ActionMask'] & ACTION_VIEW))
				   //$selfurl=ClearParaminURL(getenv('REQUEST_URI'),"tAction");
				   //$selfurl =AddParamtoURL($selfurl,"tAction=".$this->lCurAction);
				   $selfurl ='';
			else $selfurl=$forwardurl;

		}
		$forwardurl=ClearParaminURL($forwardurl,"selfurl");
		$selfurl=ClearParaminURL($selfurl,"backurl");
		$selfurl=ClearParaminURL($selfurl,"selfurl");
		$forwardurl=AddParamtoURL($forwardurl,'selfurl='. urlencode($selfurl));
		$this->lFieldArr['selfurl']['CurValue']=$selfurl;

		if (!$this->lFieldArr["backurl"]['CurValue'] )  $this->lFieldArr["backurl"]['CurValue']=$forwardurl;
		$this->lFieldArr["selfurl"]['CurValue']=$selfurl;
	}

	function CheckActionMask() {
		$lMask = $this->lFieldArr[$this->lCurAction]["ActionMask"];

		if (($lMask & ACTION_FETCH) && !($lMask & ACTION_EXEC)) {
			trigger_error("Cannot ACTION_FETCH if there is no ACTION_EXEC for action $this->lCurAction", E_USER_ERROR);
			return false;
		}

		if (($lMask & ACTION_EXEC) && !($lMask & ACTION_CHECK)) {
			trigger_error("Cannot ACTION_EXEC if there is no ACTION_CHECK for action $this->lCurAction", E_USER_ERROR);
			return false;
		}

		if (($lMask & ACTION_SHOW) && ($lMask & ACTION_REDIRECT)) {
			trigger_error("Cannot use both ACTION_SHOW and ACTION_REDIRECT for action $this->lCurAction", E_USER_ERROR);
			return false;
		}

		if (($lMask & ACTION_CCHECK) && !($lMask & ACTION_CHECK)) {
			trigger_error("Cannot use ACTION_CCHECK without ACTION_CHECK for action $this->lCurAction", E_USER_ERROR);
			return false;
		}
	}

	function GetFormValue($pFieldName) {
		$isaction = ($pFieldName == 'tAction' ? true : false);
		switch ($this->lFormMethod) {
			case "GET":
				$t = $_REQUEST[$pFieldName];
				break;
			case "POST":
				$t = $_POST[$pFieldName];
				if (!isset($t)) $t = $_REQUEST[$pFieldName];
				break;
			default:
				$t = $_POST[$pFieldName];
				if (!isset($t)) $t = $_REQUEST[$pFieldName];
		}
		if (is_array($t)) {
			return array_map("s", $t);
		} else {
			if ($isaction && preg_match('/.*tAction=###(.*)###.*/', $t, $mtch)) {
				if ($mtch[1]) $t = $mtch[1];
			}
			return s($t);
		}
	}

	/**
	 * ShowFieldValue($pField)
	 * $pField - pole nad koeto 6te se izvyr6i deiistvieto
	 *
	 * Tazi funkciq se vika ako ima podadeno property 'add_format_func', eval-va kakvoto e podadeno na tova property i prisvoqva
	 * rezultata ot eval na konkretnoto pole (izpolzva se za nqkakvi formatiraniq na stoiinostta)
	 *
	 * */
	function ShowFieldValue($pField) {
		if ($this->lFieldArr[$pField]['add_format_func']) {
			$evalStr = 'return ' . preg_replace("/\{([a-z].*?)\}/e", "\$this->EvalPrepare('\\1')", $this->lFieldArr[$pField]['add_format_func']) . ';';

			$lRes = eval($evalStr);
			if ($lRes) {
				$this->lFieldArr[$pField]['CurValue'] = $lRes;
			}
		}
	}

	/**
	 * RemoveFormatFieldValue($pField)
	 * $pField - pole nad koeto 6te se izvyr6i deiistvieto
	 *
	 * Tazi funkciq se vika ako ima podadeno property 'remove_format_func', eval-va kakvoto e podadeno na tova property i prisvoqva
	 * rezultata ot eval na konkretnoto pole (izpolzva se za nqkakvi formatiraniq na stoiinostta)
	 *
	 * */
	function RemoveFormatFieldValue($pField) {
		if ($this->lFieldArr[$pField]['remove_format_func']) {
			$evalStr = 'return ' . preg_replace("/\{([a-z].*?)\}/e", "\$this->EvalPrepare('\\1')", $this->lFieldArr[$pField]['remove_format_func']) . ';';

			$lRes = eval($evalStr);
			if ($lRes) {
				$this->lFieldArr[$pField]['CurValue'] = $lRes;
			}
		}
	}

	function FillFormValues() {
            global $forwardurl, $selfurl;
		foreach ($this->lFieldArr as $lFname => $lFArr) {
			switch ($lFArr["VType"]) {
				case "int":
				case "float":
				case "string":
				case "mlstring":
				case "mlint":
				case "date":
					$this->lFieldArr[$lFname]['CurValue'] = $this->GetFormValue($lFname);
					$this->RemoveFormatFieldValue($lFname);
					break;
				case "file":
					if ($_FILES[$lFname]) {
						$this->lFieldArr[$lFname]['FileUp'] = true;
						$this->lFieldArr[$lFname]['FileName'] = $_FILES[$lFname]['name'];
						$this->lFieldArr[$lFname]['FileType'] = $_FILES[$lFname]['type'];
						$this->lFieldArr[$lFname]['FileSize'] = $_FILES[$lFname]['size'];
						$this->lFieldArr[$lFname]['FileTmpName'] = $_FILES[$lFname]['tmp_name'];
						$this->lFieldArr[$lFname]['FileError'] = $_FILES[$lFname]['error'];
					}
					break;
				default:
					break;
			}
		}
	}

	function DoChecks($lFname = '') {

		if($lFname == '') {
			// matchvame sichko v kudravite skobi bez tia deto pochvat s underscore
			preg_match_all("/\{([^_].*?)\}/", $this->lFieldArr[$this->lCurAction]["SQL"], $lOutArr, PREG_PATTERN_ORDER);

			// var_dump($lOutArr[1]);

			foreach ($this->lFieldArr as $lFname => $lFArr) {
				if (!in_array($lFname, $lOutArr[1]))
					continue;
				$lCurValue =& $this->lFieldArr[$lFname]['CurValue'];

				if ((is_null($lCurValue) || $lCurValue === '') && $this->lFieldArr[$lFname]['AllowNulls']) {
					continue;
				}

				if ((is_array($lCurValue) && !strlen(implode("", $lCurValue))) && $this->lFieldArr[$lFname]['AllowNulls']) {
					continue;
				}

				switch ($lFArr["VType"]) {
					case "float":
					case "int":
					case "mlint":
						$this->lFieldArr[$lFname] = $this->ExplicitFloatPrepare($lFArr);
						// moje da e tupo ama ne mi se zanimava :)
						$lCurValue = $this->lFieldArr[$lFname]['CurValue'];

						if (is_array($lCurValue)) {
							foreach($lCurValue as $k => $v) {
								if (is_null($v)  || $v === '') {
									if ($this->lFieldArr[$lFname]['AllowNulls'])
										continue;
									else {
										$this->SetError($lFname, ERR_EMPTY_NUMERIC);
										continue 2; // minavame na sledvashtiat euement
									}
								}
								if (!is_numeric($v)) {
									$this->SetError($lFname, ERR_NAN);
									continue 2; // minavame na sledvashtiat euement
								}
								$this->lFieldArr[$lFname]['CurValue'][$k] = (($lFArr["VType"] == "float") ? (float) $this->lFieldArr[$lFname]['CurValue'][$k] : (int) $this->lFieldArr[$lFname]['CurValue'][$k]);
							}
						} else {
							if (is_null($lCurValue) || $lCurValue === '') {
								$this->SetError($lFname, ERR_EMPTY_NUMERIC);
								continue;
							}
							if (!is_numeric($lCurValue)) {
								$this->SetError($lFname, ERR_NAN);
								continue;
							}

							$this->lFieldArr[$lFname]['CurValue'] = (($lFArr["VType"] == "float") ? (float) $this->lFieldArr[$lFname]['CurValue'] : (int) $this->lFieldArr[$lFname]['CurValue']);
						}
						break;
					case "date":
						if (is_array($lCurValue)) {
							foreach ($lCurValue as $k => $v) {
								$lstrError = manageckdate($lCurValue[$k], $this->lFieldArr[$lFname]['DateType']);
								if ($lstrError) {
									$this->SetError($lFname, $lstrError);
									continue 2; // minavame na sledvashtiat euement
								}
							}
						} else {
							$lstrError = manageckdate($lCurValue, $this->lFieldArr[$lFname]['DateType']);
							if ($lstrError) {
								$this->SetError($lFname, $lstrError);
								continue;
							}
						}
						break;
					case "string":
	//					var_dump($lFname);
	//					echo "<br>";
						if (is_null($lCurValue) || $lCurValue == '') {
							$this->SetError($lFname, ERR_EMPTY_STRING);
							continue;
						}
						break;
					case "mlstring":
						if (is_null($lCurValue) || !is_array($lCurValue) || !strlen(implode("",$lCurValue))) { //tuk da vidia
							$this->SetError($lFname, ERR_EMPTY_STRING);
							continue;
						}
						break;
					default:
						break;
				}
			}
		} else {

			$lCurValue =& $this->lFieldArr[$lFname]['CurValue'];

			if ((is_null($lCurValue) || $lCurValue === '') && $this->lFieldArr[$lFname]['AllowNulls']) {
				return;
			}

			if ((is_array($lCurValue) && !strlen(implode("", $lCurValue))) && $this->lFieldArr[$lFname]['AllowNulls']) {
				return;
			}

			switch ($this->lFieldArr[$lFname]["VType"]) {
				case "float":
				case "int":
				case "mlint":
					$this->lFieldArr[$lFname] = $this->ExplicitFloatPrepare($this->lFieldArr[$lFname]);
					// moje da e tupo ama ne mi se zanimava :)
					$lCurValue = $this->lFieldArr[$lFname]['CurValue'];

					if (is_array($lCurValue)) {
						foreach($lCurValue as $k => $v) {
							if (is_null($v)  || $v === '') {
								if ($this->lFieldArr[$lFname]['AllowNulls'])
									continue;
								else {
									$this->SetError($lFname, ERR_EMPTY_NUMERIC);
									return; // minavame na sledvashtiat euement
								}
							}
							if (!is_numeric($v)) {
								$this->SetError($lFname, ERR_NAN);
								return; // minavame na sledvashtiat euement
							}
							$this->lFieldArr[$lFname]['CurValue'][$k] = (($this->lFieldArr[$lFname]["VType"] == "float") ? (float) $this->lFieldArr[$lFname]['CurValue'][$k] : (int) $this->lFieldArr[$lFname]['CurValue'][$k]);
						}
					} else {
						if (is_null($lCurValue) || $lCurValue === '') {
							$this->SetError($lFname, ERR_EMPTY_NUMERIC);
							continue;
						}
						if (!is_numeric($lCurValue)) {
							$this->SetError($lFname, ERR_NAN);
							continue;
						}

						$this->lFieldArr[$lFname]['CurValue'] = (($this->lFieldArr[$lFname]["VType"] == "float") ? (float) $this->lFieldArr[$lFname]['CurValue'] : (int) $this->lFieldArr[$lFname]['CurValue']);
					}
					break;
				case "date":
					if (is_array($lCurValue)) {
						foreach ($lCurValue as $k => $v) {
							$lstrError = manageckdate($lCurValue[$k], $this->lFieldArr[$lFname]['DateType']);
							if ($lstrError) {
								$this->SetError($lFname, $lstrError);
								return; // minavame na sledvashtiat euement
							}
						}
					} else {
						$lstrError = manageckdate($lCurValue, $this->lFieldArr[$lFname]['DateType']);
						if ($lstrError) {
							$this->SetError($lFname, $lstrError);
							continue;
						}
					}
					break;
				case "string":
//					var_dump($lFname);
//					echo "<br>";
					if (is_null($lCurValue) || $lCurValue == '') {
						$this->SetError($lFname, ERR_EMPTY_STRING);
						continue;
					}
					break;
				case "mlstring":
					if (is_null($lCurValue) || !is_array($lCurValue) || !strlen(implode("",$lCurValue))) { //tuk da vidia
						$this->SetError($lFname, ERR_EMPTY_STRING);
						continue;
					}
					break;
				default:
					break;
			}
		}
	}

	function DoCustomChecks($pField = '') {
		// custom checks here
		// narochno sa v nov foreach!
		// shtoto iskam da moje da se replace-vat stoinostite na poletata i trjabva da sa minali proverkite za tipovete na vsichki poleta

		if(!$pField) {

			foreach ($this->lFieldArr as $pField => $lFArr) {
				/*if ((is_null($this->lFieldArr[$pField]['CurValue']) || $this->lFieldArr[$pField]['CurValue'] === '') && $this->lFieldArr[$pField]['AllowNulls']) {
					continue;
				}*/

				if (is_array($this->lFieldArr[$pField]["Checks"])) {
					foreach ($this->lFieldArr[$pField]["Checks"] as $lCkN => $lCk) {
						$evalStr = 'return ' . preg_replace("/\{([a-z].*?)\}/e", "\$this->EvalPrepare('\\1')", $lCk) . ';';

					//~ var_dump($evalStr);
						$lRes = eval($evalStr);
						if ($lRes) {
							$this->SetError($pField, $lRes);
							break;
						}
					}
				}

			}

		} else {
			/*if ((is_null($this->lFieldArr[$pField]['CurValue']) || $this->lFieldArr[$pField]['CurValue'] === '') && $this->lFieldArr[$pField]['AllowNulls']) {
				return;
			}*/

			if (is_array($this->lFieldArr[$pField]["Checks"])) {
				foreach ($this->lFieldArr[$pField]["Checks"] as $lCkN => $lCk) {
					$evalStr = 'return ' . preg_replace("/\{([a-z].*?)\}/e", "\$this->EvalPrepare('\\1')", $lCk) . ';';

					$lRes = eval($evalStr);
					if ($lRes) {
						$this->SetError($pField, $lRes);
						break;
					}
				}
			}
		}

	}

	function ExplicitFloatPrepare($lFArr) {
		/* USAGE
			'price' => array(
				'CType' => 'text',
				'VType' => 'float',
				'floatReplace' => array(
					'match' => '/,/',
					'replace' => '.',
				),
				'DisplayName' => 'Цена',
				'AllowNulls' => false,
			),
		*/
		if ($lFArr['VType'] != 'float') return $lFArr;
		if (!is_array($lFArr['floatReplace']) || count($lFArr['floatReplace']) != 2) return $lFArr;
		$lFArr['CurValue'] = preg_replace($lFArr['floatReplace']['match'], $lFArr['floatReplace']['replace'], $lFArr['CurValue']);
		return $lFArr;
	}

	function EvalPrepare($pName) {
		return var_export($this->lFieldArr[$pName]["CurValue"], true);
	}

	function SqlPrepare($pName) {
		$lRetStr = '';

		if (substr($pName, 0, 1) == '_') {
			$lTmpArr = array();

			foreach($this->lFieldArr as $k => $v) {
				$lTmpArr[$k] = $v["CurValue"];
			}

			$lEvalStr = "return " . substr($pName, 1) . "(" . var_export($lTmpArr, true) . ");";

			return eval($lEvalStr);
		}

		if (!$this->lFieldArr[$pName]) {
			trigger_error("Cannot find the \{$pName} field", E_USER_WARNING);
			return '';
		}

		if ($this->lFieldArr[$pName]["VType"] == "action") {
			trigger_error("Cannot replace actions in a string", E_USER_WARNING);
			return '';
		}

		if (is_null($this->lFieldArr[$pName]["IsNull"])) {
			$lDefNull = "NULL";
		} else {
			$lDefNull = $this->lFieldArr[$pName]["IsNull"];
		}
		if ($this->lFieldArr[$pName]["AllowNulls"] && (isset($this->lFieldArr[$pName]["IsNull"]) || !$this->lFieldArr[$pName]["DefValue"])&& ($this->lFieldArr[$pName]["CurValue"] === '' || is_null($this->lFieldArr[$pName]["CurValue"]))) {

			$lRetStr = $lDefNull;
		} else {

			if (isset($this->lFieldArr[$pName]["DefValue"]) && (is_null($this->lFieldArr[$pName]["CurValue"]) || $this->lFieldArr[$pName]["CurValue"] === '' ))
				$this->lFieldArr[$pName]["CurValue"]=$this->lFieldArr[$pName]["DefValue"];
			if (is_array($this->lFieldArr[$pName]["CurValue"])) {
				if ($this->lFieldArr[$pName]["TransType"] == MANY_TO_STRING) {
					if ($this->lFieldArr[$pName]["VType"] == "date") {
						$lTmpArr = array();
						foreach($this->lFieldArr[$pName]["CurValue"] as $k => $v) {
							$lTmpArr[] = manageckdate($v, $this->lFieldArr[$pName]['DateType'], 0);
						}
						$lRetStr = "'" . implode(DEF_SQLSTR_SEPARATOR, $lTmpArr) . DEF_SQLSTR_SEPARATOR . "'";
					} else {
						$lRetStr = "'" . implode(DEF_SQLSTR_SEPARATOR, array_map('q', $this->lFieldArr[$pName]["CurValue"])) . DEF_SQLSTR_SEPARATOR . "'";
					}
				} elseif ($this->lFieldArr[$pName]["TransType"] == MANY_TO_SQL_ARRAY) {
					if ($this->lFieldArr[$pName]["VType"] == "date") {
						$lTmpArr = array();
						foreach($this->lFieldArr[$pName]["CurValue"] as $k => $v) {
							$lTmpArr[] = manageckdate($v, $this->lFieldArr[$pName]['DateType'], 0);
						}
						$lRetStr = "array[" . implode(DEF_SQLSTR_SEPARATOR, $lTmpArr) . "]";
					} elseif ( $this->lFieldArr[$pName]["VType"] == "float" ){
						$lRetStr = "array[" . implode(DEF_SQLSTR_SEPARATOR,
										array_map(
											float2array($pVal) , $this->lFieldArr[$pName]["CurValue"])
									) . "]";
					} else {
						$lRetStr = "array[" . implode(DEF_SQLSTR_SEPARATOR,
								array_map( ((($this->lFieldArr[$pName]["VType"] == "string") ||  ($this->lFieldArr[$pName]["VType"] == "mlstring")) ? 'arrstr_q'  : 'arrint_q' ),
											$this->lFieldArr[$pName]["CurValue"]) ) . "]";
					}
				} elseif ($this->lFieldArr[$pName]["TransType"] == MANY_TO_BIT) {
					if ($this->lFieldArr[$pName]["VType"] == "int") {
						$lRetStr = array2bitint($this->lFieldArr[$pName]["CurValue"]);
					} else {
						if ($this->lFieldArr[$pName]["TransType"] == MANY_TO_BIT_ONE_BOX)
								if (is_null($this->lFieldArr[$pName]["CurValue"]) || $this->lFieldArr[$pName]["CurValue"]==='') $lRetStr =0;
								else $lRetStr = $this->lFieldArr[$pName]["CurValue"];
						else trigger_error("Cannot convert string or float values to bit value.", E_USER_ERROR);
					}
				}
			} else {
				if ($this->lFieldArr[$pName]["VType"] == "int" || $this->lFieldArr[$pName]["VType"] == "float") {
					$lRetStr = $this->lFieldArr[$pName]["CurValue"];
				} else if ($this->lFieldArr[$pName]["VType"] == "date") {
					$lRetStr = "'" . q(manageckdate($this->lFieldArr[$pName]["CurValue"], $this->lFieldArr[$pName]['DateType'], 0)) . "'";
				} else {
					$lRetStr = "'" . q($this->lFieldArr[$pName]["CurValue"]) . "'";
				}
			}
		}
		return $lRetStr;
	}

	function ReplaceSqlFields($pStr) {
		return preg_replace("/\{(.*?)\}/e", "\$this->SqlPrepare('\\1')", $pStr);
	}

	function GetFieldJs($pName, $pElemId) {

		$lAdditionalAddJs = '';
		$lAdditionalJs = '';

		if((int)$this->lJsValidate && $this->lFieldArr[$pName]['check_event']) {

			if($this->lFieldArr[$pName]['ErrorString'] || ($this->lFieldArr[$pName]['AllowNulls'] === false &&  $this->lFieldArr[$pName]['CurValue'] == '')) {
				if(!$this->lFieldArr[$pName]['ErrorString']) {
					$lAdditionalAddJs = ($this->lFieldArr[$pName]['req_js'] ? $this->lFieldArr[$pName]['req_js'] : ($this->lFieldArr[$pName]['error_js'] ? $this->lFieldArr[$pName]['error_js'] : ''));
				} else {
					$lAdditionalAddJs = ($this->lFieldArr[$pName]['error_js'] ? $this->lFieldArr[$pName]['error_js'] : '');
				}
			} else {
				$lAdditionalAddJs = ($this->lFieldArr[$pName]['valid_js'] ? $this->lFieldArr[$pName]['valid_js'] : '');
			}

			$lAdditionalJs = '
				<script>
					$(\'#' . $pElemId . '\').bind(\'' . $this->lFieldArr[$pName]['check_event'] . '\', function() {
						var formdata = $(\'#' . $pElemId . '\').closest(\'form\').serialize();
						$.ajax({
							type: \'POST\',
							url: \'' . DEF_AJAXCHECK_URL . '?field=' . $pName . '&path_fields=' . $this->lPathFields . '&field_key=' . $this->lKeyFields . '\',
							data: formdata,
							dataType: \'json\',
							success: function(data) {
								if(data.error_string) {
									$(\'#\' + data.error_field).addClass(data.error_field_class);
									$(\'#\' + data.error_holder).html(data.error_string);
									$(\'#\' + data.error_holder).addClass(\'fld_err_holder\');


									if(data.ajax_error_js) {
										$(\'#\' + data.error_holder).hide();
										eval(data.ajax_error_js);
									} else

									if(data.error_js) {
										$(\'#\' + data.error_holder).show();
										eval(data.error_js);
									}



								} else {
									$(\'#\' + data.error_field).removeClass(data.error_field_class);
									$(\'#\' + data.error_holder).html(\'\');
									$(\'#\' + data.error_holder).removeClass(\'fld_err_holder\');
									$(\'#\' + data.error_holder).hide();
									if(data.valid_js) {
										eval(data.valid_js);
									}
								}
							}
						});
					});
					' . $lAdditionalAddJs . '
				</script>';
		} else {
			$lAdditionalJs = ($lAdditionalAddJs ? '<script>' . $lAdditionalAddJs . '</script>' : '');
		}

		return $lAdditionalJs;
	}

	function simple($pName,$pVal) {
		$lRetStr = '';
		$lExtStr = '';
		$lCalIco = '';
		$lAdditionalJs = '';
		$lAdditionalAddJs = '';
		$pType=$this->lFieldArr[$pName]['CType'];
		$pExt = $this->lFieldArr[$pName]["AddTags"];
		if((int)$this->lJsValidate) {
			if(!$this->lFieldArr[$pName]['error_class']) {
				$this->lFieldArr[$pName]['error_class'] = DEF_ERROR_FIELD_CLASS;
			}
		}

		if (!in_array($pType, array("text", "password", "textarea", "hidden", "file", "mlfield"))) {
			trigger_error("Nepoznat tip na controlata ($pType)", E_USER_ERROR);
		}

		if (is_array($pExt)) {
			foreach ($pExt as $k => $v) {
				if ($k == 'addcalico') {
					$TmpCalico = explode(';', $v);
					array_map('trim', $TmpCalico);
					if (count($TmpCalico) == 1) {
						$lCalIco = ' ' . retcalico($TmpCalico[0]);
					} elseif (count($TmpCalico) == 2) {
						$lCalIco = ' ' . retcalico($TmpCalico[0], $TmpCalico[1]);
					} elseif (count($TmpCalico) == 3) {
						$lCalIco = ' ' . retcalico($TmpCalico[0], $TmpCalico[1], $TmpCalico[2]);
					}

				} elseif($k == 'class') {
					if((int)$this->lJsValidate && $this->lFieldArr[$pName]['ErrorString']) {
						$lExtStr .= ' ' . $k . '="' . $v . ' ' . $this->lFieldArr[$pName]['error_class'] . '"';
					} else {
						$lExtStr .= ' ' . $k . '="' . $v . '"';
					}
				} elseif($k == 'id' && (int)$this->lJsValidate) {
					$lExtStr .= ' ' . $k . '="' . $v . '"';
					$lElemId = $v;
				} else {
					$lExtStr .= ' ' . $k . '="' . $v . '"';
				}
			}

			if(!array_key_exists('class', $pExt)) {
				if((int)$this->lJsValidate && $this->lFieldArr[$pName]['ErrorString']) {
					$lExtStr .= ' class="' . $this->lFieldArr[$pName]['error_class'] . '"';
				}
			}

			if(!array_key_exists('id', $pExt) && (int)$this->lJsValidate) {
				$lExtStr .= ' id="' . DEF_FORM_FIELD_ID . $pName . '"';
				$lElemId = DEF_FORM_FIELD_ID . $pName;
			}

		} else {
			if((int)$this->lJsValidate && $this->lFieldArr[$pName]['ErrorString']) {
				$lExtStr .= ' class="' . $this->lFieldArr[$pName]['error_class'] . '"';
			}
			if((int)$this->lJsValidate) {
				$lExtStr .= ' id="' . DEF_FORM_FIELD_ID . $pName . '"';
				$lElemId = DEF_FORM_FIELD_ID . $pName;
			}
		}

		if ((int)$this->lFieldArr[$pName]['RichText'] && $this->lFieldArr[$pName]['VType'] != 'mlstring') {
			$oFCKeditor = new FCKeditor($pName);
			$oFCKeditor->ToolbarSet = getFCKtoolbar((int)$this->lFieldArr[$pName]['RichText']);
			$oFCKeditor->Value = $pVal;
			$oFCKeditor->Config['DefaultLanguage'] = getlang(1);
			//~ $oFCKeditor->DefaultLanguage='bg';
			$oFCKeditor->Width = defined(FCK_DEFAULT_WIDTH) ? FCK_DEFAULT_WIDTH : '100%';
			$oFCKeditor->Height = defined(FCK_DEFAULT_HEIGHT) ? FCK_DEFAULT_HEIGHT : '300';

			if (is_array($this->lFieldArr[$pName]['RichTextDim'])) {
				if ($this->lFieldArr[$pName]['RichTextDim']['width'])
					$oFCKeditor->Width = $this->lFieldArr[$pName]['RichTextDim']['width'];
				if ($this->lFieldArr[$pName]['RichTextDim']['height'])
					$oFCKeditor->Height = $this->lFieldArr[$pName]['RichTextDim']['height'];
			}
			$lRetStr .= $oFCKeditor->Create();
		}

		if ($pType == "textarea") {
			if (!(int)$this->lFieldArr[$pName]['RichText']) {
				$lAdditionalJs = $this->GetFieldJs($pName, $lElemId);
				$lRetStr .= '<textarea name="' . $pName . '"' . $lExtStr . '">' . h($pVal) . '</textarea>' . $lAdditionalJs;
			}
		} else {
			if (($this->lFieldArr[$pName]['VType']=="mlstring") || ($this->lFieldArr[$pName]['VType']=="mlint")) {
				$lcstr="";
				checkSessionLangs();
				foreach($_SESSION["langs"] as $k=>$v) {
					$ldisplname=(($this->lFieldArr[$pName]["DisplayName"]) ? $this->lFieldArr[$pName]["DisplayName"] : $pName) . '('.$v["code"].')';
					$lcstr .= '<tr><td valign="top"><b>' .$ldisplname .':</b></td><td valign="top">';
					if ((int)$this->lFieldArr[$pName]['RichText']) {
						if(defined('ETALIGENT_EDITOR') && (int)ETALIGENT_EDITOR == 1) {
							$lcstr .= '<div id="htmltext_div_' . $k . '" class="htmltextcontainer"></div>';
							$lcstr .= '</td></tr>';
						} else {
							$oFCKeditor = new FCKeditor($pName . '['.$k.']');
							$oFCKeditor->ToolbarSet = getFCKtoolbar((int)$this->lFieldArr[$pName]['RichText']);
							$oFCKeditor->Value = $pVal[$k];

							$oFCKeditor->Width = defined(FCK_DEFAULT_WIDTH) ? FCK_DEFAULT_WIDTH : '100%';
							$oFCKeditor->Height = defined(FCK_DEFAULT_HEIGHT) ? FCK_DEFAULT_HEIGHT : '300';

							if (is_array($this->lFieldArr[$pName]['RichTextDim'])) {
								if ($this->lFieldArr[$pName]['RichTextDim']['width'])
									$oFCKeditor->Width = $this->lFieldArr[$pName]['RichTextDim']['width'];
								if ($this->lFieldArr[$pName]['RichTextDim']['height'])
									$oFCKeditor->Height = $this->lFieldArr[$pName]['RichTextDim']['height'];
							}

							$lcstr .= $oFCKeditor->Create() . '</td></tr>';
						}
					} else {
						if ($pType=="mlfield") { //Trenk:tez proverki triabva da se praviat vednyzh i da se iznesat ot tuk
							if (!is_array($this->lFieldArr[$pName]['mlfield'])) {
								trigger_error("Greshka v definiciata na mlfield!", E_USER_ERROR);
								exit;
							} else {
								$lName=$pName."[".$k."]";
								$this->lFieldArr[$lName]=array( );
								$larr = &$this->lFieldArr[$lName];
								foreach($this->lFieldArr[$pName]['mlfield'] as $mk => $mv) {
									$larr[$mk]=$mv;
								}
								$larr['VType']=substr($this->lFieldArr[$pName]['VType'],2);
								$larr['DisplayName']=$ldisplname;
								if ((($larr["CType"]=="select") || ($larr["CType"]=="mselect")) && !is_array($larr["SrcValues"])) {
									foreach($v as $vk => $vv)
										$larr["SrcValues"]=str_replace("{ml_".$vk."}",$vv,$larr["SrcValues"]);
								}
								$larr["CurValue"]=$pVal[$k];
								$lcstr .= $this->HtmlPrepare($lName). '</td></tr>';
								unset($this->lFieldArr[$lName]);
							}
						} else
							$lcstr .= '<input type="' . $pType . '" name="' . $pName . '['.$k.']" value="' . h($pVal[$k]) . '"' . $lExtStr . ' />' . '</td></tr>';
					}
				}

				$lRetStr .= ($this->lFieldArr[$pName ]["DisplayFormat"]==MLSTR_D_NOTABLE ?  $lcstr : "<table>".$lcstr."</table>");
			}
			else {
				if (!(int)$this->lFieldArr[$pName]['RichText']) {
					$lAdditionalJs = $this->GetFieldJs($pName, $lElemId);
					$lRetStr .= '<input type="' . $pType . '" name="' . $pName . '" value="' . h($pVal) . '"' . $lExtStr . ' />' . $lCalIco . $lAdditionalJs;
				}
			}
		}
		return $lRetStr;
	}

	// tova trqbva da go promenim da si ba4ka sys $this
	function extended($pName, $pType, $pList, $pSelected, $pExt = null, $pSeparator = DEF_CONTR_SEP, $pDis = 0, $pJsValidation = 0, $pError_Class = DEF_ERROR_FIELD_CLASS, $pErrorString = '') {
		$lListArr = array();
		$lSelArr = array();
		$lExtStr = '';
		$lRetStr = '';
		$lAdditionalJs = '';
		$lAdditionalAddJs = '';
		$pViewRes='';

		if (!in_array($pType, array("select", "mselect", "radio", "checkbox"))) {
			trigger_error("Nepoznat tip na controlata ($pType)", E_USER_ERROR);
		}

		if (is_null($pSeparator)) {
			$pSeparator = DEF_CONTR_SEP;
		}

		if (is_array($pExt)) {
			foreach ($pExt as $k => $v) {
				if($k == 'class') {
					if((int)$this->lJsValidate && $this->lFieldArr[$pName]['ErrorString']) {
						$lExtStr .= ' ' . $k . '="' . $v . ' ' . $this->lFieldArr[$pName]['error_class'] . '"';
					} else {
						$lExtStr .= ' ' . $k . '="' . $v . '"';
					}
				} elseif($k == 'id' && (int)$this->lJsValidate) {
					$lExtStr .= ' ' . $k . '="' . $v . '"';
					$lElemId = $v;
				} else {
					$lExtStr .= ' ' . $k . '="' . $v . '"';
				}
			}

			if(!array_key_exists('class', $pExt)) {
				if((int)$this->lJsValidate && $this->lFieldArr[$pName]['ErrorString']) {
					$lExtStr .= ' class="' . $this->lFieldArr[$pName]['error_class'] . '"';
				}
			}

			if(!array_key_exists('id', $pExt) && (int)$this->lJsValidate) {
				$lExtStr .= ' id="' . DEF_FORM_FIELD_ID . $pName . '"';
				$lElemId = DEF_FORM_FIELD_ID . $pName;
			}

		} else {
			if((int)$this->lJsValidate && $this->lFieldArr[$pName]['ErrorString']) {
				$lExtStr .= ' class="' . $this->lFieldArr[$pName]['error_class'] . '"';
			}
			if((int)$this->lJsValidate) {
				$lExtStr .= ' id="' . DEF_FORM_FIELD_ID . $pName . '"';
				$lElemId = DEF_FORM_FIELD_ID . $pName;
			}
		}

		if (!is_array($pSelected)) {
			$lSelArr[] = $pSelected;
		} else {
			$lSelArr = $pSelected;
			if ($pType == 'select' || $pType == 'radio') {
				trigger_error("Masiv s selectnati stoinosti moje da se predade samo na miltiple selecti i checkboxove", E_USER_WARNING);
			}
		}

		if (!is_array($pList)) {
			$gCn = new DBCn();
			$gCn->Open();
			$gCn->Execute($pList);
			$gCn->MoveFirst();
			while(!$gCn->Eof()) {
				$lExtraListArr[$gCn->mRs['id']] = $gCn->mRs;
				foreach ($lExtraListArr[$gCn->mRs['id']] as $k=>$v)
					if(is_numeric($k))
						unset ($lExtraListArr[$gCn->mRs['id']][$k]);
				unset ($lExtraListArr[$gCn->mRs['id']]['id']);
				unset ($lExtraListArr[$gCn->mRs['id']]['name']);
				unset ($lExtraListArr[$gCn->mRs['id']]['ord']);
				$lListArr[$gCn->mRs['id']] = $gCn->mRs['name'];
				$gCn->MoveNext();
			}
		} else {
			$lListArr = $pList;
			foreach($lListArr as $k=>$v){
				$lExtraListArr[$k] = array();
			}
		}


		if ($pSeparator==MANY_TO_BIT_ONE_BOX && count($lListArr) !=2 ) {
			trigger_error("Controlata $pName e ot tip MANY_TO_BIT_ONE_BOX koito dopuska tochno dve stoinosti!", E_USER_ERROR);
		}

		$lAdditionalJs = $this->GetFieldJs($pName, $lElemId);

		if ($pType == "select" || $pType == "mselect") {
			$lRetStr .= '<select name="' . $pName . (($pType == "mselect") ? '[]' : '') . '"' . (($pType == "mselect") ? ' multiple' : '') . $lExtStr . '>';
		}

		foreach ($lListArr as $k => $v) {
			switch($pType) {
				case "select":
					if (($pDis == 2) && (in_array($k, $lSelArr) &&($k || in_array($k, $lSelArr,true)))) {
						$pViewRes = '<input type="hidden" name="' . $pName . '" value="' . $k . '" />' . $pViewRes;
						$pDis = 3;
					}
				case "mselect":
					$attrs='';
					foreach ($lExtraListArr[$k] as $attr=>$val){
						$attrs.=' '.htmlspecialchars($attr).'="'.htmlspecialchars($val).'"';
					}
					if(is_array($v)){
						$lLabel = $v['label'];
						if(is_array($v['AddTags'])){
							foreach ($v['AddTags'] as $lAddKey => $lAddValue) {
							   $attrs.=' '.htmlspecialchars($lAddKey).'="'.htmlspecialchars($lAddValue).'"';
							}
						}
					}else{
						$lLabel = $v;
					}
					$lRetStr .= '<option ' . $attrs . ' value="' . $k . '"' . ((in_array($k, $lSelArr)) ? ' selected="selected"' : '') . '>' . $lLabel . '</option>';
					if ($pDis && (in_array($k, $lSelArr) &&($k || in_array($k, $lSelArr,true)))) { if (substr($lLabel,0,2)=='--')  $pViewRes .='None; '; else  $pViewRes .= $lLabel."; ";};
				break;
				case "radio":
				case "checkbox":
					$attrs='';
					foreach ($lExtraListArr[$k] as $attr=>$val){
						$attrs.=' '.htmlspecialchars($attr).'="'.htmlspecialchars($val).'"';
					}
					if ($pSeparator==MANY_TO_BIT_ONE_BOX && $pType='checkbox') {
							if ($k)
									$lRetStr .= '<input type="' . $pType . '" name="' . $pName .  '"  value="' .$k. (($lSelArr[0]) ? '" checked="checked"' : '"') . $lExtStr . ' />'; /* . $v . '/' .$olddv;
							else
									if ($lRetStr) $lRetStr.=$v; else $olddv=$v;
									*/
					} else
							$lRetStr .= '<input ' . $attrs . ' type="' . $pType . '" name="' . $pName . (($pType == "checkbox") ? '[]' : '') . '" value="' . $k . '"' . ((in_array($k, $lSelArr)) ? ' checked="checked"' : '') . $lExtStr . ' /><label>' . $v .'</label>'. $pSeparator;
					if ($pDis && (in_array($k, $lSelArr) &&($k || in_array($k, $lSelArr,true)))) $pViewRes .= $v."; ";

					break;
			}
		}

		if ($pType == "select" || $pType == "mselect") {
			$lRetStr .= '</select>';
		}

		$lRetStr .= $lAdditionalJs;

		if ($pDis) {
				if (strlen($pViewRes)>2) $pViewRes=substr($pViewRes,0,-2);
				return $pViewRes;
			} else return $lRetStr;
	}

	function HtmlPrepare($pName, $pErrReplace = 0) {
        $cmd=substr($pName, 0, 1);
        switch (TRUE) {
			case ($cmd == '$'):
				if(!$this->lFieldArr[substr($pName, 1)]["error_templ"]) {
					$this->lFieldArr[substr($pName, 1)]["error_templ"] = 'div';
				}
				if($this->lFieldArr[substr($pName, 1)]["ErrorString"]) {
					return '<' . $this->lFieldArr[substr($pName, 1)]["error_templ"] . ' id="' . DEF_ERROR_ID_HOLDER . substr($pName, 1) . '" class="Error-Holder">' . $this->lFieldArr[substr($pName, 1)]["ErrorString"] . '</' . $this->lFieldArr[substr($pName, 1)]["error_templ"] . '>';
				} else {
					return '<' . $this->lFieldArr[substr($pName, 1)]["error_templ"] . ' id="'. DEF_ERROR_ID_HOLDER . substr($pName, 1) . '" style="display:none"></' . $this->lFieldArr[substr($pName, 1)]["error_templ"] . '>';
				}
			case ($cmd == '_') :
				// "_" - function

				$lTmpArr = array();

				foreach($this->lFieldArr as $k => $v) {
					$lTmpArr[$k] = $v["CurValue"];
				}

				$lEvalStr = "return " . substr($pName, 1) . "(" . var_export($lTmpArr, true) . ");";

				return eval($lEvalStr);

 			case ($cmd == '*'):
 				if ($this->lFieldArr[substr($pName, 1)]["DisplayName"]) {
 					return $this->lFieldArr[substr($pName, 1)]["DisplayName"];
 				}
 				return substr($pName, 1);
			case ($cmd == '#'):
				// "#" - value

				if (is_array($this->lFieldArr[substr($pName, 1)]["CurValue"])) {
					// XXX : za predpochitane e kogato imame array i iskame da go displaynem po nekuv nachin u formata
					// da go pravim s funkcia shoto taka e mnogo tupo i stava samo za debug!
					trigger_error("Opitvame se da displaynem array ot stoinosti \{$pName}. izpolzvaite \"_\"", E_USER_WARNING);
					return h(serialize($this->lFieldArr[substr($pName, 1)]["CurValue"]));
				} else {
					if (!$this->lFieldArr[substr($pName, 1)]["CurValue"] && $this->lFieldArr[substr($pName, 1)]["DefValue"])
						return $this->lFieldArr[substr($pName, 1)]["DefValue"];
					return h($this->lFieldArr[substr($pName, 1)]["CurValue"]);
				}
                        case ($cmd == '!') :
                                return "";
			case ($cmd == '@')  :
				// @ DisplayValue
				if (substr($pName, 1, 1) == '!') return "";

				if (substr($pName, 1, 1) == '@') {
					$pName=substr($pName, 2);
					$pDis = 2;
				}
				else {
					$pName=substr($pName, 1);
					$pDis = 1;
				}

				$fld=$this->lFieldArr[$pName];

				if ($fld) {
						if (isset($fld["DefValue"]) && (is_null($fld["CurValue"]))) {
								$lCurValue = $fld["DefValue"];
						} else {
								$lCurValue = $fld["CurValue"];
						}

						switch($fld["CType"]) {
							case "select":
							case "mselect":
							case "radio":
							case "checkbox":
								if (!is_array($fld["SrcValues"])) {
									$scrval = $this->ReplaceSqlFields($fld["SrcValues"]);
								} else {
									$scrval = $fld["SrcValues"];
								}
								// echo $scrval . '<br>';



								if ($fld["CType"]=="checkbox" && $fld["TransType"]==MANY_TO_BIT_ONE_BOX) {
										$dcs=MANY_TO_BIT_ONE_BOX;
								}
								else $dcs=($fld['Separator'] ? $fld['Separator'] : DEF_CONTR_SEP);
								return $this->extended($pName, $fld['CType'], $scrval, $lCurValue, $fld["AddTags"],$dcs,$pDis);
								//return extended($pName, $fld['CType'], $scrval, $lCurValue, $fld["AddTags"],$dcs,$pDis);
								break;
							case "file":
							case "text":
							case "hidden":
								$bool = 0;
								$bool = ($this->lFieldArr[$this->lCurAction]["ActionMask"] & ACTION_VIEW);
								$bool = $bool && (isset($this->lFieldArr[$pName]['ViewText']));
								$bool = $bool && (!is_null($this->lFieldArr[$pName]['ViewText']));
								$bool = $bool && (!($this->lFieldArr[$pName]['ViewText'] === ''));

								if ($bool) {
									$lRetTxt = preg_replace("/\{(.*?)\}/e", "h(\$this->lFieldArr['\\1']['CurValue'])", $this->lFieldArr[$pName]['ViewText']);
								}
								else {
									$lRetTxt = h($lCurValue);
								}

								return $lRetTxt;
								break;
							case "textarea":
								return nl2br(h($lCurValue));
								break;
							case "password":
							//~ case "hidden":
								//~ return '';
							default:
								if ($fld["CType"] == "action") {
									if (!$fld["Hidden"]) {
										$lDispName = $fld["DisplayName"];
										$lActionImgSrc = $fld["ImgSrc"];
										$lButtonHtml = ($fld["ButtonHtml"] ? $fld["ButtonHtml"] : '');

										return $this->actionbutton((($lDispName) ? $lDispName : $pName), $fld["AddTags"], $lActionImgSrc,$pName,$this->lAddbackurl, $lButtonHtml);
									} else {
										return '';
									}
								} else {
									trigger_error("A problem occured with the type of the field.", E_USER_WARNING);
									return '';
								}
						}
				} else {
			trigger_error("Cannot Find {".substr($pName, 1)."} field", E_USER_WARNING);
			return '';
				}
		default :
			if ($this->lFieldArr[$pName]) {

				if ($this->lFieldArr[$pName]['ErrorString']) {
					$lCurValue = $this->lFieldArr[$pName]["CurValue"];
				} else {
					if (isset($this->lFieldArr[$pName]["DefValue"]) && (is_null($this->lFieldArr[$pName]["CurValue"]))) {
						$lCurValue = $this->lFieldArr[$pName]["DefValue"];
					} else {
						$lCurValue = $this->lFieldArr[$pName]["CurValue"];
					}
				}

				switch($this->lFieldArr[$pName]["CType"]) {
					case "select":
					case "mselect":
					case "radio":
					case "checkbox":
						if (!is_array($this->lFieldArr[$pName]["SrcValues"])) {
							$scrval = $this->ReplaceSqlFields($this->lFieldArr[$pName]["SrcValues"]);
						} else {
							$scrval = $this->lFieldArr[$pName]["SrcValues"];
						}
						// echo $scrval . '<br>';

						if ( $this->lFieldArr[$pName]["CType"]=="checkbox" &&  $this->lFieldArr[$pName]["TransType"]==MANY_TO_BIT_ONE_BOX) $dcs=MANY_TO_BIT_ONE_BOX;
						else $dcs=($this->lFieldArr[$pName]['Separator'] ? $this->lFieldArr[$pName]['Separator'] : DEF_CONTR_SEP);

						return $this->extended($pName, $this->lFieldArr[$pName]['CType'], $scrval, $lCurValue, $this->lFieldArr[$pName]["AddTags"], $dcs, 0, (int)$this->lJsValidate, $this->lFieldArr[$pName]["error_class"], $this->lFieldArr[$pName]["ErrorString"]);
						break;
					case "text":
					case "password":
					case "textarea":
					case "hidden":
					case "mlfield":
					case "file":
						return $this->simple($pName, $lCurValue);
						//return $this->simple($pName, $this->lFieldArr[$pName]['CType'], $lCurValue, $this->lFieldArr[$pName]["AddTags"]);
						break;
					default:
						if ($this->lFieldArr[$pName]["CType"] == "action") {
							if (!$this->lFieldArr[$pName]["Hidden"]) {
								$lDispName = $this->lFieldArr[$pName]["DisplayName"];
								$lActionImgSrc = $this->lFieldArr[$pName]["ImgSrc"];
								$lButtonHtml = ($this->lFieldArr[$pName]["ButtonHtml"] ? $this->lFieldArr[$pName]["ButtonHtml"] : '');

								return $this->actionbutton((($lDispName) ? $lDispName : $pName), $this->lFieldArr[$pName]["AddTags"], $lActionImgSrc, null, null, $lButtonHtml);
							} else {
								return '';
							}
						} else {
							trigger_error("A problem occured with the type of the field.", E_USER_WARNING);
							return '';
						}
				}
			} else {
				trigger_error("Cannot Find {$pName} field", E_USER_WARNING);
				return '';
			}
		}
	}

	function ReplaceHtmlFields($pStr) {
		return preg_replace("/\{(.*?)\}/e", "\$this->HtmlPrepare('\\1')", $pStr);
	}

	function ReplaceErrorFields($pStr) {
		return preg_replace("/\{(.*?)\}/e", "\$this->HtmlPrepare('\\1', 1)", $pStr);
	}

	function SetError($pField, $pError) {
		$pError = preg_replace('/^Error:[\s]*/i', '', $pError);
		$this->lFieldArr[$pField]["ErrorString"] = getstr($pError);
		$this->lErrorCount++;
	}

	function ShowError($pErrStr) {
		if ((int)$this->StopErrDisplay) return $pErrStr . '<br/>';
		return '<div class="errorHolder">' . $pErrStr . '</div>';
	}
	function ShowErrorByField($pFieldName, $pErrStr) {
		if ((int)$this->StopErrDisplay)
			return $pFieldName . ': ' . $pErrStr . '<br/>';
		return '<div class="errstr"><span class="errorField">' . $pFieldName . ': </span><span class="errorString">' . $pErrStr . '</span></div>';
	}

	function SetFormAction($pAction) {
		if ($pAction) {
			$this->lFormAction = $pAction;
		} else {
			$this->lFormAction = null;
		}
	}

	function SetFormHtml($pFormHtml) {
		$this->lFormHtml = $pFormHtml;
	}

	function SetFormName($pFormName) {
		$this->lFormName = $pFormName;
		$this->lFieldArr['kfor_name']['CurValue'] = $pFormName;
	}

	function StopErrDisplay($errdispl) {
		$this->StopErrDisplay = $errdispl;
	}

	function GetErrStr() {
		return $this->RetErrStr;
	}

	function DisplayForm($pExtraTags = '') {

		$lActionFrmStr = '';

		if (!$this->lFormHtml) {
			$lFrmStr .= '<table>';
		// i ako njama html template si go buildvame po nai-tupia nachin
			foreach ($this->lFieldArr as $lFname => $lFArr) {
				switch($lFArr['CType']) {
					case "select":
					case "mselect":
					case "radio":
					case "checkbox":
					case "text":
					case "file":
					case "password":
					case "textarea":
					case "mlfield":
						if (($lFArr['VType']=="mlstring") || ($lFArr['VType']=="mlint")) {
							$lFrmStr .= '{' . $lFname . '}';
							$this->lFieldArr[$lFname]["DisplayFormat"]=MLSTR_D_NOTABLE;
						} else
							$lFrmStr .= '<tr><td' . (($lFArr['CType'] == 'textarea') ? ' valign="top"' : '') . '><b>' . (($lFArr["DisplayName"]) ? $lFArr["DisplayName"] : $lFname) . ':</b></td><td valign="top">{' . $lFname . '}</td></tr>';
						break;
					case "hidden":
						if ($lFArr["ShowValue"])
							$lFrmStr .= '<tr><td><b>' . (($lFArr["DisplayName"]) ? $lFArr["DisplayName"] : $lFname) . ':</b></td><td valign="top">{#' . $lFname . '}{' . $lFname . '}</td></tr>';
						else
							$lActionFrmStr .= '{' . $lFname . '}';
						break;
					default:
						if ($lFArr["CType"] == "action" && !$lFArr["Hidden"]) {
							$lActionFrmStr .= '{' . $lFname . '}';
						}
				}
			}
			$lFrmStr .= '</table>';
			$lFrmStr .= '<p>' . $lActionFrmStr;
			$this->lFormHtml = $lFrmStr;
			// $lFrmStr = $this->ReplaceHtmlFields($lFrmStr);
		}
		if ($this->lErrorCount) {
			$lFrmErrStr = '';
			if(!$this->lJsValidate) {
				foreach($this->lFieldArr as $k => $v) {
					if ($v["ErrorString"]) {
						$lFrmErrStr .= $this->ShowErrorByField((($v["DisplayName"]) ? $v["DisplayName"] : $k), $v["ErrorString"]);
						//preg_replace("/\{\!$k}/e", this->$lExtErrorString,$this->lFormHtml);
						$this->lFormHtml = str_replace("{!$k}", $this->lExtErrorString, $this->lFormHtml);
						//tuka sym;
					}
				}
			} else {
				//~ $lFrmErrStr = DEFAULT_ERROR_STRING;
				//for debuging
				/*foreach($this->lFieldArr as $k => $v) {
					if ($v["ErrorString"]) {
						trigger_error('POLE: ' . $k .  ' GRE6KA: ' . $v["ErrorString"], E_USER_NOTICE);
						echo 'POLE: ' . $k .  ' GRE6KA: ' . $v["ErrorString"] . '<br/><br/>';
					}
				}*/
				$lFrmErrStr = preg_replace('/{ErrorCount}/', (int)$this->lErrorCount, DEFAULT_ERROR_STRING);
			}

			if (!(int)$this->StopErrDisplay) {
				if( $lFrmErrStr ){
					$lFrmErrStr = '<div class="errorHolder">' . $lFrmErrStr . '</div>';
				}

				if( preg_match('/<!--###ERRORS###-->/', $this->lFormHtml) ){
					$this->lFormHtml = preg_replace('/<!--###ERRORS###-->/', $lFrmErrStr, $this->lFormHtml);
				}else{
					$this->lFormHtml = $lFrmErrStr . $this->lFormHtml;
				}
			} else {
				if( $lFrmErrStr ){
					$lFrmErrStr = '<div class="errorHolder">' . $lFrmErrStr . '</div>';
				}
				$this->RetErrStr = $lFrmErrStr;
			}
		} else {

			// gledame dali imame backurl poneje backurl se generira samo pri submit na nqkakyv action
			if($_REQUEST["backurl"] && (int)$this->lFieldArr[$this->lCurAction]['SuccessMsg']) {
				$lFrmSuccessStr = '<div class="errorHolder successHolder">' . DEF_SUCCESS_MESSAGE . '</div>';

				if( preg_match('/<!--###ERRORS###-->/', $this->lFormHtml) ){
					$this->lFormHtml = preg_replace('/<!--###ERRORS###-->/', $lFrmSuccessStr, $this->lFormHtml);
				}else{
					$this->lFormHtml = $lFrmSuccessStr . $this->lFormHtml;
				}
			}

		}

		if ($this->lFieldArr[$this->lCurAction]["ActionMask"] & ACTION_VIEW) {
			//view mode
			if (!$this->lvFormHtml) $this->lvFormHtml=$this->lFormHtml;
			$this->lvFormHtml=preg_replace("/\{([^_#\*].*?)\}/", "{@\\1}",$this->lvFormHtml);
			$this->lvFormHtml = $this->ReplaceHtmlFields($this->lvFormHtml);
			if ($this->printform) {
				echo $this->ShowError($_REQUEST['frameerrstr']) . $this->lvFormHtml;
			} else {
				return $this->ShowError($_REQUEST['frameerrstr']) . $this->lvFormHtml;
			}
		} else {
			if ($this->lAddbackurl) {$this->lFormHtml .= "{backurl}{selfurl}{kfor_name}";}
			$this->lFormHtml = $this->ReplaceHtmlFields($this->lFormHtml);
			//~ if ($this->lJsValidate) { $this->lFormHtml = $this->ReplaceErrorFields($this->lFormHtml); }
			if ($this->printform) {
				echo '<form ' . (true ? 'enctype="multipart/form-data" ' : '') . 'action="' . (($this->lFormAction) ? $this->lFormAction : getenv("SCRIPT_NAME")) . '" method="' . strtolower($this->lFormMethod) . '" name="' . $this->lFormName . '" '. $pExtraTags .'>' . $this->lFormHtml . '</form>';
			} else {
				return '<form ' . (true ? 'enctype="multipart/form-data" ' : '') . 'action="' . (($this->lFormAction) ? $this->lFormAction : getenv("SCRIPT_NAME")) . '" method="' . strtolower($this->lFormMethod) . '" name="' . $this->lFormName . '" '. $pExtraTags .'>' . $this->lFormHtml . '</form>';
			}
		}
	}

	function ExecActionSql() {
		global $gCn;

		$gCn = Con();
		$gCn->Close();
		$gCn->Open();
		if (is_array($this->lFieldArr[$this->lCurAction]["SQL"])) {
			// XXX : da execute-va array ot sql-i
			// tva e shibano shtoto sled vseki statement trjabva da fetchne resultatite i da gi populni po poletata
			// v sluchai che imame sp i posle update na textovo pole kakuvto shte imame sus sigurnost
		} else {
			// XXX : osven tva trjabva da se napravi da se executnat njakolko sql-a ako imame
			// pole many_to_many kato samo za nego stoinostite shte sa razlichni
			$lSqlStr = $this->ReplaceSqlFields($this->lFieldArr[$this->lCurAction]["SQL"]);

//			var_dump($this->debug);
//			exit;
			if ($this->debug) {
				echo 'Executing: ' . $lSqlStr;
			}

			@$gCn->Execute($lSqlStr);
			if ($gCn->GetLastError()) {
				$this->SetError($this->lCurAction, $gCn->GetLastError());;
			}
		}
	}

	function FetchResults() {
		global $gCn;

		$mFlag = null;

		foreach($this->lFieldArr as $k => $v) {
			if ($v["CType"] == 'mselect' || $v["CType"] == 'checkbox') {
				switch($v["TransType"]) {
					case MANY_TO_MANY:
						if ($mFlag) {
							trigger_error("Ne moje da ima poveche ot 1 pole s transtype many_to_many", E_USER_ERROR);
							// a moje i da moje ama za momenta ne vijdam smisul
						}
						$mFlag = $k;
						break;
					case MANY_TO_STRING:
					case MANY_TO_BIT:
					case MANY_TO_BIT_ONE_BOX:
					case MANY_TO_SQL_ARRAY:
						break;
					default:
						trigger_error("Field ($k) has Invalid TransType!", E_USER_ERROR);
				}
			}
		}

		$gCn->MoveFirst();

		if (!$gCn->Eof()) {
			foreach($this->lFieldArr as $k => $v) {
				if ($k != $mFlag && array_key_exists($k, $gCn->mRs)) {
					if ($v["CType"] == 'mselect' || $v["CType"] == 'checkbox' || $v["VType"] == 'mlstring'|| $v["VType"] == 'mlint') {
						switch($v["TransType"]) {
							case MANY_TO_STRING:
								$this->lFieldArr[$k]["CurValue"] = explode(DEF_SQLSTR_SEPARATOR, $gCn->mRs[$k]);
								break;
							case MANY_TO_BIT:
								$this->lFieldArr[$k]["CurValue"] = int2bitarray($gCn->mRs[$k]);
								break;
							case MANY_TO_BIT_ONE_BOX:
								$this->lFieldArr[$k]["CurValue"] =$gCn->mRs[$k];
								break;
							case MANY_TO_SQL_ARRAY:
								// da se napravi!
								//var_export(substr($gCn->mRs[$k], 1, -1));

								$this->lFieldArr[$k]["CurValue"] = pg_unescape_array($gCn->mRs[$k]);
								//$this->lFieldArr[$k]["CurValue"] = pg_unescape_array($gCn->mRs[$k]);
								break;
						}
					} else {
						if ($v["VType"] == 'date') {
							$this->lFieldArr[$k]["CurValue"] = formatformdate($gCn->mRs[$k]);
						} else {
							$this->lFieldArr[$k]["CurValue"] = $gCn->mRs[$k];
						}
						$this->ShowFieldValue($k);
					}
				}
			}

			if ($mFlag) {
				$lTempArr = array();

				while (!$gCn->Eof()) {
					$lTempArr[] = $gCn->mRs[$mFlag];
					$gCn->MoveNext();
				}

				$this->lFieldArr[$mFlag]["CurValue"] = $lTempArr;
			}
		}
	}

	function GetRedirUrl($pAction) {
		global $backurl;
		$lRedirUrl = $this->lFieldArr[$pAction]['RedirUrl'];

        if ($lRedirUrl == "{#selfurl}") {
		   if ($this->lFieldArr[$pAction]['ActionMask'] & ACTION_REDIRVIEW) {
				$lfoundView = 0;
				foreach ($this->lFieldArr as $k => $v) {
					if (($v['CType'] == "action") && ($v['ActionMask'] & ACTION_VIEW)) {
						$this->lFieldArr['selfurl']['CurValue'] = getenv("SCRIPT_NAME") . "?tAction=" . $k;

						foreach ($this->lFieldArr as $kk => $vv) {
							if ($vv['PK']) {
								$this->lFieldArr['selfurl']['CurValue'] .= "&" . $kk . "=" . $this->lFieldArr[$kk]['CurValue'];
							}
						}

						//$this->lFieldArr['backurl']['CurValue'] .= "&@backurl=" . urlencode($this->lFieldArr['backurl']['CurValue']);
						$this->lFieldArr['selfurl']['CurValue'] = AddParamtoURL($this->lFieldArr['selfurl']['CurValue'], 'backurl=' . urlencode($backurl));

						if (($pAction == $this->lCurAction) && $this->lFieldArr[$pAction]["ErrorString"] && ($this->lFieldArr[$pAction]["ActionMask"] & ACTION_REDIRERROR)) {
							$this->lFieldArr['selfurl']['CurValue'] = AddParamtoURL($this->lFieldArr['selfurl']['CurValue'], 'frameerrstr=' . urlencode($this->lFieldArr[$pAction]["ErrorString"]));
						}

						$lfoundView = 1;
						break;
					}
				}
				if (!$lfoundView) $lRedirUrl ="";
			} else {
				if (!$this->lFieldArr['selfurl']['CurValue']) {
					$lRedirUrl ="";
				} else {
					if (preg_match("/tAction=([^\&\?]+)/", $this->lFieldArr['selfurl']['CurValue'], $match)) {
						if ($this->lFieldArr[$match[1]]['ActionMask'] & ACTION_VIEW) {
							$this->lFieldArr['selfurl']['CurValue'] = AddParamtoURL($this->lFieldArr['selfurl']['CurValue'],'backurl=' . urlencode($backurl));
						} else {
							$lRedirUrl ="";
						}
					} else {
						if (!($this->lFieldArr['new']['ActionMask'] & ACTION_VIEW))
							$lRedirUrl ="";
						else
							$this->lFieldArr['selfurl']['CurValue'] = AddParamtoURL($this->lFieldArr['selfurl']['CurValue'],'backurl=' . urlencode($backurl));
					}
				}
			}
        }

		if (!$lRedirUrl || ($lRedirUrl == "{#backurl}")) {
            $lRedirUrl = $backurl;
		} else {
			$lRedirUrl = preg_replace("/\{([^\#].*?)\}/e", "urlencode(\$this->lFieldArr['\\1']['CurValue'])", $lRedirUrl);
			$lRedirUrl = preg_replace("/\{\#(.*?)\}/e", "\$this->lFieldArr['\\1']['CurValue']", $lRedirUrl);
		}

		return  $lRedirUrl;
	}

	function Redirect() {
		$lRedirUrl =  $this->GetRedirUrl($this->lCurAction);

		if ($this->debug) {
			echo 'Redirecting to: <a href="' . $lRedirUrl . '">' . $lRedirUrl . '</a>';
			exit;
		}

		$lRedirUrl = "Location: " . $lRedirUrl;

		// var_dump($lfoundView);
		// echo $lRedirUrl;
        // exit;

		Header($lRedirUrl);
        exit;
	}

	function ExecAction() {

		if (!$this->lExecFlag) {
			$lActMask = $this->lFieldArr[$this->lCurAction]["ActionMask"];
			$this->lExecFlag = 1;

			if ($lActMask & ACTION_CHECK) {
				$this->DoChecks();
			}
			if ($this->lErrorCount > 0) {
				//$this->DisplayForm();
				return;
			}

			if ($lActMask & ACTION_CCHECK) {
				$this->DoCustomChecks();
			}

			if ($this->lErrorCount > 0) {
				//$this->DisplayForm();
				return;
			}

			if ($lActMask & ACTION_EXEC) {
				$this->ExecActionSql();
			}


			// !!
			if (($lActMask & ACTION_FETCH) && $this->lErrorCount == 0) {
				$this->FetchResults();
			}
		}
	}

	function CheckField($pField) {

		$lElemId = DEF_FORM_FIELD_ID . $pField;

		if (is_array($this->lFieldArr[$pField]['AddTags'])) {
			if($this->lFieldArr[$pField]['AddTags']['id']) {
				$lElemId = $this->lFieldArr[$pField]['AddTags']['id'];
			}
		}

		$lArrErrors = array (
			'error_string' => '',
			'error_holder' => DEF_ERROR_ID_HOLDER . $pField,
			'error_field' => $lElemId,
			'error_field_class' => ($this->lFieldArr[$pField]["error_class"] ? $this->lFieldArr[$pField]["error_class"] : DEF_ERROR_FIELD_CLASS),
			'error_js' => ($this->lFieldArr[$pField]["error_js"] ? $this->lFieldArr[$pField]["error_js"] : ''),
			'ajax_error_js' => ($this->lFieldArr[$pField]["ajax_error_js"] ? $this->lFieldArr[$pField]["ajax_error_js"] : ''),
			'valid_js' => ($this->lFieldArr[$pField]["valid_js"] ? $this->lFieldArr[$pField]["valid_js"] : ''),
			'req_js' => ($this->lFieldArr[$pField]["req_js"] ? $this->lFieldArr[$pField]["req_js"] : ''),
		);

		$this->lExecFlag = 1;

		//normal checks
		$this->DoChecks($pField);
		if ($this->lErrorCount > 0) {
			$lArrErrors['error_string'] = $this->lFieldArr[$pField]["ErrorString"];
			return $lArrErrors;
		}

		//custom checks
		$this->DoCustomChecks($pField);
		if ($this->lErrorCount > 0) {
			$lArrErrors['error_string'] = $this->lFieldArr[$pField]["ErrorString"];
			return $lArrErrors;
		}

		return $lArrErrors;

	}

	function Display($pExtraTags = '') {
		$lRetStr = '';
		$lActMask = $this->lFieldArr[$this->lCurAction]["ActionMask"];

		if (!$this->lExecFlag) {
			if ($lActMask & ACTION_CHECK) {
				$this->DoChecks();
			}

			if ($this->lErrorCount > 0) {
				$lRetStr = $this->DisplayForm($pExtraTags);
				return $lRetStr;
			}

			if ($lActMask & ACTION_CCHECK) {
				$this->DoCustomChecks();
			}

			if ($this->lErrorCount > 0) {
				$lRetStr = $this->DisplayForm($pExtraTags);
				return $lRetStr;
			}

			if ($lActMask & ACTION_EXEC) {
				$this->ExecActionSql();
			}
		}

		if ($this->lErrorCount > 0) {
			if ($this->lFieldArr[$this->lCurAction]["ErrorString"] && ($lActMask & ACTION_REDIRERROR)) {
				if (!$this->lFieldArr[$this->lCurAction]['RedirUrl']) {
					$this->lFieldArr[$this->lCurAction]['RedirUrl'] = "{#selfurl}";
					$this->Redirect();
				}
			}
			else {
				$lRetStr = $this->DisplayForm($pExtraTags);
			}
			return $lRetStr;
		}

		if (!$this->lExecFlag) {
			if ($lActMask & ACTION_FETCH) {
				$this->FetchResults();
			}
		}
		if ($lActMask & ACTION_SHOW) {
			$lRetStr = $this->DisplayForm($pExtraTags);
		}

		if ($lActMask & ACTION_REDIRECT) {
			$this->Redirect();
		}
		return $lRetStr;
	}



	function actionbutton($pAction, $pExt = null, $pImgSrc = null, $pView=null, $pAddbackurl = null, $pButtonHtml = '') {
		$lExtStr = '';
		$btntype = ($pImgSrc ? 'image' : "submit");
		global $selfurl, $forwardurl;
		$url1="";
		if ($pView) {
			if ($this->lFieldArr[$pView]["RedirUrl"]) {
			    if (($this->lFieldArr[$pView]["RedirUrl"]=="{#backurl}") || ($this->lFieldArr[$pView]["RedirUrl"]=="{#selfurl}"))
					$sburl=1;
				$url=$this->GetRedirUrl($pView);
			}
			else
			     {
				$url=getenv('REQUEST_URI');
				$url1=preg_replace("/tAction=([^\&\?]*)/","tAction=".$pView,$url);
			      }
			if ($url==$url1) {
				$url=AddParamtoURL($url,"tAction=$pView");
				foreach ($this->lFieldArr as $kk => $vv) {
					if ($vv['PK']) {
						$url.= "&" . $kk . "=" . $this->lFieldArr[$kk]['CurValue'];
					}
				}
			} else if ($url1) $url=$url1;
			if (($pAddbackurl) && (!$sburl)) {
				$url=ClearParaminURL($url,"backurl");
				$url=ClearParaminURL($url,"selfurl");
				$url=AddParamtoURL($url,'backurl='.urlencode($forwardurl));
				$url.=AddParamtoURL($url,'selfurl='.urlencode($selfurl));
			}
			$btntype= "button";
		}

		$lVal = '';
		if (is_array($pExt)) {
			foreach ($pExt as $k => $v) {
				if (in_array($k, array("name", "value", "type"))) {
					trigger_error("V extension-a ima pole koeto verojatno se dublira s njakoe ot standartnite tagove", E_USER_WARNING);
				}

				if (preg_match('/onclick/i', $k) && (strlen($k) == 7) && !$lVal) {
					$lVal = str_replace('{loc}', 'window.location=\''.$url.'\';return false;', $v);
					$v = $lVal;
				}

				$lExtStr .= ' ' . $k . '="' . $v . '"';
			}
		}

		if ($pView && !$lVal) {
			$lExtStr .= ' onclick="javascript:window.location=\''.$url.'\';return false;"';
		}

		if ($this->lFieldArr[$pView]['IsLink']) {
			return '
				<input id="' . $pView . '" type="'.$btntype.'" name="tAction" value="' . $pAction . '"' . $lExtStr. ' style="display: none" /><a href="#" onclick="javascript: document.getElementById(\'' . $pView . '\').click();return false;">' . $this->lFieldArr[$pView]['DisplayName'] . '</a>
			';
		} elseif ($pButtonHtml != '' && $btntype != 'image') {
			$lRet = preg_replace('/{type}/me', $btntype, $pButtonHtml);
			$lRet = preg_replace('/{extstr}/me', $lExtStr, $lRet);
			$lRet = preg_replace('/{value}/me', $pAction, $lRet);
			return $lRet;
		} else {
			return '<input type="'.$btntype.'" '. ($pImgSrc ? 'src="'. $pImgSrc .'" ' : '') .' name="tAction" value="' . $pAction . '"' . $lExtStr. ' />';
		}

	}

}

// razni podpomagashti funkcii

function array2bitint($pArr) {
	return array_sum($pArr);
}

function int2bitarray($pInt) {
	$lala = decbin($pInt);
	$bitArr = preg_split('//', $lala, -1, PREG_SPLIT_NO_EMPTY);
	$bitArr = array_reverse($bitArr);
	$tArr = array();
	foreach ($bitArr as $k => $v) {
		if ($v) {
			$tArr[] = pow(2, $k);
		}
	}
	return $tArr;
}

/*
	tova se vika dva pati - pri validirane, i pri zamestvane na stojnostite v sql-a

*/
function manageckdate(&$pStr, $pType, $pCheck = 1) {
	$pStr = trim($pStr);
	$lstrRes = '';
	$lstrError = '';

//~ var_dump($pStr  );

	if (!$pType || ($pType == DATE_TYPE_ALL) || ($pType == DATE_TYPE_DATETIME)) {

		$lstrError = ERR_WRONG_DATETIME;
		$lSpacePos = strpos($pStr, ' ');
		if (!$lSpacePos) {$lPossibleDate = $pStr;}
		else {
			$lPossibleDate = trim(substr($pStr, 0, $lSpacePos));
			$lPossibleTime = trim(substr($pStr, $lSpacePos));
		}

		$lRes = true;
		if (($pType == DATE_TYPE_DATETIME) && !$lSpacePos) {
			$lRes = false;
		}

		if (!$lSpacePos && $lRes) {
			$lRes = ckdt3($lPossibleDate, $pCheck);
			if (!$pCheck && is_array($lRes)) {
				$lstrRes = adodb_mktime(0, 0, 0, $lRes[2], $lRes[1], $lRes[3]);
				$lstrRes = newformatsqldate($lstrRes);
			}
		}
		elseif ($lRes) {
			$lRes = ckdt3($lPossibleDate, $pCheck);
			if (($pCheck && $lRes) || (!$pCheck && is_array($lRes))) {
				if (!$pCheck) {
					$lstrRes = adodb_mktime(0, 0, 0, $lRes[2], $lRes[1], $lRes[3]);
					$lstrRes = newformatsqldate($lstrRes);
				}
				$lRes = cktm($lPossibleTime, $pCheck);
				//~ echo "manageckdate" . $lRes;

				if (!$pCheck && is_array($lRes)) {
					$lstrRes .= " " . newformatsqltime($lRes);
				}
			}
		}

	}
	elseif ($pType == DATE_TYPE_TIME) {
		$lRes = cktm($pStr, $pCheck);
		$lstrRes = newformatsqltime($lRes);
		$lstrError = ERR_WRONG_TIME;
	}
	elseif ($pType == DATE_TYPE_DATE) {
				//~ echo $pStr . ' ' . $pCheck . '<br>';

		$lRes = ckdt3($pStr, $pCheck);
		/*
			това не работи с двуцифрени години, а само с четирицифрени
			$lstrRes = adodb_mktime(0, 0, 0, $lRes[2], $lRes[1], $lRes[3]);

			Това : '20' . $lRes[3] трябва да се съглавува с js-то,
		*/

		//~ var_dump( $lRes );
			//~ echo  '<br>'; // mm/dd/yy

		//~ $lstrRes = adodb_mktime(0, 0, 0, $lRes[1], $lRes[2], '20' . $lRes[3]);
		$lstrRes = adodb_mktime(0, 0, 0, $lRes[2], $lRes[1], $lRes[3]);
		$lstrRes = newformatsqldate($lstrRes);

		//~ var_dump( $lstrRes ); echo '<br>';

		$lstrError = ERR_WRONG_DATE;
	}

	if ($pCheck) {
		if ($lRes) {return '';}
		else {return $lstrError;}
	}
	else {
		if ($lRes === false) {return $lRes;}
		else {return $lstrRes;}
	}

}

function ckdt3($pStr, $pCheck = 1) {
	if (!preg_match('/[\/\\\.\-]/', $pStr, $lMatches)) {
		return false;
	}

	$lSeparator = $lMatches[0]; //Kato nqma skobi v reg expa v 0-q element e kakvoto e machnalo

	if(! preg_match('/^(\d{1,2})\\' . $lSeparator . '(\d{1,2})\\' . $lSeparator . '(\d{2,4})$/i', $pStr, $lMatches)){
		return false;
	}

	if (!checkdate($lMatches[2], $lMatches[1], $lMatches[3])) {
		return false;
	}

	//~ $lNewMatches[1] = 	$lMatches[2];
	//~ $lNewMatches[2] = 	$lMatches[1];
	//~ $lNewMatches[3] = 	$lMatches[3];

	if (!$pCheck) {return $lMatches;}
	else {return true;}
}

function cktm($pStr, $pCheck = 1) {
	if (!preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/i', $pStr, $lMatches))
		if (!preg_match('/^(\d{1,2}):(\d{2})$/i', $pStr, $lMatches))
			if (!preg_match('/^(\d{1,2})$/i', $pStr, $lMatches))
				return false;
	if ($lMatches[1] < 0 || $lMatches[1] > 23) {
		return false;
	}
	if (($lMatches[2] < 0 || $lMatches[2] > 59) && count($lMatches) > 2) {
		return false;
	}
	/*
	# ifdef CHECK_SECONDS
	if (($lMatches[3] < 0 || $lMatches[3] > 59) && count($lMatches) > 3) {
		return false;
	}
	# endif
	*/
	if (!$pCheck) {
		return $lMatches;
	} else {
		return true;
	}
}

function cktm_ampm($pStr, $pCheck = 1) {
	if (!preg_match('/^(\d{1,2}):(\d{2})\s*(am|pm)$/i', $pStr, $lMatches)) {
		return false;
	}
	if ($lMatches[1] < 1 || $lMatches[1] > 12) {
		return false;
	}
	if ($lMatches[2] < 0 || $lMatches[2] > 59) {
		return false;
	}
	if (!$pCheck) {
		if ($lMatches[1] == 12) $lMatches[1] = 0;

		if (strtolower($lMatches[3]) == 'pm') {
			$lMatches[1] += 12;
		}
		return $lMatches;
	}
	else {return true;}
}

function formatsqldate($pTst) {
	// return adodb_date("m/d/Y H:i", $pTst); // Y-m-d H:i:s //  H:ia
	return adodb_date("Y-m-d H:i", $pTst); // Y-m-d H:i:s //  H:ia
}

// tova se vika,l kogato date se zamestva v sql-a
function newformatsqldate($pTst) {

	//return adodb_date("m-d-Y", $pTst); // Y-m-d H:i:s //  H:ia
	return adodb_date("Y-m-d", $pTst); // Y-m-d H:i:s //  H:ia
}

function newformatsqltime($parrTime) {
	return $parrTime[1] . ':' . ($parrTime[2] ? $parrTime[2] : '00') . ($parrTime[3] ? ':' . $parrTime[3] : ':00'); // . ':' . ($parrTime[3] ? $parrTime[3] : '00');
}

// tova se vika, kogato date se vzema ot bazata
function formatformdate($pStr) {

	if (is_null($pStr))
		return '';

	//~ var_dump( $pStr ); 01/09/2012
	$lSeparator = '/';

	// year in db is always 4 digits
	if (!preg_match('/^(\d{1,2})\\' . $lSeparator .  '(\d{1,2})\\' . $lSeparator . '(\d{4})$/i', $pStr, $lMatches)) {
		return '';
	}
	//~ var_dump( $lMatches );
	$pStr =  (int)$lMatches[2] . '/' .  (int)$lMatches[1] . '/' .  (int) substr( $lMatches[3], 2, 2 );
	return $pStr;

	//~ if (preg_match('/\d+:\d+:\d+/', $pStr)) {
		//~ return substr($pStr, 0, strrpos($pStr, ':')+3);
	//~ }

	//~ return $pStr; // date('d/m/Y H:i:s', strtotime($pStr));
	// return str_replace('.', '/', $pStr);
}

function formatformdate1($pStr) {
	if (is_null($pStr))
		return '';
	return $pStr; // date('d/m/Y H:i:s', strtotime($pStr));
	// return str_replace('.', '/', $pStr);
}

function getglobalformnumber() {
	static $globalformcount = 0;
	return (++$globalformcount);
}

function getFCKtoolbar($tb) {
	global $FCK_Custom_Toolbars;
	$tbArr = array(
		1 => 'BasicTools',
		2 => 'AllTools',
	);
	if (is_array($FCK_Custom_Toolbars)) {
		$tbArr = $tbArr + $FCK_Custom_Toolbars;
	}

	if (!array_key_exists($tb, $tbArr)) return $tbArr[1];
	return $tbArr[$tb];
}

function float2array($pVal) {
	return ($pVal) ? $pVal : 'null';
}

?>