<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once ($docroot . '/lib/static.php');

function preindexXML(&$pNode, &$pCurIndex = 1) { // преиндексира xml-a, като на
                                                 // всеки element node слага
                                                 // атрибут ETA_ATTRIBUTE_NAME
                                                 // със стойност - индекса
	if(! $pNode)
		return;
	$pNode->setAttribute(ETA_ATTRIBUTE_NAME, $pCurIndex ++);

	$lChildren = $pNode->childNodes;
	for($i = 0; $i < $lChildren->length; ++ $i){
		$lChild = $lChildren->item($i);
		if($lChild->nodeType == 1){ // preindex element child
			preindexXML($lChild, $pCurIndex);
		}
	}

}

/**
 * Връща node-овете от които се взимат източниците
 *
 * pSources - масив със източниците за текущото правило
 * pXMLDom - Dom на текущия xml
 */
function getSourceNodes($pSources, $pXMLDom) {
	$lResult = array();
	$lXpath = new DOMXPath($pXMLDom);
	foreach($pSources as $lSourceID => $lSourceData){
		$lSourceXpath = $lSourceData['xpath'];
		$lXpathResult = $lXpath->query($lSourceXpath);
		$lResultArr = nodeListToArray($lXpathResult);
		if(count($lResultArr)){
			$lResult[$lSourceID] = $lResultArr;
		}
	}
	return $lResult;
}

/**
 * Връща масив със променливите за всеки ред от източниците
 *
 * pSourceNodes - масив във формат (sourceid => масив от възли), който са
 * всъщност източниците
 * pXMLDom - Dom на текущия xml
 */
