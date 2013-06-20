<?php


/*
 Заменяме множеството от whitespace-ове с единичен интервал и накрая прави трим
*/
function xslTrim($pText){
	$pText = preg_replace('/\s+/', ' ', $pText);
	$pText = trim($pText);
	return $pText;
}

/**
 * Връща специалния символ, който ще е label на това uri.
 * За целта пази уритата в статичен масив
 * @param unknown_type $pUri
 */
function getUriSymbol($pUri){
	static $lUris = array();
	static $lUriSymbols = array("†","‡", "§", "|", "¶", "#");
	$pUri = trim($pUri);
	if(array_key_exists($pUri, $lUris)){
		return $lUris[$pUri];
	}
	$lUrisLength = count($lUris);
	$lSymbolsLength = count($lUriSymbols);
	$lUris[$pUri] = str_repeat($lUriSymbols[$lUrisLength % $lSymbolsLength], floor($lUrisLength / $lSymbolsLength) + 1);
	return $lUris[$pUri];
}


/**
 * Връща номера на aff възела - маха от него всички букви, т.е.
 * 		'А1' => 1
 * @param unknown_type $pSymbol
 */
function parseAffSymbol($pSymbol){
	$pSymbol = trim($pSymbol);
	if($pSymbol == '')
		return $pSymbol;
	return preg_replace('/[A-Z]/im', '', $pSymbol);
}

/*
 * a) ако започва с urn:lsid:zoobank.org връща 1:

b) ако започва http:// ili www. връща 2
иначе връща 0
*/
function parseUriText($pUri){
	$pUri = trim($pUri);
	if(preg_match('/^urn\:lsid\:zoobank\.org/ism', $pUri)){
		return 1;
	}
	if(preg_match('/^(http:\/\/|www\.)/ism', $pUri)){
		return 2;
	}
	return 0;
}

/**
 Връща името на месеца с подадения номер
 */
function GetMonthName($pMonthNum){
	global $gMonths;
	$pMonthNum = (int)$pMonthNum;
	//Връщаме името с главна буква
	return ucwords($gMonths[$pMonthNum]);

}

/**
 *
 * Ако $pParseType е 0 връща html, в който са поставени линкове за таксоните
 * към ptp сайта. Ако е подаден и параметъра pPutHover се слагат hover-ите за балоните.
 * Ако таксонът се състои от няколко думи и първата започва с главна буква (напр. Genus indet) на всяка от думите се поставя линк,
 * като на думите с главна буква се слага линк към страниците на самите тях, а на останалите - линк към целия таксон
 * (тук на Genus ще се постави линк към Genus, а на indet - към Genus indet)
 * Ако $pParseType е 1 връща масив с различните части на таксона - т.е. думите с големи букви и цялото име на таксона
 * (за горния пример резултата ще е
 * 		array('Genus', 'Genus indet')
 * )
 * @param unknown_type $pTaxonName
 * @param unknown_type $pPut_Hover
 * @param unknown_type $pParseType
 */
