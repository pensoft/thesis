<?php
require_once PATH_CLASSES . 'diff.php';

/**
 * Here we will recalculate the positions of the specified comments in the specified text
 * @param unknown_type $pPreviousValue
 * @param unknown_type $pCurrentValue
 * @param unknown_type $pComments - an array containing the comments. The format here is
 * 	array(
 *		comment_id => array(
 *			previous_start_offset => val,
 *			previous_end_offset => val,
 *			position_fix_type => val
 *		)
 *	)
 *	previous_start_offset, previous_end_offset => the offset position of the comment in the field (it
 * 		is possible only one of previous_start_offset/previous_end_offset to be meaningful - i.e. if the
 * 		comment only begins/ends in the specified field.
 * position_fix_type => which of the positions we will recalculate (start/end/both) - a bitmask of COMMENTS_FIX_TYPE_START_POS and COMMENTS_FIX_TYPE_END_POS
 *
 * Returns an array with the comments in the input format with added 1/2 keys (new_start_offset/new_end_offset) in accordance
 * to the position fix type
 */
function RecalculateCommentsPositions($pOriginalTxt, $pModifiedTxt, $pComments){
// 	var_dump($pOriginalTxt, $pModifiedTxt);
// 	return array();
	$lPatch = GetPatch($pOriginalTxt, $pModifiedTxt, array(), true, DIFF_CHAR_BASED_TYPE, false);

// 	var_dump($lPatch);

// 	var_dump($pComments);
	$lFirstPass = true;
	$lResult = array();

	foreach ($pComments as $lCommentId => $lCommentData) {
		$pComments[$lCommentId]['new_start_offset'] = $pComments[$lCommentId]['previous_start_offset'];
		$pComments[$lCommentId]['new_end_offset'] = $pComments[$lCommentId]['previous_end_offset'];
	}

	foreach ($lPatch as $lCurrentChange){
		foreach ($pComments as $lCommentId => $lCommentData) {
			$lCommentPrefixTypes = array(
				COMMENTS_FIX_TYPE_START_POS => 'start_',
				COMMENTS_FIX_TYPE_END_POS => 'end_',
			);
			$lProcessingIsComplete = array(
				COMMENTS_FIX_TYPE_START_POS => false,
				COMMENTS_FIX_TYPE_END_POS => false,
			);
// 			var_dump($lCurrentChange);
			foreach ($lCommentPrefixTypes as $lFixType => $lPrefix) {
				if($pComments[$lCommentId]['position_fix_type'] & $lFixType){
					if($lCurrentChange['start_idx'] <= $pComments[$lCommentId]['previous_' . $lPrefix . 'offset']){
						if($lCurrentChange['change_type'] == CHANGE_INSERT_TYPE){
							//If the inserted text is before the start - move the start offset backward
							$pComments[$lCommentId]['new_' . $lPrefix . 'offset'] += mb_strlen($lCurrentChange['modified_text']);
						}else{
							if($lCurrentChange['end_idx'] < $pComments[$lCommentId]['previous_' . $lPrefix . 'offset']){
								//If the deleted text is before the start - move the start offset backward
								$pComments[$lCommentId]['new_' . $lPrefix . 'offset'] -= mb_strlen($lCurrentChange['modified_text']);
							}else{
								//The comment is in the deleted text - move the offset before the change
// 								var_dump(($pComments[$lCommentId]['previous_' . $lPrefix . 'offset']) - $lCurrentChange['start_idx']);
								$pComments[$lCommentId]['new_' . $lPrefix . 'offset'] -= ($pComments[$lCommentId]['previous_' . $lPrefix . 'offset'] - $lCurrentChange['start_idx']);
							}
						}
					}else{
						$lProcessingIsComplete[$lFixType] = true;
					}
				}else{
					$lProcessingIsComplete[$lFixType] = true;
				}
			}
			if($lProcessingIsComplete[COMMENTS_FIX_TYPE_START_POS] && $lProcessingIsComplete[COMMENTS_FIX_TYPE_END_POS]){
				/*
				 * This comment is ready - we can place it in the result array and remove it from the pComments array
				* in order to fasten the processing of the other comments				 *
				*/
				$lResult[$lCommentId] = $pComments[$lCommentId];
				unset($pComments[$lCommentId]);
			}
		}
	}
	/*
	 * If we have processed all the changes and some comments have not been marked as
	* ready - add them to the result array. They are ready now
	*/
// 	var_dump('R', $lResult, 'R2', $pComments);
	if(count($pComments)){
		foreach ($pComments as $lCommentId => $lCommentData){
			$lResult[$lCommentId] = $lCommentData;
		}
		//Do not use array merge because it loses the key values
// 		$lResult = array_merge($lResult, $pComments);
	}
// 	var_dump('R3', $lResult);
	return $lResult;
}

