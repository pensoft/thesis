<?php

class cBrowse_Articles_Controller extends cBase_Controller {
	var $m_Categories_Controller;
	
	function __construct() {
		global $rewrite;
		parent::__construct();

		$pViewPageObjectsDataArray = array();
		$lTreeModel = new mTree_Model();
		$lJournalId = (int)$this->GetValueFromRequestWithoutChecks('journal_id');
		$lState = (int)$this->GetValueFromRequestWithoutChecks('preview');
		$lSearchText = $this->GetValueFromRequestWithoutChecks('stext');
		$lSearchedOpt = $this->GetValueFromRequestWithoutChecks('search_in');
		$lFormName = $this->GetValueFromRequestWithoutChecks('form_name');
		$lSortBy = (int)$this->GetValueFromRequestWithoutChecks('sortby');
		//var_dump($_REQUEST);
		$this->m_models['mJournal_Documents_Model'] = new mJournal_Documents_Model();
		
		if ($lState == 1){

			if(!(int)$this->GetUserId())
				header('Location: /index.php');
			
			$pViewPageObjectsDataArray['journal_features'] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'journal_features_templates',
				'controller_data' => $this->m_models['mBrowse_Model']->GetJournalFeatures($lJournalId),
				'journal_id' => $lJournalId,
			);	
			
			$lViewPageObjectsDataArray['pagetitle'] = 'Biodiversity Data Journal';
			$lViewPageObjectsDataArray['journal_id'] = $lJournalId;
			
			$lJournalArticles = $this->m_models['mJournal_Documents_Model']->getPublicReviewArticles($lJournalId, (int)$this->GetValueFromRequestWithoutChecks('p'));

			$pViewPageObjectsDataArray['contents'] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'browse_articles_templs',
				'controller_data' => $lJournalArticles,
				'journal_id' => $lJournalId,
				'fundingagency' => $lFundingAgency,
				'default_page_size' => DEFAULT_PAGE_SIZE,
				'page_parameter_name' => 'p',
				'price_state' => 1,
				
			);	
			$pViewPageObjectsDataArray['display_show_page'] = 1;
			
		} else {
			$lTaxon = $this->GetValueFromRequestWithoutChecks('alerts_taxon_cats');
			$lSubject = $this->GetValueFromRequestWithoutChecks('alerts_subject_cats');
			$lGeographical = $this->GetValueFromRequestWithoutChecks('alerts_geographical_cats');
			$lChronical = $this->GetValueFromRequestWithoutChecks('alerts_chronical_cats');
			if($lTaxon)
				$lTreeModel->returnCategoryName('taxon_categories', $lTaxon);
			if($lSubject)
				$lTreeModel->returnCategoryName('subject_categories', $lSubject);
			if($lGeographical)
				$lTreeModel->returnCategoryName('geographical_categories', $lGeographical);
			if($lChronical)
				$lTreeModel->returnCategoryName('chronological_categories', $lChronical);
			
			
			$lFromDate = $this->GetValueFromRequestWithoutChecks('from_date');
			$lToDate = $this->GetValueFromRequestWithoutChecks('to_date');
			$lSectionType = $this->GetValueFromRequestWithoutChecks('section_type');
			$lFundingAgency = $this->GetValueFromRequestWithoutChecks('funding_agency');

			$this->m_models['mEdit_Model'] = new mEdit_model();		
			
			if(!$lJournalId || !$this->m_models['mEdit_Model']->CheckJournalExist($lJournalId)){
				header('Location: /index.php');
			}
			
			$this->m_Categories_Controller 	 = new cCategories_Controller();
			
			$lFieldsMetadataTempl = array(
				'journal_id' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
				),
				'sortby' => array(
					'CType' => 'hidden',
					'VType' => 'int',
					'AllowNulls' => true,
					'DefValue' => (int)$_REQUEST['sortby'],
					'AddTags' => array(
						'id' => 'filter_articles_sortby',
					),
				),
				'alerts_subject_cats' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => true,
					'DisplayName' => getstr('regprof.exp_alerts_subject_cats'),
					'AddTags' => array(
						'id' => 'alerts_subject_cats_autocomplete',
					),
				),
				'alerts_chronical_cats' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => true,
					'DisplayName' => '',
					'AddTags' => array(
						'id' => 'alerts_chronical_cats_autocomplete',  /* тук задължително трябва да има _autocomplete, защото
																		* на класа се подава alerts_chronical_cats като идентификатор
																		* и той сам добавя _autocomplete към идентификатора
																		*/
			
					),
				),
				'alerts_taxon_cats' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => true,
					'DisplayName' => '',
					'AddTags' => array(
						'id' => 'alerts_taxon_cats_autocomplete',
					),
				),
				'alerts_geographical_cats' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => true,
					'DisplayName' => '',
					'AddTags' => array(
						'id' => 'alerts_geographical_cats_autocomplete',
					),
				),
				'from_date' => array(
					'VType' => 'date',
					'CType' => 'text',
					//'DefValue' => '', // global default date 
					'DateType' => DATE_TYPE_DATE,
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => '',
						'id' => 'from_date',
					),
				),
				'to_date' => array(
					'VType' => 'date',
					'CType' => 'text',
					//'DefValue' => '', // global default date 
					'DateType' => DATE_TYPE_DATE,
					'AllowNulls' => true,
					'AddTags' => array(
						'class' => '',
						'id' => 'to_date'
					),
				),
				'section_type' => array(
					'VType' => 'int',
					'CType' => 'checkbox',
					'DefValue' => 0,
					'AllowNulls' => true,
					'DisplayName' => '',
					'TransType' => MANY_TO_SQL_ARRAY,
					'SrcValues' => 'SELECT id as id, title as name FROM pjs.journal_sections WHERE journal_id = ' . (int)$lJournalId,
					'AddTags' => array(
						'class' => 'producttypes',
					),
				),
				'funding_agency' => array(
					'CType' => 'text',
					'VType' => 'string',
					'AllowNulls' => true,
					'DisplayName' => '',
					'AddTags' => array(
						'onblur' => '$(\'#filter_articles\').submit()',
						'style' => 'width:100% !important',
						'class' => 'fund_ag',
					),
				),
				'Filter' => array(
					'CType' => 'action',
					//'SQL' => 'SELECT 1',
					'DisplayName' => getstr('pjs.filter'),
					'ActionMask' =>  ACTION_CHECK | ACTION_CCHECK | ACTION_SHOW,
					'AddTags' => array(
						'style' => 'display:none'
					),
				),
				'showedit' => array(
					'CType' => 'action',
					'SQL' => '',
					'ActionMask' => ACTION_CHECK | ACTION_EXEC | ACTION_FETCH | ACTION_SHOW,
				)
			);
			
			$lFilterArticlesFormArr = array(
				'ctype' => 'Browse_Articles_Form_Wrapper',
				'page_controller_instance' => $this,
				'name_in_viewobject' => 'browse_articles_form_templ',
				'use_captcha' => 0,
				'form_method' => 'get',
				'form_action' => '/browse_journal_articles.php',
				'js_validation' => 0,
				'form_name' => 'filter_articles',
				'dont_close_session' => true,
				'fields_metadata' => $lFieldsMetadataTempl,
				'htmlformid' => 'filter_articles',
			);
			
			$lTrees = array_merge( 
				$this->m_Categories_Controller->getCategoriesAndAutocomplete('subjects_tree', 'subjects_tree_script', 'alerts_subject_cats', 'subject_categories'),
				$this->m_Categories_Controller->getCategoriesAndAutocomplete('chronological_tree', 'chronological_tree_script', 'alerts_chronical_cats', 'chronological_categories'),
				$this->m_Categories_Controller->getCategoriesAndAutocomplete('taxon_tree', 'taxon_tree_script', 'alerts_taxon_cats', 'taxon_categories'),
				$this->m_Categories_Controller->getCategoriesAndAutocomplete('geographical_tree', 'geographical_tree_script', 'alerts_geographical_cats', 'geographical_categories')
			);
			
			$lForm = new Browse_Articles_Form_Wrapper(array_merge($lFilterArticlesFormArr, $lTrees));
			
			$pViewPageObjectsDataArray['leftcol'] = new evSimple_Block_Display(array(
				'name_in_viewobject' => 'leftcol',
				'search_form' => $lForm,
				'journal_id' => $lJournalId
			));
			//echo $lForm->wGetFieldValue('from_date');

			$lJournalArticles = $this->m_models['mJournal_Documents_Model']->GetJournalArticles(
				$lJournalId, (int)$this->GetValueFromRequestWithoutChecks('p'),
				$lForm->wGetFieldValue('section_type'),
				$lForm->wGetFieldValue('alerts_taxon_cats'),
				$lForm->wGetFieldValue('alerts_subject_cats'),
				$lForm->wGetFieldValue('alerts_chronical_cats'),
				$lForm->wGetFieldValue('alerts_geographical_cats'),
				$lForm->wGetFieldValue('from_date'),
				$lForm->wGetFieldValue('to_date'),
				$lForm->wGetFieldValue('funding_agency'),
				$lSearchText,
				$lSearchedOpt,
				(int)$lSortBy
			);
			//var_dump($lTrees);
			//var_dump($lForm->wGetFieldValue('section_type'));
			if(!isset($lSearchedOpt)) {
				$lConvertedFilterValues = $this->m_models['mJournal_Documents_Model']->ConvertFilterValues(
					$lForm->wGetFieldValue('alerts_taxon_cats'),
					$lForm->wGetFieldValue('alerts_subject_cats'),
					$lForm->wGetFieldValue('alerts_geographical_cats'),
					$lForm->wGetFieldValue('alerts_chronical_cats'),
					$lForm->wGetFieldValue('section_type')
				);
			}
			//var_dump($lConvertedFilterValues['sectionType']);
			$pViewPageObjectsDataArray['contents'] = array(
				'ctype' => 'evList_Display',
				'name_in_viewobject' => 'browse_articles_list_templs',
				'controller_data' => $lJournalArticles,
				'journal_id' => $lJournalId,
				'fromdate' => $lForm->wGetFieldValue('from_date'),
				'todate' => $lForm->wGetFieldValue('to_date'),
				'fundingagency' => $lFundingAgency,
				'default_page_size' => DEFAULT_PAGE_SIZE,
				'page_parameter_name' => 'p',
				'taxon' => $lConvertedFilterValues['taxon'], 
				'subject' => $lConvertedFilterValues['subject'], 
				'geographical' => $lConvertedFilterValues['geographical'], 
				'chronical' => $lConvertedFilterValues['chronical'], 
				'sectiontype' => $lConvertedFilterValues['sectionType'],
				'submitted_form_name' => $lFormName,
				'sortby' => (int)$lSortBy,
			);		
			$pViewPageObjectsDataArray['journal_id'] = $lJournalId;
		}
		
		$this->AddJournalObjects($lJournalId);
		
		$this->m_pageView = new pBrowse_Articles_Page_View(array_merge($this->m_commonObjectsDefinitions, $pViewPageObjectsDataArray));
	}

	protected function InitSessionCloseVariable(){
		$this->m_closeSession = false;
	}
}
?>