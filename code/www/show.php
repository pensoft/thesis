<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$lStoryid = (int) $_GET['storyid'];


$t=array(
	'contents' => array(
		'ctype' => 'cstory2',
		'templs' => array(
			G_DEFAULT => 'stories.show',
			G_GALLERY => 'global.empty',
			G_PHOTO => 'stories.showphoto',
			G_GALPHOTO => 'global.empty',
			G_RGALPHOTO => 'global.empty',
			G_GALPREV => 'global.empty',
			G_GALNAV => 'global.empty',
			G_GALNEXT => 'global.empty',
			G_RELGAL => 'global.empty',
			G_BIGPHOTO => 'stories.showphoto',	
			G_RELINKHEADER => 'stories.relinkhead',
			G_RELINKROW => 'stories.rellinkrow',
			G_RELINKFOOTER => 'stories.relfoot',
			G_RELSTHEADER => 'stories.relsthead',
			G_RELSTROW => 'stories.relstrow',
			G_RELSTFOOTER => 'stories.relfoot',
			G_STORY_ATTACHMENTS => 'stories.showattrow',
			G_STORY_ATTACHMENTSMP3 => 'stories.attmp3',
			G_RELMEDIA_HEADER => 'stories.mediahead',
			G_RELMEDIA_FOOTER => 'stories.relfoot',
			G_STORY_ATTACHMENTS_HEADER => 'stories.attachmentshead',
			G_STORY_ATTACHMENTS_FOOTER => 'stories.relfoot',
			G_KEYHEADER => 'global.empty',
			G_KEYROW => 'global.empty',
			G_KEYFOOTER => 'global.empty',
			G_RESTRICTED => 'global.empty',			
		),
		
		'storysql' => 'SELECT s.*,
					r.id as rubrid, r.name as mainrubrname 
			FROM stories s
			left JOIN sites si ON (si.guid = s.primarysite)
			left JOIN storyproperties sp ON s.guid = sp.guid
			left JOIN rubr r on r.id = sp.valint AND sp.propid = 4 
			WHERE s.pubdate < now() AND r.id = ' . (int) HIDDEN_RUBRID . '  AND s.state IN (3,4)
				AND s.guid = '.$lStoryid ,
		'relatedsql' => 'GetStoryRelatedItems',
		'storyid' => 2,
		'photopref' => array(
			1 => 'sg198_',
			2 => 'sg198_',
			3 => 'd200x200_',
			4 => 'dx400_',
			10 => 'sg198_', // Tova za galeriq - golqma snimka
			11 => 'sg198_', // Tova za galeriq - thumbnail
		),	
	),
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.simplepage'));
$inst->Display();


?>