function ParseTaxonNameLink($pTaxonName, $pPut_Hover, $pParseType = 0) {

	$lTaxonNamesArr = array();
	$lTaxonNamesArr = split(' ', trim($pTaxonName));

	if(!(int)$pParseType) {
		//връща парснатите имена на таксоните като линкове с hover-а
		$lRes = '<span>';
		$lLink = TAXON_NAME_LINK;
		$lLinkClassName = 'taxonNameLink';
		if((int)count($lTaxonNamesArr)) {
			foreach($lTaxonNamesArr as $k => $v) {
				if(substr($v, 0, 1) == '(' || substr($v, strlen($v) - 1) == ')') {
					$lTxName = substr($v, 1, -1);
				} else {
					$lTxName = $v;
				}
				$lTxName = trim($lTxName);
				if(preg_match('([A-Z])', substr($lTxName, 0, 1))) {
					$lParsedTaxonName = trim($lTxName);
					$lParsedTaxonName = preg_replace('/\.|\s+/i', '_', $lParsedTaxonName);

					$lRes .= '<a target="_blank" class="' . $lLinkClassName . '" href="' . $lLink . $lTxName . '" ' . ((int)$pPut_Hover > 0 ? 'onMouseOver="showBaloon(2, \'' . $lParsedTaxonName . '\', event)" onMouseOut="hideBaloonEvent(\'' . $lParsedTaxonName . '\', event)"' : '') . '>' . $v . '</a> ';
				} else {
					$lParsedTaxonName = trim($pTaxonName);
					$lParsedTaxonName = preg_replace('/\.|\s+/i', '_', $lParsedTaxonName);
					$lRes .= '<a target="_blank" class="' . $lLinkClassName . '" href="' . $lLink . $pTaxonName . '" ' . ((int)$pPut_Hover > 0 ? 'onMouseOver="showBaloon(2, \'' . $lParsedTaxonName . '\', event)" onMouseOut="hideBaloonEvent(\'' . $lParsedTaxonName . '\', event)"' : '') . '>' . $v . '</a> ';
				}
			}
		} else {
			$lRes .= $pTaxonName;
		}
		$lRes = trim($lRes);
		$lRes .= '</span>';

		$lxml = $lRes;
		$lDoc = new DOMDocument;
		$lDoc->loadXml($lxml);
		return $lDoc;
	} else {
		//връща масив от парснатите имена на таксоните (това е за балончетата при mouseover)
		$lParsedTaxonNamesArr = array();
		if((int)count($lTaxonNamesArr)) {
			foreach($lTaxonNamesArr as $k => $v) {
				if(substr($v, 0, 1) == '(' || substr($v, strlen($v) - 1) == ')') {
					$lTxName = substr($v, 1, -1);
				} else {
					$lTxName = $v;
				}

				if(preg_match('([A-Z])', substr($lTxName, 0, 1))) {
					$lParsedTaxonNamesArr[] = $lTxName;
				} else {
					$lParsedTaxonNamesArr[] = $pTaxonName;
				}
			}
		} else {
			$lParsedTaxonNamesArr[] = $pTaxonName;
		}

		return $lParsedTaxonNamesArr;
	}
}

function getSingleTaxonBaloon($pTaxonName){
	$lUrl = TAXON_BALOON_SRV . '?taxon_name=' . rawurlencode($pTaxonName);	
	return executeExternalQuery($lUrl);
}

function parseTaxonNameForBaloon($pTaxonName){
	return preg_replace('/\.|\s+/i', '_', $pTaxonName);
}

function parseContribName($pFullName, $pReturnFirstName = 1){
	$pFullName = trim($pFullName);
	$lLastSpace = mb_strrpos($pFullName, ' ');
	$lFirstName = $pFullName;
	$lLastName = '';
	if( $lLastSpace !== false ){
		$lFirstName = trim(mb_substr($pFullName, 0, $lLastSpace));
		$lLastName = trim(mb_substr($pFullName, $lLastSpace + 1));
	}
	if( $pReturnFirstName )
		return $lFirstName;
	return $lLastName;
}

function getContribSurname($pFullName){
	return parseContribName($pFullName, 0);
}

function getContribGivenNames($pFullName){
	return parseContribName($pFullName);
}

function parseContribUriAffNum($pFakeAddrUriText, $pReturnAffNum = 1){
	$pFakeAddrUriText = trim($pFakeAddrUriText);
	$lLastComa = mb_strrpos($pFakeAddrUriText, ',');
	$lAddrNum = '';
	$lUriSym = $pFakeAddrUriText;
	if( $lLastComa !== false ){
		$lAddrNum = trim(mb_substr($pFakeAddrUriText, 0, $lLastComa));
		$lUriSym = trim(mb_substr($pFakeAddrUriText, $lLastComa + 1));
	}
	if( $pReturnAffNum )
		return $lAddrNum;
	return $lUriSym;

}

function getContribAffNum($pFakeAddrUriText){
	return parseContribUriAffNum($pFakeAddrUriText);
}

function getContribUriSym($pFakeAddrUriText){
	return parseContribUriAffNum($pFakeAddrUriText, 0);
}

function parseDate($pDate){
	if(!preg_match('/(?P<day>\d{1,2})\s*(?P<month>[a-z]+)\s*(?P<year>\d{2,4})/uims', $pDate, $pMatch))
		return false;
	return $pMatch;
}

function getDateDay($pDate){
	$lResult = parseDate($pDate);
	if( $lResult !== false )
		return $lResult['day'];
	return '';
}