/**
 * Here we will return the modified positions of the comments
 * of the field based on its previous and new value. We will first try to
 * extract the positions of the comments from the new value and we will recalculate
 * the positions of the remaining comments (if any marker has been deleted) with diff
 * @param unknown_type $pPreviousValue
 * @param unknown_type $pNewValue
 * @param unknown_type $pCommentsData
 */
function GetModifiedCommentPositions($pPreviousValue, $pNewValue, $pCommentsData){
	$lModifiedComments = array();
	if(count($pCommentsData)){
		$lInlineComments = array();
		$lInlineComments = GetCommentNodesPosition($pNewValue);
		// 					var_dump($lFieldData['base_value'], $lInlineComments);
		$lCommentsToRecalculateWithDiff = $pCommentsData;
		foreach ($lInlineComments as $lCommentId => $lCommentData) {
			if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
				if($lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] & COMMENTS_FIX_TYPE_START_POS){
					$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] -= COMMENTS_FIX_TYPE_START_POS;
				}
			}
			if($lCommentData['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
				if($lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] & COMMENTS_FIX_TYPE_END_POS){
					$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type'] -= COMMENTS_FIX_TYPE_END_POS;
				}
			}
			if(!$lCommentsToRecalculateWithDiff[$lCommentId]['position_fix_type']){
				//This comment's position has already been commented by the comment node
				//do not recalculate it with diff
				unset($lCommentsToRecalculateWithDiff[$lCommentId]);
			}
		}

		$lModifiedComments = $lInlineComments;
		if(count($lCommentsToRecalculateWithDiff)){
			$lDiffModifiedComments = RecalculateCommentsPositions($pPreviousValue, $pNewValue, $lCommentsToRecalculateWithDiff);
			$lModifiedComments = $lModifiedComments + $lDiffModifiedComments;
		}
	}
	return $lModifiedComments;
}

/**
 * Retrieve the positions of the comment start and end nodes in the passed field
 * @param unknown_type $pFieldValue
 *
 * Returns an array with comments with the format similar to
 * @see RecalculateCommentsPositions
 * but without the previous_start_offset/previous_end_offset keys
 */
function GetCommentNodesPosition($pFieldValue){
	$pFieldValue = CustomHtmlEntitiesDecode($pFieldValue);
	$lResult = array();
	$lDom = new DOMDocument('1.0', 'utf-8');
// 		error_reporting(-1);
	// 	var_dump($pOriginalContent);
	$lFakeRootNode = null;
	if(! $lDom->loadXML($pFieldValue)){
		//Try to append a fake root
		$lFakeRootNode = $lDom->appendChild($lDom->createElement(FAKE_ROOT_NODE_NAME));
		$lFragment = $lDom->createDocumentFragment();
		if($lFragment->appendXML($pFieldValue)){
			$lFakeRootNode->appendChild($lFragment);
		}else{
			return $lResult;
		}
	}
	$lXPath = new DOMXPath($lDom);
	$lQuery = '//' . COMMENT_START_NODE_NAME . '|//' . COMMENT_END_NODE_NAME;
	$lNodes = $lXPath->query($lQuery);
	for($i = 0; $i < $lNodes->length; ++$i){
		$lCurrentNode = $lNodes->item($i);
		$lCommentId = $lCurrentNode->getAttribute(COMMENT_ID_ATTRIBUTE_NAME);
		if(!$lCommentId){
			continue;
		}
		$lOffset = GetNodeTextOffset($lCurrentNode);
		if(!array_key_exists($lCommentId, $lResult)){
			$lResult[$lCommentId] = array(
				'position_fix_type' => 0,
			);
		}
		if($lCurrentNode->nodeName == COMMENT_START_NODE_NAME){//Start
			if(!(int)$lResult[$lCommentId]['position_fix_type']){
				$lResult[$lCommentId]['position_fix_type'] = COMMENTS_FIX_TYPE_START_POS;
			}else{
				$lResult[$lCommentId]['position_fix_type'] = $lResult[$lCommentId]['position_fix_type'] | COMMENTS_FIX_TYPE_START_POS;
			}
			$lResult[$lCommentId]['new_start_offset'] = $lOffset;
		}else{//End
			if(!(int)$lResult[$lCommentId]['position_fix_type']){
				$lResult[$lCommentId]['position_fix_type'] = COMMENTS_FIX_TYPE_END_POS;
			}else{
				$lResult[$lCommentId]['position_fix_type'] = $lResult[$lCommentId]['position_fix_type'] | COMMENTS_FIX_TYPE_END_POS;
			}
			$lResult[$lCommentId]['new_end_offset'] = $lOffset;
		}
	}

	return $lResult;
}

