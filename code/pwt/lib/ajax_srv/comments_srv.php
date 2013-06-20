<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
ini_set('display_errors', 'off');

$gDocumentId = $_REQUEST['documentid'];
$gInstanceId = (int)$_REQUEST['instanceid'];
$gMsg = $_REQUEST['msg'];
$gRootmsgid = (int)$_REQUEST['rootmsgid'];

//~ $gUserName = 'Mr Teodor Georgiev';
$gUserName = q(getUserName());

$lCon = new DBCn();
$lCon->Open();
$lSql = 'SELECT * FROM pwt.spCommentAdd(' . q($gRootmsgid) . ', ' . q($gInstanceId) . ', ' . q($gDocumentId) . ', \'' . $gUserName . '\',\'\', \'' . q($gMsg) . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\',' . (int) $user->id . ', null, null, null, null, null, null) as commentid';
$lCon->Execute($lSql);
$lCon->MoveFirst();
$gCommentId = (int)$lCon->mRs['commentid'];

$lComm = new crs(
	array('ctype'=>'crs',
		'templs'=>array(
			G_HEADER=>'', G_ROWTEMPL=>'comments.singlecomment', G_FOOTER =>'', G_NODATA =>'',
		),
		'sqlstr'=>'SELECT m.id id,
						m.document_id document_id,
						m.root_object_instance_id instance_id,
						m.author author,
						m.msg msg,
						m.rootid rootid,
						m.subject subject,
						m.usr_id usr_id,
						m.lastmoddate lastmoddate,
						u.photo_id photo_id,
						u.first_name || \' \' || u.last_name as fullname,
						m.mdate mdate,
						m.is_resolved::int as is_resolved,
						m.resolve_uid,
						coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as resolve_fullname,
						m.resolve_date
				FROM pwt.msg m
				JOIN usr u ON m.usr_id = u.id
				LEFT JOIN usr u2 ON m.resolve_uid = u2.id
				JOIN usr_titles ut ON ut.id = u.usr_title_id
				WHERE m.root_object_instance_id =' .  q($gInstanceId). ' AND m.id = ' . $gCommentId . '
			LIMIT 1',
	)
);
$lComm->GetData();
echo $lComm->Display();
//~ displayAjaxResponse($lComm->Display());
?>