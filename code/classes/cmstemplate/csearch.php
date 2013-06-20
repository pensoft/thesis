<?php

class csearch extends ct2search {
	function __construct($pFieldTempl) {
		parent::__construct($pFieldTempl);
		$this->m_pubdata['addwhere'] = ' AND s.state IN (3,4) AND sp.valint2 = 1 AND sp1.valint <>' . (int) HIDDEN_RUBRID . ' AND s.pubdate <= now()';
		$this->m_pubdata['sqlstr'] = '
			SELECT DISTINCT ON (s.pubdate::date, s.guid)
				s.*, ft.newstext, 	(case 
					when s.link is not null then s.link 
					else \'/show.php?storyid=\' || s.guid end
				) as link
			FROM stories s 
			JOIN storiesft ft USING (guid) 
			JOIN sid1storyprops sd USING (guid) 
			JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid IN (1,4) 
			JOIN storyproperties sp1 ON s.guid = sp1.guid AND sp1.propid = 4
			';
		$this->m_pubdata['orderby'] = ' ORDER BY s.pubdate::date DESC, s.guid, s.pubdate DESC';
	}
}

?>