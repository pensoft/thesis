<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$gDocumentId = (int)$_REQUEST['document_id'];
$lPageArray = array(
	'contents' => array(
		'sqlstr' => 'SELECT *
			FROM pwt.documents
			WHERE id = ' . (int)$gDocumentId . '
		',
		'document_id' => $gDocumentId,
		'ctype' => 'crs', 
		'templs'=> array(
			G_HEADER   => 'document.auto_unlocked_document',
			G_STARTRS => 'global.empty',
			G_ROWTEMPL => 'global.empty',
			G_FOOTER   => 'global.empty',
			G_NODATA   => 'global.empty',
		),
	),	
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(G_MAINBODY => 'global.simple_page'));
$inst->Display();
?>