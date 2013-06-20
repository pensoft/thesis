<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');


function preindexXML(&$pNode, &$pCurIndex = 1){//преиндексира xml-a, като на всеки element node слага атрибут ETA_ATTRIBUTE_NAME със стойност - индекса	
	if( !$pNode )
		return;
	$pNode->setAttribute(ETA_ATTRIBUTE_NAME, $pCurIndex++);
	
	$lChildren = $pNode->childNodes;
	for( $i = 0; $i < $lChildren->length; ++$i){
		$lChild = $lChildren->item($i);
		if( $lChild->nodeType == 1 ){//preindex element child
			preindexXML($lChild, $pCurIndex);
		}
	}
	
}

/**
	Връща node-овете от които се взимат източниците
	
	pSources - масив със източниците за текущото правило
	pXMLDom - Dom на текущия xml
*/
function getSourceNodes($pSources, $pXMLDom){
	$lResult = array();
	$lXpath  = new  DOMXPath($pXMLDom);
	foreach($pSources as $lSourceID => $lSourceData){
		$lSourceXpath = $lSourceData['xpath'];		
		$lXpathResult = $lXpath->query($lSourceXpath);
		$lResultArr = nodeListToArray($lXpathResult);
		if( count( $lResultArr )){
			$lResult[$lSourceID] = $lResultArr;
		}
	}
	return $lResult;	
}

/**
	Връща масив със променливите за всеки ред от източниците
	
	pSourceNodes - масив във формат (sourceid => масив от възли), който са всъщност източниците
	pXMLDom - Dom на текущия xml
*/
function getSourceVariables($pSourceNodes, $pXMLDom){
	$lResult = array();
	$lXpath  = new  DOMXPath($pXMLDom);
	foreach( $pSourceNodes as $lSourceId => $lSourceNodes){
		$lResult[$lSourceId] = array();
		$lVariables = getSourceVariableExpressions($lSourceId);//Vzimame expressionite za da vzemem stoinostite
		//~ echo "<br/>";
		//~ var_dump($lVariables);
		//~ echo "<br/>";
		//~ echo "<br/>";
		//~ echo "<br/>";
		foreach( $lSourceNodes as $lCurrentSourceNode ){//Vzimame stoinostite za vseki node ot tekushtiq iztochnik
			$lCurrentVariableValues = array();
			foreach( $lVariables as $lVariableSymbol => $lVariableDetails ){//Vzimame stoinostite na promenlivite za tekushtiq node
				$lValue = '';
				$lConcatSeparator = $lVariableDetails['concat_separator'];
				switch( (int) $lVariableDetails['variable_type'] ){//Promenlivata e xpath					
					case (int) SOURCE_XPATH_VARIABLE_TYPE:{
						$lXpathResult = $lXpath->query($lVariableDetails['expression'], $lCurrentSourceNode);
						if( $lXpathResult->length ){
							if( $lVariableDetails['concat_multiple']){
								for($i = 0; $i < $lXpathResult->length; ++$i ){
									if($i > 0){
										$lValue .= $lConcatSeparator . $lXpathResult->item($i)->textContent;
									}else{
										$lValue .= $lXpathResult->item($i)->textContent;
									}
								}
							}else{
								$lValue = $lXpathResult->item(0)->textContent;
							}
						}
						break;
						
					}
					case (int) SOURCE_REGEXP_VARIABLE_TYPE:{//Promenlivata e regexp						
						$lMatches = array();						
						preg_match_all($lVariableDetails['expression'], $lCurrentSourceNode->textContent, $lMatches);						
						if(is_array( $lMatches[0]) && count($lMatches[0])){
							if( $lVariableDetails['concat_multiple']){
								for($i = 0; $i < count($lMatches[0]); ++$i ){
									if($i > 0){
										$lValue .= $lConcatSeparator . $lMatches[0][$i];
									}else{
										$lValue .= $lMatches[0][$i];
									}
								}
							}else{
								$lValue = $lMatches[0][0];
							}
						}
						break;
					}
				}
				$lValue = escapeSourceVariableValue($lValue);
				$lCurrentVariableValues[$lVariableSymbol] = $lValue;
			}
			$lResult[$lSourceId][] = $lCurrentVariableValues;
		}
	}
	//~ var_dump($lResult);
	return $lResult;
}

