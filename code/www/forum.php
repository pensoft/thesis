<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lTopicid = (int)$_REQUEST['topicid'];
$lOrdBy = (int) $_GET['ordby'];

$lPageTpl = 'global.forumpage';

if (!(int)$lTopicid) {
	//List na vsi4ki temi ot izbranata diskusiq
	$lAllowedOrd = array(1, 2);
	if( ! (int) $lOrdBy || !in_array( (int) $lOrdBy, $lAllowedOrd ) )
		$lOrdBy = 1;
	if( (int) $lOrdBy ){
		switch ( (int) $lOrdBy ){
			case 1:{ // Title
				$lOrdBy = 2;
				break;
			}
			case 2:{ // Date
				$lOrdBy = 8;
				break;
			}
			default: break;
		
		}
	}
	$lForum = new cforum( 
		array(
			'ctype' => 'cforum',
			'showtype' => 1,
			'captcha' => 1,
			'requirelogin' => 0,
			'openforum' => true,
			'use_emticon' => 1,
			'usecustompn' => 1,
			'hidedefpaging' => 1,
			'pagesize' => 25,
			'ordby' => (int) $lOrdBy,
			'sorttypedef' => (int)$_REQUEST['ord'] ? 'DESC' : 'ASC',
			'dscid' => 2,
			'dsggroup' => 2,
			'returl' => '/forum.php',
			'templs' => array(
				G_HEADER => 'forum.topiclist_head',
				G_STARTRS => 'forum.topiclist_start',
				G_ENDRS => 'forum.topiclist_end',
				G_FOOTER => 'forum.topiclist_footer',
				G_ROWTEMPL => 'forum.topiclist_row',
				G_NODATA => 'forum.topiclist_nodata',
			),
		)
	);
	
} else {
	//Pregled na tema
	
	$lForum = new cforum( 
		array(
			'ctype' => 'cforum',
			'showtype' => 2,
			'captcha' => 1,
			'requirelogin' => 0,
			'openforum' => true,
			'use_emticon' => 1,
			'usecustompn' => 1,
			'hidedefpaging' => 1,
			'pagesize' => 15,
			'sorttypedef' => (int)$_REQUEST['ord'] ? 'DESC' : 'ASC',
			'topicid' => $lTopicid,
			'dscid' => 2,
			'dsggroup' => (int)2,
			'pid' => (int)$lPid,
			'returl' => '/forum.php?topicid=' . (int)$lTopicid . '&view=1',
			'templs' => array(
				G_STARTRS => 'forum.msglist_single_start',
				G_ENDRS => 'forum.msglist_end',
				G_ROWTEMPL => 'forum.msglist_row',
				G_NODATA => 'forum.msglist_nodata',
				G_ROWTEMPL_HIDDEN => 'forum.msglist_hidden',
			),
		)
	);
}

$t = array(
	'pagetitle' => 'Discussion Groups',
	'contents' => $lForum,
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();
?>