<?php

class cShow_Controller extends cBase_Controller {

	function __construct() {
		parent::__construct();
		
		$pViewPageObjectsDataArray = array();
		
		$this->m_models['mstories_model'] = new mStories_Model();
		
		$lMode 	    = (int)$this->GetValueFromRequestWithoutChecks('mode');
		$lStoryId   = (int)$this->GetValueFromRequestWithoutChecks('storyid');
		$lRubrId    = (int)$this->GetValueFromRequestWithoutChecks('rubrid');
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		
		$lPhotoPrefArr =  array(
			1 => 'sg198_', // top right
			2 => 'sg198_', // top left
			3 => 'dx400_', // bottom pic
			4 => 'dx400_', // big photo
			10 => 'sg198_', // Tova za galeriq - golqma snimka
			11 => 'sg198_', // Tova za galeriq - thumbnail
		);
		
		if( $lMode == 4 ){
			if(!$lRubrId){
				header('Location: /index.php');
				exit;
			}
			$lStoriesList = array();
			$lStoryIdsArr = $this->m_models['mstories_model']->GetStoryIdsByRubrId($lRubrId);
			$lJournalId   = (int)$lStoryIdsArr[0]['journal_id'];
			$lStoriesWithCutPositions = $this->CutStoriesPositions($lStoryIdsArr);
			$pViewPageObjectsDataArray['stories_tree'] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'tree_list_templs',
				'controller_data' => $lStoriesWithCutPositions,
				'showmode' => 1,
				'journal_id' => $lJournalId
			);
			
			for($i = 0; $i < count($lStoryIdsArr); $i++){
				$lStoryMainContent = $this->m_models['mstories_model']->GetStoryDetails($lStoryIdsArr[$i]['guid']);
				if($i == 0){
					$lStoryMainContent['hidetitle'] = 1;
				}
				$lStoryContent = new evStory_Display(array(
					'ctype' => 'evStory_Display',
					'name_in_viewobject' => 'stories_story_list',
					'controller_data' => $lStoryMainContent,
					'photopref' => $lPhotoPrefArr,
				));
				
				$lStoriesList[] = array('story' => $lStoryContent);
			}
			
			$lStoryList = new evList_Display(array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'stories_list_templs',
				'controller_data' => $lStoriesList,
			));
			
			$pViewPageObjectsDataArray['content'] = $lStoryList;
		}else{
			
			switch( $lMode ){
				case 1:
					if(!$lJournalId){
						header('Location: /index.php');
						exit;
					}
					$lStoriesList = array();
					$lStoryIdsArr = $this->m_models['mstories_model']->GetStoryIdsByJournalId($lJournalId);
					$lJournalId   = (int)$lStoryIdsArr[0]['journal_id'];
					$lRootStories = $this->GetOnlyRootItems($lStoryIdsArr);
					
					$pViewPageObjectsDataArray['stories_tree'] = array(
						'ctype' => 'evList_Display',
						'name_in_viewobject' => 'tree_list_templs',
						'controller_data' => $lRootStories,
						'showmode' => 1,
						'journal_id' => $lJournalId
					);
					
					for($i = 0; $i < count($lStoryIdsArr); $i++){
						$lStoryMainContent = $this->m_models['mstories_model']->GetStoryDetails($lStoryIdsArr[$i]['guid']);
						
						$lStoryContent = new evStory_Display(array(
							'ctype' => 'evStory_Display',
							'name_in_viewobject' => 'stories_story_list',
							'controller_data' => $lStoryMainContent,
							'photopref' => $lPhotoPrefArr,
						));
						
						$lStoriesList[] = array('story' => $lStoryContent);
					}
					
					$lStoryList = new evList_Display(array(
						'ctype' => 'evList_Display',
						'name_in_viewobject' => 'stories_list_templs',
						'controller_data' => $lStoriesList,
					));
					
					$pViewPageObjectsDataArray['content'] = $lStoryList;
					break;
				case 2:
					if(!$lStoryId){
						header('Location: /index.php');
						exit;
					}
					$lStoriesList = array();
					$lStoryIdsArr = $this->m_models['mstories_model']->GetStoryIdsByStoryId($lStoryId);
					$lJournalId   = (int)$lStoryIdsArr[0]['journal_id'];
					$lStoriesWithCutPositions = $this->CutStoriesPositions($lStoryIdsArr);
					$pViewPageObjectsDataArray['stories_tree'] = array(
						'ctype' => 'evList_Display',
						'name_in_viewobject' => 'tree_list_templs',
						'controller_data' => $lStoriesWithCutPositions,
						'showmode' => 1,
						'journal_id' => $lJournalId
					);
					
					for($i = 0; $i < count($lStoryIdsArr); $i++){
						$lStoryMainContent = $this->m_models['mstories_model']->GetStoryDetails($lStoryIdsArr[$i]['guid']);
						if($i == 0){
							$lStoryMainContent['hidetitle'] = 1;
						}
						$lStoryContent = new evStory_Display(array(
							'ctype' => 'evStory_Display',
							'name_in_viewobject' => 'stories_story_list',
							'controller_data' => $lStoryMainContent,
							'photopref' => $lPhotoPrefArr,
						));
						
						$lStoriesList[] = array('story' => $lStoryContent);
					}
					
					$lStoryList = new evList_Display(array(
						'ctype' => 'evList_Display',
						'name_in_viewobject' => 'stories_list_templs',
						'controller_data' => $lStoriesList,
					));
					
					$pViewPageObjectsDataArray['content'] = $lStoryList;
					break;
				case 3:
					break;
				default:
					if(!$lStoryId){
						header('Location: /index.php');
						exit;
					}
					$lStoryDetails = $this->m_models['mstories_model']->GetStoryDetails($lStoryId);
					$lJournalId    = (int)$lStoryDetails['journal_id'];
					
					$pViewPageObjectsDataArray['content'] = array(
						'ctype' => 'evStory_Display',
						'name_in_viewobject' => 'stories_story',
						'controller_data' => $lStoryDetails,
						'photopref' => $lPhotoPrefArr,
					);
					break;
			}
		}
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pShow_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}
	
	function CutStoriesPositions($pStoriesArr){
		$lPosLength = strlen($pStoriesArr[0]['pos']);
		if( $lPosLength > 2 ){
			for($i = 0; $i < count($pStoriesArr); $i++){
				$pStoriesArr[$i]['pos'] = substr($pStoriesArr[$i]['pos'], $lPosLength - 2);
			}
		}
		return $pStoriesArr;
	}
	
	function GetOnlyRootItems($pStoriesArr){
		foreach($pStoriesArr as $k => $v){
			if(strlen($v['pos']) > 2)
				continue;
			$lRetArr[] = $v;
		}
		return $lRetArr;
	}
}

?>