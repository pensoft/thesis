<?php
ini_set('mbstring.internal_encoding', 'UTF-8'); // Това е важно за да брои
                                                // мултибайт mb_strlen
require_once ('autotag_lib.php');

define('TAXON_NODE_NAME', 'tp:taxon-name');
define('UBIO_TAXON_RULE_ID', 50);
define('AUTOTAG_MARKER_MATCH_NUMBER_ATTR_NAME', 'match_number');
define('AUTOTAG_MARKER_MATCH_PART_NUMBER_ATTR_NAME', 'match_part');
define('AUTOTAG_MARKER_NODE_NAME', 'autotag_marker');
define('NAMESPACE_DECLARATIONS', 'xmlns:mml="http://www.w3.org/1998/Math/MathML" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:tp="http://www.plazi.org/taxpub"');
class cautotag {
	var $mArticleId;
	var $mRuleId;
	var $mXml;

	var $mRegularExpressions;
	var $mPlaceXpaths;
	var $mSources;
	var $mSourceNodes;
	var $mSourceVariables;
	var $mDOM;
	var $flatMatches;
	var $ignoredMatches;
	var $matchNumberOfResults;
	var $autotagRegularExpresions;
	var $autotagReplacements;
	var $m_matches;
	var $mXPath;

	function __construct($pArticleId, $pRuleId, $pXml, $pGetArticleContent, $pPreindexXml) {
		$this->mArticleId = (int) $pArticleId;
		$this->mRuleId = (int) $pRuleId;
		$this->mXml = $pXml;
		if(! $this->mArticleId || ! $this->mRuleId){
			exit();
		}

		if((int) $pGetArticleContent){
			$this->GetArticleContent();
		}

		$this->mPlaceXpaths = getRuleProperties($this->mRuleId, (int) PLACE_RULE_PROPERTY_TYPE);
		$this->mRegularExpressions = getRuleProperties($this->mRuleId, (int) REGEXP_RULE_PROPERTY_TYPE);
		$this->mSources = getRuleProperties($this->mRuleId, (int) SOURCE_RULE_PROPERTY_TYPE);

		$this->mDOM = new DOMDocument("1.0");
		$this->mDOM->resolveExternals = true;

		if(! count($this->mPlaceXpaths) || ! count($this->mRegularExpressions) || ! $this->mDOM->loadXML($this->mXml)){
			exit();
		}
		$this->mXPath = new DOMXPath($this->mDOM);

		if((int) $pPreindexXml)
			$this->PreindexXml();

	}

	function GetArticleContent() {
		$lCon = Con();
		$lCon->Execute('SELECT xml_content FROM articles WHERE id = ' . (int) $this->mArticleId);
		$lCon->MoveFirst();
		$this->mXml = $lCon->mRs['xml_content'];
	}

	function PreindexXml() {
		preindexXML($this->mDOM->documentElement);
	}

	function GetData() {
		$this->mSourceNodes = getSourceNodes($this->mSources, $this->mDOM);
		$this->mSourceVariables = getSourceVariables($this->mSourceNodes, $this->mDOM);
// 		var_dump($this->mSourceVariables);
		$lNodesToMatch = getPlaceResult($this->mDOM, $this->mPlaceXpaths);

		$lMatchResult = $this->autoMatch($this->mSourceVariables, $lNodesToMatch, $this->mRegularExpressions);
// 		var_export($lMatchResult);
		$this->m_matches = $lMatchResult;

		return $lMatchResult;
	}

