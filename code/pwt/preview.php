<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');


$gInstanceId = (int) $_REQUEST['instance_id'];
$gDocumentId = (int) $_REQUEST['document_id'];
// session_write_close();

$gDontRedirectAgain = 1;

$lDocumentState = getDocumentState($gDocumentId);

if(!$gDocumentId || $lDocumentState == DELETED_DOCUMENT_STATE){
	header('Location:/index.php');
	exit;
}

$lDocumentIdByInstanceId = getInstanceDocumentId($gInstanceId);

$lDocumentState = getDocumentState($lDocumentIdByInstanceId);
if($lDocumentState == DELETED_DOCUMENT_STATE){
	header('Location:/index.php');
	exit;
}


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
		'skip_tree_initializing' => 1,
	)
);


$gDocumentId = $gDocument->getDocumentId();
$gInstanceId = $gDocument->getInstanceId();
// var_dump($gInstanceId);
checkDocumentMenuAndColumnsState($gDocumentId);
MarkActiveTab($gInstanceId);
// $_SESSION['asd'] = 1;
session_write_close();
// exit;
$gXSLPath = $gDocument->getDocumentXSLDirName();

if($gInstanceId == getDocumentMetadataInstanceId($gDocumentId)){
	header("Location: /create_document.php?tAction=edit&document_id=" . (int) $gDocumentId);
	exit();
}

$gLoadTreeWithAjax = 1;
$gTree = '';
$gPageTemplate = 'global.editdocument_page_ajax_tree';
if(!$gLoadTreeWithAjax){
	$gTree = $gDocument->getDocumentTree();
	$gPageTemplate = 'global.editdocument_page';
}

$lPageArray = array(
	'title' => strip_tags($gDocument->getDocumentName()) . ' - ',
	'content' => array(
		'ctype' => 'csimple',
		'templs' => array(
			G_DEFAULT => 'preview.content',
		),
		'preview_header' => displayEditPreviewHeader($gDocumentId, getDocumentLatestRevisionId($gDocumentId), false),
		//~ 'preview' => getDocumentPreview($gDocumentId, 0, $gXSLPath),
		'document_has_unprocessed_changes' => checkIfDocumentHasUnprocessedChangesSimple($gDocumentId),
		'document_id' => $gDocumentId,
		'template_xsl_path' => $gXSLPath,
	),
	'commentform' => $gDocument->GetVal('commentform'),
	'comments' => $gDocument->GetVal('comments'),
	'path' => $gDocument->getDocumentPath(),
	'tree' => $gTree,
	'document_id' => $gDocumentId,
	'instance_id' => $gInstanceId,
	'document_is_locked' => $gDocument->getDocumentIsLock(),
	'document_lock_usr_id' => $gDocument->getDocumentLockUserId(),
	'preview_mode' => 1,
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(
	G_MAINBODY => $gPageTemplate
));
$inst->Display();

?>