function getSourceVariables($pSourceNodes, $pXMLDom) {
	$lResult = array();
	$lXpath = new DOMXPath($pXMLDom);
	foreach($pSourceNodes as $lSourceId => $lSourceNodes){
		$lResult[$lSourceId] = array();
		$lVariables = getSourceVariableExpressions($lSourceId);
// 		var_dump($lVariables);
		// Vzimame exxpressionite za da vzemem stoinostite
		foreach($lSourceNodes as $lCurrentSourceNode){
			// Vzimame stoinostite za vseki node ot tekushtiq iztochnik
			$lCurrentVariableValues = array();
			foreach($lVariables as $lVariableSymbol => $lVariableDetails){
				// Vzimame stoinostite na promenlivite za tekushtiq node
				$lValue = '';
				$lConcatSeparator = $lVariableDetails['concat_separator'];
				switch ((int) $lVariableDetails['variable_type']) {
					// Promenlivata e xpath
					case (int) SOURCE_XPATH_VARIABLE_TYPE :
						{
							$lXpathResult = $lXpath->query($lVariableDetails['expression'], $lCurrentSourceNode);
							if($lXpathResult->length){
								if($lVariableDetails['concat_multiple']){
									for($i = 0; $i < $lXpathResult->length; ++ $i){
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
					case (int) SOURCE_REGEXP_VARIABLE_TYPE :
						{ // Promenlivata e
						  // regexp
							$lMatches = array();
							preg_match_all($lVariableDetails['expression'], $lCurrentSourceNode->textContent, $lMatches);
							if(is_array($lMatches[0]) && count($lMatches[0])){
								if($lVariableDetails['concat_multiple']){
									for($i = 0; $i < count($lMatches[0]); ++ $i){
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
// 			var_dump($lCurrentVariableValues);
			$lFoundSimilarValue = false;
			foreach($lResult[$lSourceId] as $lPreviousMatches){
				if(count($lPreviousMatches) != count( $lCurrentVariableValues)){
					var_dump(1, count($lPreviousMatches), $lCurrentVariableValues, 2);
					continue;
				}
				foreach ($lPreviousMatches as $lSymbol => $lVal) {
					if($lVal != $lCurrentVariableValues[$lSymbol]){
						break 2;
					}
				}
				$lFoundSimilarValue = true;
			}
			if(!$lFoundSimilarValue){
				$lResult[$lSourceId][] = $lCurrentVariableValues;
			}
		}
// 		var_dump($lResult[$lSourceId]);
	}
	// ~ var_dump($lResult);
	return $lResult;
}

/**
 * Връща имената на променливите за източника заедно с изразите за взимане на
 * стойността им
 */
function getSourceVariableExpressions($pSourceId) {
	$lResult = array();
	$lCon = Con();
	$lSql = '
		SELECT v.expression, v.variable_type, v.variable_symbol, v.concat_multiple, v.concat_separator
		FROM autotag_re_variables v
		WHERE v.source_id = ' . (int) $pSourceId . '
	';
	$lCon->Execute($lSql);
	$lCon->MoveFirst();
	while(! $lCon->Eof()){
		$lResult[$lCon->mRs['variable_symbol']] = array(
			'expression' => $lCon->mRs['expression'],
			'variable_type' => $lCon->mRs['variable_type'],
			'concat_multiple' => $lCon->mRs['concat_multiple'],
			'concat_separator' => $lCon->mRs['concat_separator']
		);
		$lCon->MoveNext();
	}
	$lCon->CloseRS();
	return $lResult;

}

/**
 * В зависимост от параметъра pGetXPath връща масив със всички XPath
 * expression-и за даден autotag rule
 * или връща масив със всички regular expression-и за дадения autotag rule
 */
function getRuleProperties($pRuleID, $pPropertyType) {
	$lCon = Con();
	$lResult = array();
	switch ((int) $pPropertyType) {
		default :
		case (int) PLACE_RULE_PROPERTY_TYPE :
			{ // Xpath-ове
				$lSql = '
				SELECT p.xpath as xpath, ar.property_modifier_id as type, p.id, p.name
				FROM place_rules p
				JOIN autotag_rules_properties ar ON ar.property_id = p.id
				WHERE ar.rule_id = ' . (int) $pRuleID . '
			';
				$lPropertyName = 'xpath';
				break;
			}
		case (int) REGEXP_RULE_PROPERTY_TYPE :
			{ // re-ta
				$lSql = '
				SELECT p.expression as expression, ar.property_modifier_id as type, p.replacement, p.groupsupdepth, p.id, p.name, ar.priority
				FROM regular_expressions p
				JOIN autotag_rules_properties ar ON ar.property_id = p.id
				WHERE ar.rule_id = ' . (int) $pRuleID . '
				ORDER BY ar.priority ASC
			';
				// Приоритета е важен, т.к. по него се филтрират match-овете
				$lPropertyName = 'expression';
				break;
			}
		case (int) SOURCE_RULE_PROPERTY_TYPE :
			{
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
	while(! $lCon->Eof()){
		$lTemp = array();
		// ~ $lTemp['id'] = (int) $lCon->mRs['id'];
		$lTemp['type'] = (int) $lCon->mRs['type'];
		$lTemp['name'] = $lCon->mRs['name'];
		$lTemp[$lPropertyName] = $lCon->mRs[$lPropertyName];
		switch ((int) $pPropertyType) {
			case (int) REGEXP_RULE_PROPERTY_TYPE :
				{ // re-ta
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

function dumpDomElementArray($pArray) {
	foreach($pArray as $lElement){
		var_dump($lElement->nodeName);
	}
}

function nodeListToArray($pNodeList) {
	$lResult = array();
	for($i = 0; $i < $pNodeList->length; ++ $i){
		array_push($lResult, ($pNodeList->item($i)));
	}
	return $lResult;
}

function arrayOfDOMElementsDiff($pArray1, $pArray2) {
	$lResultArr = array();
	foreach($pArray1 as $lElement){
		$lFound = false;
		foreach($pArray2 as $lElement2){
			if($lElement === $lElement2){
				$lFound = true;
				break;
			}

		}
		if(! $lFound){
			array_push($lResultArr, $lElement);
		}
	}
	return $lResultArr;
}

function arrayOfDOMElementsIntersect($pArray1, $pArray2) {
	$lResultArr = array();
	foreach($pArray1 as $lElement){
		$lFound = false;
		foreach($pArray2 as $lElement2){
			if($lElement === $lElement2){
				$lFound = true;
				break;
			}

		}
		if($lFound){
			array_push($lResultArr, $lElement);
		}
	}
	return $lResultArr;
}

function getPlaceResult($pXMLDom, $pPlacesXpathArr) {
	$lPlacesAnd = array();
	$lPlacesOr = array();
	$lPlacesNot = array();

	if(! is_array($pPlacesXpathArr))
		return array();

	foreach($pPlacesXpathArr as $lId => $lXpathExp){
		switch ($lXpathExp['type']) {
			default :
			case (int) XPATH_AND_TYPE :
				{
					array_push($lPlacesAnd, $lXpathExp['xpath']);
					break;
				}
			case (int) XPATH_OR_TYPE :
				{
					array_push($lPlacesOr, $lXpathExp['xpath']);
					break;
				}
			case (int) XPATH_NOT_TYPE :
				{
					array_push($lPlacesNot, $lXpathExp['xpath']);
					break;
				}

		}
	}

	$lXpath = new DOMXPath($pXMLDom);
	$lNodes = array();
	foreach($lPlacesOr as $lXpathQuery){
		$lResult = $lXpath->query($lXpathQuery);
		$lResultArr = nodeListToArray($lResult);
		$lNodes = array_merge($lNodes, $lResultArr);
	}

	foreach($lPlacesAnd as $lXpathQuery){
		$lResult = $lXpath->query($lXpathQuery);
		$lResultArr = nodeListToArray($lResult);
		$lNodes = arrayOfDOMElementsIntersect($lNodes, $lResultArr);
	}

	foreach($lPlacesNot as $lXpathQuery){
		$lResult = $lXpath->query($lXpathQuery);
		$lResultArr = nodeListToArray($lResult);
		$lNodes = arrayOfDOMElementsDiff($lNodes, $lResultArr);
	}
	return getDistinctParentDOMElements($lNodes);

}

function getDistinctParentDOMElements($pArray) { // Връща масив от уникални ДОМ
                                                 // елементи, като никой от
                                                 // резултатните ДОМ елементи не
                                                 // е наследник на някой от
                                                 // останалите резултати
                                                 // Задължително е node-овете да
                                                 // имат атрибут
                                                 // ETA_ATTRIBUTE_NAME
                                                 // ~ return $pArray;
	$lRes = array();
	$lResult = array();
	$lParentCountArray = array();
	foreach($pArray as $lNode){
		$lParents = array();
		$lTempNode = $lNode->parentNode;
		while($lTempNode){
			array_push($lParents, $lTempNode);
			$lTempNode = $lTempNode->parentNode;
		}
		$lNodeIdx = (int) $lNode->getAttribute(ETA_ATTRIBUTE_NAME);
		if(! $lNodeIdx){
			continue;
		}
		$lParentCount = count($lParents);
		if(! is_array($lParentCountArray[$lParentCount]))
			$lParentCountArray[$lParentCount] = array();
		$lParentCountArray[$lParentCount][] = $lNode;
		$lRes[$lNodeIdx] = $lParents;

	}

	foreach($lParentCountArray as $lCount => $lNodeArr){
		foreach($lNodeArr as $lSingleNode){
			$lNodeIdx = (int) $lSingleNode->getAttribute(ETA_ATTRIBUTE_NAME);
			if(! $lNodeIdx){
				continue;
			}
			$lParents = array_reverse($lRes[$lNodeIdx]);
			$lParentFound = false;
			$lCurParentParentCount = $lCount;
			foreach($lParents as $lParentNode){
				-- $lCurParentParentCount;
				if(is_array($lParentCountArray[$lCurParentParentCount]) && in_array($lParentNode, $lParentCountArray[$lCurParentParentCount])){
					$lParentFound = true;
					break;
				}
			}
			if(! $lParentFound){
				array_push($lResult, $lSingleNode);
			}
		}
	}
	return $lResult;

}

function getFormatTagsInRe($pRE) { // Връща масив с форматиращите тагове, които
                                   // се срещат във pRe
                                   // TO DO
	$lResult = array();
	$lPattern = '<([A-Z:_a-z][A-Z:_a-z0-9\-\.]*)>';
	if(preg_match_all('/' . $lPattern . '/i', $pRE, $lMatch)){
		if(is_array($lMatch[1])){
			foreach($lMatch[1] as $lTag){
				array_push($lResult, strtolower($lTag));
			}
		}
	}
	return array_unique($lResult);
}

function getTextFromNode($pTagsArray, $pXMLNode, &$pCurrentPos = 0) {
	/**
	 * Връща масив със следните елементи:
	 * text - текста на pXMLNode обработен съобразно форматиращите тагове
	 * pTagsArray
	 * openTagPos - масив с позициите в стринга на всички отварящи тагове
	 * closeTagPos - масив с позициите в стринга на всички затварящи тагове
	 */
	$lResultString = '';
	$lResultOpenTagPos = array();
	$lResultCloseTagPos = array();
// 	var_dump(1, $pXMLNode);
	if(true || (is_array($pTagsArray)&& count($pTagsArray))){
		if($pXMLNode->hasChildNodes()){
			foreach($pXMLNode->childNodes as $lItem){
				if($lItem->nodeType == 3){
					$lResultString .= $lItem->nodeValue;
					$pCurrentPos += mb_strlen($lItem->nodeValue);
				}else if($lItem->nodeType == 1){
					if(in_array($lItem->nodeName, $pTagsArray)){
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
	}else{
		var_dump($pXMLNode->ownerDocument->saveXML($pXMLNode));
		$lResult = $pXMLNode->textContent;
	}
	$lResult = array();
	$lResult['text'] = $lResultString;
	$lResult['openTagPos'] = $lResultOpenTagPos;
	$lResult['closeTagPos'] = $lResultCloseTagPos;
	// ~ var_dump($lResult);
	return $lResult;
}

function calculateRealMatchLength($pText, $pFormattingTagsArr) {
	$lText = $pText;
	for($i = 0; $i < count($pFormattingTagsArr); ++ $i){
		// $lStartRE = new RegExp('</?' + pFormattingTagsArr[i].toLowerCase() +
		// '>', 'gim');
		// ~ lText = lText.replace('<' + pFormattingTagsArr[i].toLowerCase() +
		// '>');
		// ~ lText = lText.replace('</' + pFormattingTagsArr[i].toLowerCase() +
		// '>');
		$lText = preg_replace('/\<\/?' . strtolower($pFormattingTagsArr[$i]) . '\>/', '', $lText);
		// $lText = $lText.replace($lStartRE, '');
	}
	return mb_strlen($lText);
}

function remove_array_value($pArray, $pValue) {
	$i = 0;
	while($i < count($pArray)){
		if($pArray[$i] == $pValue){
			array_splice($pArray, $i, 1);
		}else{
			$i ++;
		}
	}
	return $pArray;
}

/**
 * Гледа дали текущият мач се застъпва с някой от мачовете от предходните RE на
 * този node;
 * Връща 0 ако не се застъпват или връща позицията, на която свършва мач-ът с
 * който се застъпват
 */
function checkForOverlappingMatches(&$pCurrentMatches, &$lCurrentMatch) {
	$lStartPos = $lCurrentMatch[0][1];
	$lEndPos = $lStartPos + mb_strlen($lCurrentMatch[0][0]);
	// ~ $lMatches[$pRE['id']][$pRE['expr']][$pRE['replacement']]
	foreach($pCurrentMatches as $lRe => $lReMatches){
		foreach($lReMatches as $lExpression => $lExpressionMatches){
			foreach($lExpressionMatches as $lReplacement => $lReplacementMatches){
				foreach($lReplacementMatches as $lSingleMatch){
					// Позиции на match-а
					$lCurrentMatchStartPos = $lSingleMatch[0][1];
					$lCurrentMatchEndPos = $lCurrentMatchStartPos + mb_strlen($lSingleMatch[0][0]);
					if(($lCurrentMatchStartPos <= $lStartPos && $lStartPos <= $lCurrentMatchEndPos) || ($lCurrentMatchStartPos <= $lEndPos && $lEndPos <= $lCurrentMatchEndPos))
						return $lCurrentMatchEndPos;
				}
			}
		}
	}
	return 0;
}

/**
 * Гледа дали във всяка част от мачнатия текст броя на отворените тагове е равен
 * на броя на затворените тагове
 * Ако е равен връща true, иначе - false
 */
// ~ function checkMatchOpenAndCloseTags($pMatchArr, $pOpenPosArr,
// $pClosePosArr, $pString){
function checkMatchOpenAndCloseTags($pMatchArr, $pOpenPosArr, $pClosePosArr) {
	// TO DO - da se napravi i da gleda dali ne si selectnal chast ot tagovete,
	// koito sa slojeni za formatirane
	global $gStartPos;
	global $gEndPos;

	foreach($pMatchArr as $lMatchPart){
		$lString = $lMatchPart[0];
		// ~ $gStartPos = NonMbOffsetToMbOffset($lMatchPart[1], $pString);
		$gStartPos = $lMatchPart[1];
		$gEndPos = $gStartPos + mb_strlen($lString);

		$lStartTags = sort(array_filter($pOpenPosArr, "valueBetween"));
		$lEndTags = sort(array_filter($pClosePosArr, "valueBetween"));
		if(count($lStartTags) != count($lEndTags) || (count($lStartTags) && $lStartTags[0] > $lEndTags[0])){
			// Zapochnati sa poveche tagove ili sa zatvoreni poveche tagove;
			// Osven tova trqbva da zapochva sys otvarqsht tag
			return false;
		}
	}

	return true;
}

function parseRegExpArrayBySource($pRegExpArray, $pSourceVariables) {
	$lResult = array();
	foreach($pRegExpArray as $lSingleRe){
		$lParsedRe = parseRegExpBySource($lSingleRe, $pSourceVariables);
		$lResult[] = $lParsedRe;
	}
	return $lResult;
}

// Във regexp-а замества променливите от източника със съответните им стойности
function parseRegExpBySource($pRegExp, $pSourceVariables) {
	$lSearchArr = array();
	$lReplacementArr = array();
	$lResult = $pRegExp;
	foreach($pSourceVariables as $key => $val){
		$lSearchArr[] = $key;
		$lReplacementArr[] = $val;
	}
	$lResult['expr'] = str_replace($lSearchArr, $lReplacementArr, $lResult['expr']);
	$lResult['replacement'] = str_replace($lSearchArr, $lReplacementArr, $lResult['replacement']);
	return $lResult;
}

function NonMbOffsetToMbOffset($pOffset, $pString) {
	$lStartString = substr(($pString), 0, $pOffset);
	return mb_strlen(($lStartString));
}

function MbOffsetToNonMbOffset($pOffset, $pString) {
	$lStartString = mb_substr($pString, 0, $pOffset);
	return strlen($lStartString);
}

function valueBetween($pCurPos) {
	global $gStartPos;
	global $gEndPos;
	return $pCurPos > $gStartPos && $pCurPos < $gEndPos;

}

function convertMatchIndexes(&$pMatch, $pText) {
	foreach($pMatch as &$lSingleMatch){
		$lSingleMatch[1] = NonMbOffsetToMbOffset($lSingleMatch[1], $pText);
	}
}

// Escape-ваме стойността за да не се образуват погрешни групи и т.н.
function escapeSourceVariableValue($pValue) {
	$lReplacementPairsArr = array(
		'.' => '\.',
		'(' => '\(',  // Spirame grupi
		')' => '\)',  // Spirame grupi
		']' => '\]',  // Spirame grupi
		'[' => '\[',  // Spirame grupi
		'/' => '\/',
		'\\' => '\\\\',
		'>' => '&gt;',  // Spirame tagowe
		'<' => '&lt;',  // Spirame tagowe
		'+' => '\+',
		'^' => '\^',
		'$' => '\$'
	);
	return str_replace(array_keys($lReplacementPairsArr), array_values($lReplacementPairsArr), $pValue);
}

function RegExpCompare($pElement1, $pElement2) {
	$lPriority1 = (float) $pElement1['priority'];
	$lPriority2 = (float) $pElement2['priority'];
	$lResult = 1;
	if($lPriority1 == $lPriority2)
		$lResult = 0;
	if($lPriority1 < $lPriority2)
		$lResult = - 1;
		// ~ var_dump( $lPriority1, $lPriority2, $lResult );
		// ~ echo '<br/>';
	return $lResult;
}

/**
 *
 * @param $pNode DomNode
 */
function getFirstTextNodeChild($pNode) {
	for($i = 0; $i < $pNode->childNodes->length; ++ $i){
		$lChild = $pNode->childNodes->item($i);
		if($lChild->nodeType == 3)
			return $lChild;
		if($lChild->nodeType == 1){
			$lTextNode = getFirstTextNodeChild($lChild);
			if($lTextNode)
				return $lTextNode;
		}
	}
	return false;
}

/**
 *
 * @param $pNode DomNode
 */
function getPreviousTextNode($pNode) {
	$lPreviousSibling = false;
	$lParent = $pNode;
	while($lParent){
		$lPreviousSibling = $lParent->previousSibling;
		while($lPreviousSibling){
			if($lPreviousSibling->nodeType == 3)
				return lPreviousSibling;
			if($lPreviousSibling->nodeType == 1){
				$lTextNode = getLastTextNodeChild($lPreviousSibling);
				if($lTextNode)
					return $lTextNode;
			}
			$lPreviousSibling = $lPreviousSibling->previousSibling;
		}
		$lParent = $lParent->parentNode;
	}
	return false;

}

/**
 *
 * @param $pNode DomNode
 */
function getNextTextNode($pNode) {
	$lNextSibling = false;
	$lParent = $pNode;
	while($lParent){
		$lNextSibling = $lParent->nextSibling;
		while($lNextSibling){
			if($lNextSibling->nodeType == 3)
				return $lNextSibling;
			if($lNextSibling->nodeType == 1){
				$lTextNode = getFirstTextNodeChild($lNextSibling);
				if($lTextNode)
					return $lTextNode;
			}
			$lNextSibling = $lNextSibling->nextSibling;
		}
		$lParent = $lParent->parentNode;
	}
	return false;
}

/**
 *
 * @param DomNode $pNode
 */
function getLastTextNodeChild($pNode){
	for( $i = $pNode->childNodes->length - 1; $i >= 0; --$i){
		$lChild = $pNode->childNodes->item(i);
		if( $lChild->nodeType == 3 )
			return $lChild;
		if( $lChild->nodeType == 1 ){
			$lTextNode = getLastTextNodeChild($lChild);
			if( $lTextNode )
				return $lTextNode;
		}
	}
	return false;
}

/**
 *
 * @param DomNode $pNodeA
 * @param DomNode $pNodeB
 * @return DomNode
 */
function getFirstCommonParent($pNodeA, $pNodeB) {
	$lParentsA = getNodeParents($pNodeA);
	$lParentsB = getNodeParents($pNodeB);
	if(in_array($pNodeB, $lParentsA))
		return $pNodeB;
	for($i = 0; i < count($lParentsA); ++ $i){
		if(in_array($lParentsA[$i], $lParentsB))
			return $lParentsA[$i];
	}
}

function getNodeParents($pNode) {
	$lResult = array();
	$lNode = $pNode->parentNode;
	while($lNode){
		$lResult[] = $lNode;
		$lNode = $lNode->parentNode;
	}
	return $lResult;
}

function js_slice($pTxt, $pStartIdx = 0, $pEndIdx = 0) {
	if($pEndIdx){
		return mb_substr($pTxt, $pStartIdx, $pEndIdx - $pStartIdx);
	}else{
		return mb_substr($pTxt, $pStartIdx);
	}
}

function getAutotagMatchNodeDetails($pNode, $pOffset, $pRefNode, $pRefNodeOffset, $pFollowToNextNode = false) {
	$lCurrentTextNode = null;
	if(! $pRefNode){
		$lCurrentTextNode = getFirstTextNodeChild($pNode);
	}else{
		$lCurrentTextNode = $pRefNode;
	}
	$lCurrentOffset = $pOffset;

	if($pRefNodeOffset)
		$lCurrentOffset += $pRefNodeOffset;
	$lCurrentTextLength = mb_strlen($lCurrentTextNode->textContent);

	while($lCurrentTextNode && $lCurrentOffset > 0){
		if($lCurrentTextLength >= $lCurrentOffset)
			break;
		$lCurrentOffset = $lCurrentOffset - $lCurrentTextLength;
		$lCurrentTextNode = getNextTextNode($lCurrentTextNode);
		if($lCurrentTextNode->textContent)
			$lCurrentTextLength = mb_strlen($lCurrentTextNode->textContent);
		else{
			// ~ console.log(1);
			$lCurrentTextLength = 0;
		}
	}
	if($pFollowToNextNode){
			// Ako sme na kraq na textov node - otivame do nachaloto na sledvashtiq textov node, koito ne e prazen
		while($lCurrentTextLength == $lCurrentOffset){
			$lCurrentTextNode = getNextTextNode($lCurrentTextNode);
			$lCurrentTextLength = mb_strlen($lCurrentTextNode->textContent);
			$lCurrentOffset = 0;
		}
	}
	if($lCurrentTextNode){
		return array(
			$lCurrentTextNode,
			$lCurrentOffset
		);
	}else{
		return array(
			null,
			0
		);
	}
}

function correctAutotagNodes($pSearchNode, $pStartNode, $pStartOffset, $pEndNode, $pEndOffset, $pUpMaxDepth) {
	// StartNode и EndNode са текстови node-ове.!!!!Задължително
	if($pStartNode->parentNode != $pEndNode->parentNode){
		$lCommonParent = getFirstCommonParent($pStartNode, $pEndNode);
		if($pSearchNode != getFirstCommonParent($lCommonParent, $pSearchNode)){
			// Ako obshtiq parent e nad node-a v koito se provejda tyrseneto - greshka
			return null;
		}

		if($pStartOffset && $lCommonParent != $pStartNode->parentNode){
			// Ako sys start-a shte trqbva da se katerim nagore prez dyrvoto, a selectiona ne pochva ot nachaloto na tekstoviq node
			return null;
		}
		if($pEndOffset != mb_strlen($pEndNode->textContent) && $lCommonParent != $pEndNode->parentNode){
			// Ako sys end-a shte trqbva da se katerim nagore prez dyrvoto, a selectiona svyrshva predi kraq na tekstoviq node
			return null;
		}
		while($pStartNode->parentNode != $lCommonParent){ // Korigirame start-a
			$lPrevSibling = $pStartNode->previousSibling;
			while($lPrevSibling && ! mb_strlen($lPrevSibling->textContent)){
				$lPrevSibling = $lPrevSibling->previousSibling;
			}
			if($lPrevSibling){
				// Ima node predi start-a, koito e sys tekst, a ne e selectnat
				return null;
			}
			$pStartNode = $pStartNode->parentNode;
		}
		while($pEndNode->parentNode != $lCommonParent){ // Korigirame end-a
			$lNextSibling = $pEndNode->nextSibling;
			while($lNextSibling && ! mb_strlen($lNextSibling->textContent)){
				$lNextSibling = $lNextSibling->nextSibling;
			}
			if($lNextSibling){
				// Ima node sled end-a, koito e sys tekst, a ne e selectnat
				return null;
			}
			$pEndNode = $pEndNode->parentNode;
		}
	}

	// Veche sme si osigurili che start i end imat obsht parent - sega trqbva da
	// gledame dali trqbva da se katerim nagore po dyrvoto za da obhvashtame
	// po-golqm range
	while($pUpMaxDepth != 0 && $pStartNode->parentNode != $pSearchNode && $pStartOffset == 0 && $pEndOffset == mb_strlen($pEndNode->textContent)){
		/**
		 * Ako e nula spirame;
		 * Ako stignem node-a v koito tyrsim - spirame;
		 * Ako e otricatelno se katerim kolkoto mojem;
		 */
		$lPrevSibling = $pStartNode->previousSibling;
		while($lPrevSibling && ! $lPrevSibling->textContent){
			$lPrevSibling = $lPrevSibling->previousSibling;
		}
		$lNextSibling = $pEndNode->nextSibling;
		while($lNextSibling && ! $lNextSibling->textContent){
			$lNextSibling = $lNextSibling->nextSibling;
		}
		if($lPrevSibling || $lNextSibling){
			// Ima predhoden ili sledvasht node s tekst, koito ne e selectnat
			break;
		}
		$pStartNode = $pStartNode->parentNode;
		$pEndNode = $pStartNode;
		-- $pUpMaxDepth;
	}

	return array(
		$pStartNode,
		$pStartOffset,
		$pEndNode,
		$pEndOffset
	);

}

/**
 *
 * @param DomDocument $pTemplateDom
 * @param unknown_type $pRemoveAutotagNodes
 */
function getReplacementXml($pTemplateDom, $pRemoveAutotagNodes = false){
	removeEtaAttribute($pTemplateDom);

	if( $pRemoveAutotagNodes )
		removeAutotagNodes($pTemplateDom);

	$lReplacementRoot = $pTemplateDom->childNodes->item(0);
	$lReplacementXml = '';
	for( $i = 0; $i < $lReplacementRoot->childNodes->length; ++$i){
		$lCurrentChild = $lReplacementRoot->childNodes->item($i);

		if( $lCurrentChild->nodeType == 1 || $lCurrentChild->nodeType == 1 ){
			$lReplacementXml = $lReplacementXml . $lCurrentChild->ownerDocument->saveXML($lCurrentChild);
		}
	}
	return $lReplacementXml;
}

/**
 *
 * @param DomNode $pNode
 */
function removeEtaAttribute($pNode){
	$lOwnerDocument = $pNode->ownerDocument;
	if(!$lOwnerDocument){
		$lOwnerDocument = $pNode;
	}
// 	var_dump($lOwnerDocument, $pNode);
	$lXPath = new DOMXPath($lOwnerDocument);
	$lNodesWithAttributes = $lXPath->query('//*[@' . ETA_ATTRIBUTE_NAME . ']', $pNode);
	for( $i = 0; $i < $lNodesWithAttributes->length; ++$i){
		$lNodesWithAttributes->item($i)->removeAttribute(ETA_ATTRIBUTE_NAME);
	}
}

/**
 *
 * @param DomNode $pNode
 */
function removeAutotagNodes($pNode){
	//TO DO - da se napravi da maha node-ovete, koito sme insertnali
	$lOwnerDocument = $pNode->ownerDocument;
	if(!$lOwnerDocument){
		$lOwnerDocument = $pNode;
	}
	$lXPath = new DOMXPath($lOwnerDocument);
	$lNodeToRemove = $lXPath->query('//' . AUTOTAG_MARKER_NODE_NAME)->item(0);
	while( $lNodeToRemove ){
		$lParent = $lNodeToRemove->parentNode;
		for( $i = 0; $i < $lNodeToRemove->childNodes->length ; ++$i ){
			$lCurrentChild = $lNodeToRemove->childNodes->item($i);
			if( $lCurrentChild->nodeType != 1 && $lCurrentChild->nodeType != 3 )
				continue;
			$lParent->insertBefore($lCurrentChild->cloneNode(true), $lNodeToRemove );

		}
		$lParent->removeChild($lNodeToRemove);
		$lNodeToRemove = $lXPath->query('//' + AUTOTAG_MARKER_NODE_NAME)->item(0);
	}
}
?>