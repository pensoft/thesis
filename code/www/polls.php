<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lPollId = ( int ) $_REQUEST['id'];
$lResults = ( int ) $_GET['results'];

if( !(int) $lPollId ){
	$lContents = new cpoll (
		array(
			'ctype' => 'cpoll',
			'templs' => array(
				G_HEADER => 'polls.browseallhead',
				G_FOOTER => 'polls.browseallfoot',
				G_STARTRS => 'polls.browseallstart',
				G_NODATA => 'polls.browsenodata',
				G_ENDRS => 'polls.browseallend',
				G_ROWTEMPL => 'polls.browseallrow',
				G_SPLITHEADER => 'polls.browseallsplithead',
			),
			'posid' => 1,
			'pagesize' => 10,
			'usecustompn' => 1,
			'siteid' => ( int ) CMS_SITEID,
			'showtype' => 1,
			'formmethod' => 'post',
			'backurl' => $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'],
		)
	);
}else{
	$lPoll = new cpoll (
		array(
			'ctype' => 'cpoll',
			'templs' => array(
				G_STARTRS => 'polls.startrs',
				G_ENDRS => 'polls.endrs',
				G_ROWTEMPL => 'polls.rowtempl',
				G_ENDRSNOBUT => 'polls.endrsnobut',
				G_ANSINPUT => 'polls.ansinput',
				G_ANSRESULT => 'polls.ansresult',
				G_BACKBUTTON => 'polls.backbutton',
				G_NODATA => 'polls.shownodata',
			),
			'posid' => 1,
			'pollid' => ( int ) $lPollId,
			'siteid' => ( int ) CMS_SITEID,
			'formmethod' => 'post',
			'backurl' => $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'],
		)
	);
	$lContents = $lPoll->Display();
}

$t = array(
	'contents' => $lContents,
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();
?>