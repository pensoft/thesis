<?php
function checkSessionLangs() {
	if(is_array($_SESSION['langs']) && count($_SESSION['langs']))
		return;
	$_SESSION['langs'] = array();
	$dbc = new DBCn();
	$dbc->Open();
	$dbc->Execute('select * from languages order by langid');
	$dbc->MoveFirst();
	while(! $dbc->Eof()){
		$_SESSION['langs'][$dbc->mRs['langid']] = array(
			'code' => $dbc->mRs['code'],
			'name' => $dbc->mRs['name'],
			'langid' => $dbc->mRs['langid']
		);
		$dbc->MoveNext();
	}
	$dbc->Close();
	if(! count($_SESSION['langs']) || (! is_array($_SESSION['langs'][DEF_LANGID]))){
		trigger_error(E_USER_NOTICE, 'Empty langs array or don\'t have lang with langid=' . DEF_LANGID . '(default)!');
	}
}

function getlangidbycode($code) {
	foreach($_SESSION['langs'] as $k => $v)
		if($v['code'] == $code)
			return $k;
	return - 1;
}

function getlang($code = false) {
	static $curlangcode;
	static $curlangid;
	if($curlangcode)
		return ($code ? $curlangcode : $curlangid);
		// $curlangcode;
	checkSessionLangs();
	if(isset($_REQUEST['lang']) && (- 1 < ($lid = getlangidbycode($_REQUEST['lang'])))){
		$curlangcode = $_SESSION['langs'][$lid]['code'];
		$curlangid = $lid;
		$_SESSION['CURLANG'] = $_SESSION['langs'][$lid];
		return ($code ? $curlangcode : $curlangid);
	}
	if(! is_array($_SESSION['CURLANG'])){
		$_SESSION['CURLANG'] = $_SESSION['langs'][DEF_LANGID];
	}
	$curlangcode = $_SESSION['CURLANG']['code'];
	$curlangid = $_SESSION['CURLANG']['langid'];
	return ($code ? $curlangcode : $curlangid);
}

function getLanguageName($pLanguageId){
	return $_SESSION['langs'][$pLanguageId]['code'];
}

/**
 * Get property of a specific language
 *
 * @param $pLangId int
 *       	 - ID of a specific language
 * @param $pPropName string
 *       	 - Property name of the specified language
 * @return mixed
 */
function getLangProp($pLangId, $pPropName) {
	if(array_key_exists((int) $pLangId, $_SESSION['langs']) && array_key_exists($pPropName, $_SESSION['langs'][(int) $pLangId]))
		return $_SESSION['langs'][(int) $pLangId][$pPropName];
	return false;
}

function getsqlang($pColName, $pSpecificLang = false) {
	if((int) $pSpecificLang){
		$lSpecificLangId = getLangProp($pSpecificLang, 'langid');
		if($lSpecificLangId)
			return $pColName . '[' . $lSpecificLangId . ']';
		return false;
	}
	return $pColName . '[' . getlang() . ']';
}

function getstr($p, $pRepArr = array()) {
	global $STRARRAY;

	if(is_array($STRARRAY) && array_key_exists($p, $STRARRAY)){
		$lRet = $STRARRAY[$p];

		if(is_array($pRepArr) && count($pRepArr)){
			$lRet = preg_replace("/\{(.*?)\}/e", "\$pRepArr['\\1']", $lRet);
		}

		return $lRet;
	}

	return $p;
}

function negotiate_language() {
	/*
	 * if ($_SERVER[HTTP_ACCEPT_LANGUAGE]) {
	 * $langarr=explode(",",$_SERVER[HTTP_ACCEPT_LANGUAGE]); foreach($langarr as
	 * $k => $v) { $lng=split("[-;]",$v,1); if
	 * (-1<=($lid=getlangidbycode($lng[0]))) {
	 * $_SESSION['CURLANG']=$_SESSION['langs'][$lid]; return ; } } }
	 */
	$_SESSION['CURLANG'] = $_SESSION['langs'][DEF_LANGID];
}

checkSessionLangs();
if(isset($_REQUEST['lang']) && (- 1 < ($lid = getlangidbycode($_REQUEST['lang'])))){
	$_SESSION['CURLANG'] = $_SESSION['langs'][$lid];
}
if(! is_array($_SESSION['CURLANG'])){
	negotiate_language();
}

?>