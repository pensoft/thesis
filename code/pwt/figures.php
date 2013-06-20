<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gInstanceId = (int) $_REQUEST['instance_id'];
$gDocumentId = (int) $_REQUEST['document_id'];

if(! $gInstanceId && $gDocumentId){
	$gInstanceId = getDocumentFirstInstanceId($gDocumentId);
}

if(! $gInstanceId){
	header('Location: /index.php');
	exit();
}

$gGetDataFromRequest = false;
$gGetObjectsModeFromRequest = false;
$lSaveMsg = '';
$lFieldValidationInfo = array();
if($_REQUEST['perform_save_action'] && $gDocumentId){
	$gGetObjectsModeFromRequest = true;
	$lSaveDocument = new cdocument_saver(array(
		'document_id' => $gDocumentId,
		'root_instance_id' => $gInstanceId,
		'instance_ids' => $_REQUEST['instance_ids']
	));
	$lSaveDocument->GetData();
	$gGetDataFromRequest = true;
	if((int) $lSaveDocument->hasErrors()){
		$lSaveMsg = $lSaveDocument->GetErrorMsg();
		if($lSaveDocument->HasValidationErrors()){
			$lFieldValidationInfo = $lSaveDocument->GetFieldValidationInfo();
		}
	}else{
		$gGetDataFromRequest = false;
		$lSaveMsg = getstr('pwt.save.successfulSaveMsg');
		saveDocumentXML($gDocumentId);
	}
}
$lCon = new DBCn();
$lCon->Open();

$lSql = '
SELECT min(move_position) as min, max(move_position) as max
FROM pwt.media
WHERE document_id = ' . $gDocumentId . ';
';
$lCon->Execute($lSql);
$lMinPos = (int)$lCon->mRs['min'];
$lMaxPos = (int)$lCon->mRs['max'];

$lFigures = new crs(array(
	'ctype' => 'crs',
	'document_id' => $gDocumentId,
	'templs' => array(
		G_HEADER => 'figures.document_figures_head',
		G_ROWTEMPL => 'figures.document_figures_row',
		G_FOOTER => 'figures.document_figures_foot',
		G_NODATA => 'global.empty'
	),
	'max_position' => $lMaxPos,
	'min_position' => $lMinPos,
	'sqlstr' => '
			(SELECT
					m.id as photo_id,
					m.document_id,
					m.plate_id,
					m.link,
					null as format_type,
					null as photo_ids_arr,
					null as photo_positions_arr,
					m.title as photo_title,
					m.description as photo_desc,
					m.position,
					m.move_position,
					null as plate_desc,
					null as plate_title,
					m.lastmod,
					m.ftype
				FROM pwt.media m
				WHERE m.plate_id IS NULL AND m.document_id = ' . (int) $gDocumentId . ' AND m.ftype <> 1
			UNION
			SELECT
					null as photo_id,
					max(m.document_id) as document_id,
					m.plate_id,
					null as link,
					max(p.format_type) as format_type,
					array_agg(m.id) as photo_ids_arr,
					array_agg(m.position) as photo_positions_arr,
					null as photo_title,
					null as photo_desc,
					null as position,
					max(m.move_position),
					max(p.description) as plate_desc,
					max(p.title) as plate_title,
					max(p.lastmod) as lastmod,
					null as ftype
				FROM pwt.media m
				JOIN pwt.plates p ON p.id = m.plate_id
				WHERE m.document_id = ' . (int) $gDocumentId . ' AND m.ftype <> 1
				GROUP BY m.plate_id
				)
			ORDER BY move_position ASC
		'
));
$lFigures->GetData();
$lFiguresData = $lFigures->Display();

$gDocument = new cdisplay_document(array(
	'ctype' => 'cdisplay_document',
	'instance_id' => $gInstanceId,
	'get_data_from_request' => $gGetDataFromRequest,
	'get_object_mode_from_request' => $gGetObjectsModeFromRequest,
	'save_msg' => $lSaveMsg,
	'lock_operation_code' => LOCK_AUTO_LOCK,
	'field_validation_info' => $lFieldValidationInfo,
	'templs' => getDocumentFiguresTempls(),
	'field_templs' => getDocumentFieldDefaultTempls(),
	'container_templs' => getDocumentContainerDefaultTempls(),
	'instance_templs' => getDocumentInstanceDefaultTempls(),
	'custom_html_templs' => getDocumentCustomHtmlItemsDefaultTempls(),
	'action_templs' => getDocumentActionsDefaultTempls(),
	'tree_templs' => getDocumentTreeDefaultTempls(),
	'path_templs' => getDocumentPathDefaultTempls(),
	'comments_templ' => 'comments',
	'comments_form_templ' => 'commentform',
	'figures_structure' => $lFiguresData
));

$gDocumentId = $gDocument->getDocumentId();
$gInstanceId = $gDocument->getInstanceId();

$lPageArray = array(
	'document' => $gDocument,
	'path' => $gDocument->getDocumentPath(),
	'document_id' => $gDocumentId
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(
	G_MAINBODY => 'global.document_page'
));
$inst->Display();
?>