/**
 * Removes the comment start and end nodes from the passed field value
 * @param unknown_type $pFieldValue
 */
function RemoveFieldCommentNodes($pFieldValue){
	$pFieldValue = CustomHtmlEntitiesDecode($pFieldValue);
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lFakeRootNode = null;
// 	error_reporting(-1);
	if(! $lDom->loadXML($pFieldValue)){
		//Try to append a fake root
		$lFakeRootNode = $lDom->appendChild($lDom->createElement(FAKE_ROOT_NODE_NAME));
		$lFragment = $lDom->createDocumentFragment();
		if($lFragment->appendXML($pFieldValue)){
			$lFakeRootNode->appendChild($lFragment);
		}else{
			return $pFieldValue;
		}
	}
	$lDom->encoding = 'utf-8';
	RemoveFieldNodeCommentNodes($lDom->documentElement);

	if($lFakeRootNode){
		return getFieldInnerXML($lFakeRootNode, LIBXML_NOEMPTYTAG);
	}else{
		return $lDom->saveXML($lDom->documentElement, LIBXML_NOEMPTYTAG);
	}
}
/**
 *	A helper function for RemoveFieldCommentNodes which removes the comment
 *	start/end nodes from the passed node
 * @param DomNode $pFieldNode
 */
function RemoveFieldNodeCommentNodes($pFieldNode){
// 	var_dump($pFieldNode);
	$lXPath = new DOMXPath($pFieldNode->ownerDocument);
	$lQuery = './/' . COMMENT_START_NODE_NAME . '|//' . COMMENT_END_NODE_NAME;
	$lNodes = $lXPath->query($lQuery, $pFieldNode);
	for($i = 0; $i < $lNodes->length; ++$i){
		$lCurrentNode = $lNodes->item($i);
		$lCurrentNode->parentNode->removeChild($lCurrentNode);
	}
}

/**
 * @formatter:off
 * Inserts the comment position nodes in the field value
 * @param html $pFieldValue
 * @param unknown_type $pComments - an array with comments in the following format
 * 		array(
 * 			'comment_id' => array(
 * 				start_offset => val,
 * 				end_offset => val,
 * 				comment_pos_type => val
 * 			),
 * 		)
 *
 *	start_offset, end_offset => may not be setted if the specific comment does not start in the field
 *	comment_pos_type a bitmask of COMMENT_START_POS_TYPE and COMMENT_END_POS_TYPE
 * @formatter:on
 */
function InsertFieldCommentPositionNodes($pFieldValue, $pComments){
	$pFieldValue = CustomHtmlEntitiesDecode($pFieldValue);
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lFakeRootNode = null;
	if(! $lDom->loadXML($pFieldValue)){
		//Try to append a fake root
		$lFakeRootNode = $lDom->appendChild($lDom->createElement(FAKE_ROOT_NODE_NAME));
		$lFragment = $lDom->createDocumentFragment();
		if($lFragment->appendXML($pFieldValue)){
			$lFakeRootNode->appendChild($lFragment);
		}else{
			return $pFieldValue;
		}
	}
// 	echo 'aaa';
	InsertFieldNodeCommentPositionNodes($lDom->documentElement, $pComments);

	$lRes = '';
	//Here we always provide node pointer for saveXml because we dont need the xml decl
	//Also we use the LIBXML_NOEMPTYTAG option because CKEditor doesn't handle well emptytags
	if($lFakeRootNode){
		$lRes = getFieldInnerXML($lFakeRootNode, LIBXML_NOEMPTYTAG);
// 		$lRes = $lDom->saveXML($lFakeRootNode, LIBXML_NOEMPTYTAG);
	}else{
		$lRes = $lDom->saveXML($lDom->documentElement, LIBXML_NOEMPTYTAG);
	}
// 	var_dump($lRes);
	return $lRes;
}

