<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gDocumentId = (int) $_REQUEST['document_id'];

$lResult = array(
	'err_msg' => '',
	'html' => '',
);

if($gDocumentId){
	$lTables = new crs(
		array(
			'ctype'=>'crs',
			'document_id' => $gDocumentId,
			'templs'=>array(
				G_HEADER=>'global.empty',
				G_ROWTEMPL=>'tables.document_tables_row_baloon',
				G_FOOTER =>'global.empty',
				G_NODATA =>'tables.empty_row',
			),
			'sqlstr'=> 'SELECT * FROM pwt.tables WHERE document_id = ' . $gDocumentId . ' ORDER BY move_position',
		)
	);
	$lTables->GetData();
	$lResult['html'] = $lTables->Display();
	displayAjaxResponse($lResult);
}
?>