/**
	Връща имената на променливите за източника заедно с изразите за взимане на стойността им
*/
function getSourceVariableExpressions($pSourceId){
	$lResult = array();
	$lCon = Con();
	$lSql = '
		SELECT v.expression, v.variable_type, v.variable_symbol, v.concat_multiple, v.concat_separator
		FROM autotag_re_variables v
		WHERE v.source_id = ' . (int) $pSourceId . '
	';
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	while(!$lCon->Eof()){
		$lResult[$lCon->mRs['variable_symbol']] = array(
			'expression' => $lCon->mRs['expression'],
			'variable_type' => $lCon->mRs['variable_type'],
			'concat_multiple' => $lCon->mRs['concat_multiple'],
			'concat_separator' => $lCon->mRs['concat_separator'],
		);
		$lCon->MoveNext();
	}
	$lCon->CloseRS();
	return $lResult;
	
}

/**
	В зависимост от параметъра pGetXPath връща масив със всички XPath expression-и за даден autotag rule
	или връща масив със всички regular expression-и за дадения autotag rule
*/
function getRuleProperties($pRuleID, $pPropertyType){
	$lCon = Con();
	$lResult = array();
	switch((int) $pPropertyType ){
		default:
		case (int) PLACE_RULE_PROPERTY_TYPE:{//Xpath-ове
			$lSql = '
				SELECT p.xpath as xpath, ar.property_modifier_id as type, p.id, p.name
				FROM place_rules p
				JOIN autotag_rules_properties ar ON ar.property_id = p.id 
				WHERE ar.rule_id = ' . (int) $pRuleID . '
			';
			$lPropertyName = 'xpath';
			break;
		}
		case (int) REGEXP_RULE_PROPERTY_TYPE:{//re-ta
			$lSql = '
				SELECT p.expression as expression, ar.property_modifier_id as type, p.replacement, p.groupsupdepth, p.id, p.name, ar.priority
				FROM regular_expressions p
				JOIN autotag_rules_properties ar ON ar.property_id = p.id 
				WHERE ar.rule_id = ' . (int) $pRuleID . '
				ORDER BY ar.priority ASC
			';
			//Приоритета е важен, т.к. по него се филтрират match-овете
			$lPropertyName = 'expression';
			break;
		}
		case (int) SOURCE_RULE_PROPERTY_TYPE:{
			$lSql = '
				SELECT p.source_xpath as xpath, p.id, p.name, ar.property_modifier_id as type
				FROM autotag_re_sources p
				JOIN autotag_rules_properties ar ON ar.property_id = p.id 
				WHERE ar.rule_id = ' . (int) $pRuleID . '
				ORDER BY ar.priority ASC
			';			
			$lPropertyName = 'xpath';
			break;
		}
		
	}	
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	while(!$lCon->Eof()){
		$lTemp = array();
		//~ $lTemp['id'] = (int) $lCon->mRs['id'];
		$lTemp['type'] = (int) $lCon->mRs['type'];
		$lTemp['name'] = $lCon->mRs['name'];
		$lTemp[$lPropertyName] = $lCon->mRs[$lPropertyName];
		switch((int) $pPropertyType ){			
			case (int) REGEXP_RULE_PROPERTY_TYPE:{//re-ta		
				$lTemp['replacement'] = $lCon->mRs['replacement'];
				$lTemp['priority'] = (int) $lCon->mRs['priority'];
				$lTemp['groupsupdepth'] = explode(',', $lCon->mRs['groupsupdepth']);
				break;
			}			
		}
		$lResult[(int) $lCon->mRs['id']] = $lTemp;		
		$lCon->MoveNext();
	}	
	return $lResult;
	
}

function dumpDomElementArray($pArray){
	foreach($pArray as $lElement){
		var_dump($lElement->nodeName);
	}
}

function nodeListToArray($pNodeList){
	$lResult = array();
	for( $i = 0; $i < $pNodeList->length; ++$i ){
		array_push($lResult, ($pNodeList->item($i)));
	}
	return $lResult;
}

function arrayOfDOMElementsDiff($pArray1, $pArray2){
	$lResultArr = array();
	foreach($pArray1 as $lElement){
		$lFound = false;
		foreach($pArray2 as $lElement2){
			if( $lElement === $lElement2 ){
				$lFound = true;
				break;
			}
			
		}
		if( !$lFound ){
			array_push($lResultArr, $lElement);
		}
	}
	return $lResultArr;
}

