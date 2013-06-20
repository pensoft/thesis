<?php
global $gSortedMaterialFieldIdsOrder;
$gSortedMaterialFieldIdsOrder = array(200,109, 107);
/**
 * Тази функция ще ни връща id-то на адреса на контрибуторите.
 * Ще ги пазим в статичен масив понеже адресите се показват на 2 различни pass-а -
 * единия път във възела на контрибутора, а втория път - в секцията с адреси.
 * Затова ще трябва id-тата на 2те места да съвпадат
 * @param unknown_type $pInstanceId
 */
function getContributorAffId($pInstanceId){
	static $lAffUris = array();

	if( !array_key_exists($pInstanceId, $lAffUris) ){
		$lAffUris[$pInstanceId] = count($lAffUris) + 1;
	}

	return $lAffUris[$pInstanceId];
}

function checkIfObjectFieldIsEditable($pObjectId, $pFieldId){
	$lAllowed = array(
		15 => array(18, 19),
		//~ 16 => array(20),
		//~ 18 => array(22),
		//~ 166 => array(20),
		//~ 168 => array(22),
	);
	if(!array_key_exists($pObjectId, $lAllowed) || !in_array($pFieldId, $lAllowed[$pObjectId])){
		return 0;
	}
	return 1;
}

function getReferenceYearLetter($pReferenceId, $pDocumentId = 0, $pInitReferences = 0, $pReferenceObject = null){
	static $lReferenceData = array();
	static $lReferencesAreInited = 0;
	if($pInitReferences && !$lReferencesAreInited){
		$lReferencesAreInited = 1;
		if(!$pReferenceObject){
			$lUnparsedData = new cdocument_references(array(
				'ctype' => 'cdocument_references',
				'document_id' => $pDocumentId,
				'templs' => array(
					G_HEADER => 'global.empty',
					G_ROWTEMPL => 'references.single_reference_preview',
					G_FOOTER => 'global.empty',
					G_NODATA => 'global.empty'
				),
				'sqlstr' => '
						SELECT *, reference_instance_id as id
						FROM spGetDocumentReferences(' . (int) $pDocumentId . ')
						ORDER BY is_website_citation ASC, first_author_combined_name ASC, authors_count ASC, authors_combined_names ASC, pubyear ASC
					'
			));
			$lUnparsedData->GetData();
		}else{
			$lUnparsedData = $pReferenceObject;
		}
		$lUnparsedDataArr = $lUnparsedData->m_resultArr;
		foreach($lUnparsedDataArr as $lCurrentRow) {
			$lReferenceData[$lCurrentRow['id']] = array(
				'element_in_group_idx' => $lCurrentRow['element_in_group_idx'],
				'group_has_more_elements' => $lCurrentRow['group_has_more_elements'],
			);
		}
		return;
	}
// 	var_dump($lReferenceData, $pReferenceId);
	if(!array_key_exists((int)$pReferenceId, $lReferenceData)){
		return;
	}
	$lCurrentReferenceData = $lReferenceData[$pReferenceId];
	if(!(int)$lCurrentReferenceData['group_has_more_elements']){
		return;
	}
	return chr(ord('a') - 1 + $lCurrentReferenceData['element_in_group_idx']);
}


/**
 * Тази функция ще ни връща id-то на референцията.
 * Ще ги пазим в статичен масив понеже референциите се реферират на много места.
 * Затова ще трябва id-тата на 2те места да съвпадат
 * @param unknown_type $pInstanceId
 */
function getReferenceId($pInstanceId){
	static $lReferenceUris = array();

	if( !array_key_exists($pInstanceId, $lReferenceUris) ){
		$lReferenceUris[$pInstanceId] = count($lReferenceUris) + 1;
	}

	return $lReferenceUris[$pInstanceId];
}

/**
 * Тази функция ще ни връща id-то на фигурата.
 * Ще ги пазим в статичен масив понеже фигурите се реферират на много места.
 * Затова ще трябва id-тата на 2те места да съвпадат
 * @param unknown_type $pInstanceId
 */
function getFigureId($pInstanceId){
	static $lFigures = array();

	if($pInstanceId) {
		if( !array_key_exists($pInstanceId, $lFigures) ){
			$lFigures[$pInstanceId] = count($lFigures) + 1;
		}
	}

	return $lFigures[$pInstanceId];
}

