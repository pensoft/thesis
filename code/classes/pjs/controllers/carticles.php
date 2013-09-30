<?php
// Disable error reporting because it can break the json output
// ini_set('error_reporting', 'off');

class cArticles extends cBase_Controller {
	var $m_articleId;
	var $m_articlesModel;
	var $m_articleMetadata = array();
	function __construct() {
		parent::__construct();
		$pViewPageObjectsDataArray = array();
		$this->m_articlesModel = new mArticles();
		$this->m_articleId = (int)$this->GetValueFromRequestWithoutChecks('id');
		$lObjectExistence = $this->m_articlesModel->GetObjectExistenceFields($this->m_articleId);
		$lMetadata = $this->m_articlesModel->GetMetadata($this->m_articleId);

		$lInfoElementContent = $this->GetInfoElementContent();
		$lResultArr = array(
				'contents' => array(
					'ctype' => 'evSimple_Block_Display',
					'object_existence' => $lObjectExistence,
					'name_in_viewobject' => 'contents',
					'id' => $this->m_articleId,
					'info_content' => $lInfoElementContent['html'],
					'main_tab_element_id' => $lInfoElementContent['main_tab_element_id'],
					'controller_data' => $lMetadata,
				),
		);
// 		var_dump($lResultArr);
	//	$this->m_pageView = new pArticles(&$lResultArr);
		$lResultArr = array_merge($this->m_commonObjectsDefinitions, $lResultArr);
		$this->m_pageView = new pArticles(&$lResultArr);
	//	$this->m_pageView = new pArticles(array_merge($this->m_commonObjectsDefinitions, $lResultArr));
	}

	protected function GetInfoElementContent(){
		$lDisplayType = $this->GetValueFromRequestWithoutChecks('display_type');
		$_REQUEST['article_id'] = $this->m_articleId;//We set it for the ajax controller
		$lElementType = (int)$this->GetValueFromRequestWithoutChecks('element_type');
		$lElementId = (int)$this->GetValueFromRequestWithoutChecks('element_id');
		$lElementName = $this->GetValueFromRequestWithoutChecks('element_name');
		$lAjaxController = new cArticle_Ajax_Srv();
		$lResult = array(
			'html' => '',
			'main_tab_element_id' => ARTICLE_MENU_ELEMENT_TYPE_CONTENTS,
		);
		if($lElementType){
			$lResult['main_tab_element_id'] = $lElementType;
		}
// 		var_dump($lElementType);
		switch($lDisplayType){
			default:
			case 'list':{
				$lResult['html'] = $lAjaxController->GetMainListElementBase($lElementType);
				break;
			}
			case 'element':{
				$lResult['html'] = $lAjaxController->GetElementBase($lElementType, $lElementId, $lElementName);
				break;
			}
		}
		if($lResult['html'] == ''){
			$lResult['html'] = $this->m_articlesModel->GetContentsListHtml($this->m_articleId);
			$lResult['main_tab_element_id'] = ARTICLE_MENU_ELEMENT_TYPE_CONTENTS;
		}
		return $lResult;
	}

	function GetShareMetaTags(){
		$this->m_articlesModel = new mArticles();
		$this->m_articleId = (int)$this->GetValueFromRequestWithoutChecks('id');
		$this->m_articleMetadata = $this->m_articlesModel->GetArticleMetadata($this->m_articleId);
		$this->m_commonObjectsDefinitions['pagetitle'] = trim($this->m_articleMetadata['title']);
		$this->m_commonObjectsDefinitions['description'] = trim($this->m_articleMetadata['abstract']);
		$this->m_commonObjectsDefinitions['keywords'] = trim($this->m_articleMetadata['keywords']);
		return $this->GetFBMetadata() . $this->GetTwitterMetadata() . $this->GetMendeleyMetadata();
	}

	function GetFBMetadata () {
		return '
			<meta property="og:image" content="' . SITE_URL . '/i/bdj-eye.png" />
			<meta property="og:title" content="' . trim($this->m_articleMetadata['title']) . '"/>
			<meta property="og:url" content="' . SITE_URL . '/articles.php?id=' . (int)$this->m_articleMetadata['document_id'] . '"/>
			<meta property="og:site_name" content="' . $this->m_articleMetadata['journal_name'] . '"/>
			<meta property="og:type" content="article"/>
			<link rel="alternate" hreflang="en" type="application/pdf" title="PDF" href="/lib/ajax_srv/generate_pdf.php?readonly_preview=1&amp;document_id=' . (int)$this->m_articleMetadata['document_id'] . '" />
			<link rel="alternate" hreflang="en" type="application/xml" title="XML" href="/lib/ajax_srv/article_elements_srv.php?action=download_xml&amp;item_id=' . (int)$this->m_articleMetadata['document_id'] . '" />
		';
	}

