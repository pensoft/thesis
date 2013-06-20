<?php

/**
 * A model class for stories
 * @author peterg
 *
 */
class mStories_Model extends emBase_Model{
	/**
	 * Returns an array containing all the stories
	 * in the passed rubric. The format of the returned array is the following
	 * 		story_guid => story_data_arr
	 * And the format of story_data_arr is the following
	 * 		title => story title,
	 * 		subtitle => subtitle,
	 * 		previewpicid => the previewpic of the story
	 * 		pubdate => pubdate
	 * 		description => description
	 */
	function GetStoriesList($pRubrId, $pLanguage, $pLimit = 0){
		$lResult = array();
		$lCon = $this->m_con;
		$lSql = 'SELECT s.guid, s.title, s.subtitle, s.previewpicid, s.pubdate, s.description
										FROM getstoriesbyrubr(' . (int)$pRubrId . ', ' . (int)$pLanguage . ') s
										WHERE s.state IN (3, 4)
										ORDER BY s.pubdate DESC';
		
		if((int)$pLimit){
			$lSql .= ' LIMIT ' . $pLimit;
		}
		$lCon->Execute($lSql);
// 		var_dump($lSql);
		
		while(!$lCon->Eof()){
			$lResult[$lCon->mRs['guid']] = array(
				'guid' => (int)$lCon->mRs['guid'],
				'title' => $lCon->mRs['title'],
				'subtitle' => $lCon->mRs['subtitle'],
				'previewpicid' => (int)$lCon->mRs['previewpicid'],
				'pubdate' => $lCon->mRs['pubdate'],
				'description' => $lCon->mRs['description']
			);
			$lCon->MoveNext();
		}
// 		var_dump($lResult);
		return $lResult;
	}