/**
 * Insert the comment position nodes in the specified field node
 * @param DOMNode $pFieldNode
 * @param array $pComments the format is the same as in InsertFieldCommentPositionNodes
 * @see InsertFieldCommentPositionNodes
 */
function InsertFieldNodeCommentPositionNodes($pFieldNode, $pComments){

	$lDom = $pFieldNode->ownerDocument;
	$lSortedComments = array();
	//First sort the comments according to their positions in order not to
	//process the value multiple times
	foreach ($pComments as $lCommentId => $lCommentData){
		$lPositions = array(
			COMMENT_START_POS_TYPE => 'start_',
			COMMENT_END_POS_TYPE => 'end_',
		);
// 		var_dump($lCommentData);
		foreach ($lPositions as $lPositionType => $lPrefix) {
			if($lCommentData['comment_pos_type'] & $lPositionType){
				$lPos = $lCommentData[$lPrefix . 'offset'];
				if(!array_key_exists($lPos, $lSortedComments)){
					$lSortedComments[$lPos] = array();
				}
				if(!array_key_exists($lCommentId, $lSortedComments[$lPos])){
					$lSortedComments[$lPos][$lCommentId] = array(
						'type' => $lPositionType
					);
				}else{
					$lSortedComments[$lPos][$lCommentId]['type'] = $lSortedComments[$lPos][$lCommentId]['type'] | $lPositionType;
				}
			}
		}
	}
	ksort($lSortedComments, SORT_NUMERIC);
// 	var_dump($lSortedComments);
	$lCurrentNode = GetFirstTextNodeDescendant($pFieldNode);
// 	var_dump($pFieldNode->ownerDocument->saveXML($pFieldNode));
	$lPreviousNode = null;
	$lCurrentPos = 0;
	foreach ($lSortedComments as $lPos => $lComments){
// 		var_dump('pos' . $lPos);
		while($lCurrentNode && $lCurrentPos + mb_strlen($lCurrentNode->nodeValue) < $lPos){
			$lCurrentPos += mb_strlen($lCurrentNode->nodeValue);
			$lPreviousNode = $lCurrentNode;
// 			var_dump($lCurrentNode->nodeValue, mb_strlen($lCurrentNode->nodeValue));
			$lCurrentNode = GetNextTextNode($lCurrentNode);
		}
// 		var_dump($lCurrentPos);

		if($lCurrentPos < $lPos && $lCurrentNode){//Split the current text node - in 2 parts one before and 1 after the comments
			$lStartCommentInnerPosition = $lPos - $lCurrentPos;
			$lBeforePart = mb_substr($lCurrentNode->nodeValue, 0, $lStartCommentInnerPosition);
			$lAfterPart =  mb_substr($lCurrentNode->nodeValue, $lStartCommentInnerPosition);
// 			trigger_error('Text' . $lCurrentNode->nodeValue,E_USER_NOTICE);
// 			var_dump('Before' . $lBeforePart);
// 			var_dump('After' . $lAfterPart);

			$lPreviousNode = $lCurrentNode->parentNode->insertBefore($lDom->createTextNode($lBeforePart), $lCurrentNode);
			$lAfterNode = $lDom->createTextNode($lAfterPart);
			$lCurrentNode->parentNode->replaceChild($lAfterNode, $lCurrentNode);
			$lCurrentNode = $lAfterNode;
			$lCurrentPos = $lPos;
		}

		foreach ($lComments as $lCommentId => $lCommentData){
// 			var_dump($lPos, $lComments);
			if($lCurrentNode || $lPreviousNode){
				$lPositions = array(
					COMMENT_START_POS_TYPE => array('prefix' => 'start_', 'node_name' => COMMENT_START_NODE_NAME),
					COMMENT_END_POS_TYPE => array('prefix' => 'end_', 'node_name' => COMMENT_END_NODE_NAME),
				);
// 				continue;
				foreach ($lPositions as $lPositionType => $lData) {
// 					var_dump($lCommentData, $lPositionType);
					if($lCommentData['type'] & $lPositionType){
// 						var_dump('t' . $lCommentData['type'] . '_' . $lPositionType . '_' . $lData['node_name']);

						$lCommentPositionNode = $lDom->createElement($lData['node_name']);
						$lCommentPositionNode->setAttribute(COMMENT_ID_ATTRIBUTE_NAME, $lCommentId);
						if($lCurrentNode){
							$lCurrentNode->parentNode->insertBefore($lCommentPositionNode, $lCurrentNode);
						}else{//No more text nodes - append it in the parent
							$lPreviousNode->parentNode->appendChild($lCommentPositionNode);
						}
					}
				}
			}
		}
// 		var_dump($lCurrentNode->ownerDocument->saveXML($pFieldNode));

	}
// 	var_dump($pFieldNode->ownerDocument->saveXML($pFieldNode));
}