function getAllUrlsFromText($pText){
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lRoot = $lDom->appendChild($lDom->createElement('root'));
	$lPattern = "/(?xi)
\b
(                       # Capture 1: entire matched URL
  (?:
    https?:\/\/               # http or https protocol
    |                       #   or
    www\d{0,3}[.]           # \"www.\", \"www1.\", \"www2.\" … \"www999.\"
    |                           #   or
    [a-z0-9.\-]+[.][a-z]{2,4}\/  # looks like domain name followed by a slash
  )
  (?:                       # One or more:
    [^\s()<>]+                  # Run of non-space, non-()<>
    |                           #   or
    \(([^\s()<>]+|(\([^\s()<>]+\)))*\)  # balanced parens, up to 2 levels
  )+
  (?:                       # End with:
    \(([^\s()<>]+|(\([^\s()<>]+\)))*\)  # balanced parens, up to 2 levels
    |                               #   or
    [^\s`!()\[\]{};:'\".,<>?«»“”‘’]        # not a space or one of these punct chars
  )
)/";
	preg_match_all($lPattern, $pText, $lUrlMatches);
	foreach($lUrlMatches[0] as $lCurrentMatch){
		$lChild= $lRoot->appendChild($lDom->createElement('url'));
		$lChild->appendChild($lDom->createTextNode($lCurrentMatch));
	}

	return $lRoot;
}

if(!function_exists('getUriSymbol')){
	/**
	 * Връща специалния символ, който ще е label на това uri.
	 * За целта пази уритата в статичен масив
	 * @param unknown_type $pUri
	 */
	function getUriSymbol($pUri){
		static $lUris = array();
		static $lUriSymbols = array("†", "†","‡", "§", "|", "¶", "#");
		$pUri = trim($pUri);
		if(array_key_exists($pUri, $lUris)){
			return $lUris[$pUri];
		}
		$lUrisLength = count($lUris);
		$lSymbolsLength = count($lUriSymbols);
		$lUris[$pUri] = str_repeat($lUriSymbols[$lUrisLength % $lSymbolsLength + 1], floor($lUrisLength / $lSymbolsLength) + 1);
		return $lUris[$pUri];
	}
}

if(!function_exists('getAffiliation')){
	/**
	 * Връща специалния символ, който ще е label на това uri.
	 * За целта пази уритата в статичен масив
	 * @param unknown_type $pUri
	 */
	function getAffiliation($fullAffiliation){
		//echo "|" . $pUri . "|";
		static $affiliations = array();
		static $Symbols = array("","†","‡", "§", "|", "¶", "#");
		$fullAffiliation = trim($fullAffiliation);
		if(!array_key_exists($fullAffiliation, $affiliations)){
			$affiliations[$fullAffiliation] = 0;
			$m = count($affiliations);
			$n = count($Symbols);
			$s = str_repeat($Symbols[$m % $n], floor($m / $n) + 1);
			return $s . ' ' . $fullAffiliation;
		}
	}
}

function h_strip_tags($html)
{
	return trim(strip_tags($html, '<i><em><sup><sub>'));
}

function getAllKeywordsFromText($pText){
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lRoot = $lDom->appendChild($lDom->createElement('root'));
	$lKeywords = explode(',', $pText);
	foreach ($lKeywords as $lKwd){
		$lChild= $lRoot->appendChild($lDom->createElement('kwd'));
		$lChild->appendChild($lDom->createTextNode($lKwd));
	}
	return $lRoot;
}

function getFormattingNodeRealNameForPmt($pNodeName){
	switch($pNodeName){
		default:
			return $pNodeName;
		case 'b':
		case 'strong':
			return 'b';
		case 'em':
		case 'i':
			return 'i';
	}
}

/**
 * Za vsichki poleta TITLE, tryabva da se pravi proverka na stringa koito e vkaran:
 - ako zavurshva na tochka[.] ili prazen interval[ ], to toi tryabva da se mahne
 - ako zavurshva na [?] ili [!], togava pri vizualizaciyata da se pokazva saotvetniya znak vmesto tochka[.]
 * @param unknown_type $pTitle
 */
function parseReferenceItemTitle($pTitle){
	$pTitle = trim($pTitle);
	$lLastChar = mb_substr($pTitle, mb_strlen($pTitle) - 1);
	if(!($lLastChar == '.' || $lLastChar == '!' || $lLastChar == '?')){
		$pTitle .= '.';
	}
	return $pTitle;
}

function getFileNameById($pFileId) {
	$lQuery = 'SELECT original_name
				FROM pwt.media
				WHERE id = ' . (int)$pFileId;
	$gCn = new DBCn();
	$gCn->Open();
	$gCn->Execute($lQuery);
	$gCn->MoveFirst();
	$lSrcValue = '';
	while(!$gCn->Eof()) {
		$lSrcValue = $gCn->mRs['original_name'];
		$gCn->MoveNext();
	}
	return $lSrcValue;
}

function getUploadedFileNameById($pFileId) {
	$lQuery = 'SELECT title
				FROM pwt.media
				WHERE id = ' . (int)$pFileId;
	$gCn = new DBCn();
	$gCn->Open();
	$gCn->Execute($lQuery);
	$gCn->MoveFirst();
	$lSrcValue = '';
	while(!$gCn->Eof()) {
		$lSrcValue = $gCn->mRs['title'];
		$gCn->MoveNext();
	}
	return $lSrcValue;
}

function getUploadedFileSize($pFileName) {
	if(file_exists(PATH_PWT_DL . $pFileName)) {
		$lSize = filesize(PATH_PWT_DL . $pFileName);
	}

	//CHECK TO MAKE SURE A NUMBER WAS SENT
	if(!empty($lSize)) {

		//SET TEXT TITLES TO SHOW AT EACH LEVEL
		$s = array('bytes', 'kb', 'MB', 'GB', 'TB', 'PB');
		$e = floor(log($lSize)/log(1024));

		//CREATE COMPLETED OUTPUT
		$output = sprintf('%.2f '.$s[$e], ($lSize/pow(1024, floor($e))));

		//SEND OUTPUT TO BROWSER
		return $output;
	}
}


function formatDate($pDate) {
	global $gMonths;

	if (!preg_match('/(\d+)[-–\/](\d+)[-–\/](\d+)/', $pDate, $lMatch)) {
		return $pDate;
	}
	$lMonth = ltrim($lMatch[2], '0');

	return (int)$lMatch[1] . ' ' . substr(ucfirst($gMonths[$lMonth]), 0, 3) . '. ' . $lMatch[3];
}


function getEditPreviewHead($pDocumentId){
	if(!(int)$pDocumentId)
		return '';
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lRoot = $lDom->appendChild($lDom->createElement('root'));
	$lScripts = array(
		'/lib/js/jquery.js',
		PJS_SITE_URL . '/lib/js/ice/lib/rangy-1.2/rangy-core.js',
		PJS_SITE_URL . '/lib/js/ice/lib/rangy-1.2/rangy-cssclassapplier.js',
		PJS_SITE_URL . '/lib/js/ice/lib/rangy-1.2/rangy-selectionsaverestore.js',
		PJS_SITE_URL . '/lib/js/ice/lib/rangy-1.2/rangy-serializer.js',
		PJS_SITE_URL . '/lib/js/ice/ice.js',
		PJS_SITE_URL . '/lib/js/ice/dom.js',
		PJS_SITE_URL . '/lib/js/ice/icePlugin.js',
		PJS_SITE_URL . '/lib/js/ice/icePluginManager.js',
		PJS_SITE_URL . '/lib/js/ice/bookmark.js',
		PJS_SITE_URL . '/lib/js/ice/selection.js',
		PJS_SITE_URL . '/lib/js/ice/plugins/IceAddTitlePlugin/IceAddTitlePlugin.js',
		PJS_SITE_URL . '/lib/js/ice/plugins/IceCopyPastePlugin/IceCopyPastePlugin.js',
		PJS_SITE_URL . '/lib/js/ice/plugins/IceEmdashPlugin/IceEmdashPlugin.js',
		PJS_SITE_URL . '/lib/js/ice/plugins/IceSmartQuotesPlugin/IceSmartQuotesPlugin.js',
		PJS_SITE_URL . '/lib/js/ice/lib/tinymce/jscripts/tiny_mce/tiny_mce.js',
		PJS_SITE_URL . '/lib/js/version_preview.js',
		'/lib/js/editable_preview.js',
	);
	/*$lCss = array(
		PJS_SITE_URL . '/lib/version_preview.css',
		'/lib/css/editable_preview.css',
	); */

	$lLatestPjsRevisionChangeUids = getLatestPjsRevisionChangeUids($pDocumentId);
// 	$lLatestPjsRevisionChangeUids = array(695);
	$lVersionCss = IncludeVersionCss($lLatestPjsRevisionChangeUids);
// 	var_dump($lVersionCss);

	$lStyle = $lRoot->appendChild($lDom->createElement('style', $lVersionCss));

	foreach($lScripts as $lCurrentScript){
		$lChild= $lRoot->appendChild($lDom->createElement('script'));
		$lChild->SetAttribute('type', 'text/javascript');
		$lChild->SetAttribute('src', $lCurrentScript);
	}

	foreach($lCss as $lCurrentCss){
		$lChild= $lRoot->appendChild($lDom->createElement('link'));
		$lChild->SetAttribute('type', 'text/css');
		$lChild->SetAttribute('title', 'default');
		$lChild->SetAttribute('rel', 'stylesheet');
		$lChild->SetAttribute('href', $lCurrentCss);
	}

	return $lRoot;
}

function getLatestPjsRevisionChangeUids($pDocumentId){
	$lCon = new DBCn();
	$lCon->Open();
	$lSql = 'SELECT u.id, coalesce(u.first_name, \'\') || \' \' || coalesce(u.last_name, \'\') as name
		FROM public.usr u
		JOIN (SELECT rd.*
			FROM pwt.pjs_revision_details rd
			JOIN pwt.document_revisions r ON r.id = rd.revision_id
			WHERE rd.document_id = ' . (int)$pDocumentId . '
			ORDER BY r.id DESC LIMIT 1
		) rde ON u.id = ANY (rde.change_user_ids)
	';
	$lCon->Execute($lSql);
	$lResult = array();
	while(!$lCon->Eof()){
		$lResult[] = $lCon->mRs;
		$lCon->MoveNext();
	}
// 	var_dump($lResult);
	return $lResult;
}

/**
 * Here we will import the css for the versions page
 * in which we will replace the user ids-
 * so that the user changes to have style
 * @param unknown_type $pUserIds an array containing info about all the users that have changes in the currently viewwed version
 * 		The format of the array is
 * 			usr_id => usr_name
 */
function IncludeVersionCss($pUserIds){
	if ($pUserIds){
		$lCss = file_get_contents(VERSION_USR_CSS_PATH);
		if(!$lCss){
			return '';
		}
		$lUserIdx = 1;
// 		var_dump($pUserIds);
		foreach ($pUserIds as $lUsrData){
			$lUsrId = $lUsrData['id'];
			$lCss = str_replace('$user_' . $lUserIdx . '$', $lUsrId, $lCss);
			$lUserIdx++;
		}
		return $lCss;
	}
}

function getYear() {
	return date('Y');
}

function checkIfLinkContainsHttp($pLink) {

	if (preg_match('%^https?://[^\s]+$%', $pLink)) {
		return $pLink;
	} else {
		return 'http://' . $pLink;
	}
}

function getYouTubeId($pUrl) {
  $pUrlString = parse_url($pUrl, PHP_URL_QUERY);
  parse_str($pUrlString, $args);
  return isset($args['v']) ? $args['v'] : false;
}

function GetSortedMaterialFields($pFields){
	$lFirstField = $pFields[0];
	$lXPath = new DOMXPath($lFirstField->ownerDocument);

	$lFieldsArr = array();
	foreach ($pFields as $lCurrentField){
		$lFieldId = $lCurrentField->getAttribute('id');
		$lFieldName = $lCurrentField->getAttribute('field_name');
		$lFieldValue = '';
		$lFieldValueNode = $lXPath->query('./value', $lCurrentField);
		if($lFieldValueNode->length){
			$lFieldValue = $lFieldValueNode->item(0)->nodeValue;
		}
		$lFieldsArr[$lFieldId] = array(
			'id' => $lFieldId,
			'field_name' => $lFieldName,
			'value' => $lFieldValue,
		);
// 		var_dump($lCurrentField->ownerDocument->saveXML($lCurrentField));
// 		var_dump($lFieldId, $lFieldValue);
	}
// 	var_dump($lFieldsArr);
	$lFieldsArr = SortMaterialFields($lFieldsArr);

	$lDom = new DOMDocument('1.0', 'utf-8');
	$lRoot = $lDom->appendChild($lDom->createElement('root'));
// 	var_dump($lFieldsArr);
	foreach($lFieldsArr as $lFieldId => $lFieldData){
		$lChild= $lRoot->appendChild($lDom->createElement('field'));

		$lFieldName = $lFieldData['field_name'];
		$lFieldValue = $lFieldData['value'];

		$lChild->SetAttribute('id', $lFieldId);
		$lChild->SetAttribute('field_name', $lFieldName);
		$lValueNode = $lChild->appendChild($lDom->createElement('value', $lFieldValue));
	}
// 	var_dump($lDom->saveXML());
	return $lDom;
}


function SortMaterialFields($pMaterialFields){
// 	uksort($pMaterialFields, 'CompareMaterialFields');
	global $gSortedMaterialFieldIdsOrder;
	$lResult = array();
	//First get the fields which are ordered
	foreach ($gSortedMaterialFieldIdsOrder as $lFieldId){
		if(array_key_exists($lFieldId, $pMaterialFields)){
			$lResult[$lFieldId] = $pMaterialFields[$lFieldId];
		}
	}
	//Get the remaining fields in the order they were originally found
	foreach ($pMaterialFields as $lFieldId => $lFieldData){
		if(!array_key_exists($lFieldId, $gSortedMaterialFieldIdsOrder)){
			$lResult[$lFieldId] = $lFieldData;
		}
	}
	return $lResult;
}

/**
 * Group the treatment materials in groups
 * according to their material type
 * @param array(DomNode) $pTreatmentMaterials
 */
function GroupTreatmentMaterials($pTreatmentMaterials){
// 	var_dump($pTreatmentMaterials);
	$lDom = new DOMDocument('1.0', 'utf-8');
	if(!is_array($pTreatmentMaterials) || !count($pTreatmentMaterials)){
		return $lDom;
	}

	$lFirstMaterial = $pTreatmentMaterials[0];
	$lXPath = new DOMXPath($lFirstMaterial->ownerDocument);

	$lFoundMaterialTypes = array();
	$lRoot = $lDom->appendChild($lDom->createElement('materials'));
	foreach ($pTreatmentMaterials as $lCurrentMaterial){
		$lTypeNodeList = $lXPath->query('./*/fields/*[@id=\'209\']/value', $lCurrentMaterial);
		if(!$lTypeNodeList->length){
			continue;
		}
		$lTypeNode = $lTypeNodeList->item(0);
		$lTypeId = $lTypeNode->getAttribute('value_id');
		$lTypeName = $lTypeNode->nodeValue;
		$lGroupNode = null;
		if(!array_key_exists($lTypeId, $lFoundMaterialTypes)){
			$lGroupNode = $lRoot->appendChild($lDom->createElement('material_group'));
			$lFoundMaterialTypes[$lTypeId] = array(
				'name' => $lTypeName,
				'group_node' => $lGroupNode
			);
			$lGroupNode->setAttribute('value_id', $lTypeId);
			$lTypeNameNode = $lGroupNode->appendChild($lDom->createElement('value', $lTypeName));
		}else{
			$lGroupNode = $lFoundMaterialTypes[$lTypeId]['group_node'];
		}
		$lGroupNode->appendChild($lDom->importNode($lCurrentMaterial->cloneNode(true), true));
	}
	return $lDom;
}

// function CompareMaterialFields($pFieldIdA, $pFieldIdB){
// // 	$lFieldIdA = $pFieldA['id'];
// // 	$lFieldIdB = $pFieldB['id'];
// 	global $gSortedMaterialFieldIdsOrder;
// 	$lFieldIdAIdx = array_search($pFieldIdA, $gSortedMaterialFieldIdsOrder);
// 	$lFieldIdBIdx = array_search($pFieldIdB, $gSortedMaterialFieldIdsOrder);
// 	if($lFieldIdAIdx === false){
// 		return 1;
// 	}
// 	if($lFieldIdBIdx === false){
// 		return -1;
// 	}
// 	if ($lFieldIdAIdx == $lFieldIdBIdx) {
// 		return 0;
// 	}
// 	return ($lFieldIdAIdx < $lFieldIdBIdx) ? -1 : 1;

// }
/**
 *
 * @param unknown_type $pInstanceId
 * @param array $pPreview
 */
function SaveInstancePreview($pInstanceId, $pPreview){
// 	var_dump($pInstanceId, $pPreview, $pPreview[0] instanceof DOMDocument);
	if(count($pPreview) && $pPreview[0] instanceof DOMDocument){
		global $gInstancePreviews;
		$gInstancePreviews[$pInstanceId] = trim($pPreview[0]->saveHtml());
	}
	return '';
}

function GetInstancePreview($pInstanceId){
	global $gInstancePreviews;
// 	var_dump($gInstancePreviews, $pInstanceId);
	return $gInstancePreviews[$pInstanceId];
}
?>