function arrayOfDOMElementsIntersect($pArray1, $pArray2){
	$lResultArr = array();
	foreach($pArray1 as $lElement){
		$lFound = false;
		foreach($pArray2 as $lElement2){
			if( $lElement === $lElement2 ){
				$lFound = true;
				break;
			}
			
		}
		if( $lFound ){
			array_push($lResultArr, $lElement);
		}
	}
	return $lResultArr;
}

function getPlaceResult($pXMLDom, $pPlacesXpathArr){	
	$lPlacesAnd = array();
	$lPlacesOr = array();
	$lPlacesNot = array();
	
	if(!is_array( $pPlacesXpathArr ))
		return array();
		
	foreach( $pPlacesXpathArr as $lId => $lXpathExp ){
		switch( $lXpathExp['type'] ){
			default:
			case (int) XPATH_AND_TYPE:{
				array_push( $lPlacesAnd, $lXpathExp['xpath']);
				break;
			}
			case (int) XPATH_OR_TYPE:{
				array_push( $lPlacesOr, $lXpathExp['xpath']);
				break;
			}
			case (int) XPATH_NOT_TYPE:{
				array_push( $lPlacesNot, $lXpathExp['xpath']);
				break;
			}
		
		}
	}	

	$lXpath  = new  DOMXPath($pXMLDom);
	$lNodes = array();
	foreach( $lPlacesOr  as $lXpathQuery ){
		$lResult = $lXpath->query($lXpathQuery);
		$lResultArr = nodeListToArray($lResult);	
		$lNodes = array_merge($lNodes, $lResultArr);
	}

	foreach( $lPlacesAnd  as $lXpathQuery ){
		$lResult = $lXpath->query($lXpathQuery);
		$lResultArr = nodeListToArray($lResult);
		$lNodes = arrayOfDOMElementsIntersect($lNodes, $lResultArr);
	}

	foreach( $lPlacesNot  as $lXpathQuery ){
		$lResult = $lXpath->query($lXpathQuery);
		$lResultArr = nodeListToArray($lResult);	
		$lNodes = arrayOfDOMElementsDiff($lNodes, $lResultArr);
	}
	return getDistinctParentDOMElements($lNodes);

}

function getDistinctParentDOMElements($pArray){//Връща масив от уникални ДОМ елементи, като никой от резултатните ДОМ елементи не е наследник на някой от останалите резултати
	//Задължително е node-овете да имат атрибут ETA_ATTRIBUTE_NAME
	//~ return $pArray;
	$lRes = array();
	$lResult = array();
	$lParentCountArray = array();	
	foreach( $pArray as $lNode ){
		$lParents = array();
		$lTempNode = $lNode->parentNode;
		while( $lTempNode ){
			array_push($lParents, $lTempNode);
			$lTempNode = $lTempNode->parentNode;			
		}
		$lNodeIdx = (int)$lNode->getAttribute(ETA_ATTRIBUTE_NAME);				
		if( !$lNodeIdx ){
			continue;		
		}
		$lParentCount = count( $lParents );
		if( !is_array( $lParentCountArray[$lParentCount] ) )
			$lParentCountArray[$lParentCount] = array();		
		$lParentCountArray[$lParentCount][] = $lNode;		
		$lRes[$lNodeIdx] = $lParents;
		
	}
	
	foreach($lParentCountArray as $lCount => $lNodeArr ){
		foreach( $lNodeArr as $lSingleNode ){
			$lNodeIdx = (int)$lSingleNode->getAttribute(ETA_ATTRIBUTE_NAME);
			if( !$lNodeIdx ){
				continue;		
			}
			$lParents = array_reverse($lRes[$lNodeIdx]);
			$lParentFound = false;
			$lCurParentParentCount = $lCount;
			foreach( $lParents as $lParentNode ){
				--$lCurParentParentCount;
				if( is_array($lParentCountArray[$lCurParentParentCount]) && in_array($lParentNode, $lParentCountArray[$lCurParentParentCount] )){
					$lParentFound = true;
					break;
				}
			}
			if( !$lParentFound ){
				array_push($lResult, $lSingleNode);
			}
		}
	}	
	return $lResult;
	
}

function getFormatTagsInRe($pRE){//Връща масив с форматиращите тагове, които се срещат във pRe
	//TO DO
	$lResult = array();
	$lPattern = '<([A-Z:_a-z][A-Z:_a-z0-9\-\.]*)>';
	if(preg_match_all('/' . $lPattern . '/i', $pRE, $lMatch )){
		if( is_array($lMatch[1]) ){
			foreach( $lMatch[1] as $lTag ){
				array_push($lResult, strtolower($lTag));
			}
		}
	}
	return array_unique($lResult);
}