	function GetTwitterMetadata () {
		return '';
		/*return '
			<meta property="og:image" content=""/>
			<meta property="og:title" content="article_title"/>
			<meta property="og:url" content="victorp.dadwa/articles.php?id=id"/>
			<meta property="og:site_name" content="Biodivwersity data journal"/>
			<meta property="og:type" content="article"/>
		';*/
	}

	function GetMendeleyMetadata() {
		$lAuthorsMetaData = '';
		$lAuthorsMetaDataSec = '';
		$lAuthorsMetaDataThird = '';
		$lAuthors = array();
		$lAuthors = explode(',', $this->m_articleMetadata['authors']);
		foreach ($lAuthors as $key => $value) {
			$lAuthorsMetaData .= '
				<meta name="dc.creator" content="' . $value . '" />
				<meta name="dc.contributor" content="' . $value . '" />
			';
		}
		foreach ($lAuthors as $key => $value) {
			$lAuthorsMetaDataSec .= '
				<meta name="eprints.creators_name" content="' . $value . '" />
			';
		}

		foreach ($lAuthors as $key => $value) {
			$lAuthorsMetaDataThird .= '
				<meta name="citation_author" content="' . $value . '" />
			';
		}

		return '
			<meta name="dc.title" content="' . trim($this->m_articleMetadata['title']) . '" />
		   ' . $lAuthorsMetaData . '
		   <meta name="dc.type" content="' . $this->m_articleMetadata['document_type'] . '" />
		   <meta name="dc.source" content="' . $this->m_articleMetadata['journal_name'] . ' 1: e' . $this->m_articleMetadata['document_id'] . '" />
		   <meta name="dc.date" content="' . $this->m_articleMetadata['publish_date'] . '" />
		   <meta name="dc.identifier" content="' . $this->m_articleMetadata['doi'] . '" />
		   <meta name="dc.publisher" content="Pensoft Publishers" />
		   <meta name="dc.rights" content="http://creativecommons.org/licenses/by/3.0/" />
		   <meta name="dc.format" content="text/html" />
		   <meta name="dc.language" content="en" />

		   <meta name="prism.publicationName" content="' . $this->m_articleMetadata['journal_name'] . '" />
		   <meta name="prism.issn" content="1314-2828" />
		   <meta name="prism.publicationDate" content="' . $this->m_articleMetadata['publish_date'] . '" />
		   <meta name="prism.volume" content="1" />

		   <meta name="prism.doi" content="' . $this->m_articleMetadata['doi'] . '" />
		   <meta name="prism.section" content="' . $this->m_articleMetadata['document_type'] . '" />
		   <meta name="prism.startingPage" content="e' . $this->m_articleMetadata['document_id'] . '" />
		   <meta name="prism.copyright" content="' . date('Y') . ' ' . $this->m_articleMetadata['authors'] . '" />
		   <meta name="prism.rightsAgent" content="bdj@pensoft.net" />

		   <meta name="eprints.title" content="' . trim($this->m_articleMetadata['title']) . '" />
		   ' . $lAuthorsMetaDataSec . '
		   <meta name="eprints.type" content="' . $this->m_articleMetadata['document_type'] . '" />
		   <meta name="eprints.datestamp" content="' . $this->m_articleMetadata['publish_date'] . '" />
		   <meta name="eprints.ispublished" content="pub" />
		   <meta name="eprints.date" content="' . date('Y') . '" />
		   <meta name="eprints.date_type" content="published" />
		   <meta name="eprints.publication" content="Pensoft Publishers" />
		   <meta name="eprints.volume" content="1" />
		   <meta name="eprints.pagerange" content="e' . $this->m_articleMetadata['document_id'] . '" />

		   <meta name="citation_journal_title" content="' . $this->m_articleMetadata['journal_name'] . '" />
		   <meta name="citation_publisher" content="Pensoft Publishers" />
		   ' . $lAuthorsMetaDataThird . '
		   <meta name="citation_title" content="' . trim($this->m_articleMetadata['title']) . '" />
		   <meta name="citation_volume" content="1" />

		   <meta name="citation_firstpage" content="e' . $this->m_articleMetadata['document_id'] . '" />
		   <meta name="citation_doi" content="' . $this->m_articleMetadata['doi'] . '" />
		   <meta name="citation_issn" content="1314-2828" />
		   <meta name="citation_date" content="' . $this->m_articleMetadata['publish_date'] . '" />
		';
	}

};