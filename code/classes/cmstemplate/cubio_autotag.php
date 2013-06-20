<?php
ini_set('mbstring.internal_encoding', 'UTF-8');//Това е важно за да брои мултибайт mb_strlen

class cubio_autotag extends cautotag{
	var $m_Encodings;
	function __construct($pArticleId, $pRuleId, $pXml, $pGetArticleContent, $pPreindexXml, $pEncodings = array()){
		
		$this->mArticleId = (int)$pArticleId;
		$this->mRuleId = (int)$pRuleId;
		$this->mXml = $pXml;
		if( !$this->mArticleId ){	
			exit;
		}
		$this->m_Encodings = $pEncodings;
		if( !is_array( $this->m_Encodings) || !count($this->m_Encodings)){
			$this->m_Encodings = array('', "ISO-8859-1");
		}
		
		if((int)$pGetArticleContent){
			$this->GetArticleContent();
		}		
		$this->mPlaceXpaths = getRuleProperties($this->mRuleId, (int)PLACE_RULE_PROPERTY_TYPE);		
		$this->mRegularExpressions = null;//Ще правим правилата после - на базата на намерените таксони в текста
		$this->mSources = null;
		
		$this->mDOM = new DOMDocument("1.0");
		$this->mDOM->resolveExternals = true;
		
		if ( !count($this->mPlaceXpaths) || !$this->mDOM->loadXML($this->mXml) ){
		    exit;
		}
		
		if((int) $pPreindexXml )
			$this->PreindexXml();
		
		$this->GetExternalTaxonNames();
	}
	
