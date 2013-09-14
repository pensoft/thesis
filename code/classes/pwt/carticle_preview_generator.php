<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static_xsl.php');
define('INSTANCE_FIGURE_TYPE', 1);
define('INSTANCE_PLATE_TYPE', 2);
define('INSTANCE_TABLE_TYPE', 3);
define('INSTANCE_REFERENCE_TYPE', 4);
define('INSTANCE_SUPFILE_TYPE', 5);
define('INSTANCE_FIGURES_LIST_TYPE', 6);
define('INSTANCE_TABLES_LIST_TYPE', 7);
define('INSTANCE_REFERENCES_LIST_TYPE', 8);
define('INSTANCE_SUP_FILES_LIST_TYPE', 9);
define('INSTANCE_WHOLE_PREVIEW_TYPE', 10);
define('INSTANCE_TAXON_LIST_TYPE', 11);
define('INSTANCE_AUTHOR_TYPE', 12);
define('INSTANCE_AUTHORS_LIST_TYPE', 13);
define('INSTANCE_CONTENTS_LIST_TYPE', 14);
define('INSTANCE_LOCALITIES_LIST_TYPE', 15);
define('INSTANCE_CITATION_LIST_TYPE', 16);

define('INSTANCE_WHOLE_PREVIEW_INSTANCE_ID', - 5);
define('INSTANCE_FIGURES_LIST_INSTANCE_ID', - 1);
define('INSTANCE_TABLES_LIST_INSTANCE_ID', - 2);
define('INSTANCE_REFERENCES_LIST_INSTANCE_ID', - 3);
define('INSTANCE_SUP_FILES_LIST_INSTANCE_ID', - 4);
define('INSTANCE_TAXON_LIST_INSTANCE_ID', - 6);
define('INSTANCE_AUTHORS_LIST_INSTANCE_ID', - 7);

global $gInstanceTypeDetails;
$gInstanceTypeDetails = array (
	INSTANCE_FIGURE_TYPE => array (
		'xpath' => '//*[@instance_id="{instance_id}"]',
		'mode' => 'article_preview_figure',
		'preview_sql' => 'SELECT * FROM spSaveArticleFigurePreview({article_id}, {instance_id}, \'{preview}\');' 
	),
	INSTANCE_PLATE_TYPE => array (
		'xpath' => '//*[@instance_id="{instance_id}"]',
		'mode' => 'article_preview_plate',
		'preview_sql' => 'SELECT * FROM spSaveArticlePlatePreview({article_id}, {instance_id}, \'{preview}\');' 
	),
	INSTANCE_TABLE_TYPE => array (
		'xpath' => '//*[@instance_id="{instance_id}"]',
		'mode' => 'article_preview_table',
		'preview_sql' => 'SELECT * FROM spSaveArticleTablePreview({article_id}, {instance_id}, \'{preview}\');' 
	),
	INSTANCE_REFERENCE_TYPE => array (
		'xpath' => '//*[@instance_id="{instance_id}"]',
		'mode' => 'article_preview_reference',
		'preview_sql' => 'SELECT * FROM spSaveArticleReferencePreview({article_id}, {instance_id}, \'{preview}\');' 
	),
	INSTANCE_SUPFILE_TYPE => array (
		'xpath' => '//*[@instance_id="{instance_id}"]',
		'mode' => 'article_preview_sup_file',
		'preview_sql' => 'SELECT * FROM spSaveArticleSupFilePreview({article_id}, {instance_id}, \'{preview}\');' 
	),
	INSTANCE_AUTHOR_TYPE => array (
		'preview_sql' => 'SELECT * FROM spSaveArticleAuthorPreview({article_id}, {instance_id}, \'{preview}\');' 
	),
	INSTANCE_FIGURES_LIST_TYPE => array (
		'xpath' => '//*[@object_id="' . FIGURE_HOLDER_OBJECT_ID . '"]',
		'mode' => 'article_figures_list',
		'preview_sql' => 'SELECT * FROM spSaveArticleFiguresListPreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_TABLES_LIST_TYPE => array (
		'xpath' => '//*[@object_id="' . TABLE_HOLDER_OBJECT_ID . '"]',
		'mode' => 'article_tables_list',
		'preview_sql' => 'SELECT * FROM spSaveArticleTablesListPreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_REFERENCES_LIST_TYPE => array (
		'xpath' => '//*[@object_id="' . REFERENCE_HOLDER_OBJECT_ID . '"]',
		'mode' => 'article_references_list',
		'preview_sql' => 'SELECT * FROM spSaveArticleReferencesListPreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_SUP_FILES_LIST_TYPE => array (
		'xpath' => '//*[@object_id="' . SUP_FILE_HOLDER_OBJECT_ID . '"]',
		'mode' => 'article_sup_files_list',
		'preview_sql' => 'SELECT * FROM spSaveArticleSupFilesListPreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_WHOLE_PREVIEW_TYPE => array (
		'xpath' => '/',
		'mode' => '',
		'preview_sql' => 'SELECT * FROM spSaveArticlePreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_TAXON_LIST_TYPE => array (
		'xpath' => '//document',
		'mode' => 'article_taxon_list',
		'preview_sql' => 'SELECT * FROM spSaveArticleTaxonListPreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_AUTHORS_LIST_TYPE => array (
		'preview_sql' => 'SELECT * FROM spSaveArticleAuthorsListPreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_CONTENTS_LIST_TYPE => array (
		'preview_sql' => 'SELECT * FROM spSaveArticleContentsListPreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_CITATION_LIST_TYPE => array (
		'preview_sql' => 'SELECT * FROM spSaveArticleCitationListPreview({article_id}, \'{preview}\');' 
	),
	INSTANCE_LOCALITIES_LIST_TYPE => array (
		'preview_sql' => 'SELECT * FROM spSaveArticleLocalitiesListPreview({article_id}, \'{preview}\');' 
	) 
);
class carticle_preview_generator extends csimple {
	var $m_instancesDetails;
	var $m_templateXslDirName;
	var $m_documentXml;
	var $m_xmlDomDocument;
	var $m_xslContent;
	var $m_instancePreviews;
	var $m_templ;
	var $m_dontGetData;
	var $m_con;
	var $m_documentId;
	var $m_pwtDocumentId;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	var $m_wholeArticlePreview = '';
	var $m_wholePreviewDom;
	var $m_authorPreviews = array ();
	var $m_authorsListPreview = '';
	var $m_contentsListPreview = '';
	var $m_articleLocalities = array ();
	var $m_localitiesListPreview = '';
	var $m_taxaListPreview = '';
	var $m_citationListPreview = '';
	var $m_taxaList = array ();
	var $m_hasFigures = false;
	var $m_hasTables = false;
	var $m_hasLocalities = false;
	var $m_hasTaxa = false;
	var $m_hasData = false;
	var $m_hasReferences = false;
	var $m_documentHTMLXPath;