function getTextFromNode($pTagsArray, $pXMLNode, &$pCurrentPos = 0) {
	/**Връща масив със следните елементи:
		text 		- текста на pXMLNode обработен съобразно форматиращите тагове pTagsArray
		openTagPos 	- масив с позициите в стринга на всички отварящи тагове
		closeTagPos 	- масив с позициите в стринга на всички затварящи тагове
	*/
	$lResultString = '';
	$lResultOpenTagPos = array();
	$lResultCloseTagPos = array();	
	if ($pXMLNode->hasChildNodes()) {		
		foreach($pXMLNode->childNodes as $lItem) {			
			if ($lItem->nodeType == 3){ 
				$lResultString .= $lItem->nodeValue;
				$pCurrentPos += mb_strlen($lItem->nodeValue);	
			}else if ($lItem->nodeType == 1)  {
				if ( in_array($lItem->nodeName, $pTagsArray) ) {
					$lStartTag = "<" . $lItem->nodeName . ">";
					$lEndTag = "</" . $lItem->nodeName . ">";
				}else{
					$lStartTag = '';
					$lEndTag = '';
				}
				
				
				$pCurrentPos += mb_strlen($lStartTag);
				array_push($lResultOpenTagPos, $pCurrentPos);
				
				$lChildResult = getTextFromNode($pTagsArray, $lItem, $pCurrentPos);
				
				$lResultOpenTagPos = array_merge($lResultOpenTagPos, $lChildResult['openTagPos']);
				$lResultCloseTagPos = array_merge($lResultCloseTagPos, $lChildResult['closeTagPos']);				
				$lResultString .= $lStartTag . $lChildResult['text'] . $lEndTag; 					
				
				array_push($lResultCloseTagPos, $pCurrentPos);
				
				$pCurrentPos += mb_strlen($lEndTag);
			}
		}
	} 
	$lResult = array();
	$lResult['text'] = $lResultString;
	$lResult['openTagPos'] = $lResultOpenTagPos;
	$lResult['closeTagPos'] = $lResultCloseTagPos;
	//~ var_dump($lResult);
	return $lResult;
}

function compareReElements( $pReArr1, $pReArr2 ){	
	if( (int) $pReArr1['priority'] < (int) $pReArr2['priority'] )
		return -1;
	if( (int) $pReArr1['priority'] > (int) $pReArr2['priority'] )
		return 1;
	return 0;
}

function autoMatch( $pSourceVariables, $pResultNodes, $pREArray ){	
	$lPositiveMatches = array();
	$lNegativeMatches = array();
	
	foreach( $pREArray as $lId => $lSingleRe){
		if( $lSingleRe['type'] == (int) RE_POSITIVE_TYPE ){
			array_push($lPositiveMatches, array('id' => $lId, 'expr' => $lSingleRe['expression'], 'priority' => $lSingleRe['priority'], 'name' => $lSingleRe['name'], 'replacement' => $lSingleRe['replacement']));
		}else{
			array_push($lNegativeMatches, array('id' => $lId, 'expr' => $lSingleRe['expression'], 'priority' => $lSingleRe['priority'], 'name' => $lSingleRe['name'], 'replacement' => $lSingleRe['replacement']));
		}
	}	
	usort($lPositiveMatches, "compareReElements");
	
	$lResult = array();
	foreach( $pResultNodes as $lSingleNode ){
		$lNodeIdx = (int) $lSingleNode->getAttribute(ETA_ATTRIBUTE_NAME);		
		//~ $lNodeIdx = (int) 1;		
		if( !$lNodeIdx )
			continue;
		$lResult[$lNodeIdx] = array();		
		if(is_array($pSourceVariables) && count($pSourceVariables)){//Ima source
			foreach($pSourceVariables as $pSourceId => $pSourceVariables ){
				foreach( $pSourceVariables as $lSourceCurrentVariables ){
					foreach( $lPositiveMatches as $lSinglePositiveMatch ){
						$lFoundMatch = false;
						$lParsedPositiveMatch = parseRegExpBySource($lSinglePositiveMatch, $lSourceCurrentVariables);						
						$lParsedNegativeMatches = parseRegExpArrayBySource($lNegativeMatches, $lSourceCurrentVariables);
						$lFormatTagsInRe = getFormatTagsInRe($lParsedPositiveMatch['expr']);
						$lTextToMatch = getTextFromNode($lFormatTagsInRe, $lSingleNode);			
									
						matchText($lParsedPositiveMatch, $lTextToMatch, $lParsedNegativeMatches, $lResult, $lNodeIdx);
						if( $lFoundMatch ){
							break;
						}
					}
				}
			}
		}else{					
			foreach( $lPositiveMatches as $lSinglePositiveMatch ){
				$lFoundMatch = false;				
				$lFormatTagsInRe = getFormatTagsInRe($lSinglePositiveMatch['expr']);
				$lTextToMatch = getTextFromNode($lFormatTagsInRe, $lSingleNode);			
							
				matchText($lSinglePositiveMatch, $lTextToMatch, $lNegativeMatches, $lResult, $lNodeIdx);
				if( $lFoundMatch ){
					break;
				}
			}
		}
	}
	return $lResult;
}

