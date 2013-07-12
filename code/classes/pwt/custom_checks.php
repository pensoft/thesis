<?php
/**
 * Тук ще пазим функциите, които ще извършват custom check-овете.
 * Те ще поемат като 1 аргумент референция към xml възела.
 * Всяка функция ще връща като резултат масив от грешки, като всяка грешка
 * ще е със следния формат:
 * array(
 * 	instance_id => ?, ид на инстанса в който е грешката
 * 	instance_name => ?, DisplayName-а на инстанса
 * 	field_id => ?, ид на филда в който е грешката
 * 	msg => ?, Съобщение на грешката
 * 	error_type => ?, тип на грешката - напр. warning, breakable error (грешка след която не се изпълняват следващите custom check-ове) и т.н.
 * )
 */


/**
 * За всеки таксон проверяваме трийтмънтите.
 * Ако таксонът е от тип ZooKeys - New treatment трябва да има задължително 1 материал от някой от типовете (Holotype, Suntypes, Hapantotype)
 * Ако таксонът е от тип phytoKeys - New treatment трябва да има задължително 1 материал от тип Holotype
 * @param DomNode $pTreatmentNode
 */
function CustomCheckMaterialTypes($pTreatmentNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){

	define('RANK_FIELD_ID', 42);
	define('TYPE_FIELD_ID', 43);
	define('CLASSIFICATION_FIELD_ID', 384);
	define('MATERIAL_OBJECT_ID', 37);
	define('MATERIALS_OBJECT_ID', 38);
	define('MATERIAL_TYPE_FIELD_ID', 209);

	define('NEW_TAXON_TYPE', 1);
	define('SPECIES_RANK', 1);
	define('HOLOTYPE_MATERIAL_TYPE', 1);
	define('SUNTYPES_MATERIAL_TYPE', 2);
	define('HAPANTOTYPE_MATERIAL_TYPE', 3);

	$lZookeysClassifications = array(5, 8);
	$lPhytoKeysClassifications = array(6, 7, 364);

	$lResult = array();
	$lXPath = new DOMXPath($pTreatmentNode->ownerDocument);

	$lInstanceId = $lXPath->query('./@instance_id', $pTreatmentNode)->item(0)->nodeValue;
	$lInstanceName = $lXPath->query('./@display_name', $pTreatmentNode)->item(0)->nodeValue;

	$lRankNode = $lXPath->query('./fields/*[@id="' . (int) RANK_FIELD_ID . '"]/value/@value_id', $pTreatmentNode);
	$lTypeNode = $lXPath->query('./fields/*[@id="' . (int) TYPE_FIELD_ID . '"]/value/@value_id', $pTreatmentNode);
	$lClassificationRootNode = $lXPath->query('./fields/*[@id="' . (int) CLASSIFICATION_FIELD_ID . '"]/value', $pTreatmentNode);
	
	if(!$lRankNode->length || !$lTypeNode->length || !$lTypeNode->length){
		return $lResult;
	}
	$lRank = (int)$lRankNode->item(0)->nodeValue;
	$lTreatmentType = (int)$lTypeNode->item(0)->nodeValue;
	$lClassification = (int)$lClassificationRootNode->item(0)->nodeValue;

	//var_dump($lRank, $lTreatmentType, $lClassification);
	if(!$lRank || !$lTreatmentType || !$lClassification){
		return $lResult;
	}
	
	if($lRank == (int) SPECIES_RANK && $lTreatmentType == (int) NEW_TAXON_TYPE && (in_array($lClassification, $lZookeysClassifications) || in_array($lClassification, $lPhytoKeysClassifications))){

		if(in_array($lClassification, $lZookeysClassifications)){
			$lMaterialsQuery = './/*[@object_id="' . (int) MATERIAL_OBJECT_ID . '"][priority_darwincore/fields/*[@id="' . (int) MATERIAL_TYPE_FIELD_ID . '"]/value[@value_id="' . (int) HOLOTYPE_MATERIAL_TYPE . '" or @value_id="' . (int) SUNTYPES_MATERIAL_TYPE . '" or @value_id="' . (int) HAPANTOTYPE_MATERIAL_TYPE . '"]]';
		}elseif(in_array($lClassification, $lPhytoKeysClassifications)){
			$lMaterialsQuery = './/*[@object_id="' . (int) MATERIAL_OBJECT_ID . '"][priority_darwincore/fields/*[@id="' . (int) MATERIAL_TYPE_FIELD_ID . '"]/value[@value_id="' . (int) HOLOTYPE_MATERIAL_TYPE . '"]]';
		}
		
		$lMaterialNodes = $lXPath->query($lMaterialsQuery, $pTreatmentNode);
		
		$lMaterialQuery = './/*[@object_id="' . (int) MATERIAL_OBJECT_ID . '"][fields/*[@id="' . (int) MATERIAL_TYPE_FIELD_ID . '"]/value]';
		
		$lMaterialsInstanceId = $lXPath->query('.//*[@object_id=' . (int) MATERIALS_OBJECT_ID . ']/@instance_id', $pTreatmentNode);
		
		if($lMaterialNodes->length == 0){
			if(in_array($lClassification, $lZookeysClassifications)){
				$lErrorMsg = getstr('pwt.zookeysNewTaxonOneMaterialIsRequired');
			}elseif(in_array($lClassification, $lPhytoKeysClassifications)){
				$lErrorMsg = getstr('pwt.phytokeysNewTaxonOneMaterialIsRequired');
			}
			$lResult[] = array(
				'instance_id' => $lMaterialsInstanceId->item(0)->nodeValue,
				'field_id' => '',
				'msg' => $lErrorMsg,
				'instance_name' => $lInstanceName,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}

		if($lMaterialNodes->length > 1){
// 			var_dump($lClassification);
			if(in_array($lClassification, $lZookeysClassifications)){
				$lErrorMsg = getstr('pwt.zookeysNewTaxonOnlyOneMaterialIsAllowed');
			}elseif(in_array($lClassification, $lPhytoKeysClassifications)){
				$lErrorMsg = getstr('pwt.phytokeysNewTaxonOnlyOneMaterialIsAllowed');
			}
			$lResult[] = array(
				'instance_id' => $lInstanceId,
				'field_id' => '',
				'instance_name' => $lInstanceName,
				'msg' => $lErrorMsg,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}
	}

// 	var_dump($lResult);
	return $lResult;

}

/**
 * За всеки таксон проверяваме дали е попълнено полето за habitat,
 * ако таксонът не е species, new taxon с класификация Plantae
 * @param DomNode $pTreatmentNode
 */
function CustomCheckTreatmentHabitat($pTreatmentNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('RANK_FIELD_ID', 42);
	define('TYPE_FIELD_ID', 43);
	define('CLASSIFICATION_FIELD_ID', 384);
	define('HABITAT_FIELD_ID', 45);

	define('NEW_TAXON_TYPE', 1);
	define('SPECIES_RANK', 1);
	define('PLANTAE_CLASSIFICATION_TYPE', 7);


	$lResult = array();
	$lXPath = new DOMXPath($pTreatmentNode->ownerDocument);

	$lInstanceId = $lXPath->query('./@instance_id', $pTreatmentNode)->item(0)->nodeValue;
	$lInstanceName = $lXPath->query('./@display_name', $pTreatmentNode)->item(0)->nodeValue;

	$lRankNode = $lXPath->query('./fields/*[@id="' . (int) RANK_FIELD_ID . '"]/value/@value_id', $pTreatmentNode);
	$lTypeNode = $lXPath->query('./fields/*[@id="' . (int) TYPE_FIELD_ID . '"]/value/@value_id', $pTreatmentNode);
	$lHabitatNode = $lXPath->query('./fields/*[@id="' . (int) HABITAT_FIELD_ID . '"]/value/@value_id', $pTreatmentNode);
	$lClassificationRootNode = $lXPath->query('./fields/*[@id="' . (int) CLASSIFICATION_FIELD_ID . '"]/value', $pTreatmentNode);

	if(!$lRankNode->length || !$lTypeNode->length || !$lTypeNode->length){
		return $lResult;
	}
	$lRank = (int)$lRankNode->item(0)->nodeValue;
	$lTreatmentType = (int)$lTypeNode->item(0)->nodeValue;
	$lHabitat = (int)$lTypeNode->item(0)->nodeValue;
	$lClassification = (int)$lClassificationRootNode->item(0)->nodeValue;

	// 	var_dump($lRank, $lTreatmentType, $lClassification);
	if(!$lRank || !$lTreatmentType || !$lClassification){
		if(!(int)$lHabitat){
			if($pCheckMode == (int) CUSTOM_CHECK_AFTER_SAVE_MODE){
				//След save вдигаме грешката в директния парент
				$lResult[] = array(
					'instance_id' => $lInstanceId,
					'field_id' => HABITAT_FIELD_ID,
					'msg' => getstr('kfor.emptyField'),
					'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
				);
			}else{
				//След валидация - вдигаме грешката в целия материал
				$lResult[] = array(
					'instance_id' => $lInstanceId,
					'field_id' => HABITAT_FIELD_ID,
					'instance_name' => 'Treatment',
					'msg' => getstr('kfor.emptyField') . ' habitat' ,
					'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
				);
			}
		}
		return $lResult;
	}



	//Само в този случай няма грешка
	if($lRank == (int) SPECIES_RANK && $lTreatmentType == (int) NEW_TAXON_TYPE && $lClassification == PLANTAE_CLASSIFICATION_TYPE && (int)$lHabitat){
	}elseif(!(int)$lHabitat){
		if($pCheckMode == (int) CUSTOM_CHECK_AFTER_SAVE_MODE){
			//След save вдигаме грешката в директния парент
			$lResult[] = array(
				'instance_id' => $lInstanceId,
				'field_id' => HABITAT_FIELD_ID,
				'msg' => getstr('kfor.emptyField'),
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}else{
			//След валидация - вдигаме грешката в целия материал
			$lResult[] = array(
				'instance_id' => $lInstanceId,
				'field_id' => HABITAT_FIELD_ID,
				'instance_name' => 'Treatment',
				'msg' => getstr('kfor.emptyField') . ' habitat' ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}
	}

	// 	var_dump($lResult);
	return $lResult;

}

/**
 * Ще гледаме ако триитмънта е от тип phytoKeys - New treatment да слагаме not null-ове на някои полета
 * Ако сме след save - ще вдигаме грешка и за primary и за extended dc обектите
 * Ако сме след validate - ще вдигаме грешка само за extended dc обектите
 * @param unknown_type $pMaterialNode
 */
function CustomCheckPhytoKeysMaterialFieldsBase($pMaterialNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('RANK_FIELD_ID', 42);
	define('TYPE_FIELD_ID', 43);
	define('CLASSIFICATION_FIELD_ID', 384);
	define('TREATMENT_OBJECT_ID', 41);
	define('EXTENDED_DC_OBJECT_ID', 84);
	define('PRIMARY_DC_OBJECT_ID', 83);

	define('RECORD_NUM_FIELD_ID', 57);
	define('RECORDED_BY_FIELD_ID', 58);
	define('INSTITUTION_CODE_FIELD_ID', 200);
	define('COLLECTION_CODE_FIELD_ID', 201);

	$lObjectsToProcess = array(EXTENDED_DC_OBJECT_ID);
	if($pCheckMode == (int) CUSTOM_CHECK_AFTER_SAVE_MODE){
		$lObjectsToProcess = array(EXTENDED_DC_OBJECT_ID, PRIMARY_DC_OBJECT_ID);
	}

	define('NEW_TAXON_TYPE', 1);
	define('SPECIES_RANK', 1);

	$lPhytoKeysClassifications = array(6, 7, 364);

	$lResult = array();
	$lXPath = new DOMXPath($pMaterialNode->ownerDocument);

	$lMaterialId = $lXPath->query('./@instance_id', $pMaterialNode)->item(0)->nodeValue;


	if($pCheckMode == CUSTOM_CHECK_AFTER_SAVE_MODE && !in_array($lMaterialId, $_REQUEST['instance_ids'])){
		return $lResult;
	}

	$lTreatmentNode = $lXPath->query('./ancestor::*[@object_id="' . TREATMENT_OBJECT_ID . '"]', $pMaterialNode)->item(0);
	if(!$lTreatmentNode){
		return $lResult;
	}

	$lRankNode = $lXPath->query('./fields/*[@id="' . (int) RANK_FIELD_ID . '"]/value/@value_id', $lTreatmentNode);
	$lTypeNode = $lXPath->query('./fields/*[@id="' . (int) TYPE_FIELD_ID . '"]/value/@value_id', $lTreatmentNode);
	$lClassificationRootNode = $lXPath->query('./fields/*[@id="' . (int) CLASSIFICATION_FIELD_ID . '"]/value', $lTreatmentNode);

	if(!$lRankNode->length || !$lTypeNode->length || !$lTypeNode->length){
		return $lResult;
	}
	$lRank = (int)$lRankNode->item(0)->nodeValue;
	$lTreatmentType = (int)$lTypeNode->item(0)->nodeValue;
	$lClassification = (int)$lClassificationRootNode->item(0)->nodeValue;

	// 	var_dump($lRank, $lTreatmentType, $lClassification);
	if(!$lRank || !$lTreatmentType || !$lClassification){
		return $lResult;
	}
	

	if($lRank == (int) SPECIES_RANK && $lTreatmentType == (int) NEW_TAXON_TYPE && in_array($lClassification, $lPhytoKeysClassifications)){
		
		$lFieldIds = array(RECORD_NUM_FIELD_ID, RECORDED_BY_FIELD_ID, INSTITUTION_CODE_FIELD_ID, COLLECTION_CODE_FIELD_ID);

		foreach ($lObjectsToProcess as $lCurrentObjectToProcess){
			$lObjectNode = $lXPath->query('//*[@object_id="' . $lCurrentObjectToProcess . '"]', $pMaterialNode)->item(0);
			$lObjectNodeName = $lXPath->query('./@display_name', $lObjectNode)->item(0)->nodeValue;
			if(!$lObjectNode){
				continue;
			}
			$lObjectInstanceId = $lObjectNode->getAttribute('instance_id');
			//След save показваме грешките само на видимите обекти
			if($pCheckMode == CUSTOM_CHECK_AFTER_SAVE_MODE && !in_array($lObjectInstanceId, $_REQUEST['instance_ids'])){
				continue;
			}
			foreach ($lFieldIds as $lCurrentFieldId){
				$lFieldNode = $lXPath->query('.//fields/*[@id="' . (int)$lCurrentFieldId . '"]/value', $lObjectNode);

				if(!$lFieldNode->length){
					continue;
				}
				$lFieldName = $lXPath->query('./parent::*/@field_name', $lFieldNode->item(0))->item(0)->nodeValue;
				$lFieldParentInstance = $lFieldNode->item(0);
				while(!$lFieldParentInstance->getAttribute('instance_id')){
					$lFieldParentInstance = $lFieldParentInstance->parentNode;
				}
// 				$lFieldParentInstanceId = $lXPath->query('./parent::*/parent::*/parent::*/@instance_id', $lFieldNode->item(0))->item(0)->nodeValue;
				$lFieldParentInstanceId = $lFieldParentInstance->getAttribute('instance_id');
				$lFieldValue = $lFieldNode->item(0)->nodeValue;

				if(trim($lFieldValue) == ''){
// 					var_dump($lCurrentFieldId, $lFieldValue, $lFieldParentInstanceId);
					if($pCheckMode == (int) CUSTOM_CHECK_AFTER_SAVE_MODE){
						//След save вдигаме грешката в директния парент
						$lResult[] = array(
							'instance_id' => $lFieldParentInstanceId,
							'field_id' => $lCurrentFieldId,
							'instance_name' => $lObjectNodeName,
							'msg' => getstr('kfor.emptyField'),
							'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
						);
					}else{
						//След валидация - вдигаме грешката в целия материал
						$lResult[] = array(
							'instance_id' => $lMaterialId,
							'field_id' => $lCurrentFieldId,
							'instance_name' => $lObjectNodeName,
							'msg' => getstr('kfor.emptyField') . ' ' . $lFieldName ,
							'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
						);
					}
				}
			}
		}
	}

	return $lResult;
}

function CustomCheckPhytoKeysMaterialFieldsAfterSave($pMaterialNode, $pCheckMode = CUSTOM_CHECK_AFTER_SAVE_MODE){
	return CustomCheckPhytoKeysMaterialFieldsBase($pMaterialNode, CUSTOM_CHECK_AFTER_SAVE_MODE);
}

function CustomCheckPhytoKeysMaterialFieldsAfterValidate($pMaterialNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	return CustomCheckPhytoKeysMaterialFieldsBase($pMaterialNode, CUSTOM_CHECK_VALIDATION_MODE);
}

/**
 * Ще гледаме при save/validate на референция да има попълнен поне 1 автор
 * Ако няма такъв - ще сигнализираме за грешка
 * @param unknown_type $pAuthorsHolderNode
 */
function CustomCheckReferenceAuthors($pAuthorsHolderNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){

	define('REFERENCE_AUTHOR_OBJECT_ID', 90);
	define('REFERENCE_EDITOR_OBJECT_ID', 91);
	define('REFERENCE_AUTHOR_COMBINED_NAME_FIELD_ID', 250);
	define('REFERENCE_OBJECT_ID', 95);

	$lAuthorHoldersObjectIds = array(92, 100, 101);

	$lResult = array();
	$lXPath = new DOMXPath($pAuthorsHolderNode->ownerDocument);
	$lInstanceId = $lXPath->query('./@instance_id', $pAuthorsHolderNode)->item(0)->nodeValue;

	$lReferenceInstanceId = $lXPath->query('./ancestor::*[@object_id="' . (int)REFERENCE_OBJECT_ID . '"]/@instance_id', $pAuthorsHolderNode)->item(0)->nodeValue;
	$lCurrentInstanceObjectId = $lXPath->query('./@object_id', $pAuthorsHolderNode)->item(0)->nodeValue;

	$lAuthorNodes = $lXPath->query('.//*[@object_id = "' . REFERENCE_AUTHOR_OBJECT_ID . '" or @object_id = "' . REFERENCE_AUTHOR_OBJECT_ID . '"]', $pAuthorsHolderNode);

	if(!$lAuthorNodes->length){
		if(in_array($lCurrentInstanceObjectId, $lAuthorHoldersObjectIds)){
			$lResult[] = array(
				'instance_id' => $lReferenceInstanceId,
				'instance_name' => 'Reference',
				'msg' => getstr('pwt.referenceMustHaveAtLeastOneAuthor'),
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}
		return $lResult;
	}
	$lAuthorWithFilledNameFound = false;
	for($i = 0; $i < $lAuthorNodes->length; ++$i){
		$lCurrentAuthor = $lAuthorNodes->item($i);
		$lCombinedNameNode = $lXPath->query('./fields/*[@id="' . (int) REFERENCE_AUTHOR_COMBINED_NAME_FIELD_ID . '"]/value', $lCurrentAuthor);
		if(!$lCombinedNameNode->length){
			continue;
		}
		$lName = trim($lCombinedNameNode->item(0)->nodeValue);
		if($lName != ''){
			$lAuthorWithFilledNameFound = true;
		}
	}
	if(!$lAuthorWithFilledNameFound){
		if($pCheckMode == (int) CUSTOM_CHECK_AFTER_SAVE_MODE){
			$lAuthorInstanceId = $lAuthorNodes->item(0)->getAttribute('instance_id');
			//След save вдигаме грешката в полето на 1я автор
			$lResult[] = array(
				'instance_id' => $lAuthorInstanceId,
				'field_id' => REFERENCE_AUTHOR_COMBINED_NAME_FIELD_ID,
				'msg' => getstr('kfor.emptyField'),
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}else{
			//След валидация - вдигаме грешката в референцията
			$lResult[] = array(
				'instance_id' => $lReferenceInstanceId,
				'instance_name' => 'Reference',
				'msg' => getstr('pwt.referenceMustHaveAtLeastOneAuthor'),
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}
	}
	return $lResult;

}

/**
 * Ще гледаме при save/validate на контрибутор дали този контрибутор не присъства като автор или е добавен повече от 1 път
 * @param unknown_type $pContributorNode - възела на контрибутора
 */
function CustomCheckSingleContributor($pContributorNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('EMAIL_FIELD_ID', 4);
	define('AUTHOR_OBJECT_ID', 8);

	$lResult = array();
	$lXPath = new DOMXPath($pContributorNode->ownerDocument);
	$lEmailFieldNode = $lXPath->query('./fields/*[@id=' . (int) EMAIL_FIELD_ID . ']/value', $pContributorNode);
	if(!$lEmailFieldNode->length)
		return $lResult;
	$lEmail = trim($lEmailFieldNode->item(0)->nodeValue);
	if($lEmail == '')
		return $lResult;
// 	echo "\n\n<br/><br/>";
// 	var_dump($lEmail);
	//Първо гледаме дали има друг контрибутор с такъв мейл
	$lOtherContributorEmailQuery = './parent::*/*[@object_id="' . $pContributorNode->getAttribute('object_id') . '"][@instance_id!="' . $pContributorNode->getAttribute('instance_id') . '"]/fields/*[@id=' . (int) EMAIL_FIELD_ID . ']/value';

	$lOtherContributorEmailNodes = $lXPath->query($lOtherContributorEmailQuery, $pContributorNode);

	for($i = 0; $i < $lOtherContributorEmailNodes->length; ++$i){
// 		var_dump($lOtherContributorEmailNodes->item($i)->nodeValue);
		if(trim($lOtherContributorEmailNodes->item($i)->nodeValue) == $lEmail){
			$lResult[] = array(
				'instance_id' => $pContributorNode->getAttribute('instance_id'),
				'field_id' => (int)EMAIL_FIELD_ID,
				'instance_name' => $pContributorNode->getAttribute('display_name'),
				'msg' => getstr('pwt.thisContributorHasAlreadyBeenAddedToTheDocument') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
			break;
		}
	}

	$lAuthorEmailNodes = $lXPath->query('//*[@object_id="' . (int)AUTHOR_OBJECT_ID . '"]/fields/*[@id=' . (int) EMAIL_FIELD_ID . ']/value');
	for($i = 0; $i < $lAuthorEmailNodes->length; ++$i){
		//trim($lAuthorEmailNodes->item($i)->nodeValue
		if(trim($lAuthorEmailNodes->item($i)->nodeValue) == $lEmail){
			$lResult[] = array(
				'instance_id' => $pContributorNode->getAttribute('instance_id'),
				'field_id' => (int)EMAIL_FIELD_ID,
				'instance_name' => $pContributorNode->getAttribute('display_name'),
				'msg' => getstr('pwt.thisUserHasAlreadyBeenAddedAsAuthor') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
			break;
		}
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate на автор дали този автор не е добавен повече от 1 път
 * @param unknown_type $pAuthorNode - възела на автора
 */
function CustomCheckSingleAuthor($pAuthorNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('EMAIL_FIELD_ID', 4);
	$lResult = array();
	$lXPath = new DOMXPath($pAuthorNode->ownerDocument);
	$lEmailFieldNode = $lXPath->query('./fields/*[@id=' . (int) EMAIL_FIELD_ID . ']/value', $pAuthorNode);
	if(!$lEmailFieldNode->length)
		return $lResult;
	$lEmail = trim($lEmailFieldNode->item(0)->nodeValue);
	if($lEmail == '')
		return $lResult;
	//Гледаме дали има друг автор с такъв мейл
	$lOtherAuthorEmailNodes = $lXPath->query('./parent::*/*[@object_id="' . $pAuthorNode->getAttribute('object_id') . '"][@instance_id!="' . $pAuthorNode->getAttribute('instance_id') . '"]/fields/*[@id=' . (int) EMAIL_FIELD_ID . ']/value', $pAuthorNode);
	for($i = 0; $i < $lOtherAuthorEmailNodes->length; ++$i){
		if(trim($lOtherAuthorEmailNodes->item($i)->nodeValue) == $lEmail){
			$lResult[] = array(
				'instance_id' => $pAuthorNode->getAttribute('instance_id'),
				'field_id' => (int)EMAIL_FIELD_ID,
				'instance_name' => $pAuthorNode->getAttribute('display_name'),
				'msg' => getstr('pwt.thisUserHasAlreadyBeenAddedAsAuthor') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
			break;
		}
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate на Materials дали полето е празно понеже в друг 
 * документ същият обект трябва да не е задължителен
 * @param unknown_type $pMaterialAndMethodNode - възела на материала
 */
function CustomCheckMaterialAndMethods($pMaterialAndMethodNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('MATERIAL_FIELD_ID', 22);
	$lResult = array();
	
	$lXPath = new DOMXPath($pMaterialAndMethodNode->ownerDocument);
	$lMaterialFieldNode = $lXPath->query('./fields/*[@id=' . (int) MATERIAL_FIELD_ID . ']/value', $pMaterialAndMethodNode);
	
	$lContent = trim($lMaterialFieldNode->item(0)->nodeValue);
	if($lContent == '') {
		$lResult[] = array(
				'instance_id' => $pMaterialAndMethodNode->getAttribute('instance_id'),
				'field_id' => (int)MATERIAL_FIELD_ID,
				'instance_name' => $pMaterialAndMethodNode->getAttribute('display_name'),
				'msg' => getstr('kfor.emptyStringErr') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate на Cheklist при таксон и различен избран Rank
 * имаме различни задължителни полета
 * @param unknown_type $pChecklistNode - възела на чеклиста
 */
function CustomCheckChecklistTaxonFields($pChecklistNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('TAXON_CHECKLIST_RANK_FIELD_ID', 414);
	define('TAXON_CHECKLIST_OBJECT_ID', 174);
	
	define('TAXON_CHECKLIST_RANK_KINGDOM_TYPE_ID', 1);
	define('TAXON_CHECKLIST_RANK_KINGDOM_FIELD_ID', 419);
	define('TAXON_CHECKLIST_RANK_SUBKINGDOM_TYPE_ID', 2);
	define('TAXON_CHECKLIST_RANK_SUBKINGDOM_FIELD_ID', 420);
	define('TAXON_CHECKLIST_RANK_PHYLUM_TYPE_ID', 3);
	define('TAXON_CHECKLIST_RANK_PHYLUM_FIELD_ID', 421);
	define('TAXON_CHECKLIST_RANK_SUBPHYLUM_TYPE_ID', 4);
	define('TAXON_CHECKLIST_RANK_SUBPHYLUM_FIELD_ID', 422);
	define('TAXON_CHECKLIST_RANK_SUPERCLASS_TYPE_ID', 5);
	define('TAXON_CHECKLIST_RANK_SUPERCLASS_FIELD_ID', 423);
	define('TAXON_CHECKLIST_RANK_CLASS_TYPE_ID', 6);
	define('TAXON_CHECKLIST_RANK_CLASS_FIELD_ID', 424);
	define('TAXON_CHECKLIST_RANK_SUBCLASS_TYPE_ID', 7);
	define('TAXON_CHECKLIST_RANK_SUBCLASS_FIELD_ID', 425);
	define('TAXON_CHECKLIST_RANK_SUPERORDER_TYPE_ID', 8);
	define('TAXON_CHECKLIST_RANK_SUPERORDER_FIELD_ID', 426);
	define('TAXON_CHECKLIST_RANK_ORDER_TYPE_ID', 9);
	define('TAXON_CHECKLIST_RANK_ORDER_FIELD_ID', 427);
	define('TAXON_CHECKLIST_RANK_SUBORDER_TYPE_ID', 10);
	define('TAXON_CHECKLIST_RANK_SUBORDER_FIELD_ID', 428);
	define('TAXON_CHECKLIST_RANK_INFRAORDER_TYPE_ID', 11);
	define('TAXON_CHECKLIST_RANK_INFRAORDER_FIELD_ID', 429);
	define('TAXON_CHECKLIST_RANK_SUPERFAMILY_TYPE_ID', 12);
	define('TAXON_CHECKLIST_RANK_SUPERFAMILY_FIELD_ID', 430);
	define('TAXON_CHECKLIST_RANK_FAMILY_TYPE_ID', 13);
	define('TAXON_CHECKLIST_RANK_FAMILY_FIELD_ID', 431);
	define('TAXON_CHECKLIST_RANK_SUBFAMILY_TYPE_ID', 14);
	define('TAXON_CHECKLIST_RANK_SUBFAMILY_FIELD_ID', 432);
	define('TAXON_CHECKLIST_RANK_TRIBE_TYPE_ID', 15);
	define('TAXON_CHECKLIST_RANK_TRIBE_FIELD_ID', 433);
	define('TAXON_CHECKLIST_RANK_SUBTRIBE_TYPE_ID', 16);
	define('TAXON_CHECKLIST_RANK_SUBTRIBE_FIELD_ID', 434);
	define('TAXON_CHECKLIST_RANK_GENUS_TYPE_ID', 17);
	define('TAXON_CHECKLIST_RANK_GENUS_FIELD_ID', 48);
	define('TAXON_CHECKLIST_RANK_SUBGENUS_TYPE_ID', 18);
	define('TAXON_CHECKLIST_RANK_SUBGENUS_FIELD_ID', 417);
	
	define('TAXON_CHECKLIST_RANK_SPECIES_TYPE_ID', 19);
	define('TAXON_CHECKLIST_RANK_SPECIES_GENUS_FIELD_ID', 48);
	define('TAXON_CHECKLIST_RANK_SPECIES_SPECIES_FIELD_ID', 49);
	
	
	define('TAXON_CHECKLIST_RANK_SUBSPECIES_TYPE_ID', 20);
	define('TAXON_CHECKLIST_RANK_SUBSPECIES_GENUS_FIELD_ID', 48);
	define('TAXON_CHECKLIST_RANK_SUBSPECIES_SPECIES_FIELD_ID', 49);
	define('TAXON_CHECKLIST_RANK_SUBSPECIES_SUBSPECIES_FIELD_ID', 418);	
	
	define('TAXON_CHECKLIST_RANK_VARIETY_TYPE_ID', 21);
	define('TAXON_CHECKLIST_RANK_VARIETY_GENUS_FIELD_ID', 48);
	define('TAXON_CHECKLIST_RANK_VARIETY_SPECIES_FIELD_ID', 49);
	define('TAXON_CHECKLIST_RANK_VARIETY_VARIETY_FIELD_ID', 435);
	
	define('TAXON_CHECKLIST_RANK_FORM_TYPE_ID', 22);
	define('TAXON_CHECKLIST_RANK_FORM_GENUS_FIELD_ID', 48);
	define('TAXON_CHECKLIST_RANK_FORM_SPECIES_FIELD_ID', 49);
	define('TAXON_CHECKLIST_RANK_FORM_VARIETY_FIELD_ID', 436);
	
	
	
	$lResult = array();
	
	$lXPath = new DOMXPath($pChecklistNode->ownerDocument);
	$lChecklistTaxonRankQuery = './fields/*[@id="' . (int) TAXON_CHECKLIST_RANK_FIELD_ID . '"]/value/@value_id';
	
	$lChecklistTaxonRankValue = $lXPath->query($lChecklistTaxonRankQuery, $pChecklistNode);
	
	$lTaxonRankId = trim($lChecklistTaxonRankValue->item(0)->nodeValue);
	if($lTaxonRankId != '') {
	
		switch ((int)$lTaxonRankId) {
			case (int)TAXON_CHECKLIST_RANK_SUBORDER_TYPE_ID: 
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBORDER_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.suborder.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_KINGDOM_TYPE_ID: 
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_KINGDOM_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.kingdom.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUBKINGDOM_TYPE_ID: 
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBKINGDOM_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.subkingdom.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_PHYLUM_TYPE_ID: 
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_PHYLUM_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.phylum.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUBPHYLUM_TYPE_ID: 
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBPHYLUM_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.subphylum.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUPERCLASS_TYPE_ID: 
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUPERCLASS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.superclass.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_CLASS_TYPE_ID: 
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_CLASS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.class.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUBCLASS_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBCLASS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.subclass.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUPERORDER_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUPERORDER_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.superorder.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_ORDER_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_ORDER_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.order.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUBORDER_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBORDER_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.suborder.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_INFRAORDER_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_INFRAORDER_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.infraorder.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUPERFAMILY_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUPERFAMILY_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.superfamily.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_FAMILY_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_FAMILY_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.family.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUBFAMILY_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBFAMILY_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.subfamily.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_TRIBE_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_TRIBE_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.tribe.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUBTRIBE_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBTRIBE_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.subtribe.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_GENUS_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_GENUS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.genus.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SUBGENUS_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBGENUS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.subgenus.error');
				break;
			case (int)TAXON_CHECKLIST_RANK_SPECIES_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SPECIES_GENUS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.genus_field.error');
				/* ТУК ТРЯБВА ДА СЕ ДОБАВЯТ ГРЕШКИТЕ ЗА ДРУГИТЕ ПОЛЕТА */
				break;
			case (int)TAXON_CHECKLIST_RANK_SUBSPECIES_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_SUBSPECIES_GENUS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.genus_field.error');
				/* ТУК ТРЯБВА ДА СЕ ДОБАВЯТ ГРЕШКИТЕ ЗА ДРУГИТЕ ПОЛЕТА */
				break;
			case (int)TAXON_CHECKLIST_RANK_VARIETY_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_VARIETY_GENUS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.genus_field.error');
				/* ТУК ТРЯБВА ДА СЕ ДОБАВЯТ ГРЕШКИТЕ ЗА ДРУГИТЕ ПОЛЕТА */
				break;
			case (int)TAXON_CHECKLIST_RANK_FORM_TYPE_ID:
				$lTaxonRankTypeFieldId = (int)TAXON_CHECKLIST_RANK_FORM_GENUS_FIELD_ID;
				$lValidationInstanceName = getstr('pwt.checklist_taxon.genus_field.error');
				/* ТУК ТРЯБВА ДА СЕ ДОБАВЯТ ГРЕШКИТЕ ЗА ДРУГИТЕ ПОЛЕТА */
				break;
			default:
				$lResult[] = array(
					'instance_id' => $pChecklistNode->getAttribute('instance_id'),
					'field_id' => (int)MATERIAL_FIELD_ID,
					'instance_name' => $pChecklistNode->getAttribute('display_name'),
					'msg' => getstr('kfor.emptyStringErr') ,
					'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
				);
				break;
		}
		
		
		$lFieldValueCheckQuery = './fields/*[@id="' . (int) $lTaxonRankTypeFieldId . '"]/value';
	
		$lFieldValue = $lXPath->query($lFieldValueCheckQuery, $pChecklistNode);
	
		$lFieldValue = trim($lFieldValue->item(0)->nodeValue);
		if($lFieldValue == '') {
			if($pCheckMode == (int) CUSTOM_CHECK_AFTER_SAVE_MODE){
				$lResult[] = array(
					'instance_id' => $pChecklistNode->getAttribute('instance_id'),
					'field_id' => $lTaxonRankTypeFieldId,
					'msg' => getstr('kfor.emptyStringErr'),
					'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
				);
			}else{
				//След валидация - вдигаме грешката в референцията
				$lResult[] = array(
					'instance_id' => $pChecklistNode->getAttribute('instance_id'),
					'instance_name' => $lValidationInstanceName,
					'msg' => getstr('kfor.emptyStringErr'),
					'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
				);
			}
		}
		
		
		return $lResult;
	
	}	
	
	return $lResult;
}

/**
 * Ще гледаме при save/validate на Classifications за Taxon classification дали е празно
 * @param unknown_type $pClassificationNode - възела classifications
 */
function CustomCheckTaxonPaperTaxonomicClassification($pClassificationNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('TAXON_CLASSIFICATION_FIELD_ID', 244);
	$lResult = array();
	
	$lXPath = new DOMXPath($pClassificationNode->ownerDocument);
	$lTaxonClassificationNode = $lXPath->query('./fields/*[@id=' . (int) TAXON_CLASSIFICATION_FIELD_ID . ']/value', $pClassificationNode);
	
	$lContent = trim($lTaxonClassificationNode->item(0)->nodeValue);
	if($lContent == '') {
	
		$lResult[] = array(
				'instance_id' => $pClassificationNode->getAttribute('instance_id'),
				'field_id' => (int)TAXON_CLASSIFICATION_FIELD_ID,
				'instance_name' => $pClassificationNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.missingTaxonClassification') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate на Classifications в Taxonomic paper-a за Subject classification дали е празно
 * @param unknown_type $pClassificationNode - възела classifications
 */
function CustomCheckSubjectClassification($pClassificationNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('SUBJECT_CLASSIFICATION_FIELD_ID', 245);
	$lResult = array();
	
	$lXPath = new DOMXPath($pClassificationNode->ownerDocument);
	$lSubjectClassificationNode = $lXPath->query('./fields/*[@id=' . (int) TAXON_CLASSIFICATION_FIELD_ID . ']/value', $pClassificationNode);
	
	$lContent = trim($lSubjectClassificationNode->item(0)->nodeValue);
	if($lContent == '') {
	
		$lResult[] = array(
				'instance_id' => $pClassificationNode->getAttribute('instance_id'),
				'field_id' => (int)SUBJECT_CLASSIFICATION_FIELD_ID,
				'instance_name' => $pClassificationNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.missingSubjectClassification') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate ако имаме добавен Identification key, то той трябва да има и key couplet
 * с няколко задължителни полета
 * @param unknown_type $pIdentificationKeyNode
 */
function CustomCheckIdentificationKeyCouplet($pIdentificationKeyNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('KEY_COUPLET_OBJECT_ID', 22);
	$lResult = array();
	
	$lXPath = new DOMXPath($pIdentificationKeyNode->ownerDocument);
	$lIfKeyCoupletExists = $lXPath->query('.//*[@object_id=' . (int) KEY_COUPLET_OBJECT_ID . ']', $pIdentificationKeyNode);
	
	if(!(int)$lIfKeyCoupletExists->length) {
	
		$lResult[] = array(
				'instance_id' => $pIdentificationKeyNode->getAttribute('instance_id'),
				'instance_name' => $pIdentificationKeyNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.identificationKeyKeyCoupletRequired') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate дали има добавена поне една референция
 * @param unknown_type $pReferenceNode
 */
function CustomCheckForReference($pReferenceNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	$lResult = array();
	
	$lXPath = new DOMXPath($pReferenceNode->ownerDocument);
	$lReferences = $lXPath->evaluate('count(./reference)', $pReferenceNode);
	
	if(!(int)$lReferences) {
	
		$lResult[] = array(
				'instance_id' => $pReferenceNode->getAttribute('instance_id'),
				'instance_name' => $pReferenceNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.referenceRequired') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate дали има добавен поне един Treatment
 * @param unknown_type $pSuppFileNode
 */
function CustomCheckForTreatment($pTreatmentNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('TREATMENT_OBJECT_ID', 41);
	define('CHECKLIST_SINGLE_OBJECT_ID', 174);
	define('ID_KEYS_OBJECT_ID', 23);
	
	$lResult = array();
	
	$lXPath = new DOMXPath($pTreatmentNode->ownerDocument);
	$lFile = $lXPath->evaluate('count(.//*[@object_id=' . (int) TREATMENT_OBJECT_ID . '])', $pTreatmentNode);
	
	$lCheckListRoot = $pTreatmentNode->parentNode;
	$lCheckListsCount = $lXPath->evaluate('count(.//*[@object_id="' . (int)CHECKLIST_SINGLE_OBJECT_ID . '"])',$lCheckListRoot);
	
	$lIdKeysRoot = $pTreatmentNode->parentNode;
	$lIdKeysListCount = $lXPath->evaluate('count(.//*[@object_id="' . (int)ID_KEYS_OBJECT_ID . '"])',$lIdKeysRoot);

	if(!((int)$lFile || (int)$lCheckListsCount || (int)$lIdKeysListCount)) {
	
		$lResult[] = array(
				'instance_id' => $pTreatmentNode->getAttribute('instance_id'),
				'instance_name' => $pTreatmentNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.treatmentRequired1') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate дали има добавен поне един Taxon в Checklist-а
 * @param unknown_type $pChecklistNode
 */
function CustomCheckForChecklistTaxonAtLeastOne($pChecklistNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('SINGLE_CHECKLIST_TAXON_OBJECT_ID', 205);
	
	$lResult = array();
	
	$lXPath = new DOMXPath($pChecklistNode->ownerDocument);
	$lTaxonCount = $lXPath->evaluate('count(.//*[@object_id=' . (int) SINGLE_CHECKLIST_TAXON_OBJECT_ID . '])', $pChecklistNode);

	if(!(int)$lTaxonCount) {
	
		$lResult[] = array(
				'instance_id' => $pChecklistNode->getAttribute('instance_id'),
				'instance_name' => $pChecklistNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.ChecklistTaxonRequired1') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	return $lResult;
}


/**
 * Ще гледаме типа на триитмънта, за да слагаме not null-ове на някои полета
 * @param unknown_type $pTreatmentNode
 */
function CustomCheckTaxonTreatmentFieldsByType($pTreatmentNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('RANK_FIELD_ID', 42);
	define('TYPE_FIELD_ID', 43);
	define('CLASSIFICATION_FIELD_ID', 384);
	define('TREATMENT_OBJECT_ID', 41);
	define('EXTENDED_DC_OBJECT_ID', 84);
	define('PRIMARY_DC_OBJECT_ID', 83);

	define('RECORD_NUM_FIELD_ID', 57);
	define('RECORDED_BY_FIELD_ID', 58);
	define('INSTITUTION_CODE_FIELD_ID', 200);
	define('COLLECTION_CODE_FIELD_ID', 201);
	
	define('NEW_TAXON_TYPE', 1);
	define('SPECIES_RANK', 1);
	define('GENUS_RANK', 2);
	define('REDESCRIPTION_TYPE', 5);
	
	define('ANIMALIA_CLASSIFICATION_TYPE', 8);
	define('PLANTAE_CLASSIFICATION_TYPE', 7);
	
	define('DESCRIPTION_SECTION_OBJECT_ID', 47);
	define('DIAGNOSIS_SECTION_OBJECT_ID', 48);
	define('ETYMOLOGY_SECTION_OBJECT_ID', 49);
	define('TAXON_DISCUSSION_SECTION_OBJECT_ID', 75);
	define('DISTRIBUTION_SECTION_OBJECT_ID', 76);
	define('ECOLOGY_SECTION_OBJECT_ID', 77);
	define('BIOLOGY_SECTION_OBJECT_ID', 78);
	define('CONSERVATION_SECTION_OBJECT_ID', 79);
	define('NOTES_SECTION_OBJECT_ID', 80);
	
	
	define('FIELD_SECTION_VALUE_ID', 212);
	
	

	$lPhytoKeysClassifications = array(6, 7, 364);

	$lResult = array();
	$lXPath = new DOMXPath($pTreatmentNode->ownerDocument);

	$lMaterialId = $lXPath->query('./@instance_id', $pTreatmentNode)->item(0)->nodeValue;

	if($pCheckMode == CUSTOM_CHECK_AFTER_SAVE_MODE && !in_array($lMaterialId, $_REQUEST['instance_ids'])){
		return $lResult;
	}
	
	$lRankNode = $lXPath->query('./fields/*[@id="' . (int) RANK_FIELD_ID . '"]/value/@value_id', $pTreatmentNode);
	$lTypeNode = $lXPath->query('./fields/*[@id="' . (int) TYPE_FIELD_ID . '"]/value/@value_id', $pTreatmentNode);
	$lClassificationRootNode = $lXPath->query('./fields/*[@id="' . (int) CLASSIFICATION_FIELD_ID . '"]/value', $pTreatmentNode);
	
	if(!$lRankNode->length || !$lTypeNode->length || !$lClassificationRootNode->length){
		return $lResult;
	}
	$lRank = (int)$lRankNode->item(0)->nodeValue;
	$lTreatmentType = (int)$lTypeNode->item(0)->nodeValue;
	$lClassification = (int)$lClassificationRootNode->item(0)->nodeValue;

	
	if(!$lRank || !$lTreatmentType || !$lClassification){
		return $lResult;
	}
	$lObjectsToProcess = array(
		DESCRIPTION_SECTION_OBJECT_ID, 
		DIAGNOSIS_SECTION_OBJECT_ID,
		ETYMOLOGY_SECTION_OBJECT_ID,
		TAXON_DISCUSSION_SECTION_OBJECT_ID,
		DISTRIBUTION_SECTION_OBJECT_ID,
		ECOLOGY_SECTION_OBJECT_ID,
		BIOLOGY_SECTION_OBJECT_ID,
		CONSERVATION_SECTION_OBJECT_ID,
		NOTES_SECTION_OBJECT_ID		
	);
	
	/*
	// ANIMALIA New Species
	if($lRank == (int) SPECIES_RANK && $lTreatmentType == (int) NEW_TAXON_TYPE && $lClassification == (int) ANIMALIA_CLASSIFICATION_TYPE){
		
		$lObjectsToProcess = array(DIAGNOSIS_SECTION_OBJECT_ID, DISTRIBUTION_SECTION_OBJECT_ID);
		$lFieldIds = array(FIELD_SECTION_VALUE_ID);
		
		$lResult = getRequiredTaxonTreatmentFieldsByType($lObjectsToProcess, $lFieldIds, $pTreatmentNode, $pCheckMode);
		
	} elseif($lRank == (int) SPECIES_RANK && $lTreatmentType == (int) NEW_TAXON_TYPE && $lClassification == (int) PLANTAE_CLASSIFICATION_TYPE){
		// PLANTAE NEW SPECIES
		$lObjectsToProcess = array(DIAGNOSIS_SECTION_OBJECT_ID, DISTRIBUTION_SECTION_OBJECT_ID);
		
		
		$lResult = getRequiredTaxonTreatmentFieldsByType($lObjectsToProcess, $lFieldIds, $pTreatmentNode, $pCheckMode);
		
	} elseif($lRank == (int) GENUS_RANK && $lTreatmentType == (int) NEW_TAXON_TYPE && $lClassification == (int) PLANTAE_CLASSIFICATION_TYPE){
		// PLANTAE New Genus
		$lObjectsToProcess = array(DIAGNOSIS_SECTION_OBJECT_ID, DISTRIBUTION_SECTION_OBJECT_ID);
		$lFieldIds = array(FIELD_SECTION_VALUE_ID);
		
		$lResult = getRequiredTaxonTreatmentFieldsByType($lObjectsToProcess, $lFieldIds, $pTreatmentNode, $pCheckMode);
		
	}
	*/
	
	$lFieldIds = array(FIELD_SECTION_VALUE_ID);
	$lResult = getRequiredTaxonTreatmentFieldsByType($lObjectsToProcess, $lFieldIds, $pTreatmentNode, $pCheckMode);
	
	return $lResult;
}

function getRequiredTaxonTreatmentFieldsByType($pObjectsToProcess, $pFieldsToProcess, $pCurrNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE) {
	$lResult = array();
	$lXPath = new DOMXPath($pCurrNode->ownerDocument);
	$lCurrNodeInstanceId = $pCurrNode->getAttribute('instance_id');
	foreach ($pObjectsToProcess as $lCurrentObjectToProcess){
		$lObjectNode = $lXPath->query('.//*[@object_id="' . $lCurrentObjectToProcess . '"]', $pCurrNode);
		$lObjectNode = $lObjectNode->item(0);
		
		if(!$lObjectNode){
			continue;
		}
		$lObjectNodeName = $lXPath->query('./@display_name', $lObjectNode)->item(0)->nodeValue;
		
		$lObjectInstanceId = $lObjectNode->getAttribute('instance_id');
		$lObjectParentInstanceId = $lObjectNode->parentNode->getAttribute('instance_id');
		$lObjectParentNodeName = $lXPath->query('./@display_name', $lObjectNode->parentNode)->item(0)->nodeValue;
		
		//След save показваме грешките само на видимите обекти
		if($pCheckMode == CUSTOM_CHECK_AFTER_SAVE_MODE && !in_array($lObjectInstanceId, $_REQUEST['instance_ids'])){
			continue;
		}
		foreach ($pFieldsToProcess as $lCurrentFieldId){
			$lFieldNode = $lXPath->query('.//fields/*[@id="' . (int)$lCurrentFieldId . '"]/value', $lObjectNode);

			if(!$lFieldNode->length){
				continue;
			}
			$lFieldName = $lXPath->query('./parent::*/@field_name', $lFieldNode->item(0))->item(0)->nodeValue;
			$lFieldParentInstance = $lFieldNode->item(0);
			while(!$lFieldParentInstance->getAttribute('instance_id')){
				$lFieldParentInstance = $lFieldParentInstance->parentNode;
			}
			$lFieldParentInstanceId = $lFieldParentInstance->getAttribute('instance_id');
			$lFieldValue = $lFieldNode->item(0)->nodeValue;

			$lCurrObj[$lObjectParentInstanceId][$lFieldParentInstanceId] = $lFieldValue;
		}
	}
	
	if (array_empty($lCurrObj)) {
		$lInstanceKey = array_keys($lCurrObj);
			
		$lResult[] = array(
			'instance_id' => $lInstanceKey[0],
			'field_id' => $lCurrentFieldId,
			'instance_name' => $lObjectParentNodeName,
			'msg' => getstr('pwt.validation.anyTTSectionFieldRequired'),
			'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
		);
	
	}
	return $lResult;
}

/**
 * Ще гледаме типа на Locality-то в Cheklist документ според типа му и ще слагаме not null полета
 * @param unknown_type $pTreatmentNode
 */
function CustomCheckLocalityFieldsByType($pLocalityNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('LOCALITY_TYPE_FIELD_ID', 445);
	
	define('LOCALITY_REGION_TYPE', 1);
	define('LOCALITY_HABITAT_TYPE', 2);
	define('LOCALITY_NATURA2000_TYPE', 3);
	
	define('LOCALITY_HABITAT_CODE_FIELD_ID', 446);
	define('LOCALITY_HABITAT_CLASSIFICATION_FIELD_ID', 447);
	define('LOCALITY_NATURA_CODE_FIELD_ID', 448);
	
	define('LOCALITY_higherGeography_FIELD_ID', 109);
	define('LOCALITY_continent_FIELD_ID', 110);
	define('LOCALITY_waterBody_FIELD_ID', 111);
	define('LOCALITY_islandGroup_FIELD_ID', 112);
	define('LOCALITY_island_FIELD_ID', 113);
	define('LOCALITY_country_FIELD_ID', 114);
	define('LOCALITY_countryCode_FIELD_ID', 115);
	define('LOCALITY_stateProvince_FIELD_ID', 116);
	define('LOCALITY_municipality_FIELD_ID', 118);
	define('LOCALITY_locality_FIELD_ID', 119);
	define('LOCALITY_verbatimLocality_FIELD_ID', 120);
	define('LOCALITY_verbatimElevation_FIELD_ID', 121);
	define('LOCALITY_minimumElevationInMeters_FIELD_ID', 122);
	define('LOCALITY_maximumElevationInMeters_FIELD_ID', 123);
	define('LOCALITY_verbatimDepth_FIELD_ID', 124);
	define('LOCALITY_minimumDepthInMeters_FIELD_ID', 125);
	define('LOCALITY_maximumDepthInMeters_FIELD_ID', 126);
	define('LOCALITY_locationAccordingTo_FIELD_ID', 129);
	define('LOCALITY_verbatimLatitude_FIELD_ID', 132);
	define('LOCALITY_verbatimLongitude_FIELD_ID', 133);
	define('LOCALITY_verbatimCoordinateSystem_FIELD_ID', 134);
	
	$lResult = array();
	$lXPath = new DOMXPath($pLocalityNode->ownerDocument);

	$lLocalityType = $lXPath->query('./fields/*[@id="' . (int) LOCALITY_TYPE_FIELD_ID . '"]/value/@value_id', $pLocalityNode);
	
	if(!$lLocalityType->length){
		return $lResult;
	}
	
	$lLocalityTypeValue = (int)$lLocalityType->item(0)->nodeValue;
	
	if(!$lLocalityTypeValue){
		return $lResult;
	}
	
	if($lLocalityTypeValue == (int)LOCALITY_HABITAT_TYPE) {
		$lLocalityHabitatCodeValue = $lXPath->query('./fields/*[@id="' . (int) LOCALITY_HABITAT_CODE_FIELD_ID . '"]/value', $pLocalityNode)->item(0)->nodeValue;
		$lLocalityHabitatClassValue = $lXPath->query('./fields/*[@id="' . (int) LOCALITY_HABITAT_CLASSIFICATION_FIELD_ID . '"]/value', $pLocalityNode)->item(0)->nodeValue;
		if(trim($lLocalityHabitatCodeValue) == '' && trim($lLocalityHabitatClassValue) == '') {
			$lResult[] = array(
				'instance_id' => $pLocalityNode->getAttribute('instance_id'),
				'instance_name' => $pLocalityNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.missingHabitatCodeOrHabitatClassification') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}
	} elseif ($lLocalityTypeValue == (int)LOCALITY_NATURA2000_TYPE) {
		$lLocalityNaturaCodeValue = $lXPath->query('./fields/*[@id="' . (int) LOCALITY_NATURA_CODE_FIELD_ID . '"]/value', $pLocalityNode)->item(0)->nodeValue;
		if(trim($lLocalityNaturaCodeValue) == '') {
			$lResult[] = array(
				'instance_id' => $pLocalityNode->getAttribute('instance_id'),
				'instance_name' => $pLocalityNode->getAttribute('display_name'),
				'field_id' => LOCALITY_NATURA_CODE_FIELD_ID,
				'msg' => getstr('pwt.validation.missingNaturaCodeField') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}
	} elseif ($lLocalityTypeValue == (int)LOCALITY_REGION_TYPE) {
		
		$pFieldsToProcess = array(
			LOCALITY_higherGeography_FIELD_ID,
			LOCALITY_continent_FIELD_ID,
			LOCALITY_waterBody_FIELD_ID,
			LOCALITY_islandGroup_FIELD_ID,
			LOCALITY_island_FIELD_ID,
			LOCALITY_country_FIELD_ID,
			LOCALITY_countryCode_FIELD_ID,
			LOCALITY_stateProvince_FIELD_ID,
			LOCALITY_municipality_FIELD_ID,
			LOCALITY_locality_FIELD_ID,
			LOCALITY_verbatimLocality_FIELD_ID,
			LOCALITY_verbatimElevation_FIELD_ID,
			LOCALITY_minimumElevationInMeters_FIELD_ID,
			LOCALITY_maximumElevationInMeters_FIELD_ID,
			LOCALITY_verbatimDepth_FIELD_ID,
			LOCALITY_minimumDepthInMeters_FIELD_ID,
			LOCALITY_maximumDepthInMeters_FIELD_ID,
			LOCALITY_locationAccordingTo_FIELD_ID, 
			LOCALITY_verbatimLatitude_FIELD_ID, 
			LOCALITY_verbatimLongitude_FIELD_ID, 
			LOCALITY_verbatimCoordinateSystem_FIELD_ID,
		);
		$lFieldValues = array();
		foreach ($pFieldsToProcess as $lCurrentFieldId){
			$lFieldValues[] = $lXPath->query('./fields/*[@id="' . (int)$lCurrentFieldId . '"]/value', $pLocalityNode)->item(0)->nodeValue;
		}
		// if all values are empty
		if (!array_filter($lFieldValues)) {
			$lResult[] = array(
				'instance_id' => $pLocalityNode->getAttribute('instance_id'),
				'instance_name' => $pLocalityNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.anyLocalityRegionFieldRequired') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		}
		
	}

	return $lResult;
}

/**
 * Ще гледаме при save/validate на Web Location трябва да е попълнено поне едно от полетата Homepage, Wiki, Download page
 * @param unknown_type $pWebLocationNode - възела Web location
 */
function CustomCheckWebLocationFieldsNotEmpty($pWebLocationNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('WEB_LOCATION_HOMEPAGE_FIELD_ID', 293);
	define('WEB_LOCATION_WIKI_FIELD_ID', 294);
	define('WEB_LOCATION_DOWNLOAD_PAGE_FIELD_ID', 295);
	$lResult = array();
	
	$lXPath = new DOMXPath($pWebLocationNode->ownerDocument);
	$lHomepageNode = $lXPath->query('./fields/*[@id=' . (int) WEB_LOCATION_HOMEPAGE_FIELD_ID . ']/value', $pWebLocationNode);
	$lWikiNode = $lXPath->query('./fields/*[@id=' . (int) WEB_LOCATION_WIKI_FIELD_ID . ']/value', $pWebLocationNode);
	$lDownloadpageNode = $lXPath->query('./fields/*[@id=' . (int) WEB_LOCATION_DOWNLOAD_PAGE_FIELD_ID . ']/value', $pWebLocationNode);
	
	$lHomepageContent = trim($lHomepageNode->item(0)->nodeValue);
	$lWikiContent = trim($lWikiNode->item(0)->nodeValue);
	$lDownloadpageContent = trim($lDownloadpageNode->item(0)->nodeValue);
	if($lHomepageContent == '' && $lWikiContent == '' && $lDownloadpageContent == '') {
	
		$lResult[] = array(
				'instance_id' => $pWebLocationNode->getAttribute('instance_id'),
				'field_id' => '',
				'instance_name' => $pWebLocationNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.atLeastOneFieldRequiredInWebLocations') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	return $lResult;
}

/**
 * Ще гледаме при save/validate на Usage rights ако за Use license е избрано 'Other' то IP rights notes трябва да е попълнено
 * @param unknown_type $pUsageRightsNode - възела Usage rights
 */
function CustomCheckUsageRightsIPRightsNotesFieldsNotEmpty($pUsageRightsNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('USAGE_RIGHTS_USE_LICENSE_FIELD_ID', 311);
	define('USAGE_RIGHTS_IP_RIGHTS_NOTES_FIELD_ID', 312);
	define('USAGE_RIGHTS_USE_LICENSE_OTHER_VALUE', 'other');
	$lResult = array();
	
	$lXPath = new DOMXPath($pUsageRightsNode->ownerDocument);
	$lUsageRightsNode = $lXPath->query('./fields/*[@id=' . (int) USAGE_RIGHTS_USE_LICENSE_FIELD_ID . ']/value', $pUsageRightsNode);
	
	$lUsageRightsContent = trim($lUsageRightsNode->item(0)->nodeValue);
	
	if(strtolower($lUsageRightsContent) == USAGE_RIGHTS_USE_LICENSE_OTHER_VALUE){
		$lIPRightsNotesNode = $lXPath->query('./fields/*[@id=' . (int) USAGE_RIGHTS_IP_RIGHTS_NOTES_FIELD_ID . ']/value', $pUsageRightsNode);
		
		$lIPRightsNotesContent = trim($lIPRightsNotesNode->item(0)->nodeValue);
		
		if($lIPRightsNotesContent == '') {
	
			$lResult[] = array(
					'instance_id' => $pUsageRightsNode->getAttribute('instance_id'),
					'field_id' => (int)USAGE_RIGHTS_IP_RIGHTS_NOTES_FIELD_ID,
					'instance_name' => $pUsageRightsNode->getAttribute('display_name'),
					'msg' => getstr('pwt.validation.usageRightsNotesisRequired') ,
					'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
				);
			return $lResult;
		}
	}
	
	return $lResult;
}

/**
 * Ще гледаме при save/validate на Project description - трябва поне 1 от полетата да е попълнено - Study area description или design description
 * @param unknown_type $pUsageRightsNode - възела Usage rights
 */
function CustomCheckProjectDescriptionFieldsNotEmpty($pProjectDescriptionNode, $pCheckMode = CUSTOM_CHECK_VALIDATION_MODE){
	define('PROJECT_DESCRIPTION_STUDY_AREA_DESCRIPTION_FIELD_ID', 290);
	define('PROJECT_DESCRIPTION_DESIGN_DESCRIPTION_FIELD_ID', 291);
	$lResult = array();
	
	$lXPath = new DOMXPath($pProjectDescriptionNode->ownerDocument);
	$lStudyAreaDescriptionNode = $lXPath->query('./fields/*[@id=' . (int) PROJECT_DESCRIPTION_STUDY_AREA_DESCRIPTION_FIELD_ID . ']/value', $pProjectDescriptionNode);
	$lDesignDescriptionNode = $lXPath->query('./fields/*[@id=' . (int) PROJECT_DESCRIPTION_DESIGN_DESCRIPTION_FIELD_ID . ']/value', $pProjectDescriptionNode);
	
	$lStudyAreaDescriptionContent = trim($lStudyAreaDescriptionNode->item(0)->nodeValue);
	$lDesignDescriptionContent = trim($lDesignDescriptionNode->item(0)->nodeValue);
	
	if($lStudyAreaDescriptionContent == '' && $lDesignDescriptionContent == '') {

		$lResult[] = array(
				'instance_id' => $pProjectDescriptionNode->getAttribute('instance_id'),
				'field_id' => '',
				'instance_name' => $pProjectDescriptionNode->getAttribute('display_name'),
				'msg' => getstr('pwt.validation.projectDescriptionAtLeasOneFieldRequired') ,
				'error_type' => CUSTOM_CHECK_NORMAL_ERROR_TYPE,
			);
		return $lResult;
	}
	
	return $lResult;
}

?>