function getDateMonth($pDate){
	$lResult = parseDate($pDate);
	if( $lResult !== false )
		return $lResult['month'];
	return '';
}

function getDateYear($pDate){
	$lResult = parseDate($pDate);
	if( $lResult !== false )
		return $lResult['year'];
	return '';
}

/**
 Тук инициализираме съдържанието на балоните в експортната статия в HTML формат.
 Взимаме всички таксони и за всеки таксон взимаме съдържанието от ptp сайта, където би трябвало да се взима статично, за да не бави показването на статията.
 За всеки от таксоните вкарваме запис в таблицата за генериране на кеш за таксони
 и по-късно чрез отложен скрипт генерираме кеша, за да не бавим показването на статията тук.
 */
function GetHtmlBallons($pXSLResult){
	$lTaxonNamePattern = '/\{\*\*_(.*)_\*\*\}/';
	$lTaxonDivPattern = '/\{\$\$__\$\$\}/';
// 	error_reporting(E_ALL);
// 	ini_set('display_errors', 'Off');
	if( preg_match_all( $lTaxonNamePattern, $pXSLResult, $lMatch ) ){
		$lTaxonNames = array();
		$lTaxonNamesParsed = array();
		foreach( $lMatch[1] as $lSingleMatch ){
			$lTaxonNamesParsed = ParseTaxonNameLink($lSingleMatch, 0, 1);
			foreach($lTaxonNamesParsed as $lSingleParsedtaxonName) {
				$lTaxonNames[] = $lSingleParsedtaxonName;
			}
		}
		$lTaxonNames = array_unique( $lTaxonNames );
		$lTaxonDivs = '';
		$lTaxonCacheSql = '';

		//Взимаме съдържанието на балоните
		foreach($lTaxonNames as $lSingleTaxonName){
			$lLinks = GetLinksArray($lSingleTaxonName, false);
			$lCurrentTaxonDiv = new csimple(array(
				'templs' => array(
					G_DEFAULT => 'article_html.taxonBaloonDiv',
				),

				'gbif_href' => $lLinks['gbif']['href'],
				'gbif_title' => $lLinks['gbif']['title'],
				'ncbi_href' => $lLinks['ncbi']['href'],
				'ncbi_title' => $lLinks['ncbi']['title'],
				'eol_href' => $lLinks['eol']['href'],
				'eol_title' => $lLinks['eol']['title'],
				'biodev_href' => $lLinks['biodev']['href'],
				'biodev_title' => $lLinks['biodev']['title'],
				'wikipedia_href' => $lLinks['wikipedia']['href'],
				'wikipedia_title' => $lLinks['wikipedia']['title'],

				'cache' => 'article_html_taxon_baloon',
				'cachetimeout' => CACHE_TIMEOUT_LENGTH,
				'taxon_name' => $lSingleTaxonName,
			));
			//~ trigger_error("Exec: " . $lSingleTaxonName , E_USER_NOTICE);
			$lTaxonDivs .= $lCurrentTaxonDiv->DisplayC();
			//Строим sql-a за скрипта за кеша
			$lTaxonCacheSql .= 'SELECT * FROM spCreateTaxonCacheEntry(\'' . q($lSingleTaxonName) . '\');';
			
		}
		//Изпълняваме sql-a за скрипта за кеша. По-късно отложения скрипт ще мине и ще генерира кеша
		if ($lTaxonCacheSql != '') {
			$lCon = getPmtCon();
			$lCon->CloseRs();
			$lCon->Execute($lTaxonCacheSql);
		}
		
		
		$pXSLResult = preg_replace($lTaxonNamePattern, '', $pXSLResult);
		$pXSLResult = preg_replace($lTaxonDivPattern, $lTaxonDivs, $pXSLResult);
	}
	return $pXSLResult;

}

function ParsePubmedTaxonName($pTaxonName){//Parsva taxona taka 4e v pubmed da go tyrsi s AND, a ne s OR
	return str_replace(' ', ' AND ', $pTaxonName);//Zamenq intervalite s AND
}

function getPmtCon(){
	$lCon = new DBCn(PMT_DEF_DBTYPE);
	$lCon->Open(PMT_PGDB_SRV, PMT_PGDB_DB, PMT_PGDB_USR, PMT_PGDB_PASS, PMT_PGDB_PORT);
	return $lCon;
}
?>