	function ProcessMatches() {

// 		$this->m_matches = array(
// 			59 => array(
// 				53 => array(
// 					'/(\\s\\bet\\s\\bal\\.\\s\\(\\))/umis' => array(
// 						'<xref ref-type="bibr" rid="">$1</xref>' => array()
// 					)
// 				),
// 				31 => array(
// 					'/(\\s\\bet\\s\\bal\\.\\s)/umis' => array(
// 						'<xref ref-type="bibr" rid="">$1</xref>' => array(
// 							0 => array(
// 								0 => array(
// 									0 => ' et al. ',
// 									1 => 1566
// 								),
// 								1 => array(
// 									0 => ' et al. ',
// 									1 => 1566
// 								)
// 							),
// 							1 => array(
// 								0 => array(
// 									0 => ' et al. ',
// 									1 => 1909
// 								),
// 								1 => array(
// 									0 => ' et al. ',
// 									1 => 1909
// 								)
// 							),
// 							2 => array(
// 								0 => array(
// 									0 => ' et al. ',
// 									1 => 2584
// 								),
// 								1 => array(
// 									0 => ' et al. ',
// 									1 => 2584
// 								)
// 							)
// 						)
// 					)
// 				)
// 			),
// 			159 => array(
// 				53 => array(
// 					'/($AuthorName\\s\\bet\\s\\bal\\.\\s\\($Year\\))/umis' => array(
// 						'<xref ref-type="bibr" rid="$Id">$1</xref>' => array()
// 					)
// 				),
// 				31 => array(
// 					'/($AuthorName\\s\\bet\\s\\bal\\.\\s$Year)/umis' => array(
// 						'<xref ref-type="bibr" rid="$Id">$1</xref>' => array()
// 					)
// 				)
// 			)
// 		);
		$this->FlattenMatches();

// 		echo $this->mDOM->saveXML();
		$this->placeAutotagMarkers();


		$this->parseAutoMatch(0);
		$this->replaceAutotagMatch(0);
		removeEtaAttribute($this->mDOM);
		removeAutotagNodes($this->mDOM);
		$lXML = $this->mDOM->saveXML();
		$lSql = 'INSERT INTO article_autotag_temp(article_id, rule_id, after_xml)
			VALUES (' . (int)$this->mArticleId . ', ' . (int)$this->mRuleId . ', \'' . q($lXML) . '\')
		';
// 		var_dump($lSql);
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lSql);
// 		echo $lXML;
	}

	function FlattenMatches() {
		$this->flatMatches = array();
		foreach($this->m_matches as $lNodeIdx => $lNodeMatches){
			foreach($lNodeMatches as $lExpressionId => $lReMatches){
				foreach($lReMatches as $lPattern => $lPatternMatches){
					foreach($lPatternMatches as $lReplacement => $lReplacementMatches){
						foreach($lReplacementMatches as $lMatchIdx => $lMatchData){
							$this->flatMatches[] = array(
								$lMatchData,
								$lNodeIdx,
								$lExpressionId,
								$lPattern,
								$lReplacement
							);
						}
					}
				}
			}
		}
		$this->matchNumberOfResults = count($this->flatMatches);
		$this->ignoredMatches = array();
		$this->autotagReplacements = array();
	}

