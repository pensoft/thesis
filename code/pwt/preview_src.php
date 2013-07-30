<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
require_once(PATH_CLASSES . 'comments.php');

session_write_close();

//error_reporting(-1);
//ini_set('display_errors', 'On');

//error_reporting(-1);
//ini_set('display_errors', 'On');


$gDocumentId = (int)$_REQUEST['document_id'];
$gTrackFigures = (int)$_REQUEST['track_figures'];
$gXSLPath = $_REQUEST['template_xsl_path'];

$gRevisionId = (int)$_REQUEST['revision_id'];
// $gRevisionId = 2826;
$gShowRevision = (int)$_REQUEST['show_revision'];
$gRevisionXML = '';
$lHideTip = false;
if($gShowRevision){
	$gRevisionXML = getRevisionXML($gDocumentId, $gRevisionId);
	$lHideTip = true;
}


$lCanEditPreview = checkIfPreviewCanBeEdited($gDocumentId, $gRevisionId);
// $lCanEditPreview = false;
// var_dump($lCanEditPreview);
// $lCanEditPreview = true;


if(!$gRevisionId){
	$gRevisionId = getDocumentLatestRevisionId($gDocumentId);
}

if($gDocumentId){
	header("Content-type: text/html");
// 	error_reporting(-1);
	$lPutEditableJSAndCss = 1;
	$lPreview = getDocumentPreview($gDocumentId, 1, $gXSLPath, $gRevisionXML, $lCanEditPreview, $lHideTip, $lPutEditableJSAndCss, true, $gTrackFigures);

	$lPreviewHeader = displayEditPreviewHeader($gDocumentId, $gRevisionId);
	$lPreview = str_replace('<!--' . PREVIEW_EDITABLE_HEADER_REPLACEMENT_TEXT . '-->', $lPreviewHeader, $lPreview);

	echo $lPreview;
	exit();

}

?>