<?php
$gDontRedirectToLogin = 1;
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gFigId = (int)$_REQUEST['fig_id'];

$lCon = new DBCn();
$lCon->Open();
$lSql = '
	SELECT c.id
	FROM pwt.document_object_instances i
	JOIN pwt.document_object_instances c ON c.parent_id = i.id AND c.object_id = ' . FIGURE_IMAGE_OBJECT_ID . '
	WHERE i.id = ' . (int) $gFigId . '
';

$lCon->Execute($lSql);
if((int)$lCon->mRs['id']){
	$gFigId = (int)$lCon->mRs['id'];
}


$lResult = new crs(array(
	'sqlstr' => '
		SELECT cfv.value_str as photo_desc, pfv.value_int as id -- Caption
		FROM pwt.instance_field_values cfv 
		JOIN pwt.instance_field_values pfv ON pfv.instance_id = cfv.instance_id AND pfv.field_id IN (483, 484)
		WHERE cfv.instance_id = ' . (int)$gFigId . ' and cfv.field_id in (482, 487)
	',
	'templs' => array(
		G_ROWTEMPL => 'figures.zoomed_fig'
	),
	
));

$lPageArray = array(
	'content' => $lResult,
);

$inst = new cpage(array_merge($lPageArray, DefObjTempl()), array(
	G_MAINBODY => 'global.zoomed_figure_page'
));
$inst->Display();
?>