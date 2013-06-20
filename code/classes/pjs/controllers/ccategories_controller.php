<?php

class cCategories_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();
		
		$this->m_models['mTree_Model'] = new mTree_Model();
	}
	
	function getCategoriesAndAutocomplete($pTreeKey, $pTreeKeyScript, $pHtmlIdentifier, $pDbSrcTable,
									$pNameInViewObject = 'tree_list', $pScriptNameInViewObject = 'tree_script',
									$pIsTreeMultiple = 2, $pIsTreeDisabled = 0, $pIsTokenInputMultiple = 1, $pIsByJournalId = 1){
		

		$lJournalIdData = $this->GetValueFromRequest('journal_id', 'GET', 'int', false, false);
		$lJournalId = $lJournalIdData['value'];
		
		$lTree = array(
			$pTreeKey => new evList_Display(array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => $pNameInViewObject,
				'controller_data' => $this->m_models['mTree_Model']->getRegTreeCategoriesByName($pDbSrcTable, true, 0, $lJournalId, $pIsByJournalId),
				'unique' => $pTreeKey[0],
			)),
			$pTreeKeyScript => new evSimple_Block_Display(array(
				'ctype' => 'evSimple_Block_Display',
				'name_in_viewobject' => $pScriptNameInViewObject,
				'html_identifier' => $pHtmlIdentifier, // това е id - то на инпута,  
													   // (автоматично към него се добавя _autocomplete)	
				'is_multiple' => (int)$pIsTreeMultiple, // 2 - multiple, 1 - single
				'db_src_table' => $pDbSrcTable, //'chronological_categories',
				'is_disabled' => (int)$pIsTreeDisabled, // 1 - disabled
				'is_token_input_multiple' => (int)$pIsTokenInputMultiple, // 1 - multiple select, 0 - single
			))
		);
		
		return $lTree;
	}
	
	function GetSelectedValues($pDbTable, $pSelectedValues){
		return $this->m_models['mTree_Model']->GetFieldAutoItems($pDbTable, prepAutocompleteKforField($pSelectedValues));
	}
	
	function SetTreeSelectedVals($pTreeField, $pTreeTable, $pTemplKey, $pFormControllerInstance){
		if($pFormControllerInstance->GetFieldValue($pTreeField)){
			$lSelectedValues 	= $pFormControllerInstance->GetFieldValue($pTreeField);
			$lSelectedValuesArr = $this->GetSelectedValues( $pTreeTable, $lSelectedValues);
			$pFormControllerInstance->SetPubdataValue($pTemplKey, $lSelectedValuesArr);
		}else{
			$pFormControllerInstance->SetPubdataValue($pTemplKey, array());
		}
	}
	function ClearExpertiseValues($pPost){
		$lRes = explode(',', $pPost);
		foreach($lRes as $Data){
			$lResult[] = substr($Data, 1);
		}
		$lResult = implode(',', $lResult);
		return $lResult;
	}
	
	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}

?>