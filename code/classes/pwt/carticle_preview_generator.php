<?php
define('INSTANCE_FIGURE_TYPE', 1);
define('INSTANCE_PLATE_TYPE', 2);
define('INSTANCE_TABLE_TYPE', 3);
define('INSTANCE_REFERENCE_TYPE', 4);
define('INSTANCE_SUPFILE_TYPE', 5);
define('INSTANCE_FIGURES_LIST_TYPE', 6);
define('INSTANCE_TABLES_LIST_TYPE', 7);
define('INSTANCE_REFERENCES_LIST_TYPE', 8);
define('INSTANCE_SUP_FILES_LIST_TYPE', 9);

define('INSTANCE_FIGURES_LIST_INSTANCE_ID', - 1);
define('INSTANCE_TABLES_LIST_INSTANCE_ID', - 2);
define('INSTANCE_REFERENCES_LIST_INSTANCE_ID', - 3);
define('INSTANCE_SUP_FILES_LIST_INSTANCE_ID', - 4);
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
	) 
);
class carticle_preview_generator extends csimple {
	var $m_instancesDetails;
	var $m_templateXslDirName;
	var $m_documentXml;
	var $m_xslContent;
	var $m_instancePreviews;
	var $m_templ;
	var $m_dontGetData;
	var $m_con;
	var $m_documentId;
	var $m_errCnt = 0;
	var $m_errMsg = '';
	function __construct($pFieldTempl) {
		$this->m_instancesDetails = array ();
		$this->m_templateXslDirName = $pFieldTempl ['template_xsl_dirname'];
		$this->m_documentId = $pFieldTempl ['document_id'];
		$this->m_documentXml = $pFieldTempl ['document_xml'];
		$this->m_templ = $pFieldTempl ['templ'];
		$this->m_instancePreviews = array ();
		$this->m_dontGetData = false;
		$this->m_con = new DBCn();
		$this->m_con->Open();
	}
	function SetDocumentId($pDocumentId) {
		$this->m_documentId = $pDocumentId;
	}
	function SetDocumentXml($pDocumentXml) {
		$this->m_documentXml = $pDocumentXml;
	}
	function SetTemplateXslDirName($pDirName) {
		$this->m_templateXslDirName = $pDirName;
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
		$this->m_documentXml = $this->m_con->mRs ['cached_val'];		
	}
	protected function GetInstanceViewXPathAndMode($pInstanceType, $pInstanceId, $pReturnViewMode = false) {
		global $gInstanceTypeDetails;
// 		var_dump($gInstanceTypeDetails);
// 		exit;
		$lResult = $gInstanceTypeDetails [$pInstanceType] ['xpath'];
		if ((int) $pReturnViewMode) {
			$lResult = $gInstanceTypeDetails [$pInstanceType] ['mode'];
		}
		$lResult = str_replace('{instance_id}', $pInstanceId, $lResult);
		return $lResult;
	}
	protected function GetInstancePreviewSql($pInstanceId, $pInstanceType, $pPreview) {
		global $gInstanceTypeDetails;
		$lResult = $gInstanceTypeDetails [$pInstanceType] ['preview_sql'];
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
		$this->m_instancesDetails [$pInstanceId] = array (
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
		$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
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
		
		// Register list instances
		$this->RegisterInstance((int) INSTANCE_FIGURES_LIST_INSTANCE_ID, (int) INSTANCE_FIGURES_LIST_TYPE);
		$this->RegisterInstance((int) INSTANCE_TABLES_LIST_INSTANCE_ID, (int) INSTANCE_TABLES_LIST_TYPE);
		$this->RegisterInstance((int) INSTANCE_REFERENCES_LIST_INSTANCE_ID, (int) INSTANCE_REFERENCES_LIST_TYPE);
		$this->RegisterInstance((int) INSTANCE_SUP_FILES_LIST_INSTANCE_ID, (int) INSTANCE_SUP_FILES_LIST_TYPE);
	}
	protected function GenerateXsl() {
		if (file_exists(PATH_XSL . $this->m_templateXslDirName . "/template_example_preview_base.xsl") && file_exists(PATH_XSL . $this->m_templateXslDirName . "/template_example_preview_custom.xsl")) {
			$docroot = getenv('DOCUMENT_ROOT');
			require_once ($docroot . '/lib/static_xsl.php');
			
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
				$lXPathInstanceSelector = $lInstanceData ['view_xpath'];
				$lMode = $lInstanceData ['view_mode'];
// 				var_dump($lXPathInstanceSelector, $lMode);
				if ($lXPathInstanceSelector && $lMode) {
					$lVariable = $lDomDoc->createElement("xsl:variable");
					$lVariable->setAttribute('name', 'instance_id' . $lInstanceId);
					$lTemplate = $lVariable->appendChild($lDomDoc->createElement("xsl:apply-templates"));
					$lTemplate->setAttribute("select", replaceInstancePreviewField($lXPathInstanceSelector, $lInstanceId));
					$lTemplate->setAttribute("mode", $lMode);
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
		global $gInstancePreviews;
		// error_reporting(-1);
		// ini_set('display_errors', 'on');
		if ($this->m_documentXml && $this->m_xslContent) {
// 			var_dump($this->m_xslContent);
			// var_dump($this->m_documentXml);
			$lXslParameters = array ();
			// error_reporting(-1);
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
	}
	function GetInstancePreview($pInstanceId) {
		if (! array_key_exists($pInstanceId, $this->m_instancesDetails)) {
			return '';
		}
		$lResult = $this->m_instancePreviews [$pInstanceId];
		return $lResult;
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
				$lInstanceType = $lInstanceDetails ['instance_type'];
				$lSql = $this->GetInstancePreviewSql($lInstanceId, $lInstanceType, $lPreview);				
				if (! $lCon->Execute($lSql)) {
					throw new Exception($lCon->GetLastError());
				}
			}
			if (! $lCon->Execute('COMMIT TRANSACTION;')) {
				throw new Exception(getstr('pwt.couldNotBeginTransaction'));
			}
		} catch ( Exception $lException ) {
			$lCon->Execute('ROLLBACK TRANSACTION;');
			$this->SetError($lException->getMessage());
		}
	}
	function GetData() {
		if ($this->m_dontGetData) {
			return;
		}
		if ($this->m_documentXml == '') {
			$this->LoadDocumentXml();
		}
		$this->RegisterAllInstances();
		$this->GenerateXsl();
		$this->ProcessXsl();
		$this->ImportGeneratedPreviews();
		$this->m_dontGetData = true;
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