	function __construct($pFieldTempl) {
		$this->m_instancesDetails = array ();
		$this->m_documentId = $pFieldTempl['document_id'];
		$this->m_documentXml = $pFieldTempl['document_xml'];
		$this->m_templ = $pFieldTempl['templ'];
		$this->m_instancePreviews = array ();
		$this->m_dontGetData = false;
		$this->m_con = new DBCn();
		$this->m_con->Open();
		$this->LoadDocumentData();
	}

	function LoadDocumentData() {
		$lSql = 'SELECT a.*, t.xsl_dir_name
			FROM pjs.articles a
			JOIN pwt.documents d ON d.id = a.pwt_document_id
			JOIN pwt.templates t ON t.id = d.template_id
			WHERE a.id = ' . (int) $this->m_documentId . '
		';
		// var_dump($lSql);
		$this->m_con->Execute($lSql);
		if (! $this->m_con->mRs['id']) {
			$this->SetError(getstr('pwt.noSuchDocument'));
			return;
		}
		$this->m_pwtDocumentId = (int) $this->m_con->mRs['pwt_document_id'];
		$this->m_templateXslDirName = $this->m_con->mRs['xsl_dir_name'];
	}

	function SetDocumentId($pDocumentId) {
		$this->m_documentId = $pDocumentId;
	}

	function SetDocumentXml($pDocumentXml) {
		$this->m_documentXml = $pDocumentXml;
	}

	function SetTemplate($pTemplate) {
		$this->m_templ = $pTemplate;
	}

	function LoadDocumentXml() {
		$lSql = '
			SELECT i.cached_val
			FROM pjs.articles a
			JOIN pjs.article_cached_items i ON i.id = a.xml_cache_id
			WHERE a.id = ' . (int) $this->m_documentId . '
		';
		$this->m_con->Execute($lSql);
		$this->m_documentXml = $this->m_con->mRs['cached_val'];
	}

	protected function GetInstanceViewXPathAndMode($pInstanceType, $pInstanceId, $pReturnViewMode = false) {
		global $gInstanceTypeDetails;
		// var_dump($gInstanceTypeDetails);
		// exit;
		$lResult = $gInstanceTypeDetails[$pInstanceType]['xpath'];
		if ((int) $pReturnViewMode) {
			$lResult = $gInstanceTypeDetails[$pInstanceType]['mode'];
		}
		$lResult = str_replace('{instance_id}', $pInstanceId, $lResult);
		return $lResult;
	}

	protected function GetInstancePreviewSql($pInstanceId, $pInstanceType, $pPreview) {
		global $gInstanceTypeDetails;
		// if($pInstanceType == INSTANCE_SUPFILE_TYPE){
		// var_dump($pPreview);
		// exit;
		// }
		$lResult = $gInstanceTypeDetails[$pInstanceType]['preview_sql'];
		$lResult = str_replace('{article_id}', $this->m_documentId, $lResult);
		$lResult = str_replace('{instance_id}', $pInstanceId, $lResult);
		$lResult = str_replace('{preview}', q($pPreview), $lResult);
		return $lResult;
	}

	/**
	 *
	 * @param cdocument_instance $pInstanceObject        	
	 */
	function RegisterInstance($pInstanceId, $pInstanceType) {
		if (! (int) $pInstanceId || ! (int) $pInstanceType) {
			return;
		}
		switch ($pInstanceType) {
			case (int) INSTANCE_FIGURE_TYPE :
				$this->m_hasFigures = true;
				break;
			case (int) INSTANCE_TABLE_TYPE :
				$this->m_hasTables = true;
				break;
			case (int) INSTANCE_SUPFILE_TYPE :
				$this->m_hasData = true;
				break;
			case (int) INSTANCE_REFERENCE_TYPE :
				$this->m_hasReferences = true;
				break;
		}
		$this->m_instancesDetails[$pInstanceId] = array (
			'view_xpath' => $this->GetInstanceViewXPathAndMode($pInstanceType, $pInstanceId),
			'view_mode' => $this->GetInstanceViewXPathAndMode($pInstanceType, $pInstanceId, 1),
			'instance_type' => $pInstanceType 
		);
	}

	function SetError($pErrMsg) {
		$this->m_errCnt ++;
		$this->m_errMsg = $pErrMsg;
	}

