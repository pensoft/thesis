<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

$rid = (int) $_GET['rubrid'];
$enc = (int) $_GET['enc'];


if ($enc) {
	$enc = 'windows-1251';
}else {
	$enc = 'UTF-8';
}

header('Content-type: text/xml; charset=' . $enc . '');


if ($rid) {
	$gSql = 
	'SELECT DISTINCT ON (s.guid) s.title, s.guid, st.valint, st.propid,
	(CASE WHEN s.link IS NOT NULL THEN s.link ELSE \'' . SITE_URL . '/\' || (CASE WHEN s.storytype=1 THEN \'gallery_\' ELSE \'\' END) || \'show.php?storyid=\' || s.guid END) as link
	FROM stories as s
	JOIN storyproperties as st on st.guid=s.guid and (st.propid=1 OR st.propid=4)
	JOIN storyproperties as sp on st.guid=s.guid and sp.propid=4
	JOIN rubr as main_rubr ON st.valint = main_rubr.id AND sp.propid = 4 
	JOIN rubr as r on st.valint='. $rid .' and r.state IN (0,1)
	WHERE s.state IN (3,4) AND main_rubr.id <>' . (int) HIDDEN_RUBRID . ';';		
}
else{
	
	$gSql=
	'SELECT s.*,
	(CASE WHEN s.link IS NOT NULL THEN s.link ELSE \'' . SITE_URL . '/\' || (CASE WHEN s.storytype=1 THEN \'gallery_\' ELSE \'\' END) || \'show.php?storyid=\' || s.guid END) as link
	FROM stories as s 
	JOIN storyproperties as sp on sp.guid=s.guid and sp.propid=4
	JOIN rubr as r ON sp.valint = r.id AND sp.propid = 4 
	WHERE  s.state IN (3,4) AND r.id <>' . (int) HIDDEN_RUBRID . ';';	
}

$rssAttrArray = array (
	'title' => SITE_URL,
	'encoding' => $enc,
	'sql' => $gSql,
	'authoremail' => 'info@svetidimitar.com',
);

$r = new crss($rssAttrArray);
echo $r->DisplayC();

?>