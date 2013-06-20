<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gDocumentId = (int) $_REQUEST['document_id'];

$lResult = array(
	'err_msg' => '',
	'html' => ''
);

if($gDocumentId){
	$gObjects = new cdocument_references(array(
		'ctype' => 'cdocument_references',
		'document_id' => $gDocumentId,
		'object_id' => (int)REFERENCE_HOLDER_OBJECT_ID,
		'templs' => array(
			G_HEADER => 'global.empty',
			G_ROWTEMPL => 'references.single_reference_preview',
			G_FOOTER => 'global.empty',
			G_NODATA => 'references.empty_row'
		),
		'sqlstr' => '
				SELECT *, reference_instance_id as id
				FROM spGetDocumentReferences(' . (int) $gDocumentId . ')
				ORDER BY is_website_citation ASC, first_author_combined_name ASC, authors_count ASC, authors_combined_names ASC, pubyear ASC
			'
	));
	$gObjects->GetData();
	$lResult['html'] = $gObjects->Display();
	displayAjaxResponse($lResult);
}
?>