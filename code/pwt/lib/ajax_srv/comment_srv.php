<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$gDocumentId = $_REQUEST['documentid'];
$gInstanceId = (int)$_REQUEST['instanceid'];
$gMsg = $_REQUEST['msg'];
$gRootmsgid = (int)$_REQUEST['rootmsgid'];

$gAction = $_REQUEST['action'];
$gResult = array(
	'err_msgs' => array(),
	'err_cnt' => 0,
);
switch ($gAction) {
	default :
		$gResult['err_cnt'] ++;
		$gResult['err_msgs'][] = array(
			'err_msg' => getstr('pwt.unrecognizedAction')
		);
		break;
	case 'resolve_comment' :
		ResolveComment();
		break;
	case 'get_filtered_ids_list':
		GetDocumentFilteredRootIdsList();
		break;
}

displayAjaxResponse($gResult);

function ResolveComment(){
	global $gResult;
	global $user;

	$lCon = new DBCn();
	$lCon->Open();

	$lCommentId = (int) $_REQUEST['comment_id'];
	$lResolveId = (int) $_REQUEST['resolve'];
	$lSql = 'SELECT m.*, m.is_resolved::int as is_resolved,
		m.resolve_uid,
		coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as resolve_fullname,
		m.resolve_date
	FROM pwt.spResolveComment(' . $lCommentId . ',' . (int)$lResolveId . ',' . $user->id . ') m
	LEFT JOIN usr u2 ON m.resolve_uid = u2.id;';
	// 		var_dump($lCon->Execute($lSql));
	// 		var_dump($lSql);
	// 		var_dump($lCon->Execute($lSql));
	if(!$lCon->Execute($lSql)){
		$gResult['err_cnt']++;
		$gResult['err_msg'] = getstr($lCon->GetLastError());
	}else{
		$gResult['is_resolved'] = (int)$lCon->mRs['is_resolved'];
		$gResult['resolve_date'] = $lCon->mRs['resolve_date'];
		$gResult['resolve_fullname'] = $lCon->mRs['resolve_fullname'];
		$gResult['resolve_uid'] = (int)$lCon->mRs['resolve_uid'];
	}

}

function GetDocumentFilteredRootIdsList(){
	global $gResult;

	$lDocumentId = (int) $_REQUEST['document_id'];
	$lDisplayResolved = (int) $_REQUEST['display_resolved'];
	$lDisplayInline = (int) $_REQUEST['display_inline'];
	$lDisplayGeneral = (int) $_REQUEST['display_general'];
	$lFilterUsers = (int) $_REQUEST['filter_users'];
	$lSelectedUsers = $_REQUEST['selected_users'];
	$lCon = new DBCn();
	$lCon->Open();
	$lResult = array();

	$lSql = '
	SELECT DISTINCT m1.id
	FROM pwt.msg m1
	JOIN pwt.msg m2 ON (m1.id = m2.rootid)
	WHERE m2.document_id = ' . (int) $lDocumentId  . ' AND m2.revision_id = spGetDocumentLatestCommentRevisionId(' . (int) $lDocumentId  . ', 0)';

	if(!$lDisplayResolved){
		$lSql .= ' AND coalesce(m1.is_resolved, false) = false ';
	}

	if(!$lDisplayInline){
		$lSql .= ' AND (coalesce(m1.start_object_instances_id, 0) = 0
		AND coalesce(m1.end_object_instances_id, 0) = 0
		) ';
	}

	if(!$lDisplayGeneral){
		$lSql .= ' AND (coalesce(m1.start_object_instances_id, 0) <> 0
		OR coalesce(m1.end_object_instances_id, 0) <> 0
		) ';
	}

	if($lFilterUsers){
		if(!is_array($lSelectedUsers) || !count($lSelectedUsers)){
			$lSelectedUsers = array(0);
		}else{
			$lSelectedUsers = array_map('intval', $lSelectedUsers);
		}
		$lSql .= ' AND m2.usr_id IN (' . implode(',', $lSelectedUsers) . ') ';
	}

	// 		var_dump($pDisplayResolved, $pDisplayInline, $pDisplayGeneral);
// 	var_dump($lSql);
	$lCon->Execute($lSql);
	while(!$lCon->Eof()){
		$lResult[] = $lCon->mRs['id'];
		$lCon->MoveNext();
	}
	$gResult['visible_rootids'] = $lResult;
}
?>