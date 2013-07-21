<?php
class cinstance_preview_generator extends csimple {
	var $m_instancesDetails;
	var $m_templateXslDirName;
	var $m_documentXml;
	var $m_xslContent;
	var $m_instancePreviews;
	var $m_templ;
	var $m_dontGetData;

	function __construct($pFieldTempl){
		$this->m_instancesDetails = array();
		$this->m_templateXslDirName = $pFieldTempl['template_xsl_dirname'];
		$this->m_documentId = $pFieldTempl['document_id'];
		$this->m_documentXml = $pFieldTempl['document_xml'];
		$this->m_templ = $pFieldTempl['templ'];
		$this->m_instancePreviews = array();
		$this->m_dontGetData = false;
	}

	function SetDocumentId($pDocumentId){
		$this->m_documentId = $pDocumentId;
	}

	function SetDocumentXml($pDocumentXml){
		$this->m_documentXml = $pDocumentXml;
	}

	function SetTemplateXslDirName($pDirName){
		$this->m_templateXslDirName = $pDirName;
	}

	function SetTemplate($pTemplate){
		$this->m_templ = $pTemplate;
	}

	/**
	 *
	 * @param cdocument_instance $pInstanceObject
	 */
	function registerInstance($pInstanceObject){
		if(! $pInstanceObject instanceof cdocument_instance || !$pInstanceObject->m_instanceId){
			return;
		}
		$this->m_instancesDetails[$pInstanceObject->m_instanceId] = array(
			'instance_ref' => $pInstanceObject,
			'view_xpath' => $pInstanceObject->m_view_xpath,
			'view_mode' => $pInstanceObject->m_view_mode
		);
	}

	protected function generateXsl(){
		if(file_exists(PATH_XSL  . $this->m_templateXslDirName . "/template_example_preview_base.xsl")
			&& file_exists(PATH_XSL  . $this->m_templateXslDirName . "/template_example_preview_custom.xsl")
		){
			$docroot = getenv('DOCUMENT_ROOT');
			require_once($docroot . '/lib/static_xsl.php');


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

			// Import base xsl file
			$lBaseXsl = $lDomDoc->createElement("xsl:import");
			$lBaseXsl->setAttribute("href", PATH_XSL  . $this->m_templateXslDirName . "/template_example_preview_base.xsl");
			$lStylesheet->appendChild($lBaseXsl);

			// Import custom xsl file
			$lCustomXsl = $lDomDoc->createElement("xsl:import");
			$lCustomXsl->setAttribute("href", PATH_XSL  . $this->m_templateXslDirName . "/template_example_preview_custom.xsl");
			$lStylesheet->appendChild($lCustomXsl);

			// Import references xsl file
			$lReferencesXsl = $lDomDoc->createElement("xsl:import");
			$lReferencesXsl->setAttribute("href", PATH_XSL  .  "common_reference_preview.xsl");
			$lStylesheet->appendChild($lReferencesXsl);
			
			$lReferencesXsl = $lDomDoc->createElement("xsl:import");
			$lReferencesXsl->setAttribute("href", PATH_XSL  .  "static2.xsl");
			$lStylesheet->appendChild($lReferencesXsl);

			// XSL matching root node
			$lRootMatch = $lDomDoc->createElement("xsl:template");
			$lRootMatch->setAttribute("match", "/document");
			$lStylesheet->appendChild($lRootMatch);

			// Applying template with xpath selection and view mode
			foreach ($this->m_instancesDetails as $lInstanceId => $lInstanceData){
				$lXPathInstanceSelector = $lInstanceData['view_xpath'];
				$lMode = $lInstanceData['view_mode'];
				if($lXPathInstanceSelector && $lMode){
					$lVariable = $lDomDoc->createElement("xsl:variable");
					$lVariable->setAttribute('name', 'instance_id' . $lInstanceId);
					$lTemplate = $lVariable->appendChild($lDomDoc->createElement("xsl:apply-templates"));
					$lTemplate->setAttribute("select", replaceInstancePreviewField($lXPathInstanceSelector, $lInstanceId));
					$lTemplate->setAttribute("mode", $lMode);
					$lFunctionCall = $lDomDoc->createElement("xsl:value-of");
					$lFunctionCall->setAttribute('select', 'php:function(\'SaveInstancePreview\', ' . $lInstanceId . ', exslt:node-set($instance_id' . $lInstanceId  . '))');
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

	protected function ProcessXsl(){
		global $gInstancePreviews;
// 		error_reporting(-1);
// 		ini_set('display_errors', 'on');
		if($this->m_documentXml && $this->m_xslContent){
// 			var_dump($this->m_xslContent);
// 			var_dump($this->m_documentXml);
			$lXslParameters = array();
// 			error_reporting(-1);
// 			trigger_error('START ' . USE_PREPARED_STATEMENTS . ' ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
			$lHtml = transformXmlWithXsl($this->m_documentXml, $this->m_xslContent, $lXslParameters, 0);
// 			trigger_error('END ' . USE_PREPARED_STATEMENTS . ' ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
// 			exit;
// 			error_reporting(0);
// 			var_dump($lHtml);
// 			var_dump($gInstancePreviews);
// 			exit;
		}
// 		var_dump($this->m_documentXml, $this->m_xslContent);
// 		var_dump($gInstancePreviews);
		$this->m_instancePreviews = $gInstancePreviews;
	}

	function GetInstancePreview($pInstanceId){
		if(!array_key_exists($pInstanceId, $this->m_instancesDetails)){
			return '';
		}
		$lResult = $this->m_instancePreviews[$pInstanceId];
		if($lResult == ''){
			$lInstanceObject = $this->m_instancesDetails[$pInstanceId]['instance_ref'];
			$lInstanceObject->GetData(true);
			$lResult = $lInstanceObject->GetToStringRepresentation();
			$this->m_instancePreviews[$pInstanceId] = $lResult;
		}
		return $lResult;
	}

	function GetData(){
		if($this->m_dontGetData){
			return;
		}
		$this->generateXsl();
		$this->ProcessXsl();
		$this->m_dontGetData = true;
	}

	function ReplaceHtmlFields($pStr){
		return preg_replace("/\{%(.*?)%\}/e", "\$this->HtmlPrepare('\\1')", $pStr);
	}

	function HtmlPrepare($pInstanceId){
// 		trigger_error('REP ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		return $this->GetInstancePreview($pInstanceId);
	}

	function Display() {
		if(! $this->m_dontGetData)
			$this->GetData();
// 		trigger_error('START DI ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		$lRet .= $this->ReplaceHtmlFields($this->m_templ);
// 		trigger_error('END DI ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6), E_USER_NOTICE);
		return $lRet;
	}


}



?>