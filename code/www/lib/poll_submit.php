<?php

$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$type = (int)$_GET['type'];		// 1 = block, 2 = page, 3 = tema

function getajaxtempls($t) {
	if ($t == 1) return array (
		G_STARTRS => 'polls.startrs_ajax',
		G_ENDRS => 'polls.endrs_ajax',
		G_ROWTEMPL => 'polls.rowtempl',
		G_ENDRSNOBUT => 'polls.endrsnobut_ajax',
		G_ANSINPUT => 'polls.ansinput',
		G_ANSRESULT => 'polls.ansresult',
		G_BACKBUTTON => 'polls.backbutton',
	);
	
	if ($t == 2) return array (
		G_STARTRS => 'polls.leftcol_startrs',
		G_ENDRS => 'polls.leftcol_endrs',
		G_ROWTEMPL => 'polls.rowtempl',
		G_ENDRSNOBUT => 'polls.leftcol_endrsnobut',
		G_ANSINPUT => 'polls.ansinput',
		G_ANSRESULT => 'polls.ansresult',
		G_BACKBUTTON => 'polls.backbutton',
	);
	
	return array();
}

$p = new cpoll (
	array(
		'ctype' => 'cpoll',
		'colwidth' => ($type == 2) ? 120 : 200,
		'btnclass' => ($type == 1 ? 'gray' :''),
		'templs' => getajaxtempls($type),
		'siteid' => (int)CMS_SITEID,
	)
);

echo $p->Display();

?>