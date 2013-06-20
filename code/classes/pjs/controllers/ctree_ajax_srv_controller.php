<?php

class cTree_Ajax_Srv_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		
		$lTreeModel = new mTree_Model();
		
		$lAction     		 = $this->GetValueFromRequestWithoutChecks('action');
		$lFieldId    		 = (int)$this->GetValueFromRequestWithoutChecks('field_id');
		$lSearchTerm 		 = $this->GetValueFromRequestWithoutChecks('term');
		$lTableName  		 = $this->GetValueFromRequestWithoutChecks('table_name');
		$lKey        		 = $this->GetValueFromRequestWithoutChecks('key');
		$lNodeId     		 = (int)$this->GetValueFromRequestWithoutChecks('nodeid');
		$lFilterByDocJournal = $this->GetValueFromRequestWithoutChecks('filter_by_document_journal');
		$lJournalId 		 = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		
		switch($lAction){
			default:
			case 'get_autocomplete_options':
				break;
			case 'get_reg_autocomplete':
				$pViewPageObjectsDataArray = $lTreeModel->getRegFieldAutocompleteItems($lSearchTerm, $lTableName, $lFilterByDocJournal, $lJournalId);
				break;
			case 'get_tree_autocomplete':
				$pViewPageObjectsDataArray = $lTreeModel->getRegTreeAutocompleteItems($lTableName, false, $lKey, $lFilterByDocJournal, $lJournalId);
				break;
			case 'get_tree_nodestructure':
				break;
			case 'get_email_recipients':
				break;
		}
		
		$this->m_pageView = new epPage_Json_View($pViewPageObjectsDataArray);
	}
}

?>