	function autoMatch($pSourceVariables, $pResultNodes, $pREArray) {
		$lPositiveMatches = array();
		$lNegativeMatches = array();

		foreach($pREArray as $lId => $lSingleRe){
			if($lSingleRe['type'] == (int) RE_POSITIVE_TYPE){
				array_push($lPositiveMatches, array(
					'id' => $lId,
					'expr' => $lSingleRe['expression'],
					'priority' => $lSingleRe['priority'],
					'name' => $lSingleRe['name'],
					'replacement' => $lSingleRe['replacement']
				));
			}else{
				array_push($lNegativeMatches, array(
					'id' => $lId,
					'expr' => $lSingleRe['expression'],
					'priority' => $lSingleRe['priority'],
					'name' => $lSingleRe['name'],
					'replacement' => $lSingleRe['replacement']
				));
			}
		}
		usort($lPositiveMatches, 'RegExpCompare');
		$lResult = array();

// 		var_dump($pSourceVariables);
// 		var_dump($lPositiveMatches);
// 		exit;
		foreach($pResultNodes as $lSingleNode){
			$lNodeIdx = (int) $lSingleNode->getAttribute(ETA_ATTRIBUTE_NAME);
			// ~ $lNodeIdx = (int) 1;
			if(! $lNodeIdx)
				continue;
			$lResult[$lNodeIdx] = array();
// 			var_dump($pSourceVariables);
// 			exit;
			if(is_array($pSourceVariables) && count($pSourceVariables)){
				// Ima source
				foreach($pSourceVariables as $pSourceId => $pSourceVariablesDetails){
					foreach($pSourceVariablesDetails as $lSourceCurrentVariables){
						foreach($lPositiveMatches as $lSinglePositiveMatch){
							$lParsedPositiveMatch = parseRegExpBySource($lSinglePositiveMatch, $lSourceCurrentVariables);

							$lParsedNegativeMatches = parseRegExpArrayBySource($lNegativeMatches, $lSourceCurrentVariables);
							$lFormatTagsInRe = getFormatTagsInRe($lParsedPositiveMatch['expr']);
							$lTextToMatch = getTextFromNode($lFormatTagsInRe, $lSingleNode);

							$this->matchText($lParsedPositiveMatch, $lTextToMatch, $lParsedNegativeMatches, $lResult, $lNodeIdx);
// 							var_dump($pSourceVariables);
// 							exit;
						}
					}
				}
			}else{
				foreach($lPositiveMatches as $lSinglePositiveMatch){
					$lFormatTagsInRe = getFormatTagsInRe($lSinglePositiveMatch['expr']);
					$lTextToMatch = getTextFromNode($lFormatTagsInRe, $lSingleNode);
// 					var_dump($lTextToMatch);
// 					exit;

					$this->matchText($lSinglePositiveMatch, $lTextToMatch, $lNegativeMatches, $lResult, $lNodeIdx);
				}
			}
		}
// 		var_dump($lResult);
		return $lResult;
	}

