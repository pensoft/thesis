<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');
ini_set('display_errors', 'off');
session_write_close();

$gAction = $_REQUEST['action'];

if((int) $_REQUEST['instance_id']){
	checkIfDocumentIsLockedByTheCurrentUserForAjax($_REQUEST['instance_id'], 0);
}elseif($_REQUEST['citation_id']){
	$lCon = new DBCn();
	$lCon->Open();
	$lCon->Execute('SELECT document_id FROM pwt.citations WHERE id = ' . (int) $_REQUEST['citation_id']);
	checkIfDocumentIsLockedByTheCurrentUserForAjax(0, (int)$lCon->mRs['document_id']);
}

switch ($gAction) {
	case 'get_instance_field_citations' :
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT * FROM spGetInstanceFieldCitations(' . (int) $_REQUEST['instance_id'] . ', ' . (int) $_REQUEST['field_id'] . ', ' . (int) $_REQUEST['citation_type'] . ');';
		$lCon->Execute($lSql);

		$lResult = array();
		while(! $lCon->Eof()){
			$lResult[$lCon->mRs['citation_id']] = array(
				'citation_id' => (int) $lCon->mRs['citation_id'],
				'preview' => $lCon->mRs['preview'],
				'citation_mode' => (int) $lCon->mRs['citation_mode'],
				'citation_objects' => array_values(pg_unescape_array($lCon->mRs['citation_objects']))
			);
			$lCon->MoveNext();
		}
		displayAjaxResponse($lResult);
	case 'get_instance_citations' ://Get all the citations of the instance and its subinstances
		$lInstanceIds = array();
		$lCon = new DBCn();
		$lCon->Open();
		$lSql = 'SELECT c.id
			FROM pwt.document_object_instances p
			JOIN pwt.document_object_instances c ON c.document_id = p.document_id AND c.pos ILIKE (p.pos || \'%\')
			WHERE p.id = ' . (int) $_REQUEST['instance_id'] . '
		';
		
		$lCon->Execute($lSql);
		while(! $lCon->Eof()){
			$lInstanceIds[] = $lCon->mRs['id'];
			$lCon->MoveNext();
		}
			
		$lSql = 'SELECT * FROM spGetInstanceCitations(' . (int) $_REQUEST['instance_id'] . ');';
		$lCon->Execute($lSql);
		$lResult = array();
		while(! $lCon->Eof()){
			$lRow = $lCon->mRs;
			$lInstanceId = $lRow['instance_id'];
			$lFieldId = $lRow['field_id'];
			$lCitationType = $lRow['citation_type'];
				
			
			if(!array_key_exists($lResult, $lInstanceId)){
				$lResult[$lInstanceId] = array();
			}
			if(!array_key_exists($lResult[$lInstanceId], $lFieldId)){
				$lResult[$lInstanceId][$lFieldId] = array();
			}
			if(!array_key_exists($lResult[$lInstanceId][$lFieldId], $lCitationType)){
				$lResult[$lInstanceId][$lFieldId][$lCitationType] = array();
			}
			$lResult[$lInstanceId][$lFieldId][$lCitationType][$lCon->mRs['citation_id']] = array(
				'citation_id' => (int) $lCon->mRs['citation_id'],
				'preview' => $lCon->mRs['preview'],
				'citation_mode' => (int) $lCon->mRs['citation_mode'],
				'citation_objects' => array_values(pg_unescape_array($lCon->mRs['citation_objects']))
			);
			$lCon->MoveNext();
		}
		$lResult = array(
			'instance_ids' => $lInstanceIds,
			'citations' => $lResult,
		);
		displayAjaxResponse($lResult);
	case 'delete_citation' :
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute('SELECT * FROM spDeleteCitation(' . (int) $_REQUEST['citation_id'] . ', ' . (int) $user->id . ');');
		exit();
	case 'save_citation' :
		$lCon = new DBCn();
		$lCon->Open();
		$lCitationObjects = $_REQUEST['citation_objects'];
		if(! is_array($lCitationObjects) || ! count($lCitationObjects)){
			$lResult = array(
				'err_cnt' => 1,
				'err_msg' => getstr('pwt.youCantCreateEmptyCitations')
			);
			displayAjaxResponse($lResult);
		}
		$lSql = 'SELECT * FROM spSaveCitation(' . (int) $_REQUEST['citation_id'] . ', ' . (int) $_REQUEST['instance_id'] . ', ' . (int) $_REQUEST['field_id'] . ', ' . (int) $_REQUEST['citation_type'] . ', ' . (int) $_REQUEST['citation_mode'] . ', ARRAY[' . implode(', ', $lCitationObjects) . ']::int[], ' . (int) $user->id . ');';
		$lCon->Execute($lSql);
// 		var_dump($lSql);

		//var_dump($lCon->GetLastError());
		$lResult = array(
			'citation_id' => (int) $lCon->mRs['citation_id'],
			'preview' => $lCon->mRs['preview'],
			'citation_mode' => (int) $lCon->mRs['citation_mode'],
			'citation_objects' => array_values(pg_unescape_array($lCon->mRs['citation_objects']))
		);
		displayAjaxResponse($lResult);

}

?>