<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gDocumentId = (int) $_REQUEST['document_id'];

$lResult = array(
	'err_msg' => '',
	'html' => '',
);

if($gDocumentId){
	$gObjects = new cdocument_tables(array(
		'ctype' => 'cdocument_tables',
		'document_id' => $gDocumentId,
		'templs' => array(
			G_HEADER => 'global.empty',
			G_ROWTEMPL => 'tables.single_table_preview',
			G_FOOTER => 'global.empty',
			G_NODATA => 'tables.empty_row'
		),
		'sqlstr' => '
				SELECT *, instance_id as id
				FROM spGetDocumentTables(' . (int) $gDocumentId . ')
				ORDER BY pos ASC
			'
	));
	$gObjects->GetData();
	$lResult['html'] = $gObjects->Display();
	displayAjaxResponse($lResult);
}
?>