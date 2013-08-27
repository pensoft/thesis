<?php
class ctaxon_preview_generator extends csimple {
	var $m_taxonName;
	var $m_taxonId;
	var $m_encodedTaxonName;
	var $m_templ;
	var $m_dontGetData;
	var $m_con;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_dataGenerator;
	var $m_ncbiPreview;
	var $m_ncbiHasResults = false;
	var $m_gbifPreview;
	var $m_gbifHasResults = false;
	var $m_bhlPreview;
	var $m_bhlHasResults = false;
	var $m_wikimediaPreview;
	var $m_wikimediaHasResults = false;
	var $m_wholePreview = '';
	var $m_categories = array ();

	function __construct($pFieldTempl) {
		$this->m_taxonName = $pFieldTempl ['taxon_name'];
		$this->m_encodedTaxonName = urlencode($this->m_taxonName);
		$this->m_templ = $pFieldTempl ['templ'];
		$this->m_dontGetData = false;
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->m_dataGenerator = new ctaxon_data_generator(array (
			'taxon_name' => $this->m_taxonName 
		));
		$this->LoadTaxonData();
		$this->m_categories = array (
			'distribution' => array (
				'special_sites' => array (
					GBIF_SITE_ID 
				),
				'regular_sites' => array (),
				'is_empty' => true,
				'display_name' => 'Distribution',
				'preview' => '' 
			),
			'genomics' => array (
				'special_sites' => array (
					NCBI_SITE_ID 
				),
				'regular_sites' => array (
					25 //BOLD
				),
				'is_empty' => true,
				'display_name' => 'Genomics',
				'preview' => '' 
			),
			'nomenclature' => array (
				'special_sites' => array (),
				'regular_sites' => array (
					17,//ZooBank
					12,//IPNI
					11,//IndexFung
					3 //Cat Of Life
				),
				'is_empty' => true,
				'display_name' => 'Nomenclature',
				'preview' => '' 
			),
			'literature' => array (
				'special_sites' => array (
					BHL_SITE_ID 
				),
				'regular_sites' => array (
					31,//Pubmed
					30 //Google scholar
				),
				'is_empty' => true,
				'display_name' => 'Literature',
				'preview' => '' 
			),
			'images' => array (
				'special_sites' => array (
					WIKIMEDIA_SITE_ID 
				),
				'regular_sites' => array (
					2,//Eol
					26 //Morphbank
				),
				'is_empty' => true,
				'display_name' => 'Images',
				'preview' => '' 
			),
			'treatments' => array (
				'special_sites' => array (),
				'regular_sites' => array (
					35 //Plazi
				),
				'is_empty' => true,
				'display_name' => 'Treatments',
				'preview' => '' 
			),
			'other' => array (
				'special_sites' => array (),
				'regular_sites' => array (
					8,//Wikipedi
					9,//Wikispecies
					7,//Worms
					10,//IUCN
					36,//Daisie
					37,//Invasive org
					16,//Gymnosperm
					38,//Lias
					14 //Tropicos
				),
				'is_empty' => true,
				'display_name' => 'Other',
				'preview' => '' 
			) 
		);
		$this->LoadTaxonData();
	}

	function SetTemplate($pTemplate) {
		$this->m_templ = $pTemplate;
	}

