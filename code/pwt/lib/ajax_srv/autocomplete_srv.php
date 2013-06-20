<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$gAction = $_REQUEST['action'];
$gInstanceId = (int)$_REQUEST['instance_id'];
$gFieldId = (int)$_REQUEST['field_id'];
$gSearchTerm = strtolower($_REQUEST['term']);
$gTableName = $_REQUEST['table_name'];
$gKey = $_REQUEST['key'];
$gNodeId = (int)$_REQUEST['nodeid'];
$gFilterByDocumentJournal = (int)$_REQUEST['filter_by_document_journal'];


switch($gAction){
	default:
	case 'get_autocomplete_options':{
			if(!$gInstanceId || !$gFieldId)
				exit;
			$lQuery = getFieldSrcQuery($gInstanceId, $gFieldId);
			$lResult = getFieldAutocompleteItems($lQuery, $gSearchTerm);
			displayAjaxResponse($lResult);
		}
		break;
	case 'get_reg_autocomplete':{
			$lResult = getRegFieldAutocompleteItems($gSearchTerm, $gTableName, $gFilterByDocumentJournal, $gInstanceId);
			displayAjaxResponse($lResult);
		}
		break;
	case 'get_tree_autocomplete':{
			$lResult = getRegTreeAutocompleteItems($gTableName, false, $gKey, $gFilterByDocumentJournal, $gInstanceId);
			displayAjaxResponse($lResult);
		}
		break;
	case 'get_tree_nodestructure':{
			$lResult = getTreeStructureByNodeId($gNodeId, $gTableName);
			echo 1;
		}
		break;
	case 'get_email_recipients':{
			$lResult = getEmailRecipientsItems($gSearchTerm);
			displayAjaxResponse($lResult);
		}
		break;
}

?>