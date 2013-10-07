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
	case 'edit_comment':
		EditComment();
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

function EditComment(){
	global $gResult;
	try{
		$gCommentId = (int)$_REQUEST['comment_id'];
		$gDocumentId = (int)$_REQUEST['document_id'];
		if(!$gCommentId){
			throw new Exception(getstr('pwt.youHaveToSpecifyCommentId'));
		}
		$lComment = new ccomments( array (
				'ctype' => 'ccomments',
				'showtype' => 4,
				'comment_id' => $gCommentId,
				'document_id' => $gDocumentId,
				'use_as_ajax_srv' => 1,
				'formaction' => $_SERVER ['REQUEST_URI'],				
		) );
		$lForm = $lComment->form; 
		$lForm->GetData();
		if($lForm->KforErrCnt()){
			throw new Exception($lForm->GetErrStr());
		}		
		unset($_REQUEST['tAction']);
		unset($_REQUEST['kfor_name']);
		$lComment = new ccomments( array (
				'ctype' => 'ccomments',
				'showtype' => 5,
				'comment_id' => $gCommentId,
				'document_id' => $gDocumentId,				
		) );		
		$gResult['html'] = $lComment->GetVal('single_comment');
		$lSql = '
			SELECT CASE WHEN id = rootid THEN 1 ELSE 0 END as is_root,
				msg, (
					SELECT min(m1.id) as sub_id 
					FROM pwt.msg m1 
				WHERE m1.rootid = m.rootid AND m1.id <> m.id
			) 
			FROM pwt.msg m
			WHERE id = ' . (int)$gCommentId . '
		';
		$lCon = new DBCn();
		$lCon->Open();
		$lCon->Execute($lSql);
		$gResult['is_root'] = (int)$lCon->mRs['is_root'];
		$gResult['is_empty'] = (trim($lCon->mRs['msg']) == '') ? 1 : 0;
		$gResult['has_no_children'] = ((int)$lCon->mRs['sub_id'] == 0) ? 1 : 0;
		
	}catch(Exception $pException){
		$gResult['err_cnt'] ++;
		$gResult['err_msg']=  strip_tags($pException->getMessage());
	}
}
?>