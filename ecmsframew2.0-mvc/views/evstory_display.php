<?php

class evStory_Display extends evSimple_Block_Display {
	/**
	 * The data provided by the controller
	 *
	 * @var array
	 */
	var $m_controllerData;

	/**
	 * The id of the story
	 * @var int
	 */
	var $m_storyId;
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_controllerData = $pData['controller_data'];
		if(! is_array($this->m_controllerData)){
			$this->m_controllerData = array();
		}

		$this->m_storyId = (int)$this->m_controllerData['guid'];

		/**
		 * Import the data from the controller in the pubdata
		 */
		$this->processData();
	}

	function GetData() {

	}

	function processData(){
		$lData = $this->m_controllerData;


		if ($lData['linktype'] && $lData['link']) {
			header('Location: ' . $lData['link']);
			exit;
		}
		if ($lData['linktype'] && $lData['storylink']) {
			header('Location: ' . $lData['storylink']);
			exit;
		}

		$this->m_pubdata += $lData;

// 		var_dump($this->m_pubdata['previewpicid']);

		$this->m_pubdata['storyid'] = (int) $lData['guid'];
		$this->m_pubdata['storytitle'] = $lData['title'];
		$this->m_pubdata['storysubtitle'] = $lData['subtitle'];
		$this->m_pubdata['storysuptitle'] = $lData['nadzaglavie'];
		$this->m_pubdata['storyauthor'] = $lData['author'];
		$this->m_pubdata['storydate'] = $lData['pubdate'];

		if (!(int)$this->m_pubdata['dontusefile']) {
			$lFileName = PATH_STORIES . $this->m_pubdata['storyid'] . '.html';
			if (!is_file($lFileName)) {
				touch($lFileName);
			}

			$this->m_pubdata['storycontent'] = file_get_contents($lFileName);
		}


		$this->m_pubdata['keywordsall'] = $this->GetKeyWords();

		$this->StoryType = $lData['storytype'];


		// tova se polzva samo za light za stati4nite stranici
		$this->m_pubdata['subrubr'] = $lData['subrubr'];

		// flag za foruma v careers
		$this->m_pubdata['showforum'] = $lData['showforum'];


		$this->m_pubdata['description'] = $lData['description'];


	}

	/**
	 * Function that process story related items (related pics, stories, documents, videos)
	 * 
	 */
	function GetRelatedItems(){
		global $gMediaReplStr;

		//$this->m_pubdata['numpics'] = 0;

		foreach($gMediaReplStr as $lKey => $lMediaValArr){
			foreach ($lMediaValArr as $lMediaKey => $lMediaVal) {
				$this->m_pubdata[$lMediaVal] = '';	
			}
		}
		
		if(!empty($this->m_controllerData['related_items']))
			$this->m_pubdata['has_related_items'] = 1;
		else
			$this->m_pubdata['has_related_items'] = '';
		
		foreach ($this->m_controllerData['related_items'] as $lCurrentRelatedItem) {
			
			$this->m_pubdata['type'] = $lCurrentRelatedItem['valint2'];
			$this->m_pubdata['relvalint'] = $lCurrentRelatedItem['valint'];

			if ($lCurrentRelatedItem['propid'] == 2) { // snimki
				//~ print_r($lCurrentRelatedItem);
				
				/*if ($this->StoryType == GALLERY_STORYTYPE) { // Ako e galeriq
					$this->m_pubdata['numpics'] ++;
					$this->m_pubdata['photofname'] = SHOWIMG_URL . $this->m_pubdata['photopref'][10] . $lCurrentRelatedItem['valint'] . '.jpg';
					$this->m_pubdata['thumbnail'] = SHOWIMG_URL . $this->m_pubdata['photopref'][11] . $lCurrentRelatedItem['valint'] . '.jpg';
					$this->m_pubdata['bigphoto'] = SHOWIMG_URL . $this->m_pubdata['photopref'][4] . $lCurrentRelatedItem['valint'] . '.jpg';
				} else { // Ako sa si obiknovenni svurzani snimki
					$this->m_pubdata['photofname'] = SHOWIMG_URL . $this->m_pubdata['photopref'][$this->m_pubdata['type']] . $lCurrentRelatedItem['valint'] . '.jpg';
				}*/
				
				$this->m_pubdata['photofname'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_PHOTOFNAME));
				$this->m_pubdata['photodesc'] = $lCurrentRelatedItem['valstr'];
				$this->m_pubdata['phototype'] = $lCurrentRelatedItem['phototype'];
				$this->m_pubdata['photoauthor'] = $lCurrentRelatedItem['author'];
				

				/*if ($this->StoryType == GALLERY_STORYTYPE) { // Ako e galeriq
					$this->m_pubdata['gallery'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_GALPHOTO));
					$this->m_pubdata['gallnav'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_GALNAV));
				} else { // Ako sa si obiknovenni svurzani snimki
				 */
				if ($lCurrentRelatedItem['valint2'] == 4) {	// big photo
					$this->m_pubdata[$gMediaReplStr['photos'][4]] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_BIGPHOTO));
				} else {
					$this->m_pubdata[$gMediaReplStr['photos'][$lCurrentRelatedItem['valint2']]] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_PHOTO));
				}
				/*}
				 */
			
			} elseif ($lCurrentRelatedItem['propid'] == 6) { // related galleries
				//~ print_r($lCurrentRelatedItem);
				$this->m_pubdata['galtitle'] = ($lCurrentRelatedItem['valstr'] != '' ? $lCurrentRelatedItem['valstr'] : $lCurrentRelatedItem['title']);
				$this->m_pubdata['galpreviewpic'] = $lCurrentRelatedItem['previewpicid'];
				$this->m_pubdata['galid'] = $lCurrentRelatedItem['valint'];
				$this->m_pubdata['photofname'] = SHOWIMG_URL . $this->m_pubdata['photopref'][11] . $lCurrentRelatedItem['previewpicid'] . '.jpg';
				//~ foreach ($lCurrentRelatedItem['pics'] as $lCurrentRelatedPic){
					
					//~ if ($lCurrentRelatedPic['propid'] == 2) {
						//~ $this->m_pubdata['photodesc'] = $lCurrentRelatedPic['valstr'];
						//~ $this->m_pubdata['phototype'] = $lCurrentRelatedPic['phototype'];
						//~ $this->m_pubdata['photoclassname'] = 'boxRight';
						//~ $this->m_pubdata['photoauthor'] = $lCurrentRelatedPic['author'];
						//~ $this->m_pubdata['rg_photos'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_RGALPHOTO));
						
					//~ }
				//~ }
				//~ $this->m_pubdata['relgall'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_RELGAL));
				
				
				$this->m_pubdata[$gMediaReplStr['galleries'][$lCurrentRelatedItem['valint2']]] = $this->ReplaceHtmlFields($this->getObjTemplate(G_RELGAL));
				//~ var_dump($this->m_pubdata['relgall']);
			
			} elseif ($lCurrentRelatedItem['propid'] == 3) { // related stories

				$this->m_pubdata['relstoryid'] = $lCurrentRelatedItem['relguid'];
				$this->m_pubdata['relstorylink'] = $lCurrentRelatedItem['link'];
				$this->m_pubdata['relsttitle'] = $lCurrentRelatedItem['title'];
				$this->m_pubdata['relstsubtitle'] = $lCurrentRelatedItem['subtitle'];
				$this->m_pubdata['relstsuptitle'] = $lCurrentRelatedItem['suptitle'];
				$this->m_pubdata['relstorytype'] = $lCurrentRelatedItem['storytype'];
				$this->m_pubdata['relstorymainrubr'] = $lCurrentRelatedItem['mainrubrid'];

				$this->m_pubdata['relstories'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_RELSTROW));

			} elseif ($lCurrentRelatedItem['propid'] == 9) { //related links

				$this->m_pubdata['relinktitle'] = $lCurrentRelatedItem['valstr2'];
				$this->m_pubdata['relinkurl'] = $lCurrentRelatedItem['valstr'];
				$this->m_pubdata['relinks'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_RELINKROW));

			} elseif ($lCurrentRelatedItem['propid'] == 5) { //attachmenti
				$this->m_pubdata['title'] = ($lCurrentRelatedItem['valstr'] != '' ? $lCurrentRelatedItem['valstr'] : $lCurrentRelatedItem['ptitle']);
				$this->m_pubdata['atthref'] = ATTCH_HREF;
				$this->m_pubdata['imgname'] = $lCurrentRelatedItem['imgname'];
				$this->m_pubdata['type'] = 4;
				$this->m_pubdata['photoauthor'] = $lCurrentRelatedItem['author'];
				$this->m_pubdata['attachments'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTS));
			} elseif ($lCurrentRelatedItem['propid'] == 13) { // video
				//~ print_r($lCurrentRelatedItem);
				$this->m_pubdata['imgname'] = $lCurrentRelatedItem['imgname'];
				$this->m_pubdata['videotext'] = $lCurrentRelatedItem['valstr'];
				$this->m_pubdata['title'] = ($lCurrentRelatedItem['valstr'] != '' ? $lCurrentRelatedItem['valstr'] : $lCurrentRelatedItem['ptitle']);
				$this->m_pubdata['dim_x'] = (int)$lCurrentRelatedItem['dim_x'] ? (int)$lCurrentRelatedItem['dim_x'] : '400';
				$this->m_pubdata['dim_y'] = (int)$lCurrentRelatedItem['dim_y'] ? (int)$lCurrentRelatedItem['dim_y'] : '320';
				$this->m_pubdata['ftype'] = $lCurrentRelatedItem['ftype'];
				$this->m_pubdata['videoid'] = $lCurrentRelatedItem['valint'];
				$this->m_pubdata[$gMediaReplStr['videos'][$lCurrentRelatedItem['valint2']]] = $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTSFLV));
			} elseif ($lCurrentRelatedItem['propid'] == 17) { // kareta
				//var_dump($lCurrentRelatedItem);
				$this->m_pubdata['reldesc'] = $lCurrentRelatedItem['reldesc'];
				if( $lCurrentRelatedItem['valint2'] == 1 ){
					$this->m_pubdata['karetatr'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_KARETATR));
				}elseif( $lCurrentRelatedItem['valint2'] == 2 ){
					$this->m_pubdata['karetatl'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_KARETATL));
				}else{
					$this->m_pubdata['karetab'] .= $this->ReplaceHtmlFields($this->getObjTemplate(G_KARETAB));
				}
			}
		}

		if ($this->m_pubdata['attachments']) {
			$this->m_pubdata['attachments'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTS_HEADER)) . $this->m_pubdata['attachments'] . $this->ReplaceHtmlFields($this->getObjTemplate(G_STORY_ATTACHMENTS_FOOTER));
		} else {
			$this->m_pubdata['attachments'] = '';
		}

		if ($this->m_pubdata['relinks']) {
			$this->m_pubdata['relinks'] =  $this->ReplaceHtmlFields($this->getObjTemplate(G_RELINKHEADER)) . $this->m_pubdata['relinks'] .  $this->ReplaceHtmlFields($this->getObjTemplate(G_RELINKFOOTER));
		} else {
			$this->m_pubdata['relinks'] = '';
		}

		if ($this->m_pubdata['relstories']) {
			$this->m_pubdata['relstories'] =  $this->ReplaceHtmlFields($this->getObjTemplate(G_RELSTHEADER)) . $this->m_pubdata['relstories'] .  $this->ReplaceHtmlFields($this->getObjTemplate(G_RELSTFOOTER));
		} else {
			$this->m_pubdata['relstories'] = '';
		}
		
		if (!$this->m_pubdata['photoauthor'])
			$this->m_pubdata['photoauthor'] = '';
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
	}

	public function Display() {
		$this->GetData();
		$this->GetRelatedItems();

		if(!$this->m_storyId){
			return $this->ReplaceHtmlFields($this->getObjTemplate(G_NOSTORY));
		}

		/*
		if ($this->StoryType == GALLERY_STORYTYPE) {
			// Nachalo i krai na navigaciqta za galeriqta
			$this->m_pubdata['gallprev'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_GALPREV));
			$this->m_pubdata['gallnext'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_GALNEXT));
			// Nulirame navigaciata ako ima 1 ili po malko snimki
			if ($this->m_pubdata['numpics'] <= 1) $this->m_pubdata['gallnav'] = '';

			return $this->ReplaceHtmlFields($this->getObjTemplate(G_GALLERY));
		}*/
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_DEFAULT));


	}

}






?>