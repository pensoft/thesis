<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gFigId = (int)$_REQUEST['fig_id'];

$lResult = new crs(array(
	'sqlstr' => 'select max(a) as photo_desc, max(b) as id from (
						SELECT value_str as a, 0 as b
						FROM pwt.instance_field_values 
						WHERE instance_id = ' . (int)$gFigId . ' and field_id in (482, 487)
						union 
						SELECT \'\' as a, value_int as b
						FROM pwt.instance_field_values 
						WHERE instance_id = ' . (int)$gFigId . ' and field_id in (483, 484) ) as c',
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