	function RegisterAllInstances() {
		if ((int) $this->m_errCnt) {
			return;
		}
		$this->m_xmlDomDocument = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$lDom = $this->m_xmlDomDocument;
		if (! $lDom->loadXML($this->m_documentXml)) {
			$this->SetError(getstr('pjs.couldNotLoadArticleXml'));
			return;
		}
		$lXPath = new DOMXPath($lDom);
		// Register all figures
		$lFigureNodes = $lXPath->query('//*[@object_id="' . (int) FIGURE_OBJECT_ID . '"]');
		for($i = 0; $i < $lFigureNodes->length; ++ $i) {
			$lInstanceId = $lFigureNodes->item($i)->getAttribute('instance_id');
			$this->RegisterInstance($lInstanceId, (int) INSTANCE_FIGURE_TYPE);
		}
		// Register all plate parts
		$lPlatePartObjectIds = array (
			(int) PLATE_PART_TYPE_A_OBJECT_ID,
			(int) PLATE_PART_TYPE_B_OBJECT_ID,
			(int) PLATE_PART_TYPE_C_OBJECT_ID,
			(int) PLATE_PART_TYPE_D_OBJECT_ID,
			(int) PLATE_PART_TYPE_E_OBJECT_ID,
			(int) PLATE_PART_TYPE_F_OBJECT_ID 
		);
		foreach ( $lPlatePartObjectIds as $lPlatePartObjectId ) {
			$lNodes = $lXPath->query('//*[@object_id="' . $lPlatePartObjectId . '"]');
			for($i = 0; $i < $lNodes->length; ++ $i) {
				$lInstanceId = $lNodes->item($i)->getAttribute('instance_id');
				$this->RegisterInstance($lInstanceId, (int) INSTANCE_PLATE_TYPE);
			}
		}
		// Register all tables
		$lNodes = $lXPath->query('//*[@object_id="' . (int) TABLE_OBJECT_ID . '"]');
		for($i = 0; $i < $lNodes->length; ++ $i) {
			$lInstanceId = $lNodes->item($i)->getAttribute('instance_id');
			$this->RegisterInstance($lInstanceId, (int) INSTANCE_TABLE_TYPE);
		}
		
		// Register all sup files
		$lNodes = $lXPath->query('//*[@object_id="' . (int) SUP_FILE_OBJECT_ID . '"]');
		for($i = 0; $i < $lNodes->length; ++ $i) {
			$lInstanceId = $lNodes->item($i)->getAttribute('instance_id');
			$this->RegisterInstance($lInstanceId, (int) INSTANCE_SUPFILE_TYPE);
		}
		
		// Register all references
		$lNodes = $lXPath->query('//*[@object_id="' . (int) REFERENCE_OBJECT_ID . '"]');
		for($i = 0; $i < $lNodes->length; ++ $i) {
			$lInstanceId = $lNodes->item($i)->getAttribute('instance_id');
			$this->RegisterInstance($lInstanceId, (int) INSTANCE_REFERENCE_TYPE);
		}
		
		// // Register list instances
		$this->RegisterInstance((int) INSTANCE_FIGURES_LIST_INSTANCE_ID, (int) INSTANCE_FIGURES_LIST_TYPE);
		$this->RegisterInstance((int) INSTANCE_TABLES_LIST_INSTANCE_ID, (int) INSTANCE_TABLES_LIST_TYPE);
		$this->RegisterInstance((int) INSTANCE_REFERENCES_LIST_INSTANCE_ID, (int) INSTANCE_REFERENCES_LIST_TYPE);
		$this->RegisterInstance((int) INSTANCE_SUP_FILES_LIST_INSTANCE_ID, (int) INSTANCE_SUP_FILES_LIST_TYPE);
		// $this->RegisterInstance((int) INSTANCE_TAXON_LIST_INSTANCE_ID, (int) INSTANCE_TAXON_LIST_TYPE);
	}

	protected function GenerateXsl() {
		if ((int) $this->m_errCnt) {
			return;
		}
		if (file_exists(PATH_XSL . $this->m_templateXslDirName . "/template_example_preview_base.xsl") && file_exists(PATH_XSL . $this->m_templateXslDirName . "/template_example_preview_custom.xsl")) {			
			$lDomDoc = new DOMDocument();
			$lDomDoc->formatOutput = true;
			
			// XSL stylesheet node
			$lStylesheet = $lDomDoc->createElementNS("http://www.w3.org/1999/XSL/Transform", "xsl:stylesheet");
			$lStylesheet->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
			$lStylesheet->setAttribute("xmlns:tp", "http://www.plazi.org/taxpub");
			$lStylesheet->setAttribute("xmlns:php", "http://php.net/xsl");
			$lStylesheet->setAttribute("xmlns:exslt", "http://exslt.org/common");
			$lStylesheet->setAttribute("exclude-result-prefixes", "php tp xlink xsl");
			$lStylesheet->setAttribute('version', '1.0');
			$lDomDoc->appendChild($lStylesheet);
			
			$lXslsToImport = array (
				PATH_XSL . "static2.xsl",
				PATH_XSL . $this->m_templateXslDirName . "/template_example_preview_base.xsl",
				PATH_XSL . $this->m_templateXslDirName . "/template_example_preview_custom.xsl",
				PATH_XSL . "common_reference_preview.xsl" 
			);
			foreach ( $lXslsToImport as $lCurrentXslPath ) {
				$lXslNode = $lDomDoc->createElement("xsl:import");
				$lXslNode->setAttribute("href", $lCurrentXslPath);
				$lStylesheet->appendChild($lXslNode);
			}
			
			// XSL matching root node
			$lRootMatch = $lDomDoc->createElement("xsl:template");
			$lRootMatch->setAttribute("match", "/document");
			$lStylesheet->appendChild($lRootMatch);
			
			// Applying template with xpath selection and view mode
			foreach ( $this->m_instancesDetails as $lInstanceId => $lInstanceData ) {
				$lXPathInstanceSelector = $lInstanceData['view_xpath'];
				$lMode = $lInstanceData['view_mode'];
				// var_dump($lInstanceId, $lXPathInstanceSelector, $lMode);
				if ($lXPathInstanceSelector) {
					$lVariable = $lDomDoc->createElement("xsl:variable");
					$lVariable->setAttribute('name', 'instance_id' . $lInstanceId);
					$lTemplate = $lVariable->appendChild($lDomDoc->createElement("xsl:apply-templates"));
					$lTemplate->setAttribute("select", replaceInstancePreviewField($lXPathInstanceSelector, $lInstanceId));
					if ($lMode) {
						$lTemplate->setAttribute("mode", $lMode);
					}
					$lFunctionCall = $lDomDoc->createElement("xsl:value-of");
					$lFunctionCall->setAttribute('select', 'php:function(\'SaveInstancePreview\', ' . $lInstanceId . ', exslt:node-set($instance_id' . $lInstanceId . '))');
					$lRootMatch->appendChild($lVariable);
					$lRootMatch->appendChild($lFunctionCall);
				}
			}
			// Output formatting node
			$lOutput = $lDomDoc->createElement("xsl:output");
			$lOutput->setAttribute("method", "html");
			$lOutput->setAttribute("encoding", "UTF-8");
			$lOutput->setAttribute("indent", "yes");
			$lStylesheet->appendChild($lOutput);
			
			$this->m_xslContent = $lDomDoc->saveXML();
		}
	}

