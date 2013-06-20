<?php

error_reporting((int)ERROR_REPORTING);
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');

define('GET_XML_ACTION', 1);
define('SAVE_XML_ACTION', 2);
define('VALIDATE_XML_ACTION', 3);
define('SAVE_AND_CREATE_NEW_XML_ACTION', 4);

$lAction = (int) $_REQUEST['action'];
$lArticleId = (int) $_REQUEST['id'];
//~ echo 'action:' . $lAction . ';articleid:' . $lArticleId;
$lCon = Con();
switch($lAction){
	default:
	case (int) GET_XML_ACTION:{
		$lSql = 'SELECT * FROM spArticle(0, ' . (int) $lArticleId. ', null, null)';
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		if( !$lCon->Eof()){
			echo $lCon->mRs['xml'];
		}
		break;
	}
	case (int) SAVE_XML_ACTION:{
		$lXml = s($_POST['xml']);
		//~ trigger_error($lXml, E_USER_NOTICE);
		//~ $lFile = fopen('/tmp/pmttest.txt', 'wa');
		//~ fwrite($lFile, $lXml);
		//~ fclose($lFile);
		$lXml = GetFormattedXml($lXml);
		$lSql = 'SELECT * FROM spArticle(1, ' . (int) $lArticleId. ', \'' . q($lXml) . '\', ' . (int) $user->id . ')';
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		if( !$lCon->Eof()){
			SyncXmlData($lArticleId, $lXml);
			echo (int)$lCon->mRs['id'];			
		}
		break;
	}
	case (int) VALIDATE_XML_ACTION:{
		$lXml = s($_POST['xml']);
			
		$lDOM = new DOMDocument("1.0");
		$lDOM->validateOnParse = true;
		$lDOM->resolveExternals = true;
		 
		libxml_clear_errors();
			   
		if (!$lDOM->loadXML($lXml)) {
		    echo('Error Loading Document');
		    return;
		}

		if ( $lError = libxml_get_last_error()) {
		    echo('Error Parsing Document:' . $lError->message);
		    return;
		} 
		echo 'Validated successfully';
		break;
	}
	case (int) SAVE_AND_CREATE_NEW_XML_ACTION:{
		$lXml = s($_POST['xml']);	
		$lXml = GetFormattedXml($lXml);	
		$lSql = 'SELECT * FROM spArticle(2, ' . (int) $lArticleId. ', \'' . q($lXml) . '\', ' . (int) $user->id . ')';
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		if( !$lCon->Eof()){
			SyncXmlData($lArticleId, $lXml);
			$lOldId = (int)$lCon->mRs['id'];			
			$lNewId = (int)$lCon->mRs['new_id'];			
			$lResult = array('oldId' => $lOldId, 'newId' => $lNewId);
			echo json_encode($lResult);
		}
		break;
	}
}



?>