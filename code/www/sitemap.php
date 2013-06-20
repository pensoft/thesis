<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

header('Content-type: text/xml; charset=UTF-8');

$sitemap = new csitemap(array(
	'sql' => array(
		// Obshti
		0 => '
			SELECT \'' . SITE_URL . '\' as url, 1 as priority, \'daily\' as changefreq
		',
		// Rubriki
		1 => '
			SELECT \'' . SITE_URL . '/browse.php?rubrid=\' || id as url, 0.8 as priority, \'daily\' as changefreq, pos
			FROM rubr 
			WHERE sid = 1 AND state = 1 AND id = rootnode  
			ORDER BY pos',
		// Statii
		2 => 'SELECT DISTINCT ON (s.guid)
				(case 
					when s.link is not null then s.link
					else \'' . SITE_URL . '/show.php?storyid=\' || s.guid end) as url,
					0.8 as priority, \'daily\' as changefreq
			FROM stories s
			left JOIN sites si ON (si.guid = s.primarysite)
			left JOIN storyproperties sp ON s.guid = sp.guid
			left JOIN rubr r on r.id = sp.valint AND sp.propid = 4
			left JOIN sid1storyprops sid on s.guid = sid.guid			
			WHERE s.pubdate < now() AND s.state IN (3,4)
			ORDER BY s.guid DESC',
	),
));

echo $sitemap->Display();

?>