/**
 * @formatter:off
 * Insert the comment position nodes in the html of the document(usually after preview)
 * @param html $pHtml
 * @param array $pComments an array with comments in the following format
 * 		array(
 * 			instance_id => array(
 * 				non_field_comments => array(//Comments which are in the beginning/end of the instance - not in a specific field
 *	 				comment_id => array(
 * 						start_offset => val,
 *	 					end_offset => val,
 * 						comment_pos_type => val
 * 					),
 * 				),
 * 				field_comments => array(
 * 					field_id => array(
 * 						comment_id => array(
 * 							start_offset => val,
 *	 						end_offset => val,
 * 							comment_pos_type => val
 * 						),
 * 					),
 * 				),
 * 			),
 * 		)
 * @formatter:on
 */
function InsertDocumentCommentPositionNodes($pXml, $pComments){
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lFakeRootNode = null;
// 	var_dump($pXml);
// 	error_reporting(-1);
	if(! $lDom->loadXML($pXml)){
		//Try to append a fake root
		$lFakeRootNode = $lDom->appendChild($lDom->createElement(FAKE_ROOT_NODE_NAME));
		$lFragment = $lDom->createDocumentFragment();
		if($lFragment->appendXML($pXml)){
			$lFakeRootNode->appendChild($lFragment);
		}else{
			return $pXml;
		}
	}
	$lXPath = new DOMXPath($lDom);

	$lPositions = array(
		COMMENT_START_POS_TYPE => array('prefix' => 'start_', 'node_name' => COMMENT_START_NODE_NAME),
		COMMENT_END_POS_TYPE => array('prefix' => 'end_', 'node_name' => COMMENT_END_NODE_NAME),
	);

	foreach($pComments as $lInstanceId => $lInstanceData){
		$lInstanceQuery = '//*[@instance_id="' . $lInstanceId . '"]';
		$lInstanceNodes = $lXPath->query($lInstanceQuery);
		if(!$lInstanceNodes->length){
			continue;
		}

		$lInstanceNode = $lInstanceNodes->item(0);
		foreach ($lInstanceData['non_field_comments'] as $lCommentId => $lCommentData){
			foreach ($lPositions as $lPositionType => $lData) {
				if($lCommentData['comment_pos_type'] & $lPositionType){
					$lPos = $lCommentData[$lData['prefix'] . 'offset'];
					$lCommentPositionNode = $lDom->createElement($lData['node_name']);
					$lCommentPositionNode->setAttribute(COMMENT_ID_ATTRIBUTE_NAME, $lCommentId);
					if($lPos == 0){//Start of instance
						if($lInstanceNode->firstChild){
							$lInstanceNode->insertBefore($lCommentPositionNode, $lInstanceNode->firstChild);
						}else{
							$lInstanceNode->appendChild($lCommentPositionNode);
						}
					}elseif($lPos == -1){//End of instance
						$lInstanceNode->appendChild($lCommentPositionNode);
					}
				}
			}
		}
		foreach ($lInstanceData['field_comments'] as $lFieldId => $lFieldComments){
// 			var_dump($lFieldId);
			$lFieldNodeQuery = './fields/*[@id="' . $lFieldId . '"]/value';
			$lFieldNodes = $lXPath->query($lFieldNodeQuery, $lInstanceNode);
			if(!$lFieldNodes->length){
				continue;
			}
// 			var_dump($lFieldComments);

			$lFieldNode = $lFieldNodes->item(0);
			InsertFieldNodeCommentPositionNodes($lFieldNode, $lFieldComments);
		}
	}
	$lDom->encoding = DEFAULT_XML_ENCODING;
	if($lFakeRootNode){
		return getFieldInnerXML($lFakeRootNode);
// 		return $lDom->saveXML($lFakeRootNode);
	}else{
		return $lDom->saveXML();
	}
}

