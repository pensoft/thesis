<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$gDocumentId = $_REQUEST['documentid'];

$lResult = array(
	'err_cnt' => 0,
	'err_msg' => '',
);
if(!$gDocumentId){
	$lResult['err_cnt']++;
	$lResult['err_msg'] = getstr('pwt.documentIdIsRequired');
	displayAjaxResponse($lResult);
}

$lCountComments = countDocumentComments($gDocumentId);
/*
	Ако нямаме коментари сменяме темплейта - добавяме секциите за разпъване горе и долу
*/
if( !$lCountComments ){
	$lRowTemplate = 'comments.firstPreviewCommentAjax';
	$lResult['is_first'] = 1;
}else{
	$lRowTemplate = 'comments.previewCommentAjax';
	$lResult['is_first'] = 0;
}

$gStartInstanceId = $_REQUEST['start_instance_id'];
if(!$gStartInstanceId){
	$_REQUEST['instanceid'] = getDocumentMetadataInstanceId($gDocumentId);
}else{
	$_REQUEST['instanceid'] = $gStartInstanceId;
}
//$_REQUEST['instanceid'] = getDocumentFirstInstanceId($gDocumentId);
$lCommentForm = new ccomments(array(
	'showtype' => 1,
	'instance_id' => null,
	'document_id' => $gDocumentId,
	//'comments_in_preview_mode' => 1,
	'use_as_ajax_srv' => 1,
));

// $lCommentForm->GetData();

if($lCommentForm->form->KforErrCnt()){
	$lResult['err_cnt']++;
	$lResult['err_msg'] = $lCommentForm->form->GetErrStr();
}else{
	$lCommentId = $lCommentForm->form->getKforVal('comment_id');
	if(!$lCommentId){
		$lResult['err_cnt']++;
		$lResult['err_msg'] = getstr('pwt.couldNotCreateComment');
	}else{
		//Ако не занулим REQUESTA - ще сложим reply на коментара и ще го попълним
// 		$_REQUEST = array();
		$lComment = new crs(
			array('ctype'=>'crs',
				'templs'=>array(
					G_HEADER=>'',
					G_ROWTEMPL=> $lRowTemplate,
					G_FOOTER =>'',
					G_NODATA =>'',
				),
				'preview_is_readonly' => false,
				'sqlstr'=>'SELECT m2.id id,
						m2.document_id document_id,
						m2.root_object_instance_id instance_id,
						m2.author author,
						m2.msg msg,
						m2.rootid rootid,
						m2.subject subject,
						m2.usr_id usr_id,
						m2.lastmoddate lastmoddate,
						u.photo_id photo_id,
						u.first_name || \' \' || u.last_name as fullname,
						m2.mdate mdate,
						coalesce(m2.start_object_instances_id, 0) as start_instance_id,
						coalesce(m2.end_object_instances_id, 0) as end_instance_id,
						coalesce(m2.start_object_field_id, 0) as start_field_id,
						coalesce(m2.end_object_field_id, 0) as end_field_id,
						coalesce(m2.start_offset, 0) as start_offset,
						coalesce(m2.end_offset, 0) as end_offset,
						m2.is_resolved::int as is_resolved,
						m2.resolve_uid,
						coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as resolve_fullname,
						m2.resolve_date
				FROM pwt.msg m1
				JOIN pwt.msg m2 ON (m1.id = m2.rootid)
				JOIN usr u ON m2.usr_id = u.id
				LEFT JOIN usr u2 ON m2.resolve_uid = u2.id
				JOIN usr_titles ut ON ut.id = u.usr_title_id
				WHERE m2.rootid = ' .  (int)$lCommentId . '
				ORDER BY m2.rootid, m2.mdate
				LIMIT 1',
			)
		);
// 		exit;
		$lResult['result'] = $lComment->Display();
		$lResult['start_instance_id'] = $lComment->GetVal('start_instance_id');
		$lResult['start_field_id'] = $lComment->GetVal('start_field_id');
		$lResult['start_offset'] = $lComment->GetVal('start_offset');

		$lResult['end_instance_id'] = $lComment->GetVal('end_instance_id');
		$lResult['end_field_id'] = $lComment->GetVal('end_field_id');
		$lResult['end_offset'] = $lComment->GetVal('end_offset');
		$lResult['comment_id'] = $lComment->GetVal('id');
	}
}
displayAjaxResponse($lResult);
?>