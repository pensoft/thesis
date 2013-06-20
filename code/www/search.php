<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$contents = new csearch(
	array(
		'ctype' => 'csearch',
		'allownull' => 0,
		'templs' => array(
			G_HEADER => 'search.head',
			G_FOOTER => 'search.foot',
			G_ROWTEMPL => 'search.row',
			G_STARTRS => 'search.startrs',
			G_ENDRS => 'search.endrs',
			G_NODATA => 'search.nodata',
		),
		'usecustompn' => 1,
		'pagesize' => 5,
		'shema' => 'bg_utf8',
		'vectors' => array('body'), // v koi vektori da tursi tsearch2
		'fields' => array('title', 'description', 'nadzaglavie', 'subtitle', 'author'), // ako ima frazi, v koi poleta da tursi s like
		'toparse' => array('title', 'description', 'nadzaglavie', 'subtitle'), // koi poleta da ocvetqva... trqbva da sa selektnati!!!
	)
);
$contents->debug = false;
$cntDispl = $contents->Display();

$t = array (
	'contents' => $cntDispl,
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();

?>