/**
 * Calculates the position of the comment according to the real field value
 * not to the html one (the html one is the field value taken from the preview with the
 * comment start/end node positioned in it)
 * @param unknown_type $pFieldHtml
 * @param unknown_type $pFieldRealValue
 * @param unknown_type $pCommentId
 * @param unknown_type $pIsStart
 */
function CalculateCommentRealPosition($pFieldHtml, $pFieldRealValue, $pCommentId, $pIsStart = true){
	//Replace the comment start/end node
	if(COMMENT_POS_MARKER_FAKE_TEXT == '' || !$pCommentId){
		return 0;
	}
	$lDom = new DOMDocument('1.0', 'utf-8');
	$lDom->formatOutput = false;
	$lFakeRootNode = null;

	if(! $lDom->loadHTML('<?xml encoding="' . DEFAULT_XML_ENCODING . '">' . $pFieldHtml)){//A hack to load utf8 symbols correctly
		//Try to append a fake root
		$lFakeRootNode = $lDom->appendChild($lDom->createElement(FAKE_ROOT_NODE_NAME));
		$lFragment = $lDom->createDocumentFragment();
		if($lFragment->appendXML($pFieldHtml)){
			$lFakeRootNode->appendChild($lFragment);
		}else{
			return 0;
		}
	}else{
		foreach ($lDom->childNodes as $lItem){
			if($lItem->nodeType == XML_PI_NODE){
				$lDom->removeChild($lItem);
			}
		}
	}


	$lXPath = new DOMXPath($lDom);
	$lCommentNodeQuery = '//' .($pIsStart ? COMMENT_START_NODE_NAME : COMMENT_END_NODE_NAME) . '[@' . COMMENT_ID_ATTRIBUTE_NAME . '="' . $pCommentId . '"]';
	$lCommentNodes = $lXPath->query($lCommentNodeQuery);
	if(!$lCommentNodes->length){
		return 0;
	}
	$lCommentNode = $lCommentNodes->item(0);
	/*
	 * Now we will insert a new fake text node before the marker
	 * After that we will perform a diff of the html and the real value
	 * and see the position of the change in which the marker is
	 */
	$lCommentNode->parentNode->insertBefore($lDom->createTextNode(COMMENT_POS_MARKER_FAKE_TEXT), $lCommentNode);

	$lFieldHtml = '';
	if($lFakeRootNode){
		$lFieldHtml = getFieldInnerXML($lFakeRootNode);
	}else{
		$lFieldHtml = $lDom->saveXML($lDom->documentElement);
	}
// 	var_dump($lFieldHtml);

// 	var_export("1|".$pFieldRealValue."|");
// 	var_export("2|".$lFieldHtml."|");
	$lPatch = GetPatch($pFieldRealValue, $lFieldHtml, array(), true, DIFF_CHAR_BASED_TYPE);
// 	var_export($lPatch);
	//$loffset=0;
	foreach ($lPatch as $lCurrentChange){
		if($lCurrentChange['change_type'] == CHANGE_INSERT_TYPE){
			if(mb_strpos($lCurrentChange['modified_text'], COMMENT_POS_MARKER_FAKE_TEXT) !== false){
				//var_export("LOFF:".$loffset);
				//return $lCurrentChange['start_idx']+$loffset;
				return $lCurrentChange['start_idx'];
			} else {
			//	$loffset-=mb_strlen($lCurrentChange['modified_text']);

			}
		}/* else if($lCurrentChange['change_type'] == CHANGE_DELETE_TYPE){
			$loffset+=mb_strlen($lCurrentChange['modified_text']);
		}*/
	}
	return 0;

}

function getFieldInnerXML($pNode, $pOptions = null){
	$lResult = '';
	foreach ($pNode->childNodes as $lChild){
		$lResult .= $pNode->ownerDocument->saveXML($lChild, $pOptions);
	}
	return $lResult;
}
?>