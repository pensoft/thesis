<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');


$gInstanceId = (int) $_REQUEST['instance_id'];
$gDocumentId = (int) $_REQUEST['document_id'];
$gRevisionId = (int) $_REQUEST['revision_id'];

$gDontRedirectAgain = 1;

if(!$gDocumentId){
	header('Location:/index.php');
}

$lDocumentIdByInstanceId = getInstanceDocumentId($gInstanceId);

if( (int)$lDocumentIdByInstanceId != $gDocumentId ){
	$gInstanceId = 0;
}

if(! $gInstanceId && $gDocumentId){
	$gInstanceId = getDocumentFirstInstanceId($gDocumentId);
}

$gDocument = new cdisplay_document(
	array(
		'ctype' => 'cdisplay_document',
		'instance_id' => $gInstanceId,
		'get_data_from_request' => false,
		'get_object_mode_from_request' => false,
		'lock_operation_code' => LOCK_AUTO_LOCK,
		'templs' => getDocumentDefaultTempls(),
		'field_templs' => getDocumentFieldDefaultTempls(),
		'container_templs' => getDocumentContainerDefaultTempls(),
		'instance_templs' => getDocumentInstanceDefaultTempls(),
		'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
		'action_templs' => getDocumentActionsDefaultTempls(),
		'tabbed_element_templs' => getDocumentTabbedElementDefaultTempls(),
		'tree_templs' => getDocumentTreeDefaultTempls(),
		'path_templs' => getDocumentPathDefaultTempls(),
		'dont_redir_to_view' => $gDontRedirectAgain,
		'comments_templ' => 'comments',
		'comments_in_preview_mode' => 1,
		'comments_form_templ' => 'commentform',
		'preview_mode' => 1,
	)
);


$gDocumentId = $gDocument->getDocumentId();
$gInstanceId = $gDocument->getInstanceId();
$gXSLPath = $gDocument->getDocumentXSLDirName();

if($gInstanceId == getDocumentMetadataInstanceId($gDocumentId)){
	header("Location: /create_document.php?tAction=edit&document_id=" . (int) $gDocumentId);
	exit();
}

$lPageArray = array(
	'title' => strip_tags($gDocument->getDocumentName()) . ' - ',
	'content' => array(
		'ctype' => 'csimple',
		'templs' => array(
			G_DEFAULT => 'preview.content_revision',
		),
		//~ 'preview' => getDocumentPreview($gDocumentId, 0, $gXSLPath),
		'document_id' => $gDocumentId,
		'template_xsl_path' => $gXSLPath,
		'revision_id' => $gRevisionId,
	),
	'revisions' => array(
		'ctype' => 'crs',
		'templs' => array(
			G_HEADER => 'preview.revisions_head',
			G_STARTRS => 'preview.revisions_startrs',
			G_ROWTEMPL => 'preview.revisions_row',
			G_ENDRS => 'preview.revisions_endrs',
			G_FOOTER => 'preview.revisions_foot',
			G_NODATA => 'preview.revisions_empty'
		),
		'sqlstr' => 'SELECT dr.*, u.first_name || \' \' || u.last_name as fullname, u.photo_id
						FROM pwt.document_revisions dr
						JOIN usr u ON u.id = dr.createuid
						WHERE document_id = ' . (int)$gDocumentId . '
						ORDER BY createdate DESC',
		'template_xsl_path' => $gXSLPath,
		'document_id' => $gDocumentId,
	),
	//~ 'commentform' => $gDocument->GetVal('commentform'),
	//~ 'comments' => $gDocument->GetVal('comments'),
	'path' => $gDocument->getDocumentPath(),
	'tree' => ($gDocument->getDocumentIsLock() ? '' : $gDocument->getDocumentTree()),
	'document_id' => $gDocumentId,
	'document_is_locked' => $gDocument->getDocumentIsLock(),
	'document_lock_usr_id' => $gDocument->getDocumentLockUserId(),
	'preview_mode' => 1,
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(
	G_MAINBODY => 'global.document_revisions_page'
));
$inst->Display();

?>