<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');
$rubrid= (int)$_REQUEST['rubrid'];
$t=array(
	'rubriki'=>array(
		'ctype'=>'crs', 
		'templs'=>array(
			G_HEADER=>'home.rubrheader',
			G_ROWTEMPL=>'home.rubrrowtempl',
			G_FOOTER =>'home.rubrfooter',
			G_ENDRS => 'global.empty',
			G_NODATA=>'global.empty',
			G_PAGING=>'global.empty',
			G_STARTRS => 'global.empty',
			
			
		),
		
		'sqlstr'=>' SELECT * from GetRubrSiblings('.$rubrid.','. (int)CMS_SITEID.','.getlang().')',
		'templadd'=>'ftype',
	),
	'browse' => array (
			'ctype' => 'crs',
			'sqlstr' => $rubrid ? 'SELECT * FROM SGGetRubrStories(' . $rubrid . ','.getlang().')' :'SELECT * FROM SGGetRubrStories('.getlang().')' ,
			'templs' => array(
				G_HEADER => 'global.empty',
				G_FOOTER => 'global.empty',
				G_STARTRS => 'global.empty',
				G_ENDRS => 'stories.browsepageing',
				G_ROWTEMPL => 'stories.browserow',
				G_NODATA => 'stories.browsenodata',
				G_PAGEING => 'global.empty',
			),
			'usecustompn' => 0,
			'pagesize' => 10,
		),
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.main'));
$inst->Display();
?>