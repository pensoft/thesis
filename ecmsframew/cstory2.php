<?php

class cstory2 extends cbase {
	var $StoryType;
	var $sid;
	private $lm_togetdata = 1;
	
	function __construct($pFieldTempl) {
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];
		$this->sid = (int)$this->m_pubdata['sid'];
	}
	
	function CheckVals() {
		if($this->m_state == 0) {
			$this->m_state++;
		} else {
			// NOTICE
		}
	}
	
	function GetSecurity() {
		global $gAllowedIPsStory;
		
		$gUid = $this->m_pubdata['uid'];
		$gGrpid = $this->m_pubdata['grpid'];
		
		if ($this->m_pubdata['userights']) {
			$skiprights = 0;
			
			if ($this->m_pubdata['ipcheck']) {
				$skiprights = checkAllowedIP(getenv('REMOTE_ADDR'), $gAllowedIPsStory);
			} 
			
			if($this->m_pubdata['timecheck'] && !$skiprights) {
				
				$lastissue = getLastIssue();
				$curwt = date('w') * 24 + date('H');
				// ot vtornik 00 chasa do petuk 18:00
				$freeperiod = ((2 * 24) < $curwt) && ($curwt <= (5 * 24 + 17));
				if((!$freeperiod && $this->m_pubdata['broiid'] == $lastissue['id']) || $this->m_pubdata['broiid'] != $lastissue['id']) {
					$skiprights = 0;
				} else {
					$skiprights = 1;
				}
				
			}
			
		} else 
			$skiprights = 1;
		
		// tva zashto e tuk? i tuk li my e mqstoto, shtoto spored men ne.
		//~ $this->m_pubdata['description'] = $descr;
		
		//~ 1 - otvorena
		//~ 2 - zatvorena (samo za abonati e)
		//~ 3 - vinagi otvorena
		//~ 4 - bila e otvorena	
		//~ 5 - zatvorena (za abonati i registrirani)
		
		if (($this->m_pubdata['rights'] == 2 || $this->m_pubdata['rights'] == 4 || $this->m_pubdata['rights'] == 5) && $skiprights == 0) {
			
			if (!$gUid && $this->m_pubdata['rights'] == 5) { // za registrirani i abonirani
				$this->m_pubdata['nostory'] = 1;
				$this->m_pubdata['restrmsg'] = getstr('story.RestrNotLogged2');
				return 0;
			} elseif (!$gUid && $this->m_pubdata['rights'] != 5) { // samo za abonirani
				$this->m_pubdata['nostory'] = 1;
				$this->m_pubdata['restrmsg'] = getstr('story.RestrNotLogged');
				return 0;
			} elseif ($gGrpid < 1 && $this->m_pubdata['rights'] != 5) {
				$this->m_pubdata['nostory'] = 1;
				$this->m_pubdata['restrmsg'] = getstr('story.RestrNoSub');
				return 0;
			}
		}
		
		return 1;
	}
	
	function GetData() {
		global $storiespath;
		$this->CheckVals();
		
		if ($this->m_state == 1) {
			$cn = new DBCn;		
			$cn->Open();
			$cn->Execute($this->m_pubdata['storysql']);
			$cn->MoveFirst();
			
			if (!$cn->Eof()) {
				if ($cn->mRs['linktype'] && $cn->mRs['link']) {
					header('Location: ' . $cn->mRs['link']);
					exit;
				}
				if ($cn->mRs['linktype'] && $cn->mRs['storylink']) {
					header('Location: ' . $cn->mRs['storylink']);
					exit;
				}
				
				$this->m_pubdata += $cn->mRs;
				
				$this->m_pubdata['storyid'] = (int) $cn->mRs['guid'];
				$this->m_pubdata['storytitle'] = $cn->mRs['title'];
				$this->m_pubdata['storysubtitle'] = $cn->mRs['subtitle'];
				$this->m_pubdata['storysuptitle'] = $cn->mRs['nadzaglavie'];
				$this->m_pubdata['storyauthor'] = $cn->mRs['author'];
				$this->m_pubdata['storydate'] = $cn->mRs['pubdate'];
				$this->m_pubdata['keywordsnaked'] = $cn->mRs['keywords'];
				
				if (!(int)$this->m_pubdata['dontusefile']) {
					$lFileName = PATH_STORIES . $this->m_pubdata['storyid'] . '.html';
					if (!is_file($lFileName)) {
						touch($lFileName);
					}
					
					$this->m_pubdata['storycontent'] = file_get_contents($lFileName);
				}
					
					
				$this->m_pubdata['keywordsall'] = $this->GetKeyWords();
				
				$this->StoryType = $cn->mRs['storytype'];
				
				// ala bala za capital
				$this->m_pubdata['kmainrubr'] = $cn->mRs['rubrid'];
				$this->m_pubdata["krubr"] = $cn->mRs['krubr'];
				$this->m_pubdata['dropcapletter'] = $cn->mRs['fletter'];
				
				// tova se polzva samo za light za stati4nite stranici
				$this->m_pubdata['subrubr'] = $cn->mRs['subrubr'];
				
				// flag za foruma v careers
				$this->m_pubdata['showforum'] = $cn->mRs['showforum'];
				
				// clearvame description shtoto ako imash prava v statiqta nqma nujda da se pokazva
				//~ $this->m_pubdata['description'] = '';
				
				$this->m_pubdata['description'] = $cn->mRs['description'];
				
				if(!$this->GetSecurity()) {
					$this->m_state++;
					return;					
				}
				
				// Ako v body-to ima link kym stariq site(novina sys staro id) - redirectva kym noviq (s novoto id)
				$scontent = trim($this->m_pubdata['storycontent']);
				//~ if (substr($scontent,0,5) == 'link:http://') {
				if (preg_match('/link\:http\:\/\//',$scontent)) {
					if (preg_match('/\/evropa\/show\/Default.asp\?storyid=(\d+)/i',$scontent,$matches)) {
						$oldid = (int) $matches[1];
						//~ echo $oldid;
						$cnx = Con();
						$cnx->Execute('SELECT guid FROM stories where euimp_itemid = ' . $oldid);
						$cnx->MoveFirst();
						if (!$cn->Eof() && $cn->mRs['guid']) {
							header('Location: http://evropa.dnevnik.bg/show?storyid=' . $cn->mRs['guid']);
							exit;
						}
					}
				}
				
				$this->m_pubdata["rubrics"] = array();
				while (! $cn->Eof()) {
					if ($cn->mRs["propid"] == 4) {
						$this->m_pubdata["mainrubr"] = $cn->mRs["rubrid"];
						$this->m_pubdata['rootrubrid'] = $cn->mRs['rootrubrid'];
					}
					$cn->MoveNext() ;
				}
				
			} else {
				$this->m_pubdata['msg'] = 'Няма такава статия!<p>';
				$this->m_pubdata['nostory'] = 2;
				$this->m_state++;
				return;
			}
			
			
			// Svurzani neshta 
			if (isset($this->m_pubdata['relatedsql']) && !$this->m_pubdata['nostory']) {
				$cn->Execute('SELECT sp.*, r.name as kwdname 
					FROM ' . $this->m_pubdata['relatedsql'] . '(' . $this->m_pubdata['storyid'] . ') sp
					LEFT JOIN rubr r ON r.sid = 18 AND r.state > 0 AND sp.valint = r.id AND sp.propid IN (14,15)
				');

				$cn->MoveFirst();
			
				$lPhotoReplStr = array(1 => 'photostr', 2 => 'photostl', 3 => 'photosb', 4 => 'photosf');
			
				$this->RelGallery = 0;
				$this->m_pubdata['numpics'] = 0;
				$this->m_pubdata['keywords'] = array();
				$this->m_pubdata['themes'] = array();
				
				foreach($lPhotoReplStr as $lKey => $lPhotoType){
					$this->m_pubdata[$lPhotoType] = '';
				}
				
				while (!$cn->Eof()) {
					if ($cn->mRs['propid'] == 2) { // snimki
					
						$this->m_pubdata['type'] = $cn->mRs['valint2'];
					
						if ($this->StoryType == 1) { // Ako e galeriq
							$this->m_pubdata['numpics'] ++;
							$this->m_pubdata['photofname'] = SHOWIMG_URL . $this->m_pubdata['photopref'][10] . $cn->mRs['valint'] . '.jpg';
							$this->m_pubdata['thumbnail'] = SHOWIMG_URL . $this->m_pubdata['photopref'][11] . $cn->mRs['valint'] . '.jpg';
							$this->m_pubdata['bigphoto'] = SHOWIMG_URL . $this->m_pubdata['photopref'][4] . $cn->mRs['valint'] . '.jpg';
						} else { // Ako sa si obiknovenni svurzani snimki
							$this->m_pubdata['photofname'] = SHOWIMG_URL . $this->m_pubdata['photopref'][$this->m_pubdata['type']] . $cn->mRs['valint'] . '.jpg';
							
						}
					
						$this->m_pubdata['photodesc'] = $cn->mRs['valstr'];
						$this->m_pubdata['phototype'] = $cn->mRs['phototype'];
						$this->m_pubdata['photoclassname'] = 'boxRight';
						$this->m_pubdata['photoauthor'] = $cn->mRs['author'];
					
						if ($this->m_pubdata['type'] == 3) $this->m_pubdata['photoclassname'] = 'boxLeft';
					
						$this->m_pubdata['source'] = '';
						$this->m_pubdata['photoauthorpref'] = '';
					
						if ($cn->mRs['source']) $this->m_pubdata['source'] = '[' . $cn->mRs['source'] . ']';
					
						if ($this->m_pubdata['phototype'] == 1) {
							if ($cn->mRs['author']) $this->m_pubdata['photoauthorpref'] = 'Фотограф: ';
						} else {
							if ($cn->mRs['author']) $this->m_pubdata['photoauthorpref'] = 'Автор: ';
						}
					
						$this->m_pubdata['zoomlink'] = '';
						$this->m_pubdata['piczlstart'] = '';
						$this->m_pubdata['piczlend'] = '';
						if ($this->m_pubdata['phototype'] == 3) {
							$this->m_pubdata['zoomlink'] = '<a href="' . SHOWIMG_URL . 'oo_' . $cn->mRs['valint'] . '.jpg" target="_blank" class="zoom">Уголемяване</a>';
							$this->m_pubdata['piczlstart'] = '<a class="zlink" href="' . SHOWIMG_URL . 'oo_' . $cn->mRs['valint'] . '.jpg" target="_blank">';
							$this->m_pubdata['piczlend'] = '</a>';
						}
					
						if ($this->m_pubdata['photodesc']) {
							$this->m_pubdata['photodesc'] = $this->m_pubdata['photodesc'] . '<br/>';
						}
					
						if ($this->StoryType == 1) { // Ako e galeriq
							$this->m_pubdata['gallery'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_GALPHOTO));
							$this->m_pubdata['gallnav'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_GALNAV));
						} else { // Ako sa si obiknovenni svurzani snimki
							if ($cn->mRs['valint2'] == 4) {	// big photo
								$this->m_pubdata[$lPhotoReplStr[4]] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_BIGPHOTO));
								$this->m_pubdata['dropcapletter'] = '';
							} else {
								$this->m_pubdata[$lPhotoReplStr[$cn->mRs['valint2']]] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PHOTO));
							}
						}
					
					} elseif ($cn->mRs['propid'] == 6 && $cn->mRs['storytype'] == 1) { // related galleries	
						//~ var_dump($cn->mRs);
						//~ exit;
						$this->m_pubdata['relguid'] = $cn->mRs['relguid'];
						//~ $tmpCn = Con();
						$tmpCn = new DBCn;
						$tmpCn->Open();
						$tmpCn->Execute('SELECT * FROM ' . $this->m_pubdata['relatedsql'] . '(' . $this->m_pubdata['relguid'] . ')');
						$tmpCn->MoveFirst();
					
						$this->m_pubdata['gallprev'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_GALPREV));			
						$this->m_pubdata['gallnext'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_GALNEXT));	
						//~	var_dump($tmpCn->mRs);
						//~ exit;
						$this->m_pubdata['rnumpics'] = 0;
						while (!$tmpCn->Eof()) {
							if ($tmpCn->mRs['propid'] == 2) {
								$this->m_pubdata['rnumpics'] ++;
								$this->m_pubdata['photodesc'] = $tmpCn->mRs['valstr'];
								$this->m_pubdata['phototype'] = $tmpCn->mRs['phototype'];
								$this->m_pubdata['photoclassname'] = 'boxRight';
								$this->m_pubdata['photoauthor'] = $tmpCn->mRs['author'];
								$this->m_pubdata['photofname'] = SHOWIMG_URL . $this->m_pubdata['photopref'][1] . $tmpCn->mRs['valint'] . '.jpg';
								$this->m_pubdata['rg_photos'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_RGALPHOTO));
							}
							$tmpCn->MoveNext();
						}
					
						$this->m_pubdata['relgall'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_RELGAL));
					
					} elseif ($cn->mRs['propid'] == 3) { // related stories	
				
						$this->m_pubdata['relstoryid'] = $cn->mRs['relguid'];
						$this->m_pubdata['relstorylink'] = $cn->mRs['link'];
						$this->m_pubdata['relsttitle'] = $cn->mRs['title'];
						$this->m_pubdata['relstsubtitle'] = $cn->mRs['subtitle'];
						$this->m_pubdata['relstsuptitle'] = $cn->mRs['suptitle'];
						$this->m_pubdata['relstorytype'] = $cn->mRs['storytype'];
						$this->m_pubdata['relstorymainrubr'] = $cn->mRs['mainrubrid'];
						
						$cn2 = new DBCn;
						$cn2->Open();
						$cn2->Execute('SELECT guid, propid FROM storyproperties WHERE guid = '. $cn->mRs['relguid'] .' AND propid IN (12,13)');
						$cn2->MoveFirst();
						$this->m_pubdata['relaudio'] = $this->m_pubdata['relvideo'] = 0;
						while (!$cn2->Eof()) {
							if ($cn2->mRs['propid'] == 12) $this->m_pubdata['relaudio'] = 1;
							if ($cn2->mRs['propid'] == 13) $this->m_pubdata['relvideo'] = 1;
							$cn2->MoveNext();
						}
						
						$this->m_pubdata['relstories'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_RELSTROW));
					
					} elseif ($cn->mRs['propid'] == 9) { //related links
				
						$this->m_pubdata['relinktitle'] = $cn->mRs['valstr2'];
						$this->m_pubdata['relinkurl'] = $cn->mRs['valstr'];				
						$this->m_pubdata['relinks'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_RELINKROW));
					
					} elseif ($cn->mRs['propid'] == 5) { //attachmenti
						$this->m_pubdata['title'] = ($cn->mRs['valstr'] != '' ? $cn->mRs['valstr'] : $cn->mRs['ptitle']);
						$this->m_pubdata['valint'] = $cn->mRs['valint'];
						$this->m_pubdata['atthref'] = ATTCH_HREF;
						$this->m_pubdata['imgname'] = 'o_' . $cn->mRs['imgname'];
						$this->m_pubdata['type'] = 4;
						$this->m_pubdata['photoauthor'] = $cn->mRs['author'];
					
						$imgnfo = pathinfo($this->m_pubdata['imgname']);
						$ext = strtolower($imgnfo['extension']);
					
						if ($ext == 'mp3') {
							$this->m_pubdata['multimedia'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTSMP3));
						} elseif ($ext == 'flv') {
							$this->m_pubdata['dim_x'] = (int)$cn->mRs['dim_x'] ? (int)$cn->mRs['dim_x'] : '400';
							$this->m_pubdata['dim_y'] = (int)$cn->mRs['dim_y'] ? (int)$cn->mRs['dim_y'] : '320';
							$this->m_pubdata['multimedia'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTSFLV));
						} else {
							$this->m_pubdata['attachments'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTS));
						}
						
					} elseif ($cn->mRs['propid'] == 12) { // audio
						$this->m_pubdata['imgname'] = 'o_' . $cn->mRs['imgname'];
						$this->m_pubdata['title'] = ($cn->mRs['valstr'] != '' ? $cn->mRs['valstr'] : $cn->mRs['ptitle']);
						$this->m_pubdata['multimedia'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTSMP3));
						
					} elseif ($cn->mRs['propid'] == 13) { // video
						$this->m_pubdata['imgname'] = 'o_' . $cn->mRs['imgname'];
						$this->m_pubdata['videotext'] = $cn->mRs['valstr'];
						$this->m_pubdata['title'] = ($cn->mRs['valstr'] != '' ? $cn->mRs['valstr'] : $cn->mRs['ptitle']);
						$this->m_pubdata['dim_x'] = (int)$cn->mRs['dim_x'] ? (int)$cn->mRs['dim_x'] : '400';
						$this->m_pubdata['dim_y'] = (int)$cn->mRs['dim_y'] ? (int)$cn->mRs['dim_y'] : '320';
						$this->m_pubdata['multimedia'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTSFLV));
					
					} elseif ($cn->mRs['propid'] == 14) { //keywords
						// build na string s kluchovi dumi
						if ($cn->mRs['kwdname']) {
							$this->m_pubdata['word'] = trim($cn->mRs['kwdname']);
							$this->m_pubdata['kwdid'] = (int)$cn->mRs['valint'];
							if (preg_match('/^(.*)\s([АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЪЮЯ]+)$/', $this->m_pubdata['word'], $kwarr)) {
								$this->m_pubdata['wordtxt'] = $kwarr[2];
								$this->m_pubdata['wordtitle'] = $kwarr[1];
							} else {
								$this->m_pubdata['wordtxt'] = $this->m_pubdata['word'];
								$this->m_pubdata['wordtitle'] = '';
							}
							$this->m_pubdata['keywords'][] = $this->ReplaceHtmlFields($this->getObjTemplate(G_KEYROW));
						}
					} elseif ($cn->mRs['propid'] == 15 && $this->sid) { //temi
						$this->m_pubdata['kwdid'] = (int)$cn->mRs['valint'];
						
						$tmpCn = new DBCn;
						$tmpCn->Open();
						$tmpCn->Execute('SELECT DISTINCT ON (s.pubdate::date, s.pubdate, s.guid)
								s.guid,
								s.title,
								(case 
									when s.link is not null then s.link 
									when sd.linktype = 1 then si.storyurl || s.guid
									else \'' . $this->m_pubdata['storyurl'] . '\' || s.guid 
								end) as link
							FROM stories s 
							JOIN sites si on si.guid = s.primarysite
							JOIN storyproperties sp ON s.guid = sp.guid AND sp.propid = 15 AND sp.valint = ' . $this->m_pubdata['kwdid'] . '
							JOIN sid' . $this->sid . 'storyprops sd ON s.guid = sd.guid
							WHERE s.state IN (3,4) AND s.pubdate <= now() AND s.guid <> ' . (int)$this->m_pubdata['storyid'] . '
							ORDER BY s.pubdate::date DESC, s.pubdate DESC 
							LIMIT 10
						');
						$tmpCn->MoveFirst();
						while (!$tmpCn->Eof()) {
							$this->m_pubdata['tstorylink'] = $tmpCn->mRs['link'];
							$this->m_pubdata['tstorytitle'] = $tmpCn->mRs['title'];
							$this->m_pubdata['themes'][$tmpCn->mRs['guid']] = $this->ReplaceHtmlFields($this->getObjTemplate(G_THEMESROW));
							$tmpCn->MoveNext();
						}
					}				
					$cn->MoveNext();
				}
			}
			
			if (count($this->m_pubdata['themes'])) {
				$this->m_pubdata['themes'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_THEMESHEAD)) . 
				implode('', $this->m_pubdata['themes']) . $this->ReplaceHtmlFields($this->getObjTemplate(G_THEMESFOOT));
			} else {
				$this->m_pubdata['themes'] = '';
			}
			
			if (count($this->m_pubdata['keywords'])) {
				$this->m_pubdata['keywords'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_KEYHEADER)) . 
					implode(', ', $this->m_pubdata['keywords']) . $this->ReplaceHtmlFields($this->getObjTemplate(G_KEYFOOTER));
			} else {
				$this->m_pubdata['keywords'] = '';
			}
			
			if ($this->m_pubdata['attachments']) {
				$this->m_pubdata['attachments'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTS_HEADER)) . $this->m_pubdata['attachments'] . $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTS_FOOTER));
			}
			
			if ($this->m_pubdata['multimedia']) {
				$this->m_pubdata['multimedia'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_RELMEDIA_HEADER)) . $this->m_pubdata['multimedia'] . $this->ReplaceHtmlFields($this->getObjTemplate(G_RELMEDIA_FOOTER));
			}
			
			if ($this->m_pubdata['relinks']) {
				$this->m_pubdata['relinks'] =  $this->ReplaceHtmlFields($this->getObjTemplate(G_RELINKHEADER)) . $this->m_pubdata['relinks'] .  $this->ReplaceHtmlFields($this->getObjTemplate(G_RELINKFOOTER));
			}
			
			if ($this->m_pubdata['relstories']) {
				$this->m_pubdata['relstories'] =  $this->ReplaceHtmlFields($this->getObjTemplate(G_RELSTHEADER)) . $this->m_pubdata['relstories'] .  $this->ReplaceHtmlFields($this->getObjTemplate(G_RELSTFOOTER));
			}
			
			$this->m_state++;
		}
	}
	
	function GetKeyWords() {
		//replace-vam , i interval , s interval
		$lTitle = str_replace(",", " ", str_replace(", ", " ", $this->m_pubdata['storytitle']));
		$lSubTitle = str_replace(",", " ", str_replace(", ", " ", $this->m_pubdata['storysubtitle']));
		$lSupTitle = str_replace(",", " ", str_replace(", ", " ", $this->m_pubdata['storysuptitle']));
		//~ $lDesc = str_replace(",", " ", str_replace(", ", " ", $this->m_pubdata['description']));
		$lTmpSTr =   ($lTitle ? ", " . $lTitle : "") . ($lSubTitle ? ", " . $lSubTitle : "") . ($lSupTitle ? ", " . $lSupTitle : "") . ($lDesc ? ", " . $lDesc : "") . ($this->m_pubdata['keywordsnaked'] ? ", " . $this->m_pubdata['keywordsnaked'] : "");
		$lArr = split(' ', $lTmpSTr);
		foreach($lArr as $k) {
			$k = trim($k);
			$lRetStr .= $k . ' ';
		}
		return $lRetStr;
		//~ return substr($lRetStr, 0, 1800);
	}
	
	function Display() {
		$this->GetData();
		
		if($this->m_pubdata['nostory'] == 2) return $this->ReplaceHtmlFields($this->getObjTemplate(G_NOSTORY));
		
		if ($this->m_pubdata['nostory'] == 1) return $this->ReplaceHtmlFields($this->getObjTemplate(G_RESTRICTED));
		
		if ($this->m_state < 2) return $this->ReplaceHtmlFields($this->getTemplate(D_MSG));
		
		if ($this->StoryType == 1) {
			// Nachalo i krai na navigaciqta za galeriqta
			$this->m_pubdata['gallprev'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_GALPREV));			
			$this->m_pubdata['gallnext'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_GALNEXT));			
			// Nulirame navigaciata ako ima 1 ili po malko snimki
			if ($this->m_pubdata['numpics'] <= 1) $this->m_pubdata['gallnav'] = '';
			
			return $this->ReplaceHtmlFields($this->getObjTemplate(G_GALLERY));
		}
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));
	}
}
?>