/**
	Връща масив със всички съвпадения отговарящи на RE pRE, които не съвпадат с никой от RE-тата в масива pNegativeREs
*/
function matchText( $pRE, $pTextArray, $pNegativeREs, &$pCurrentMatches, $pNodeIdx){		
	$lTextString = $pTextArray['text'];
	$lTextOpenPos = $pTextArray['openTagPos'];
	$lTextClosePos = $pTextArray['closeTagPos'];
	
	//~ var_dump($pRE['expr'], $lTextString);
						//~ echo '<br/>';echo '<br/>';echo '<br/>';
	
	$lCurrentPos = 0;
	$lResult = array();
	$lTextLength = mb_strlen( $lTextString );
	while( $lCurrentPos < $lTextLength ){		
		if( preg_match( $pRE['expr'], $lTextString, $lMatch, PREG_OFFSET_CAPTURE, MbOffsetToNonMbOffset($lCurrentPos, $lTextString) ) ){
			//~ echo '111@@@@' . $pRE['expr'];
			//~ var_dump($lMatch);
			//~ echo '<br/><br/>';
			$lNegativeMatchFound = false;
			convertMatchIndexes($lMatch, $lTextString);
			foreach( $pNegativeREs as $lNegativeRE ){
				if( preg_match( $lNegativeRE['expr'], $lMatch[0][0] )){					
					$lCurrentPos = $lMatch[0][1] + 1;
					$lNegativeMatchFound = true;
					break;
				}
			}
			if( !$lNegativeMatchFound ){		
					
				if( checkMatchOpenAndCloseTags($lMatch, $lTextOpenPos, $lTextClosePos)){//Правилен match					
					$lOverlapPos = (int)checkForOverlappingMatches( $pCurrentMatches[$pNodeIdx], $lMatch );					
					if( !$lOverlapPos ){//Не се застъпва с някой от предходните
						array_push($lResult, $lMatch);					
						$lCurrentPos = $lMatch[0][1] + mb_strlen($lMatch[0][0]);//Продължаваме търсенето от края на този match						
					}else{//Продължаваме търсенето от края на застъпващия се match;
						$lCurrentPos = $lOverlapPos + 1;
					}
				}else{						
					$lCurrentPos = $lMatch[0][1] + 1;
						
				}
			}
		}else{
			break;
		}
	}
	//Pazime expr i replacement-а като ключове на масива за да може после да заместим коректно в js
	$lPreviousResult = $pCurrentMatches[$pNodeIdx][$pRE['id']][$pRE['expr']][$pRE['replacement']];
	if(is_array( $lPreviousResult )){//Tyi kato za nqkoi regexp mojem da vlezem nqkolko pyti ako ima nqkolko iztochnika - mergevame predishniq rezultat za expressiona i tekushtiq
		$lNewResult = array_merge($lPreviousResult, $lResult);
	}else{
		$lNewResult = $lResult;
	}	
	$pCurrentMatches[$pNodeIdx][$pRE['id']][$pRE['expr']][$pRE['replacement']] = $lNewResult;	
	return $lResult;	
}