	function GetStoriesFromOrderedList($pListId, $pLanguage){
		$lResult = array();
		$lLanguageName = getLanguageName($pLanguage);

		$lCon = $this->m_con;
		$lSql = 'SELECT s.guid, s.title, s.description, s.previewpicid, ' . getsqlang('r.name', $pLanguage) . ' as mainrubrname, r.id as rubrid, s.pubdate
			FROM stories s
			JOIN storyproperties sp ON s.guid = sp.guid
			JOIN rubr r on r.id = sp.valint AND sp.propid = 4
			JOIN listdets d ON d.objid = s.guid
			JOIN listnames l ON l.listnameid = d.listnameid
			WHERE s.state IN (3, 4) AND l.listnameid = ' . (int)$pListId . ' AND s.lang=\'' . q($lLanguageName) . '\'
			ORDER BY d.posid, s.pubdate DESC';
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$lResult[$lCon->mRs['guid']] = array(
				'title' => $lCon->mRs['title'],
				'subtitle' => $lCon->mRs['subtitle'],
				'previewpicid' => $lCon->mRs['previewpicid'],
				'pubdate' => 'pubdate',
				'description' => 'description'
			);
			$lCon->MoveNext();
		}
		return $lResult;
	}

	/**
	 * Returns an array containing all the data about a single story
	 * @param unknown_type $pStoryId
	 */
	function GetStoryDetails($pStoryId){

		$lCon = $this->m_con;
		$lCon2 = new DBCn();
		$lCon2->Open();

		$lSql = 'SELECT s.*, r.id as rubrid, r.name as mainrubrname, sd.journal_id
							FROM stories s
							left JOIN sid1storyprops sd ON sd.guid = s.guid
							left JOIN sites si ON (si.guid = s.primarysite)
							left JOIN storyproperties sp ON s.guid = sp.guid
							left JOIN rubr r on r.id = sp.valint AND sp.propid = 4
							WHERE s.pubdate < now() AND s.state IN (3,4)
								AND s.guid = ' . (int)$pStoryId;
		$lCon->Execute($lSql);
		$lResult = $this->m_con->mRs;

		$lRelatedItemsSql = 'SELECT sp.*, r.name as kwdname
					FROM GetStoryRelatedItems(' . (int)$pStoryId . ') sp
					LEFT JOIN rubr r ON r.sid = 18 AND r.state > 0 AND sp.valint = r.id AND sp.propid IN (14,15)
		';
		$lCon->Execute($lRelatedItemsSql);
		$lResult['related_items'] = array();
		while(!$lCon->Eof()){
			$lRelatedItem = $lCon->mRs;
			switch($lRelatedItem['propid']){
				case 6:{//Related galleries
					if($lRelatedItem['storytype'] != 1){
						break;
					}
					$lRelatedItem['pics'] = array();
					$lAddItemSql = 'SELECT * FROM GetStoryRelatedItems(' . $lRelatedItem['relguid'] . ') WHERE propid = 2';
					$lCon2->Execute($lAddItemSql);
					while($lCon2->Eof()){
						$lRelatedItem['pics'][] = $lCon2->mRs;
						$lCon2->MoveNext();
					}
					break;
				}
				case 15:{//Related themes
					if(!$lResult['sid']){
						break;
					}
					$lRelatedItem['themes'] = array();
					$lAddItemSql = 'SELECT DISTINCT ON (s.pubdate::date, s.pubdate, s.guid)
								s.guid,
								s.title,
								(case
									when s.link is not null then s.link
									when sd.linktype = 1 then si.storyurl || s.guid
									else \'' . $lResult['storyurl'] . '\' || s.guid
								end) as link
							FROM stories s
							JOIN sites si on si.guid = s.primarysite
							JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid = 15 AND sp.valint = ' . $lRelatedItem['valint'] . '
							JOIN sid' . $lResult['sid'] . 'storyprops sd ON s.guid = sd.guid
							WHERE s.state IN (3,4) AND s.pubdate <= now() AND s.guid <> ' . (int)$lResult['storyid'] . '
							ORDER BY s.pubdate::date DESC, s.pubdate DESC
							LIMIT 10
						';
					$lCon2->Execute($lAddItemSql);
					while($lCon2->Eof()){
						$lRelatedItem['themes'][] = $lCon2->mRs;
						$lCon2->MoveNext();
					}
				}
			}
			$lResult['related_items'][] = $lRelatedItem;
			$lCon->MoveNext();
		}
		return $lResult;
	}
	
	/**
	 * Returns the name of the rubr with the specified id
	 * @param unknown_type $pRubrid
	 * @param unknown_type $pLanguage
	 */
	function GetRubrName($pRubrid, $pLanguage){
		$lSql = 'SELECT ' . getsqlang('r.name', $pLanguage) . ' as name FROM rubr r WHERE r.id = ' . (int)$pRubrid;
		$this->m_con->Execute($lSql);
		return $this->m_con->mRs['name'];
	}
	
	function GetStoryIdsByStoryId($pStoryId){
		$lResult = array();
		$lSql = 'SELECT guid, pos, journal_id FROM sid1storyprops WHERE guid = ' . (int)$pStoryId;
		$lCon = $this->m_con;
		$lCon->Execute($lSql);
		if($lCon->mRs['guid']){
			if(strlen($lCon->mRs['pos']) > 2){
				$lPositionStr = substr($lCon->mRs['pos'], 0, -2);
			}else{
				$lPositionStr = $lCon->mRs['pos'];
			}
			$lSql = 'SELECT s.guid, s.title, sp.pos, sp.journal_id
						FROM sid1storyprops sp 
						JOIN stories s ON s.guid = sp.guid
						JOIN languages l ON s.lang = l.code
						WHERE sp.journal_id = ' . $lCon->mRs['journal_id'] . ' AND sp.pos LIKE \'' . $lPositionStr . '\' || \'%\'
							AND s.state IN (3,4)
							AND l.langid = ' . getlang() . '
							AND s.pubdate < current_timestamp
						ORDER BY sp.pos ASC';
			
			$lCon->Execute($lSql);

			while(!$lCon->Eof()){
				$lResult[] = array(
					'guid' 		 => $lCon->mRs['guid'],
					'title' 	 => $lCon->mRs['title'],
					'pos' 	 	 => $lCon->mRs['pos'],
					'journal_id' => $lCon->mRs['journal_id'],
				);
				$lCon->MoveNext();
			}
			return $lResult;
		}
		return 0;
	}
	
	function GetStoryIdsByRubrId($pRubrId){
		$lResult = array();
		$lSql = 'SELECT s.*, sp.pos FROM getstoriesbyrubr(' . (int)$pRubrId . ', ' . getlang() . ') s
					JOIN sid1storyprops sp ON sp.guid = s.guid
					ORDER BY sp.pos ASC';
		$lCon = $this->m_con;
		$lCon->Execute($lSql);
		if($lCon->mRs['guid']){
			while(!$lCon->Eof()){
				$lResult[] = array(
					'guid' 		 => $lCon->mRs['guid'],
					'title' 	 => $lCon->mRs['title'],
					'pos' 	 	 => $lCon->mRs['pos'],
					'journal_id' => $lCon->mRs['journal_id'],
				);
				$lCon->MoveNext();
			}
			return $lResult;
		}
		return 0;
	}
	
	function GetStoryIdsByJournalId($pJournalId){
		$lResult = array();
		$lSql = 'SELECT si.pos, s.title, s.guid, si.journal_id FROM sid1storyprops si
						JOIN stories s ON s.guid = si.guid
						WHERE journal_id = ' . (int)$pJournalId . ' 
							AND s.pubdate < now() AND s.state IN (3,4)
						ORDER BY pos ASC';
					
		$lCon = $this->m_con;
		$lCon->Execute($lSql);
		if($lCon->mRs['guid']){
			while(!$lCon->Eof()){
				$lResult[] = array(
					'guid' 		 => $lCon->mRs['guid'],
					'title' 	 => $lCon->mRs['title'],
					'pos' 	 	 => $lCon->mRs['pos'],
					'journal_id' => $lCon->mRs['journal_id'],
				);
				$lCon->MoveNext();
			}
			return $lResult;
		}
		return 0;
	}
}


?>