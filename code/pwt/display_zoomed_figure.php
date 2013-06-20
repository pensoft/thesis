<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gFigId = (int)$_REQUEST['fig_id'];

$lResult = new crs(array(
	'sqlstr' => 'SELECT 	m.id, 
				m.document_id, 
				m.plate_id, 
				m.title as photo_title, 
				m.description as photo_desc, 
				m.position
			FROM pwt.media m
			WHERE m.id = ' . (int)$gFigId . '
			ORDER BY m.createdate ASC',
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