	protected function ProcessXsl() {
		if ((int) $this->m_errCnt) {
			return;
		}
		global $gInstancePreviews;
// 		error_reporting(-1);
// 		ini_set('display_errors', 'on');
		if ($this->m_documentXml && $this->m_xslContent) {
			// var_dump($this->m_xslContent);
			// var_dump($this->m_documentXml);
			$lXslParameters = array ();
			$lXslParameters[] = array (
				'namespace' => null,
				'name' => 'pDocumentId',
				'value' => $this->m_pwtDocumentId 
			);
			$lXslParameters[] = array (
				'namespace' => null,
				'name' => 'pInArticleMode',
				'value' => 1 
			);
			// error_reporting(-1);
			// ini_set('display_errors', 'on');
			// trigger_error('START ' . USE_PREPARED_STATEMENTS . ' ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
			$lHtml = transformXmlWithXsl($this->m_documentXml, $this->m_xslContent, $lXslParameters, 0);
			if ($lHtml === false) {
				$this->SetError(getstr('pwt.couldNotTransformXml'));
				return;
			}
			// trigger_error('END ' . USE_PREPARED_STATEMENTS . ' ' . date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
			// exit;
			// error_reporting(0);
			// var_dump($lHtml);
			// var_dump($gInstancePreviews);
			// exit;
		}
		
		// var_dump($gInstancePreviews);
		$this->m_instancePreviews = $gInstancePreviews;
		// var_dump($this->GetInstancePreview(INSTANCE_TAXON_LIST_INSTANCE_ID));
		// exit();
	}

	function GetInstancePreview($pInstanceId) {
		if (! array_key_exists($pInstanceId, $this->m_instancesDetails)) {
			return '';
		}
		$lResult = $this->m_instancePreviews[$pInstanceId];		
		return $lResult;
	}

	protected function GenerateArticleWholePreview() {
		global $gDocumentHtmlXPath;
		$lXslPath = PATH_XSL . $this->m_templateXslDirName . '/template_example_preview_full.xsl';
		$lXslParameters[] = array (
			'namespace' => null,
			'name' => 'pEditableHeaderReplacementText',
			'value' => PREVIEW_EDITABLE_HEADER_REPLACEMENT_TEXT 
		);
		$lXslParameters[] = array (
			'namespace' => null,
			'name' => 'pDocumentId',
			'value' => $this->m_pwtDocumentId 
		);
		$lXslParameters[] = array (
			'namespace' => null,
			'name' => 'pInArticleMode',
			'value' => 1 
		);		
// 		error_reporting(-1);
// 		ini_set('display_errors', 'on');
		$lHtml = transformXmlWithXsl($this->m_documentXml, $lXslPath, $lXslParameters);
// 		var_dump($lHtml);
// 		exit;
		
		$lDomHtml = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$this->m_wholePreviewDom = $lDomHtml;
		$lDomHtml->loadHTML($lHtml);
		$lDomHtml->normalizeDocument();
		$lDomHtml->preserveWhiteSpace = false;
		$lDomHtml->formatOutput = false;
		
		$lHtml = posCitations($lDomHtml, $lHtml, $this->m_pwtDocumentId, 'figure_position', 'fig-citation', 'fignumber');
		// позициониране на таблиците
		$lHtml = posCitations($lDomHtml, $lHtml, $this->m_pwtDocumentId, 'table_position', 'tbls-citation', 'tblnumber');
		
		$this->m_documentHTMLXPath = new DOMXPath($lDomHtml);
// 		var_dump($lDomHtml->saveHTML());
		$gDocumentHtmlXPath = $this->m_documentHTMLXPath; 
		$lNode = $this->m_documentHTMLXPath->query('//div[@class="P-Article-Preview"]');
		if ($lNode->length) {
			$this->m_wholeArticlePreview = $lDomHtml->saveHTML($lNode->item(0));
		}
	}

	protected function GetAuthorsSql() {
		$lSql = "
			SELECT  du.first_name, du.middle_name, du.last_name,
				du.affiliation, du.city, du.country,
				u.photo_id, u.uname as email, u.website, u.id as usrid,
				(case when du.co_author=1 then ' - Corresponding author'
				else '' end) as is_corresponding, du.zoobank_id
			FROM pjs.document_users du
			JOIN public.usr u ON u.id = du.uid
			WHERE du.document_id = " . (int) $this->m_documentId . " AND role_id = " . (int) PJS_AUTHOR_ROLE_ID . " order by du.ord ASC
		";
		// var_dump($lSql);
		return $lSql;
	}