	function matchText($pRE, $pTextArray, $pNegativeREs, &$pCurrentMatches, $pNodeIdx) {
		$lTextString = $pTextArray['text'];
		$lTextOpenPos = $pTextArray['openTagPos'];
		$lTextClosePos = $pTextArray['closeTagPos'];

		// ~ var_dump($pRE['expr'], $lTextString);
		// ~ echo '<br/>';echo '<br/>';echo '<br/>';

		$lCurrentPos = 0;
		$lResult = array();
		$lTextLength = mb_strlen($lTextString);
		while($lCurrentPos < $lTextLength){
			if(preg_match($pRE['expr'], $lTextString, $lMatch, PREG_OFFSET_CAPTURE, MbOffsetToNonMbOffset($lCurrentPos, $lTextString))){
				// ~ echo '111@@@@' . $pRE['expr'];
				// ~ var_dump($lMatch);
				// ~ echo '<br/><br/>';
				$lNegativeMatchFound = false;
				convertMatchIndexes($lMatch, $lTextString);
				foreach($pNegativeREs as $lNegativeRE){
					if(preg_match($lNegativeRE['expr'], $lMatch[0][0])){
						$lCurrentPos = $lMatch[0][1] + 1;
						$lNegativeMatchFound = true;
						break;
					}
				}
				if(! $lNegativeMatchFound){

					if(checkMatchOpenAndCloseTags($lMatch, $lTextOpenPos, $lTextClosePos)){
						// Правилен match
						$lOverlapPos = (int) checkForOverlappingMatches($pCurrentMatches[$pNodeIdx], $lMatch);
						if(! $lOverlapPos){
							// Не се застъпва с някой от предходните
							array_push($lResult, $lMatch);
							$lCurrentPos = $lMatch[0][1] + mb_strlen($lMatch[0][0]);
							// Продължаваме търсенето от края на този match
						}else{
							// Продължаваме търсенето от края на застъпващия се match;
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
		// Pazime expr i replacement-а като ключове на масива за да може после
		// да заместим коректно в js
		$lPreviousResult = $pCurrentMatches[$pNodeIdx][$pRE['id']][$pRE['expr']][$pRE['replacement']];
		if(is_array($lPreviousResult)){
			// Tyi kato za nqkoi regexp mojem da  vlezem nqkolko pyti ako ima nqkolko
  			//iztochnika - mergevame predishniq rezultat za expressiona i tekushtiq
			$lNewResult = array_merge($lPreviousResult, $lResult);
		}else{
			$lNewResult = $lResult;
		}
		$pCurrentMatches[$pNodeIdx][$pRE['id']][$pRE['expr']][$pRE['replacement']] = $lNewResult;
		return $lResult;
	}

	function Display() {
// 		$this->GetData();
		echo json_encode(array('re' => $this->mRegularExpressions, 'match' => $this->GetData()));
	}

	/**
	 *
	 * @param $pNode DomNode
	 * @param $pStartPos int
	 * @param $pLength int
	 * @param $pMatchNumber int
	 * @param $pMatchPartNumber int
	 * @param $pUpMaxDepth int
	 */
	function placeSingleAutotagMarker($pNode, $pStartPos, $pLength, $pMatchNumber, $pMatchPartNumber, $pUpMaxDepth) {
		if(! $pNode)
			return true;

		$lStartNodeDetails = getAutotagMatchNodeDetails($pNode, $pStartPos, null, 0, 1);
		$lStartNode = $lStartNodeDetails[0]; // Tekstov node
		$lStartOffset = $lStartNodeDetails[1];

		$lEndNodeDetails = getAutotagMatchNodeDetails($pNode, $pLength, $lStartNode, $lStartOffset);
		$lEndNode = $lEndNodeDetails[0]; // Tekstov node
		$lEndOffset = $lEndNodeDetails[1];

		$lNodesDetails = correctAutotagNodes($pNode, $lStartNode, $lStartOffset, $lEndNode, $lEndOffset, $pUpMaxDepth);
		if(! $lNodesDetails){
			// Stanala e greshka zashtoto na teoriq php-to vryshta match-ove s obsht parent
			return true;
		}
		$lStartNode = $lNodesDetails[0];
		$lStartOffset = $lNodesDetails[1];
		$lEndNode = $lNodesDetails[2];
		$lEndOffset = $lNodesDetails[3];

		if(! $lStartNode || ! $lEndNode)
			return true;

		$lParentNode = $lStartNode->parentNode;

		if($lParentNode != $lEndNode->parentNode){
			// Stanala e greshka zashtoto na teoriq php-to vryshta
			// match-ove s obsht parent. No za vseki sluchai se podsigurqvame
			return true;
		}

		if($pMatchPartNumber == 0 && $this->mRuleId == UBIO_TAXON_RULE_ID){
			if($lStartNode->nodeType == 1 && $lStartNode->nodeName == TAXON_NODE_NAME) {
				// Ако стартовия възел е възел за таксон
				return false;
			}
			if($lEndNode->nodeType == 1 && $lEndNode->nodeName == TAXON_NODE_NAME){
				//Ако крайния възел е възел за таксон
				return false;
			}
			$lTempParent = $lParentNode;
			while($lTempParent){ // Ако някой от родителите е възел за таксон
				if($lTempParent->nodeType == 1 && strtolower($lTempParent->nodeName) == strtolower(TAXON_NODE_NAME))
					return false;
				$lTempParent = $lTempParent->parentNode;
			}
		}

		$lReplacementNode = $pNode->ownerDocument->createElement(AUTOTAG_MARKER_NODE_NAME);
		$lReplacementNode->setAttribute(AUTOTAG_MARKER_MATCH_NUMBER_ATTR_NAME, $pMatchNumber);
		$lReplacementNode->setAttribute(AUTOTAG_MARKER_MATCH_PART_NUMBER_ATTR_NAME, $pMatchPartNumber);
		if($lStartNode != $lEndNode){
			if($lStartNode->nodeType != 3 || $lStartOffset == 0){
				// Vzimame celiq node;
				$lReplacementNode->appendChild($lStartNode->cloneNode(true));
			}else{
				$lTextContent = $lStartNode->textContent;
				$lOuterText = $pNode->ownerDocument->createTextNode(js_slice($lTextContent, 0, $lStartOffset));
				$lInnerText = $pNode->ownerDocument->createTextNode(js_slice($lTextContent, $lStartOffset));
				$lParentNode->insertBefore($lOuterText, $lStartNode);
				$lReplacementNode->appendChild($lInnerText);
			}
			$lCurrentNode = $lStartNode->nextSibling;
			while($lCurrentNode != $lEndNode){
				$lTempNode = $lCurrentNode;
				$lCurrentNode = $lCurrentNode->nextSibling;
				$lReplacementNode->appendChild($lTempNode->cloneNode(true));
				$lParentNode->removeChild($lTempNode);
			}
			if($lEndNode->nodeType != 3 || $lEndOffset == mb_strlen($lEndNode->textContent)){
				// Dobavqme celiq node
				$lReplacementNode->appendChild($lEndNode->cloneNode(true));
				$lParentNode->removeChild($lEndNode);
			}else{
				$lTextContent = $lEndNode->textContent;
				$lInnerText = $pNode->ownerDocument->createTextNode(js_slice($lTextContent, 0, $lEndOffset));
				$lOuterText = $pNode->ownerDocument->createTextNode(js_slice($lTextContent, $lEndOffset));
				$lParentNode->replaceChild($lOuterText, $lEndNode);
				$lReplacementNode->appendChild($lInnerText);
			}

		}else{
			// Cql node ili tekstov node ot chasti
			if($lStartNode->nodeType == 3){
				// Razdelqme tekstoviq node na chasti
				$lTextContent = $lStartNode->textContent;
				$lInnerText = $pNode->ownerDocument->createTextNode(js_slice($lTextContent, $lStartOffset, $lEndOffset));
				$lReplacementNode->appendChild($lInnerText);
				if($lStartOffset > 0){
					// Dobavqme startova chast izvyn node-a
					$lOuterText = $pNode->ownerDocument->createTextNode(js_slice($lTextContent, 0, $lStartOffset));
					$lParentNode->insertBefore($lOuterText, $lStartNode);
				}
				if($lEndOffset < mb_strlen($lEndNode->textContent)){
					// Dobavqme kraina chast izvyn node-a
					$lOuterText = $pNode->ownerDocument->createTextNode(js_slice($lTextContent, $lEndOffset));
					$lNextSibling = $lStartNode->nextSibling;
					if($lNextSibling){
						$lParentNode->insertBefore($lOuterText, $lNextSibling);
					}else{
						$lParentNode->appendChild($lOuterText);
					}
				}
			}else{
				// Slagame celiq node v konteinera
				$lReplacementNode->appendChild($lStartNode->cloneNode(true));
			}
		}
		$lParentNode->replaceChild($lReplacementNode, $lStartNode);
		return true;
	}

	function placeAutotagMarkers() {
		$lPreviousNodeIdx = 0;
		$lCurrentNode = false;
		$lCurrentNodeCustomText = '';
		$lCurrentFormattingTags = array();
		$lCurrentPattern = '';
		$lXPath = $this->mXPath;
		for($i = 0; $i < $this->matchNumberOfResults; ++ $i){
			if(in_array($i, $this->ignoredMatches))
				continue;
			$lRecalculateTextContent = false;
			$lMatch = $this->flatMatches[$i][0];
			$lNodeIdx = $this->flatMatches[$i][1];
			$lReId = $this->flatMatches[$i][2];
			$lPattern = $this->flatMatches[$i][3];
			if($lNodeIdx != $lPreviousNodeIdx){
				$lPreviousNodeIdx = $lNodeIdx;
				$lCurrentNode = $lXPath->query('//*[@' . ETA_ATTRIBUTE_NAME . '="' . $lNodeIdx . '"]')->item(0);
				// var_dump($lCurrentNode->length);
				// var_dump($lNodeIdx);
				// var_dump($this->mDOM->saveXML());
				// exit;
				$lRecalculateTextContent = true;
			}

			if($lCurrentPattern != $lPattern){
				$lCurrentPattern = $lPattern;
				$lCurrentFormattingTags = getFormatTagsInRe($lCurrentPattern);
				$lRecalculateTextContent = true;
			}

			if($lRecalculateTextContent){
				// var_dump($lCurrentNode);
				$lCurrentNodeCustomText = getTextFromNode($lCurrentFormattingTags, $lCurrentNode);
				$lCurrentNodeCustomText = $lCurrentNodeCustomText['text'];
			}

			if(! $lCurrentNode)
				continue;
			for($lMatchPart = 0; $lMatchPart < count($lMatch); ++ $lMatchPart){
				if(! mb_strlen($lMatch[$lMatchPart][0]))
					continue;
				$lRealStartPos = calculateRealMatchLength(mb_substr($lCurrentNodeCustomText, 0, $lMatch[$lMatchPart][1]), $lCurrentFormattingTags);
				$lTextLength = calculateRealMatchLength($lMatch[$lMatchPart][0], $lCurrentFormattingTags);
				if(! $this->placeSingleAutotagMarker($lCurrentNode, $lRealStartPos, $lTextLength, $i, $lMatchPart, $this->autotagRegularExpresions[$lReId]['groupsupdepth'][$lMatchPart]) && $lMatchPart == 0){
					/**
					 * Ако слагаме маркера на целия мач и сме в аутотаг правило
					 * за убио, и частта от мач-а е във възел tp:taxon-name,
					 * махаме мач-а от масива с мач-ове, намаляваме броя на
					 * масива с мачове с 1,
					 * намаляваме номера на текущия обработван мач за да можем
					 * да обработим следващия (номера му ще се смали с 1 и той
					 * ще стане равен на i)
					 * и излизаме от цикъла за слагане на маркерите на
					 * подмачовете на мач-а, който сме изтрили;
					 */
					// $this->fixAutotagIgnoredMatches($i);//Оправяме
					// игнорираните мачове
					array_splice($this->flatMatches, $i, 1); // Махаме мач-а
					-- $this->matchNumberOfResults; // Намаляваме броя на мачовете
					-- $i; // Намаляваме индекса на текущия мач
					break; // Спираме маркирането на подмачовете на вече изтрития мач
				}
			}
		}
	}

	function fixAutotagIgnoredMatches($pIndexRemoved) {
		$this->ignoredMatches = remove_array_value($this->ignoredMatches, pIndexRemoved);
		for($i = 0; i < count($this->ignoredMatches); ++ $i){
			if($this->ignoredMatches[$i] < $pIndexRemoved)
				continue;
			$this->ignoredMatches[$i] = $this->ignoredMatches[$i] - 1;
		}
	}

	function parseAutoMatch($pMatchIdx) {
		if($pMatchIdx >= $this->matchNumberOfResults){
			// Обработили сме всички мачове
			return true;
		}else{
			// ~ var lTemplate =
			// this.autotagRegularExpresions[this.flatMatches[pMatchIdx][2]]['replacement'];
			$lTemplate = $this->flatMatches[$pMatchIdx][4];
			$lTemplate = '<root ' . NAMESPACE_DECLARATIONS . '>' . $lTemplate . '</root>';
			// Slagame root-a za da moje da slagame po nqkolko node-a edin sled drug primerno <taxon>a</taxon><taxon>b</taxon>
			$lTemplateDom = new DOMDocument('1.0', 'utf-8');

			if($lTemplateDom->loadXML($lTemplate)){
				$lTemplateXPath = new DOMXPath($lTemplateDom);
				$lMatch = $this->flatMatches[$pMatchIdx][0];
				for($i = 1; $i < count($lMatch); ++ $i){
					// Replacevame vsichki dolari, koito sa v atributi
					$lNodesWithAttributes = $lTemplateXPath->query('//*[@*=\'$' + $i + '\']');
					for($k = 0; $k < $lNodesWithAttributes->length; ++ $k){
						$lNode = $lNodesWithAttributes->item($k);
						for($j = 0; $j < $lNode->attributes->length; ++ $j){
							$lAttribute = $lNode->attributes->item($j);
							if($lAttribute->nodeValue == ('$' + $i)){
								$lNode->setAttribute($lAttribute->nodeName, $lMatch[i][0]);
							}
						}
					}
				}
				// To DO - parsevame template-a
				$lCurrentTextNode = getFirstTextNodeChild($lTemplateDom);
				$lMoveToFirstChildIfExists = false;
				while($lCurrentTextNode){
// 					var_dump($lCurrentTextNode);
					$lTextContent = $lCurrentTextNode->textContent;
					preg_match('/\$([\d]+)(?=$|[\D])/m', $lTextContent, $lMatchResult, PREG_OFFSET_CAPTURE);
					while($lMatchResult){
						$lMatchPart = $lMatchResult[1][0];
						$lMatchStartIdx = NonMbOffsetToMbOffset($lMatchResult[0][1], $lTextContent);
						if($lMatchStartIdx > 0){
							$lStartTextContent = mb_substr($lTextContent, 0, $lMatchStartIdx);
						}else{
							$lStartTextContent = '';
						}
						$lEndTextContent = mb_substr($lTextContent, $lMatchStartIdx + mb_strlen($lMatchResult[0][0]));
						// $lEndTextContent = RegExp.rightContext;
						if($lMatchPart > 0 && $lMatchPart < count($lMatch)){
							// Trqbva da replace-vame
							$lReplaceNode = $this->mXPath->query('//' . AUTOTAG_MARKER_NODE_NAME . '[@' . AUTOTAG_MARKER_MATCH_NUMBER_ATTR_NAME . '=' . $pMatchIdx . ']' . '[@' . AUTOTAG_MARKER_MATCH_PART_NUMBER_ATTR_NAME . '=' . $lMatchPart . ']')->item(0);
							$lReplaceClone = null;
							if($lReplaceNode)
								$lReplaceClone = $lTemplateDom->importNode($lReplaceNode->cloneNode(true), 1);
							if($lStartTextContent)
								$lCurrentTextNode->parentNode->insertBefore($lTemplateDom->createTextNode($lStartTextContent), $lCurrentTextNode);
							if(! $lEndTextContent){
								// Slagame tekushtiq node da byde tekushto insertnatiq i produljavame natam
								if($lReplaceNode){
									$lCurrentTextNode->parentNode->replaceChild($lReplaceClone, $lCurrentTextNode);
									$lCurrentTextNode = $lReplaceClone;
								}else{ // chastta ot match-a e prazna - samo mahame
								       // dolara
									$lTempNode = $lCurrentTextNode;
									$lCurrentTextNode = getPreviousTextNode($lCurrentTextNode);
									$lMoveToFirstChildIfExists = true;
									$lTempNode->parentNode->removeChild($lTempNode);
								}
								break;
							}else{
								if($lReplaceNode){
									// chastta ot match-a e prazna -samo mahame dolara
									$lCurrentTextNode->parentNode->insertBefore($lReplaceClone, $lCurrentTextNode);
								}

								$lAfterPart = $lTemplateDom->createTextNode($lEndTextContent);
								$lCurrentTextNode->parentNode->replaceChild($lAfterPart, $lCurrentTextNode);
								$lCurrentTextNode = $lAfterPart;
								// Prodyljavame tyrseneto ot kraq na replacement-a
							}
						}else{ // Prodyljavame tyrseneto sled kraq na match-a
							if(! lEndTextContent){ // Otivame na sledvashtiq node
								break;
							}
							$lStartTextContent = $lStartTextContent + $lMatchResult[0];
							$lCurrentTextNode->parentNode->insertBefore($lTemplateDom->createTextNode(lStartTextContent), $lCurrentTextNode);
							$lCurrentTextNode = $lCurrentTextNode->parentNode->replaceChild($lTemplateDom->createTextNode($lEndTextContent), $lCurrentTextNode);

						}
						$lTextContent = $lEndTextContent;
						preg_match('/\$([\d]+)(?=$|[\D])/m', $lTextContent, $lMatchResult, PREG_OFFSET_CAPTURE);
					}
					if($lCurrentTextNode){
						$lCurrentTextNode = getNextTextNode($lCurrentTextNode);
					}else{
						if($lMoveToFirstChildIfExists)
							$lCurrentTextNode = getFirstTextNodeChild($lTemplateDom);
					}
					$lMoveToFirstChildIfExists = false;
				}

				$lReplacementXml = getReplacementXml($lTemplateDom, 1);
// 				var_dump($lReplacementXml);
				$this->autotagReplacements[$pMatchIdx] = $lReplacementXml;

				// Smenqme v kloniraniq xml match-a s parsenatiq template
				// ~ var lMatchNode = this.autotagCloneXml.selectSingleNode('//'
			// + gAutotagMarkerNodeName + '[@' +
			// gAutotagMarkerMatchNumberAttributeName + '=\'' + pMatchIdx +
			// '\']' + '[@' + gAutotagMarkerMatchPartNumberAttributeName +
			// '=\'0\']');
				// ~ if( lMatchNode ){//Za vseki sluchai
				// ~ var lMatchNodeParent = lMatchNode.parentNode;
				// ~ lMatchNodeParent.replaceChild(lTemplateDom.childNodes[0],
			// lMatchNode);
				// ~ }

			}else{
				echo ('Template for match#' . $pMatchIdx . ' is not a valid XML! Continuing with next match!');
			}
			$this->parseAutoMatch($pMatchIdx + 1);
		}

	}

	function replaceAutotagMatch($pMatchIdx) {
		while(in_array($pMatchIdx, $this->ignoredMatches))
			$pMatchIdx ++;
		if($pMatchIdx >= $this->matchNumberOfResults){
			// Заменили сме всички тагове
// 			echo $this->mDOM->saveXML();
			return true;
		}
		$lTemplateDom = new DOMDocument('1.0', 'utf-8');
		$lReplacementXml = '<root ' . NAMESPACE_DECLARATIONS . '>' . $this->autotagReplacements[$pMatchIdx] . '</root>';
		if($lTemplateDom->loadXML($lReplacementXml)){
			$lMatchNode = $this->mXPath->query('//' . AUTOTAG_MARKER_NODE_NAME . '[@' . AUTOTAG_MARKER_MATCH_NUMBER_ATTR_NAME . '=\'' . $pMatchIdx . '\']' . '[@' . AUTOTAG_MARKER_MATCH_PART_NUMBER_ATTR_NAME . '=\'0\']')->item(0);
			if($lMatchNode){ // Za vseki sluchai
				$lMatchNodeParent = $lMatchNode->parentNode;
				$lReplacementRoot = $lTemplateDom->childNodes->item(0); // Fake
				                                                        // root-a koito sme
				                                                        // slojili otgore
				for($i = 0; $i < $lReplacementRoot->childNodes->length; ++ $i){
					$lCurrentChild = $lReplacementRoot->childNodes->item($i);
					if($lCurrentChild->nodeType == 1 || $lCurrentChild->nodeType == 3){
						$lMatchNodeParent->insertBefore($this->mDOM->importNode($lCurrentChild->cloneNode(true), true), $lMatchNode);
					}
				}
				// ~ lMatchNodeParent.replaceChild(lTemplateDom.childNodes[0],
				// lMatchNode);
				$lMatchNodeParent->removeChild($lMatchNode);
			}
		}else{
			echo ('Replacement for match#' . pMatchIdx . ' is not a valid XML! Continuing with next match!');
		}
		$this->replaceAutotagMatch($pMatchIdx + 1);
	}

}

?>