	function LoadTaxonData() {
		$lSql = '
			SELECT id
			FROM pjs.taxons a
			WHERE lower(translate(a.name::text, \' ,.-*\', \'\'\'\')) = lower(translate(\'' . q($this->m_taxonName) . '\', \' ,.-*\', \'\'\'\'))
		';
		$this->m_con->Execute($lSql);
		$this->m_taxonId = (int) $this->m_con->mRs ['id'];
		if (! $this->m_taxonId) {
			$this->SetError(getstr('pjs.noSuchTaxon'));
		}
	}

	function SetError($pErrMsg) {
		$this->m_errCnt ++;
		$this->m_errMsg = $pErrMsg;
	}

	protected function ImportPreview() {
		if ($this->m_errCnt) {
			return;
		}
		try {
			if($this->m_dataGenerator->m_errCnt){
				throw new Exception($this->m_dataGenerator->m_errMsg);
			}
			$lCon = $this->m_con;
			$lSql = 'SELECT * FROM spSaveTaxonPreview(' . (int)$this->m_taxonId . ', \'' . q($this->m_wholePreview) . '\')';
			if (! $lCon->Execute($lSql)) {
				throw new Exception(getstr('pwt.couldNotBeginTransaction'));
			}			
		} catch ( Exception $lException ) {			
			$this->SetError($lException->getMessage());
		}
	}

	protected function ExecuteTransactionalQuery($pSql) {
		if (! $this->m_con->Execute($pSql)) {
			throw new Exception($this->m_con->GetLastError());
		}
	}

	function GetData() {
		if ($this->m_dontGetData) {
			return;
		}
		$this->GeneratePreview();
		$this->ImportPreview();
		$this->m_dontGetData = true;
		return $this->m_wholePreview;
	}

	protected function GenerateNCBIPreview() {
		$lNCBIData = $this->m_dataGenerator->GetNCBIData();
		if (! $lNCBIData ['ncbi_taxon_id']) {
			$lPreview = new csimple(array (
				'templs' => array (
					G_DEFAULT => 'article.ncbi_no_data' 
				),
				'taxon_name' => $this->m_taxonName 
			));
			$this->m_ncbiPreview = $lPreview->Display();
			return;
		}
		$this->m_ncbiHasResults = true;
		$lLineage = new crs_display_array(array (
			'input_arr' => $lNCBIData ['lineage'],
			'taxon_name' => $this->m_taxonName,
			'templs' => array (
				G_HEADER => 'article.ncbi_lineage_head',
				G_FOOTER => 'article.ncbi_lineage_foot',
				G_STARTRS => 'article.ncbi_lineage_start',
				G_ENDRS => 'article.ncbi_lineage_end',
				G_ROWTEMPL => 'article.ncbi_lineage_row',
				G_NODATA => 'article.ncbi_lineage_nodata' 
			) 
		));
		$lRelatedLinks = new crs_display_array(array (
			'input_arr' => $lNCBIData ['related_links'],
			'taxon_name' => $this->m_taxonName,
			'see_all_link' => NCBI_SUBTREE_LINK . '&db=pubmed&term=' . $this->m_taxonName,
			'templs' => array (
				G_HEADER => 'article.ncbi_related_links_head',
				G_FOOTER => 'article.ncbi_related_links_foot',
				G_STARTRS => 'article.ncbi_related_links_start',
				G_ENDRS => 'article.ncbi_related_links_end',
				G_ROWTEMPL => 'article.ncbi_related_links_row',
				G_NODATA => 'article.ncbi_related_links_nodata' 
			) 
		));
		$lEntrezRecords = new crs_display_array(array (
			'input_arr' => $lNCBIData ['entrez_records'],
			'taxon_name' => $this->m_taxonName,
			'taxon_ncbi_id' => $lNCBIData ['ncbi_taxon_id'],
			'templs' => array (
				G_HEADER => 'article.ncbi_entrez_records_head',
				G_FOOTER => 'article.ncbi_entrez_records_foot',
				G_STARTRS => 'article.ncbi_entrez_records_start',
				G_ENDRS => 'article.ncbi_entrez_records_end',
				G_ROWTEMPL => 'article.ncbi_entrez_records_row',
				G_NODATA => 'article.ncbi_entrez_records_nodata' 
			) 
		));
		$lPreview = new csimple(array (
			'rank' => $lNCBIData ['rank'],
			'division' => $lNCBIData ['division'],
			'lineage' => $lLineage,
			'related_links' => $lRelatedLinks,
			'entrez_records' => $lEntrezRecords,
			'taxon_name' => $this->m_taxonName,
			'ncbi_link' => NCBI_TAXON_URL . $lNCBIData ['ncbi_taxon_id'],
			'templs' => array (
				G_DEFAULT => 'article.ncbi' 
			) 
		));
		$this->m_ncbiPreview = $lPreview->Display();
		return $this->m_ncbiPreview;
	}

	protected function GenerateGBIFPreview() {
		$lGBIFData = $this->m_dataGenerator->GetGBIFData();
		$lGbifLinkData = $this->m_dataGenerator->GetSiteData(GBIF_SITE_ID);
		if (! $lGBIFData ['gbif_taxon_id']) {
			$lPreview = new csimple(array (
				'templs' => array (
					G_DEFAULT => 'article.gbif_no_data' 
				),
				'gbif_link' => $lGbifLinkData ['taxon_link'],
				'taxon_name' => $this->m_taxonName 
			));
			$this->m_gbifPreview = $lPreview->Display();
			return $this->m_gbifPreview;
		}
		$this->m_gbifHasResults = true;
		
		$lPreview = new csimple(array (
			'templs' => array (
				G_DEFAULT => 'article.gbif' 
			),
			'postform' => $lGbifLinkData ['postform'],
			'postfields' => $lGbifLinkData ['postfields'],
			'gbif_link' => $lGbifLinkData ['taxon_link'],
			'gbif_taxon_id' => $lGBIFData ['gbif_taxon_id'],
			'map_iframe_src' => $lGBIFData ['map_iframe_src'],
			'link_prefix' => addslashes(ParseTaxonExternalLink($this->m_taxonName)),
			'taxon_name' => $this->m_taxonName 
		));
		$this->m_gbifPreview = $lPreview->Display();
		return $this->m_gbifPreview;
	}

	protected function GenerateBHLPreview() {
		$lBHLData = $this->m_dataGenerator->GetBHLData();
		$lBHLLink = BHL_TAXON_EXTERNAL_LINK . $this->m_encodedTaxonName;
		if (! $lBHLData ['result_taken_successfully']) {
			$this->m_bhlHasResults = true;
			$lPreview = new csimple(array (
				'templs' => array (
					G_DEFAULT => 'article.bhl_not_successfully_taken' 
				),
				'nodata' => 1,
				'bhl_link' => $lBHLLink,
				'taxon_name' => $this->m_taxonName 
			));
			$this->m_bhlPreview = $lPreview->Display();
			return $this->m_bhlPreview;
		}
		$lTitles = new crs_display_array(array (
			'input_arr' => $lBHLData ['titles'],
			'taxon_name' => $this->m_taxonName,
			'pagesize' => 6,
			'templs' => array (
				G_HEADER => 'article.bhl_titles_head',
				G_FOOTER => 'article.bhl_titles_foot',
				G_STARTRS => 'article.bhl_titles_start',
				G_ENDRS => 'article.bhl_titles_end',
				G_ROWTEMPL => 'article.bhl_titles_row',
				G_NODATA => 'article.bhl_titles_nodata' 
			) 
		));
		$lFullSizeImgUrl = '';
		$lThumbnailUrl = '';
		foreach ( $lBHLData ['titles'] as $lTitle ) {
			foreach ( $lTitle ['items'] as $lItem ) {
				foreach ( $lItem ['pages'] as $lPage ) {
					if ($lPage ['thumbnail_url'] && $lPage ['fullsize_image_url']) {
						$lFullSizeImgUrl = $lPage ['fullsize_image_url'];
						$lThumbnailUrl = $lPage ['thumbnail_url'];
						break 3;
					}
				}
			}
		}
		
		$this->m_bhlHasResults = count($lBHLData ['titles']) ? true : false;
		
		$lPreview = new csimple(array (
			'templs' => array (
				G_DEFAULT => 'article.bhl' 
			),
			'fullsize_img_url' => $lFullSizeImgUrl,
			'thumbnail_url' => $lThumbnailUrl,
			'nodata' => count($lBHLData ['titles']) ? false : true,
			'titles' => $lTitles,
			'bhl_link' => $lBHLLink,
			'taxon_name' => $this->m_taxonName 
		));
		$this->m_bhlPreview = $lPreview->Display();
		return $this->m_bhlPreview;
	}

	protected function GenerateWikimediaPreview() {
		$lWikimediaData = $this->m_dataGenerator->GetWikimediaData();
		$lLinkData = $this->m_dataGenerator->GetSiteData(WIKIMEDIA_SITE_ID);
		$lWikimediaLink = $lLinkData ['taxon_link'];
		$lImages = array ();
		foreach ( $lWikimediaData as $lCategoryName => $lCategoryData ) {
			foreach ( $lCategoryData ['images'] as $lImage ) {
				$lImages [] = $lImage;
			}
		}
		$lImagesList = new crs_display_array(array (
			'input_arr' => $lImages,
			'taxon_name' => $this->m_taxonName,
			'pagesize' => 10,
			'templs' => array (
				G_HEADER => 'article.wikimedia_images_head',
				G_FOOTER => 'article.wikimedia_images_foot',
				G_STARTRS => 'article.wikimedia_images_start',
				G_ENDRS => 'article.wikimedia_images_end',
				G_ROWTEMPL => 'article.wikimedia_images_row',
				G_NODATA => 'article.wikimedia_images_nodata' 
			) 
		));
		
		if (count($lImages)) {
			$this->m_wikimediaHasResults = true;
			$lPreview = new csimple(array (
				'templs' => array (
					G_DEFAULT => 'article.wikimedia' 
				),
				'images' => $lImagesList,
				'itemsonrow' => 5,
				'wikimedia_link' => $lWikimediaLink,
				'taxon_name' => $this->m_taxonName 
			));
		} else {
			$lPreview = new csimple(array (
				'templs' => array (
					G_DEFAULT => 'article.wikimedia_nodata' 
				),
				'images' => $lImages,
				'wikimedia_link' => $lWikimediaLink,
				'taxon_name' => $this->m_taxonName 
			));
		}
		$this->m_wikimediaPreview = $lPreview->Display();
		return $this->m_wikimediaPreview;
	}

	protected function GenerateCategoriesPreview() {
		$lNonEmptyCategories = array();
		foreach ( $this->m_categories as $lCategoryName => $lCategoryData ) {
			$lRegularSitesData = array ();
			$lSpecialSitesData = array();
			foreach ( $lCategoryData ['regular_sites'] as $lSiteId ) {
				$lSiteData = $this->m_dataGenerator->GetSiteData($lSiteId);
				if ($lSiteData ['has_results'] || $lSiteData ['show_if_not_found']) {
					$lRegularSitesData [$lSiteId] = $lSiteData;
				}
			}
			foreach ( $lCategoryData ['special_sites'] as $lSiteId ) {							
				if ($this->CheckIfSpecialSiteHasResults($lSiteId)) {
					$lSpecialSitesData [$lSiteId] = array(
						'preview' => $this->GetSpecialSitePreview($lSiteId),
					);
				}
			}
			if(count($lRegularSitesData) || count($lSpecialSitesData)){
				$this->m_categories['is_empty'] = false;
				$lSpecialSites = new crs_display_array(array (
					'input_arr' => $lSpecialSitesData,
					'taxon_name' => $this->m_taxonName,
					'templs' => array (
						G_HEADER => 'article.category_special_sites_head',
						G_FOOTER => 'article.category_special_sites_foot',
						G_STARTRS => 'article.category_special_sites_start',
						G_ENDRS => 'article.category_special_sites_end',
						G_ROWTEMPL => 'article.category_special_sites_row',
						G_NODATA => 'article.category_special_sites_nodata'
					)
				));
				
				$lRegularSites = new crs_display_array(array (
					'input_arr' => $lRegularSitesData,
					'taxon_name' => $this->m_taxonName,
					'templs' => array (
						G_HEADER => 'article.category_regular_sites_head',
						G_FOOTER => 'article.category_regular_sites_foot',
						G_STARTRS => 'article.category_regular_sites_start',
						G_ENDRS => 'article.category_regular_sites_end',
						G_ROWTEMPL => 'article.category_regular_sites_row',
						G_NODATA => 'article.category_regular_sites_nodata'
					)
				));
				
				$lPreview = new csimple(array(
					'taxon_name' => $this->m_taxonName,
					'special_sites' => $lSpecialSites,
					'regular_sites' => $lRegularSites,
					'display_name' => $lCategoryData['display_name'],
					'category_name' => $lCategoryName,
					'templs' => array(
						G_DEFAULT => 'article.category'
					),
				));
				
				$this->m_categories[$lCategoryName]['preview'] = $lPreview->Display();
				$lNonEmptyCategories[$lCategoryName] = array(
					'preview' => $this->m_categories[$lCategoryName]['preview'],
					'display_name' => $lCategoryData['display_name'],
					'category_name' => $lCategoryName,
				);
			}
		}
		$lCategoriesMenu = new crs_display_array(array (
			'input_arr' => $lNonEmptyCategories,
			'taxon_name' => $this->m_taxonName,
			'templs' => array (
				G_HEADER => 'article.categories_menu_head',
				G_FOOTER => 'article.categories_menu_foot',
				G_STARTRS => 'article.categories_menu_start',
				G_ENDRS => 'article.categories_menu_end',
				G_ROWTEMPL => 'article.categories_menu_row',
				G_NODATA => 'article.categories_menu_nodata'
			)
		));
		$lCategoriesList = new crs_display_array(array (
			'input_arr' => $lNonEmptyCategories,
			'taxon_name' => $this->m_taxonName,
			'templs' => array (
				G_HEADER => 'article.categories_list_head',
				G_FOOTER => 'article.categories_list_foot',
				G_STARTRS => 'article.categories_list_start',
				G_ENDRS => 'article.categories_list_end',
				G_ROWTEMPL => 'article.categories_list_row',
				G_NODATA => 'article.categories_list_nodata'
			)
		));
		$lPreview = new csimple(array(
			'taxon_name' => $this->m_taxonName,
			'categories_list' => $lCategoriesList,
			'categories_menu' => $lCategoriesMenu,
			'templs' => array(
				G_DEFAULT => 'article.taxon_preview'
			),
		));
		$this->m_wholePreview = $lPreview->Display();
		return $this->m_wholePreview;
	}

	protected function CheckIfSpecialSiteHasResults($pSiteId) {
		switch ($pSiteId) {
			default :
				return false;
			case (int) GBIF_SITE_ID :			
				return $this->m_gbifHasResults;
			case (int) NCBI_SITE_ID :
				return $this->m_ncbiHasResults;
			case (int) WIKIMEDIA_SITE_ID :
				return $this->m_wikimediaHasResults;
			case (int) BHL_SITE_ID :
				return $this->m_bhlHasResults;
		}
	}
	
	protected function GetSpecialSitePreview($pSiteId){
		switch ($pSiteId) {
			default :
				return '';
			case (int) GBIF_SITE_ID :
				return $this->m_gbifPreview;
			case (int) NCBI_SITE_ID :
				return $this->m_ncbiPreview;
			case (int) WIKIMEDIA_SITE_ID :
				return $this->m_wikimediaPreview;
			case (int) BHL_SITE_ID :
				return $this->m_bhlPreview;
		}
	}

	protected function GeneratePreview() {
		
		$this->GenerateNCBIPreview();
		$this->GenerateGBIFPreview();
		$this->GenerateBHLPreview();
		$this->GenerateWikimediaPreview();
		$this->GenerateCategoriesPreview();
		return $this->m_wholePreview;
		
	}

	function ReplaceHtmlFields($pStr) {
		return preg_replace("/\{%(.*?)%\}/e", "\$this->HtmlPrepare('\\1')", $pStr);
	}

	function HtmlPrepare($pInstanceId) {
		// trigger_error('REP ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		return $this->GetInstancePreview($pInstanceId);
	}

	function Display() {
		if (! $this->m_dontGetData)
			$this->GetData();
			// trigger_error('START DI ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		$lRet .= $this->ReplaceHtmlFields($this->m_templ);
		// trigger_error('END DI ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		return $lRet;
	}
}

?>