/**
	Гледа дали текущият мач се застъпва с някой от мачовете от предходните RE на този node;
	Връща 0 ако не се застъпват или връща позицията, на която свършва мач-ът с който се застъпват
*/
function checkForOverlappingMatches(&$pCurrentMatches, &$lCurrentMatch){
	$lStartPos = $lCurrentMatch[0][1];
	$lEndPos = $lStartPos + mb_strlen( $lCurrentMatch[0][0] );
	foreach( $pCurrentMatches as $lRe => $lReMatches ){
		foreach($lReMatches as $lSingleMatch){
			//Позиции на match-а 
			$lCurrentMatchStartPos = $lSingleMatch[0][1];
			$lCurrentMatchEndPos = $lCurrentMatchStartPos + mb_strlen( $lSingleMatch[0][0] );
			if( ($lCurrentMatchStartPos <= $lStartPos && $lStartPos <= $lCurrentMatchEndPos ) || ($lCurrentMatchStartPos <= $lEndPos && $lEndPos <= $lCurrentMatchEndPos ) )
				return $lCurrentMatchEndPos;
		}
	}
	return 0;
}

/**
	Гледа дали във всяка част от мачнатия текст броя на отворените тагове е равен на броя на затворените тагове
	Ако е равен връща true, иначе - false
*/
//~ function checkMatchOpenAndCloseTags($pMatchArr, $pOpenPosArr, $pClosePosArr, $pString){
function checkMatchOpenAndCloseTags($pMatchArr, $pOpenPosArr, $pClosePosArr){
	//TO DO - da se napravi i da gleda dali ne si selectnal chast ot tagovete, koito sa slojeni za formatirane 
	global $gStartPos;
	global $gEndPos;
	
	foreach( $pMatchArr as $lMatchPart ){
		$lString = $lMatchPart[0];
		//~ $gStartPos = NonMbOffsetToMbOffset($lMatchPart[1], $pString);
		$gStartPos = $lMatchPart[1];
		$gEndPos = $gStartPos + mb_strlen( $lString );
		
		$lStartTags = sort(array_filter($pOpenPosArr, "valueBetween"));
		$lEndTags = sort(array_filter($pClosePosArr, "valueBetween"));
		if( count( $lStartTags ) != count( $lEndTags ) || (count( $lStartTags ) && $lStartTags[0] > $lEndTags[0])) {//Zapochnati sa poveche tagove ili sa zatvoreni poveche tagove; Osven tova trqbva da zapochva sys otvarqsht tag
			return false;
		}
	}
	
	return true;
}

function parseRegExpArrayBySource($pRegExpArray, $pSourceVariables){
	$lResult = array();
	foreach( $pRegExpArray as $lSingleRe ){
		$lParsedRe = parseRegExpBySource($lSingleRe, $pSourceVariables);
		$lResult[] = $lParsedRe;
	}
	return $lResult;
}

//Във regexp-а замества променливите от източника със съответните им стойности
function parseRegExpBySource($pRegExp, $pSourceVariables){
	$lSearchArr = array();
	$lReplacementArr = array();
	$lResult = $pRegExp;
	foreach( $pSourceVariables as $key => $val ){
		$lSearchArr[] = $key;
		$lReplacementArr[] = $val;
	}
	$lResult['expr'] = str_replace($lSearchArr, $lReplacementArr, $lResult['expr']);
	$lResult['replacement'] = str_replace($lSearchArr, $lReplacementArr, $lResult['replacement']);
	return $lResult;
}

function NonMbOffsetToMbOffset($pOffset, $pString){
	$lStartString = substr(($pString), 0, $pOffset);
	return mb_strlen(($lStartString));
}

function MbOffsetToNonMbOffset($pOffset, $pString){
	$lStartString = mb_substr($pString, 0, $pOffset);
	return strlen($lStartString);
}

function valueBetween($pCurPos){	
	global $gStartPos;
	global $gEndPos;
	return $pCurPos > $gStartPos && $pCurPos < $gEndPos;
	
}

function convertMatchIndexes(&$pMatch, $pText){
	foreach( $pMatch as &$lSingleMatch ){
		$lSingleMatch[1] = NonMbOffsetToMbOffset($lSingleMatch[1], $pText);
	}
}

//Escape-ваме стойността за да не се образуват погрешни групи и т.н.
function escapeSourceVariableValue($pValue){
	$lReplacementPairsArr = array(
		'.' => '\.',
		'(' => '\(',//Spirame grupi
		')' => '\)',//Spirame grupi
		'/' => '\/',
		'\\' => '\\\\',
		'>' => '&gt;',//Spirame tagowe
		'<' => '&lt;',//Spirame tagowe
		'+' => '\+',
		'^' => '\^',
		'$' => '\$',
		
	);
	return str_replace(array_keys($lReplacementPairsArr), array_values($lReplacementPairsArr), $pValue);
}

?>