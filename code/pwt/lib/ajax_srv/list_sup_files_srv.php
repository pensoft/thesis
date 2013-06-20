<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gDocumentId = (int) $_REQUEST['document_id'];

$lResult = array(
	'err_msg' => '',
	'html' => ''
);

if($gDocumentId){
	$gObjects = new cdocument_supfiles(array(
		'ctype' => 'cdocument_supfiles',
		'document_id' => $gDocumentId,
		'object_id' => (int)REFERENCE_HOLDER_OBJECT_ID,
		'templs' => array(
			G_HEADER => 'global.empty',
			G_ROWTEMPL => 'sup_files.single_sup_file_preview',
			G_FOOTER => 'global.empty',
			G_NODATA => 'sup_files.empty_row'
		),
		'sqlstr' => '
				SELECT *, instance_id as id
				FROM spGetDocumentSupFiles(' . (int) $gDocumentId . ')
				ORDER BY pos ASC
			'
	));
	$gObjects->GetData();
	$lResult['html'] = $gObjects->Display();
	displayAjaxResponse($lResult);
}
?>