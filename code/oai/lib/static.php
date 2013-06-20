<?php
//Statichno slagame ezika poneje nqmame takava tablica
$_SESSION['langs'][2]=array(
	'code'=> 'en', 
	'name' => 'en', 
	'langid' => 2
);

ini_set('display_errors', 'Off');
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/conf.php');
require_once(PATH_ECMSFRCLASSES . 'static.php');

$user = unserialize($_SESSION['suser']);



mb_internal_encoding('utf-8');//Za da rabotqt korektno mb funkciite

CleanCookiesFromRequest();
// crewrite instance
$rewrite =& new crewrite();



function CleanCookiesFromRequest() {
	foreach ($_COOKIE as $k => $v) {
		unset($_REQUEST[$k]);
	}
}

/* ### USER SECTION BEGIN ### */
$COOKIE_DOMAIN = $_SERVER['SERVER_NAME'];

function modifyPageUrlStringParam($url, $par, $val) {
	if (preg_match('/[?&]' . $par . '=[^&]+/', $url)) {
		$res = preg_replace('/([?&])' . $par . '=[^&]+/', '${1}' . $par . '=' . $val, $url);
	}else {
		if (preg_match('/[?].*=\w+/', $url)) {
			$res = $url . '&' . $par . '=' . $val;
		}else{
			$res = $url . '?' . $par . '=' . $val;
		}
	}	
	return $res;
}

function DefObjTempl() {
	global $user;
	global $rewrite;
	$t = array(		
	);
	
	return $t;
}

function showItemIfExists ($item, $leftCont, $rightCont, $nl2br = false) {
	return ($item ? $leftCont . ($nl2br ? nl2br($item) : $item) . $rightCont : '');
}

function CutText($d, $len = 80) {
	$d = strip_tags($d);
	if (mb_strlen($d, 'UTF-8') < $len) return $d;
	$cut = mb_substr($d, 0, $len, 'UTF-8');
	return mb_substr($cut, 0, mb_strrpos($cut, ' ', 'UTF-8'), 'UTF-8') . '...';
}
function parseSpecialQuotes($str) {
	return str_replace(array('&bdquo;', '„', '“', '”', '&laquo;', '&raquo;', '&ldquo;', '&rdquo;'), array('"', '"', '"', '"', '"', '"', '"', '"'), $str);
}

	
function parseToInt($pItem){
	return (int) $pItem;
}

/**
 * Връща условието едно издание да е публикувано
 * Едно издание е публикувано, ако published status-а му е един от следните
 * 	-1 - Публикуван предишен брой
 *  0 - Публикуван текущ брой
 *  
 *  Ако едно издание има статус 1 - значи това е бъдещ брой и не трябва да ги показваме
 *  
 * @param $pTableName - името в заявката на таблицата J_ISSUES
 * @param $pAddAnd - дали да добави AND най отпред в резултата
 * 
 * 
 */
function getPublishedIssueWhere($pTableName, $pAddAnd = false){
	$lResult = ' ';
	if( $pAnd )
		$lResult .= 'AND ';
	$lResult .= ' (' . $pTableName . '.published = -1 OR ' . $pTableName . '.published = 0) ';
	return $lResult;
}

/**
 * Връща условието за филтриране на статиите по даден identifier
 * 
 * Тук трябва да знаем, че идентификатора отговаря на doi номера
 * на статията, а не на самото id
 *  
 * @param $pTableName - името в заявката на таблицата J_ISSUES
 * @param $pArticleIdentifier - identifier-а на статията
 * @param $pAddAnd - дали да добави AND най отпред в резултата
 * 
 * 
 */
function getArticleIdentifierWhere($pTableName, $pArticleIdentifier, $pAddAnd = false){
	$lResult = ' ';
	if( $pAddAnd )
		$lResult .= 'AND ';
	$lResult .= ' ' . $pTableName . '.doi = \'' . q(trim($pArticleIdentifier)) . '\' ';
	return $lResult;
}

/**
 * Показва параметъра в request възела, ако е зададен
 * @param unknown_type $pParamName - името на параметъра
 * @param unknown_type $pParamValue - стойността на параметъра
 */
function displayRequestParamIfExists($pParamName, $pParamValue){	
	if( $pParamValue == '')
		return;
	return ' ' . $pParamName . '="' . h($pParamValue) . '"';	
}

function getPreviousRecordsCount($pPageNum, $pPageSize){
	return ($pPageNum - 1) * $pPageSize;
}

/**
 * Escape-ва стойността за да можем да я сложим в xml-а
 * 
 * Първо правим convertStringToUtf8  за да няма не-utf8 символи , 
 * а после правим htmlspecialchars за да се ескейпнат правилно <>& и т.н.
 */

function xmlEscape($pValue){
	$pValue = convertStringToUtf8($pValue);	
	return htmlspecialchars($pValue);
}

/*
	Махаме xml-таговете. За целта лоудваме в xml dom и връщаме textContent-а на руута. Ако не стане лоуд-а - просто връщаме резултата от изпълнението на функцията xmlEscape
*/
function stripXmlTags($pValue){
	$pValue = convertStringToUtf8($pValue);
	$lXmlDom = new DOMDocument('1.0', 'utf-8');
	$lReplacements = array(
		'&' => '&amp;',
		'\'' => '&apos;'
	);
	
	$lXml = '<root>' . str_replace(array_keys($lReplacements), array_values($lReplacements), $pValue) . '</root>';//Escape-ваме xml ентитата не заместваме " и <> за да може да се разпознаят таговете правилно
	
	if($lXmlDom->loadXML($lXml)){		
		return xmlEscape($lXmlDom->documentElement->textContent);
	}
	//Ако не успеем - махаме всичко между < и >
	$pValue = preg_replace('/\<.*\>/smU', '', $pValue);//U modifier за да може да не мачнем всичко от начално < и най-крайно(на друг таг) >
	return xmlEscape($pValue);
}

/*
 * Конвертираме стринга в utf-8, за да няма непознати символи в xml-a
 * Първо правим html_entity_decode за да може ако има entity-та (напр &micro;) да станата символи
 * После правим mb_convert_encoding в utf-8 за да няма не-utf8 символи 
 
*/
function convertStringToUtf8($pValue){	
	$pValue = html_entity_decode($pValue, ENT_COMPAT, "UTF-8");
	//$pValue = mb_convert_encoding($pValue, 'UTF-8', "ISO-8859-15");
	return $pValue;
}

/*
	Връща id-то на статията с подадения doi
	10.3897/zookeys.84.774
	id-то са последните цифри след точката
*/
function getArticleIdFromDoi($pDoi){
	if( preg_match('/\.\s*(\d+)\s*$/', $pDoi, $lMatch)){
		return $lMatch[1];
	}
	return '';
}

function displayModsIssueNumber($pIssueNumber, $pShowIssueNumber = 0){
	if((int)$pIssueNumber != 1 )
		return;
	return '<mods:detail type="issue">	
										<mods:number>' . xmlEscape($pIssueNumber) . '</mods:number>
									</mods:detail>';
}
?>