	function matchText( $pRE, $pTextArray, $pNegativeREs, &$pCurrentMatches, $pNodeIdx){
		//~ var_dump($pRE['expr'], $pRE['priority']);
		//~ echo '<br/>';
		$lTextString = $pTextArray['text'];
		$lTextOpenPos = $pTextArray['openTagPos'];
		$lTextClosePos = $pTextArray['closeTagPos'];
		
		//~ var_dump($pRE['expr'], $lTextString);
							//~ echo '<br/>';echo '<br/>';echo '<br/>';
		
		$lCurrentPos = 0;
		$lResult = array();
		$lTextLength = mb_strlen( $lTextString );
		while( $lCurrentPos < $lTextLength ){	//Работим със strpos т.к. е по-бързо от preg_match
			$lPos = mb_strpos( $lTextString, $pRE['expr'], $lCurrentPos );
			if( $lPos !== false ){// !== zashtoto 0 == false
				$lMatch = array(
					0 => array(
						0 => $pRE['expr'],
						1 => $lPos
					),
				);	
				$lNegativeMatchFound = false;
				//Тук няма нужда да преминаваме към мб позиция, защото тя вече е в мб
				//~ convertMatchIndexes($lMatch, $lTextString);
				foreach( $pNegativeREs as $lNegativeRE ){
					if( mb_strpos( $lMatch[0][0], $lNegativeRE['expr'] ) !== false ){					
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
		if( !array_key_exists($pNodeIdx, $pCurrentMatches) || !array_key_exists($pRE['id'], $pCurrentMatches[$pNodeIdx]) || !array_key_exists($pRE['expr'], $pCurrentMatches[$pNodeIdx][$pRE['id']]) || !array_key_exists($pRE['replacement'], $pCurrentMatches[$pNodeIdx][$pRE['id']][$pRE['expr']]))
			$pCurrentMatches[$pNodeIdx][$pRE['id']][$pRE['expr']][$pRE['replacement']] = array();
		$lPreviousResult = $pCurrentMatches[$pNodeIdx][$pRE['id']][$pRE['expr']][$pRE['replacement']];
		if(is_array( $lPreviousResult )){//Tyi kato za nqkoi regexp mojem da vlezem nqkolko pyti ako ima nqkolko iztochnika - mergevame predishniq rezultat za expressiona i tekushtiq
			$lNewResult = array_merge($lPreviousResult, $lResult);
		}else{
			$lNewResult = $lResult;
		}	
		$pCurrentMatches[$pNodeIdx][$pRE['id']][$pRE['expr']][$pRE['replacement']] = $lNewResult;	
		return $lResult;	
	}
	
	function GetData(){
		$lNodesToMatch = getPlaceResult($this->mDOM, $this->mPlaceXpaths);
		
		$lMatchResult = $this->autoMatch(null, $lNodesToMatch, $this->mRegularExpressions);//Tuk nqma iztochnici
		return $lMatchResult;
	}
	/**
		Взимаме имената на таксоните от външния източник. След това от тези имена правим re-правила. След това тези re-та ще ги търсим в текста със mb_strpos за по-бързо
	*/
	function GetExternalTaxonNames(){
		$this->mRegularExpressions = array();
		$lText = $this->mDOM->documentElement->textContent;
		$lTempFile = tempnam(PATH_STORIES, 'sto');
		
		//Pravime temp file poneje service-a e bygav i ne raboti kato podavame sydyrjanieto kato tekst
		$lFileHandle = fopen($lTempFile, "w");
		fwrite($lFileHandle, $lText);
		fclose($lFileHandle);
		
		$lCURLHandle = curl_init(UBIO_FIND_URL);
		curl_setopt($lCURLHandle, CURLOPT_HEADER, 0);
		curl_setopt($lCURLHandle, CURLOPT_POST, 1);
		curl_setopt($lCURLHandle, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($lCURLHandle, CURLOPT_POSTFIELDS, array('function' => 'findIT', 'freeText' => $lText, 'url' => ADM_URL . '/tempfile.php?filename=' . basename($lTempFile)));		
		$lResultXML = curl_exec($lCURLHandle);		
		curl_close($lCURLHandle);						
		$lOriginalResultXML = $lResultXML;
		//~ unlink($lTempFile);
				
		$lDOM = new DOMDocument("1.0");
		$lDOM->resolveExternals = true;
		$lLoadResult = false;				
		foreach ($this->m_Encodings as $lEncoding){
			if( $lEncoding != '' )
				$lResultXML = iconv($lEncoding, 'UTF-8', $lOriginalResultXML);
			else{
				$lResultXML = $lOriginalResultXML;
			}
			$lLoadResult = $lDOM->loadXML($lResultXML);
			if( !$lLoadResult ){				
				$lResultXML = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', $lResultXML);
				$lLoadResult = $lDOM->loadXML($lResultXML);
			}
			if( $lLoadResult )
				break;
		}		
		if( !$lLoadResult){			
			return;
		}		
		$lXpath  = new  DOMXPath($lDOM);
		$lQuery = '/results/allNames/entity';
		$lXpathResult = $lXpath->query($lQuery);
		$lRuleId = 0;		
		for( $i = 0; $i < $lXpathResult->length; ++$i ){			
			$lNode = $lXpathResult->item($i);
			$lExpressionQuery = './nameString';
			$lExpressionResult =  $lXpath->query($lExpressionQuery, $lNode);						
			if( $lExpressionResult->length ){				
				$lExpressionText = $lExpressionResult->item(0)->textContent;
				if( preg_match('/\[(.+)\]/', $lExpressionText)){//Pravim 2 pravila - ednoto s teksta v [] drugoto - bez										
					$lExpression1 = preg_replace('/\[(.+?)\]/', '\1', $lExpressionText);
					$lExpression2 = preg_replace('/\[(.+?)\]/', '', $lExpressionText);					
					$lReplacement1 = $this->GetRuleReplacement($lNode, $lDOM, $lXpath, 1, 1);					
					$lReplacement2 = $this->GetRuleReplacement($lNode, $lDOM, $lXpath, 1, 0);					
					
					$this->mRegularExpressions[++$lRuleId] = $this->GetSingleRegularExpression($lRuleId, $lExpression1, $lReplacement1);						
					
					$this->mRegularExpressions[++$lRuleId] = $this->GetSingleRegularExpression($lRuleId, $lExpression2, $lReplacement2);
				
				}else{//Nqma [] - pravim edno pravilo					
					$lReplacement = $this->GetRuleReplacement($lNode, $lDOM, $lXpath, 0, 0);
					$this->mRegularExpressions[++$lRuleId] = $this->GetSingleRegularExpression($lRuleId, $lExpressionText, $lReplacement);										
				}
			}
		}
		//~ var_dump($this->mRegularExpressions);
		//~ exit;
		
	}

	function GetRuleReplacement(&$pRuleNode, &$pXMLDom, &$pXpath, $pHasBrackets = 0, $pRemoveBracketsContent = 0){
		$lNameQuery = './parsedName/component[@type=\'name\']';
		$lAuthorsQuery = './parsedName/component[@type=\'author\']';
		
		$lNameResult = $pXpath->query($lNameQuery, $pRuleNode);
		$lAuthorsResult = $pXpath->query($lAuthorsQuery, $pRuleNode);
		$lReplacement = '<tp:taxon-name>';		
		for( $j = 0; $j < $lNameResult->length; ++$j){
			$lCurrentComponentNode = $lNameResult->item($j);
			$lCurrentComponentText = $this->GetReplacementComponentText($lCurrentComponentNode, $pHasBrackets, $pRemoveBracketsContent);			
			
			$lRank = trim($lCurrentComponentNode->getAttribute('rank'));			
			if( $lRank ){
				$lCurrentComponentText = '<tp:taxon-name-part taxon-name-part-type="' . $lRank . '">' . $lCurrentComponentText . '</tp:taxon-name-part>';
			}
			if( $j != 0 )
				$lReplacement = $lReplacement . ' ';
			$lReplacement .= $lCurrentComponentText;
		}		
		$lReplacement .= '</tp:taxon-name>';		
		if( $lAuthorsResult->length ){
			$lCurrentAuthorText = '';
			for( $j = 0; $j < $lAuthorsResult->length; ++$j){
				$lCurrentComponentNode = $lAuthorsResult->item($j);
				$lCurrentComponentText = $this->GetReplacementComponentText($lCurrentComponentNode, $pHasBrackets, $pRemoveBracketsContent);				
				if( $j != 0 )
					$lCurrentAuthorText = $lCurrentAuthorText . ' ';
				$lCurrentAuthorText .= $lCurrentComponentText;				
			}
			$lReplacement .= '<tp:taxon-authority>' . $lCurrentAuthorText . '</tp:taxon-authority>';
		}		
		return $lReplacement;
	}
	
	function GetReplacementComponentText(&$pNode, $pHasBrackets = 0, $pRemoveBracketsContent = 0){
		$lComponentText = $pNode->textContent;
		if( $pHasBrackets ){
			if( $pRemoveBracketsContent ){
				$lComponentText = preg_replace('/\[(.+?)\]/', '', $lComponentText);
			}else{
				$lComponentText = preg_replace('/\[(.+?)\]/', '$1', $lComponentText);
			}
		}
		return htmlspecialchars($lComponentText);
	}
	
	function GetSingleRegularExpression($pRuleId, $pExpression, $pReplacement){
		return array(
			'id' => $pRuleId,
			'type' => (int) RE_POSITIVE_TYPE,
			'expression' => $pExpression,
			'replacement' => $pReplacement,
			'priority' => 1 / (mb_strlen($pExpression)),//Pravim go taka che pyrvo da se matchnat dylgite taxoni
			'groupsupdepth' => array(0),
			'name' => $pExpression,
		);
	}	
}