	protected function GetMetadataSql() {
		$lSql = 'SELECT to_char(d.approve_date, \'DD Mon YYYY\') as approve_date, 
						to_char(d.publish_date, \'DD Mon YYYY\') as publish_date, 
						to_char(d.create_date, \'DD Mon YYYY\') as create_date,
						d.supporting_agencies_texts as supporting_agencies_texts,
						(select string_agg(acronym || \' - \' || title, \'</div><div class="supp_agencies">\') from supporting_agencies where id = any(d.supporting_agencies_ids)) as supporting_agencies_texts2,
					EXTRACT(year FROM d.publish_date) as pubyear,
					j.name as journal_name, d.doi, d.start_page, d.end_page,
					d.name as article_title,
					i."number" as issue_number,
					d.id as article_id
			FROM pjs.documents d
			LEFT JOIN pjs.journal_issues i ON i.id = d.issue_id
			LEFT JOIN public.journals j ON j.id = i.journal_id
			WHERE d.id = ' . (int) $this->m_documentId . '	
		';
		// var_dump($lSql);
		return $lSql;
	}

	protected function GenerateArticleAuthorPreviews() {
		$lSql = $this->GetAuthorsSql();
		
		$this->m_con->Execute($lSql);
		$lAuthorsArr = array ();
		while ( ! $this->m_con->Eof() ) {
			$lCurrentRow = $this->m_con->mRs;
			$lAuthorsArr[] = $lCurrentRow;
			$lAuthorId = $lCurrentRow['usrid'];
			$this->m_con->MoveNext();
			$lPreview = new crs_display_array(array (
				'input_arr' => array (
					$lCurrentRow 
				),
				'templs' => array (
					G_ROWTEMPL => 'article.single_author_preview_row' 
				) 
			));
			$this->m_authorPreviews[$lAuthorId] = $lPreview->Display();
		}
		$lAuthorsPreview = new crs_display_array(array (
			'input_arr' => $lAuthorsArr,
			'templs' => array (
				G_HEADER => 'article.authors_preview_head',
				G_FOOTER => 'article.authors_preview_foot',
				G_STARTRS => 'article.authors_preview_start',
				G_ENDRS => 'article.authors_preview_end',
				G_NODATA => 'article.authors_preview_nodata',
				G_ROWTEMPL => 'article.authors_preview_row' 
			) 
		));
		
		$lSql = '
			SELECT  du.first_name, du.middle_name, du.last_name, du.ord,
				du.affiliation, du.city, du.country,
				u.photo_id, u.uname as email, u.website, u.id as usrid,
				0 as is_corresponding, du.zoobank_id
			FROM pjs.document_users du
			JOIN public.usr u ON u.id = du.uid
			WHERE du.document_id = ' . (int) $this->m_documentId . ' AND role_id = ' . (int) PJS_SE_ROLE_ID . ' order by du.ord ASC
		';
		
		$lSEPreview = new crs(array (
			'sqlstr' => 'SELECT  u.first_name, u.middle_name, u.last_name,
							u.affiliation, u.addr_city as city, (select "name" from countries where id = u.country_id) as country,
							u.photo_id, u.uname as email, u.website, u.id as usrid
						FROM pjs.document_users du
						JOIN public.usr u ON u.id = du.uid
						WHERE du.document_id = ' . (int) $this->m_documentId . ' AND role_id = 3	
			',
			
			'templs' => array (
				G_HEADER => 'article.authors_se_preview_head',
				G_FOOTER => 'article.authors_se_preview_foot',
				G_STARTRS => 'article.authors_se_preview_start',
				G_ENDRS => 'article.authors_se_preview_end',
				G_NODATA => 'article.authors_se_preview_nodata',
				G_ROWTEMPL => 'article.authors_se_preview_row' 
			) 
		));
		
		$lSupAPreview = new crs(array (
			'sqlstr' => 'SELECT 
							\'\' as acronym, d.supporting_agencies_texts as title
						FROM pjs.documents d
						WHERE d.id = ' . (int) $this->m_documentId . ' and d.supporting_agencies_texts is not null
						UNION
							select acronym, title from supporting_agencies where id = ANY((select supporting_agencies_ids from pjs.documents where id = ' . (int) $this->m_documentId . ')::int[])
						order by acronym	
		',
			
			'templs' => array (
				G_HEADER => 'article.supp_agencies_preview_head',
				G_FOOTER => 'article.supp_agencies_preview_foot',
				G_STARTRS => 'article.supp_agencies_preview_startrs',
				G_ENDRS => 'article.supp_agencies_preview_end',
				G_NODATA => 'article.supp_agencies_preview_nodata',
				G_ROWTEMPL => 'article.supp_agencies_preview_row' 
			) 
		));
		
		$lSql = $this->GetMetadataSql();
		
		$lPreview = new crs(array (
			'sqlstr' => $lSql,
			'authors' => $lAuthorsPreview->Display(),
			'sup_a' => $lSupAPreview->Display(),
			'se' => $lSEPreview->Display(),
			
			'templs' => array (
				G_HEADER => 'article.authors_list_template' 
			) 
		));
		
		$this->m_authorsListPreview = $lPreview->Display();
		// var_dump($this->m_authorPreviews, $this->m_authorsListPreview);
		// exit;
	}

	protected function GenerateArticleContentsListPreview() {
		$lSql = "
			SELECT i.id as instance_id, 
					replace(replace(display_name, 'Title & Authors', 'Article title'), 'Abstract & Keywords', 'Abstract') as display_name, 
					1 as level, pos, null as parent_instance_id, 0 as has_children 
					FROM pwt.document_object_instances i 
					JOIN pjs.articles a ON a.pwt_document_id = i.document_id 
					WHERE i.object_id IN (9, 153, 15) AND a.id = " . (int) $this->m_documentId . "
				UNION
			SELECT * FROM spGetArticleContentsInstances(" . (int) $this->m_documentId . ") ORDER BY pos OFFSET 1;
		";
		// var_dump($lSql);
		$lPreview = new crsrecursive(array (
			'recursivecolumn' => 'parent_instance_id',
			'templadd' => 'has_children',
			// 'sqlstr' => 'SELECT * FROM spGetDocumentTreeFast(' . $this->m_pwtDocumentId . ', 0);',
			'sqlstr' => $lSql,
			'templs' => array (
				G_HEADER => 'article.contents_list_head',
				G_FOOTER => 'article.contents_list_foot',
				G_STARTRS => 'article.contents_list_start',
				G_ENDRS => 'article.contents_list_end',
				G_NODATA => 'article.contents_list_nodata',
				G_ROWTEMPL => 'article.contents_list_row' 
			),
			'document_id' => $this->m_pwtDocumentId 
		));
		
		$this->m_contentsListPreview = $lPreview->Display();
		// var_dump($this->m_contentsListPreview);
		// exit;
	}

	protected function GenerateArticleCitationListPreview() {
		$lSql = $this->GetAuthorsSql();
		$lAuthorsPreview = new crs(array (
			'sqlstr' => $lSql,
			'templs' => array (
				G_HEADER => 'article.citations_authors_preview_head',
				G_FOOTER => 'article.citations_authors_preview_foot',
				G_STARTRS => 'article.citations_authors_preview_start',
				G_ENDRS => 'article.citations_authors_preview_end',
				G_NODATA => 'article.citations_authors_preview_nodata',
				G_ROWTEMPL => 'article.citations_authors_preview_row' 
			) 
		));
		
		// var_dump($lSql);
		$lPreview = new crs(array (
			'sqlstr' => $this->GetMetadataSql(),
			'templs' => array (
				G_HEADER => 'article.citation' 
			),
			'author_names' => $lAuthorsPreview,
			'document_id' => $this->m_pwtDocumentId 
		));
		
		$this->m_citationListPreview = $lPreview->Display();
	}

	/**
	 * Generate a list of all the localities in the preview,
	 * mark all localities with their respective treatment/checklist (if they are in 1)
	 * and generate the localities list preview
	 */
	protected function GenerateLocalitiesPreview() {
		$lXPath = new DOMXPath($this->m_wholePreviewDom);
		$lLocalitiesXPath = ('//*[@data-is-locality-coordinate]');
		$lXPathResult = $lXPath->query($lLocalitiesXPath);
		$this->m_articleLocalities = array ();
		foreach ( $lXPathResult as $lCurrentLocality ) {
			$lLatitude = $lCurrentLocality->getAttribute('data-latitude');
			$lLongitude = $lCurrentLocality->getAttribute('data-longitude');
			if (! is_array($this->m_articleLocalities[$lLatitude])) {
				$this->m_articleLocalities[$lLatitude] = array ();
			}
			if (! is_array($this->m_articleLocalities[$lLatitude][$lLongitude])) {
				$this->m_articleLocalities[$lLatitude][$lLongitude] = array ();
			}
			$lParentsQuery = './ancestor::*[@data-is-checklist-taxon]|./ancestor::*[@data-is-taxon-treatment]';
			$lParentNode = $lXPath->query($lParentsQuery, $lCurrentLocality);
			if ($lParentNode->length) {
				$lParentInstanceId = (int) $lParentNode->item(0)->getAttribute('instance_id');
				if ($lParentInstanceId) {
					if (! in_array($lParentInstanceId, $this->m_articleLocalities[$lLatitude][$lLongitude])) {
						$this->m_articleLocalities[$lLatitude][$lLongitude][] = $lParentInstanceId;
					}
					$lInstancesWithCoordinates[] = $lParentInstanceId;
				}
			}
		}
		// var_dump($this->m_articleLocalities);
		// exit;
		
		if (count($lInstancesWithCoordinates)) {
			$lInstancesWithCoordinates = array_unique($lInstancesWithCoordinates);
		} else {
			$lInstancesWithCoordinates[] = 0;
		}
		if (count($this->m_articleLocalities)) {
			$this->m_hasLocalities = true;
			$lSql = '
				SELECT id, display_name
				FROM pwt.document_object_instances i
				WHERE i.document_id = ' . (int) $this->m_pwtDocumentId . ' AND i.id IN (' . implode(',', $lInstancesWithCoordinates) . ')
				ORDER BY i.pos ASC
			';
			$lPreview = new crs(array (
				'sqlstr' => $lSql,
				'templs' => array (
					G_HEADER => 'article.localities_list_head',
					G_FOOTER => 'article.localities_list_foot',
					G_STARTRS => 'article.localities_list_start',
					G_ENDRS => 'article.localities_list_end',
					G_NODATA => 'article.localities_list_nodata',
					G_ROWTEMPL => 'article.localities_list_row' 
				),
				'document_id' => $this->m_pwtDocumentId 
			));
		} else {
			$lPreview = new csimple(array (
				'templs' => array (
					G_DEFAULT => 'article.localities_nolocalities' 
				),
				'document_id' => $this->m_pwtDocumentId 
			));
		}
		$this->m_localitiesListPreview = $lPreview->Display();
		
		// var_dump($this->m_localitiesListPreview);
		// exit;
	}

	protected function GetTaxaList() {
		$lXPath = new DOMXPath($this->m_wholePreviewDom);
		$lTaxaXPath = ('//*[@class="tn"]');
		$lXPathResult = $lXPath->query($lTaxaXPath);
		$this->m_taxaList = array ();
		$lAttributeNameThatHoldsPartType = 'class';
		$lAttributeNameThatHoldsPartValue = 'full-name';
		
		$lPartsThatLeadToSelf = array (
			'kingdom',
			'regnum',
			'subkingdom',
			'subregnum',
			'division',
			'phylum',
			'subdivision',
			'subphylum',
			'superclass',
			'superclassis',
			'class',
			'classis',
			'subclass',
			'subclassis',
			'superorder',
			'superordo',
			'order',
			'ordo',
			'suborder',
			'subordo',
			'infraorder',
			'infraordo',
			'superfamily',
			'superfamilia',
			'family',
			'familia',
			'subfamily',
			'subfamilia',
			'tribe',
			'tribus',
			'subtribe',
			'subtribus',
			'genus',
			'subgenus',
			'above-genus' 
		);
		$lPartsThatDontLeadToSelf = array (
			'species' => array (
				'genus',
				'species' 
			),
			'subspecies' => array (
				'genus',
				'species',
				'subspecies' 
			),
			'variety' => array (
				'genus',
				'species',
				'variety' 
			),
			'varietas' => array (
				'genus',
				'species',
				'varietas' 
			),
			'form' => array (
				'genus',
				'species',
				'form' 
			),
			'forma' => array (
				'genus',
				'species',
				'forma' 
			) 
		);
		$lPartsQuery = '';
		foreach ( $lPartsThatLeadToSelf as $lPartName ) {
			if ($lPartsQuery != '') {
				$lPartsQuery .= '|';
			}
			$lPartsQuery .= './/*[@' . $lAttributeNameThatHoldsPartType . '="' . $lPartName . '"]';
		}
		foreach ( $lPartsThatDontLeadToSelf as $lPartName => $lData ) {
			if ($lPartsQuery != '') {
				$lPartsQuery .= '|';
			}
			$lPartsQuery .= './/*[@' . $lAttributeNameThatHoldsPartType . '="' . $lPartName . '"]';
		}
		
		$lPartsThatLeadToSelfQuery = '';
		foreach ( $lPartsThatLeadToSelf as $lPartName ) {
			if ($lPartsThatLeadToSelfQuery != '') {
				$lPartsThatLeadToSelfQuery .= '|';
			}
			$lPartsThatLeadToSelfQuery .= './/*[@' . $lAttributeNameThatHoldsPartType . '="' . $lPartName . '"]';
		}
		$lPartsThatDontLeadToSelfQuery = '';
		foreach ( $lPartsThatDontLeadToSelf as $lPartName => $lData ) {
			if ($lPartsThatDontLeadToSelfQuery != '') {
				$lPartsThatDontLeadToSelfQuery .= '|';
			}
			$lPartsThatDontLeadToSelfQuery .= './/*[@' . $lAttributeNameThatHoldsPartType . '="' . $lPartName . '"]';
		}
		$lTaxaForListPreview = array ();
		// var_dump($lXPathResult->length);
		foreach ( $lXPathResult as $lCurrentTaxonNode ) {
			$lCurrentTaxonNames = array ();
			$lPartsLeadingToSelfNodes = $lXPath->query($lPartsThatLeadToSelfQuery, $lCurrentTaxonNode);
			foreach ( $lPartsLeadingToSelfNodes as $lPart ) {
				$lTaxonName = trim($lPart->getAttribute($lAttributeNameThatHoldsPartValue));
				if ($lTaxonName == '') {
					$lTaxonName = trim($lPart->textContent);
				}
				$lCurrentTaxonNames[] = $lTaxonName;
				$this->m_taxaList[] = $lTaxonName;
			}
			$lPartsNotLeadingToSelfNodes = $lXPath->query($lPartsThatDontLeadToSelfQuery, $lCurrentTaxonNode);
			foreach ( $lPartsNotLeadingToSelfNodes as $lPart ) {
				$lTaxonName = '';
				$lPartType = $lPart->getAttribute($lAttributeNameThatHoldsPartType);
				foreach ( $lPartsThatDontLeadToSelf[$lPartType] as $lNecessaryPartType ) {
					$lNecessaryPartQuery = './/*[@' . $lAttributeNameThatHoldsPartType . '="' . $lNecessaryPartType . '"]';
					$lNecessaryPartNodes = $lXPath->query($lNecessaryPartQuery, $lCurrentTaxonNode);
					$lPartValue = '';
					if ($lNecessaryPartNodes->length) {
						$lNode = $lNecessaryPartNodes->item(0);
						$lPartValue = trim($lNode->getAttribute($lAttributeNameThatHoldsPartValue));
						if ($lPartValue == '') {
							$lPartValue = trim($lNode->textContent);
						}
						// if($lPartValue == 'kathanus'){
						// var_dump($lNode->ownerDocument->saveXML($lCurrentTaxonNode));
						// var_dump($lTaxonName);
						// exit;
						// }
					}
					if ($lPartValue != '') {
						if ($lTaxonName != '') {
							$lTaxonName .= ' ';
						}
						$lTaxonName .= $lPartValue;
					}
				}
				$lCurrentTaxonNames[] = $lTaxonName;
				$this->m_taxaList[] = trim($lTaxonName);
			}
			
			$lTaxonUsage = (int) TAXON_NAME_USAGE_INLINE;
			$lParentQueries = array (
				'./ancestor-or-self::*[@class="figure"]' => (int) TAXON_NAME_USAGE_FIGURE,
				'./ancestor-or-self::*[@data-checklist-taxon-title]' => (int) TAXON_NAME_USAGE_CHECKLIST_TREATMENT,
				'./ancestor-or-self::*[@data-taxon-treatment-title]' => (int) TAXON_NAME_USAGE_TREATMENT,
				'./ancestor-or-self::*[@data-id-key-taxon-name]' => (int) TAXON_NAME_USAGE_ID_KEY 
			);
			$lTaxonTreatmentId = 0;
			foreach ( $lParentQueries as $lQuery => $lUsage ) {
				$lNodeResult = $lXPath->query($lQuery, $lCurrentTaxonNode);
				if ($lNodeResult->length) {
					$lTaxonUsage = $lUsage;
					if ($lUsage == TAXON_NAME_USAGE_TREATMENT) {
						$lTaxonTreatmentId = $lNodeResult->item(0)->getAttribute('date-treatment-id');
					}
					break;
				}
			}
			
			$lCurrentTaxonNodeClone = $lCurrentTaxonNode->cloneNode(true);
			
			foreach ( $lXPath->query($lPartsQuery, $lCurrentTaxonNodeClone) as $lCurrentPart ) {
				$lPartValue = trim($lCurrentPart->getAttribute($lAttributeNameThatHoldsPartValue));
				if ($lPartValue != '') {
					$lCurrentPart->nodeValue = $lPartValue;
				}
			}
			$lTaxonHtml = $lCurrentTaxonNodeClone->ownerDocument->saveXML($lCurrentTaxonNodeClone);
			$lTaxonText = $lCurrentTaxonNodeClone->textContent;
			$lCurrentTaxonNode->setAttribute('data-taxon-parsed-name', $lTaxonText);
			$lTaxonForListPreview = array (
				'html' => $lTaxonHtml,
				'text' => array (
					$lTaxonText 
				),
				'base_text' => $lTaxonText,
				'names' => $lCurrentTaxonNames,
				'usage' => array (
					$lTaxonUsage 
				),
				'treatment_id' => $lTaxonTreatmentId,
				'occurrences' => 1 
			);
			if (! $this->CheckIfTaxonIsInArrayForListPreview($lTaxaForListPreview, $lTaxonForListPreview)) {
				$lTaxaForListPreview[] = $lTaxonForListPreview;
			}
		}
		usort($lTaxaForListPreview, function ($pTaxon1, $pTaxon2) {
			return strcmp($pTaxon1['base_text'], $pTaxon2['base_text']);
		});
		
		$lPreview = new crs_display_array(array (
			'input_arr' => $lTaxaForListPreview,
			'templs' => array (
				G_HEADER => 'article.taxa_list_head',
				G_FOOTER => 'article.taxa_list_foot',
				G_STARTRS => 'article.taxa_list_start',
				G_ENDRS => 'article.taxa_list_end',
				G_NODATA => 'article.taxa_list_nodata',
				G_ROWTEMPL => 'article.taxa_list_row' 
			) 
		));
		
		$this->m_taxaListPreview = $lPreview->Display();
		// var_dump($this->m_taxaListPreview);
		// exit;
		$this->m_taxaList = array_unique($this->m_taxaList);
		if (count($this->m_taxaList)) {
			$this->m_hasTaxa = true;
		}
		
		$lNode = $lXPath->query('//div[@class="P-Article-Preview"]');
		if ($lNode->length) {
			$this->m_wholeArticlePreview = $this->m_wholePreviewDom->saveHTML($lNode->item(0));
		}
		// var_dump($this->m_wholeArticlePreview);
		// exit;
	}

	protected function CheckIfTaxonIsInArrayForListPreview(&$pArrayForList, &$pTaxon) {
		foreach ( $pArrayForList as $lKey => $lCurrentTaxon ) {
			if (count($lCurrentTaxon['names']) != count($pTaxon['names'])) {
				continue;
			}
			foreach ( $lCurrentTaxon['names'] as $lName ) {
				if (! in_array($lName, $pTaxon['names'])) {
					continue 2;
				}
			}
			if ((int) $pTaxon['treatment_id']) {
				$pArrayForList[$lKey]['treatment_id'] = (int) $pTaxon['treatment_id'];
			}
			$pArrayForList[$lKey]['usage'] = array_unique(array_merge($pArrayForList[$lKey]['usage'], $pTaxon['usage']));
			$pArrayForList[$lKey]['text'] = array_unique(array_merge($pArrayForList[$lKey]['text'], $pTaxon['text']));
			$pArrayForList[$lKey]['occurrences'] += $pArrayForList[$lKey]['occurrences'];
			return true;
		}
		return false;
	}

	protected function ImportGeneratedPreviews() {
		if ($this->m_errCnt) {
			return;
		}
		try {
			$lCon = $this->m_con;
			if (! $lCon->Execute('BEGIN TRANSACTION;')) {
				throw new Exception(getstr('pwt.couldNotBeginTransaction'));
			}
			foreach ( $this->m_instancesDetails as $lInstanceId => $lInstanceDetails ) {
				$lPreview = $this->GetInstancePreview($lInstanceId);
				$lInstanceType = $lInstanceDetails['instance_type'];
				$this->SaveElementPreview($lInstanceId, $lInstanceType, $lPreview);
			}
			// Whole preview
			$this->SaveElementPreview(INSTANCE_WHOLE_PREVIEW_INSTANCE_ID, INSTANCE_WHOLE_PREVIEW_TYPE, $this->m_wholeArticlePreview);
			// Author previews
			foreach ( $this->m_authorPreviews as $lAuthorId => $lPreview ) {
				$this->SaveElementPreview($lAuthorId, INSTANCE_AUTHOR_TYPE, $lPreview);
			}
			$this->SaveElementPreview(INSTANCE_AUTHORS_LIST_INSTANCE_ID, INSTANCE_AUTHORS_LIST_TYPE, $this->m_authorsListPreview);
			
			// Contents list previews
			$this->SaveElementPreview(0, INSTANCE_CONTENTS_LIST_TYPE, $this->m_contentsListPreview);
			// Citation list previews
			$this->SaveElementPreview(0, INSTANCE_CITATION_LIST_TYPE, $this->m_citationListPreview);
			// Localities list
			$this->SaveElementPreview(0, INSTANCE_LOCALITIES_LIST_TYPE, $this->m_localitiesListPreview);
			$this->SaveArticleLocalities();
			// Taxa
			$this->SaveElementPreview(0, INSTANCE_TAXON_LIST_TYPE, $this->m_taxaListPreview);
			$this->SaveArticleTaxa();
			
			// Metrics
			$this->CreateArticleMetrics();
			
			$this->SaveArticleElementsExistence();
			
			if (! $lCon->Execute('COMMIT TRANSACTION;')) {
				throw new Exception(getstr('pwt.couldNotBeginTransaction'));
			}
		} catch ( Exception $lException ) {
			$lCon->Execute('ROLLBACK TRANSACTION;');
			$this->SetError($lException->getMessage());
		}
	}

	protected function SaveElementPreview($pInstanceId, $pInstanceType, $pPreview) {
		$lSql = $this->GetInstancePreviewSql($pInstanceId, $pInstanceType, $pPreview);
		$this->ExecuteTransactionalQuery($lSql);
	}

	protected function ExecuteTransactionalQuery($pSql) {
		if (! $this->m_con->Execute($pSql)) {
			throw new Exception($this->m_con->GetLastError());
		}
	}

	protected function SaveArticleLocalities() {
		$this->ExecuteTransactionalQuery('SELECT * FROM spClearArticleLocalities(' . (int) $this->m_documentId . ')');
		foreach ( $this->m_articleLocalities as $lLatitude => $lDetails ) {
			foreach ( $lDetails as $lLongitude => $lInstanceIds ) {
				$lInstanceIds = array_unique(array_map('intval', $lInstanceIds));
				$lSql = 'SELECT * FROM spSaveArticleLocality(' . (int) $this->m_documentId . ', ' . (float) $lLatitude . ', ' . (float) $lLongitude . ', ARRAY[' . implode(',', $lInstanceIds) . ']::bigint[]);';
				$this->ExecuteTransactionalQuery($lSql);
			}
		}
	}

	protected function SaveArticleTaxa() {
		$this->ExecuteTransactionalQuery('SELECT * FROM spClearArticleTaxa(' . (int) $this->m_documentId . ')');
		foreach ( $this->m_taxaList as $lTaxonName ) {
			$lSql = 'SELECT * FROM spSaveArticleTaxon(' . (int) $this->m_documentId . ', \'' . q($lTaxonName) . '\');';
			$this->ExecuteTransactionalQuery($lSql);
		}
	}

	protected function SaveArticleElementsExistence() {
		$lSql = '
			UPDATE pjs.articles SET
				has_figures = ' . (int) $this->m_hasFigures . '::boolean,
				has_tables = ' . (int) $this->m_hasTables . '::boolean,
				has_localities = ' . (int) $this->m_hasLocalities . '::boolean,
				has_taxa = ' . (int) $this->m_hasTaxa . '::boolean,
				has_data = ' . (int) $this->m_hasData . '::boolean,
				has_references = ' . (int) $this->m_hasReferences . '::boolean
			WHERE id = ' . (int) $this->m_documentId . '
		';
		$this->ExecuteTransactionalQuery($lSql);
	}

	protected function CreateArticleMetrics() {
		$lSql = 'SELECT * FROM spSaveArticlePDFMetric(' . (int) $this->m_documentId . ');';
		$this->ExecuteTransactionalQuery($lSql);
		$lSql = 'SELECT * FROM spSaveArticleXMLMetric(' . (int) $this->m_documentId . ');';
		$this->ExecuteTransactionalQuery($lSql);
	}

	function GetData() {
		if ($this->m_dontGetData) {
			return;
		}
		if ($this->m_documentXml == '') {
			$this->LoadDocumentXml();
		}
		$this->GeneratePreviews();
		$this->ImportGeneratedPreviews();
		$this->m_dontGetData = true;
	}

	protected function CheckIfArticleHasData() {
		if ($this->m_errCnt || $this->m_hasData) {
			return;
		}
		$lXPath = new DOMXPath($this->m_xmlDomDocument);
		$lMaterialsQuery = '//*[@object_id=' . MATERIAL_OBJECT_ID . ']';
		if ($lXPath->query($lMaterialsQuery)->length) {
			$this->m_hasData = true;
			return;
		}
	}

	protected function GeneratePreviews() {
		$this->GenerateArticleWholePreview();
		$this->RegisterAllInstances();		
		$this->GenerateXsl();		
		$this->ProcessXsl();		
		$this->GenerateArticleAuthorPreviews();
		$this->GenerateArticleContentsListPreview();
		$this->GenerateArticleCitationListPreview();
		$this->GenerateLocalitiesPreview();
		$this->GetTaxaList();
		$this->CheckIfArticleHasData();
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