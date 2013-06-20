<?php
/**
	Същата функционалност като обикновен crs само че вместо от базата ще взима редовете
	от xml подаден като параметър. Няма да има странициране и освен xml-a като параметри
	се подават следните заявки
		row_select_xpath_query - xpath заявка, която указва кои възли да излизат като редове
		row_fields_query - масив от xpath заявки, които определят кои части от текущия възел да излизат на редовете, като атрибути
			формата е :
				името на полето => масив за данните във формат
					xpath => xpath заявка която се изчислява за полето
					evaluate => true/false в зависимост дали искаме да вземем възлите (query) или резултата от заявката (evaluate)
						http://bg2.php.net/manual/en/domxpath.evaluate.php
						
*/
class crs_xml extends cbase {
	var $m_recordCount;
	var $m_currentRecord;	
	var $m_defTempls;	
	var $m_RecordNumber;	
	var $m_dontgetdata;
	var $m_xmlDocument;
	var $m_xpath;
	var $m_errCnt;
	var $m_errMsg;
	var $m_rowResults;

	function __construct($pFieldTempl) {
		$this->m_state = 0;
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		$this->m_Templs = $this->m_pubdata['templs'];		
		$this->LoadDefTempls();
		$this->m_errCnt = 0;
		$this->m_xmlDocument = new DOMDocument("1.0");
		$this->m_xmlDocument->resolveExternals = true;		
		if( !$this->m_xmlDocument->loadXML($this->m_pubdata['xml'])){
			$this->SetError(getstr('global.wrongXml'));
		}
		$this->m_xpath = new DOMXPath($this->m_xmlDocument);
		/**
			Ako v xpath-a ni trqbvat namespace-ове trqbva da sa podadeni kato masiv vyv format
				name => url
		*/
		foreach($this->m_pubdata['register_xpath_namespaces'] as $lNamespaceName => $lNamespaceUrl){
			$this->m_xpath->registerNamespace($lNamespaceName, $lNamespaceUrl);
		}
	}
	
	private function SetError($pErrStr){
		$this->m_errCnt++;
		$this->m_errMsg .= $pErrStr . "\n";
	}
	
	function DontGetData($p) {
		$this->m_dontgetdata = $p;
	}	
	
	function LoadDefTempls() {
		if (!defined('D_EMPTY')) {
			define('D_EMPTY', 'global.empty');
		}
		
		$this->m_defTempls = array(
			G_HEADER => D_EMPTY, 
			G_FOOTER => D_EMPTY, 
			G_STARTRS => D_EMPTY, 
			G_ENDRS => D_EMPTY, 
			G_NODATA => D_EMPTY, 
			G_PAGEING => D_EMPTY, 
			G_ROWTEMPL => D_EMPTY,
			G_ERR_ROW => D_EMPTY
		);
	}
	
	function SetTemplate($pTemplId) {
		$this->m_HTempl = $pTemplId;
	}
	
	function GetRowFromRs($pKey) {
		return $this->m_currentRecord[$pKey];
	}
	
	function CheckVals() {
		if($this->m_state == 0) {
			//~ if (((int)$this->m_pubdata['storyid']) !== $this->m_pubdata['storyid']) {
				//~ return;
				//~ trigger_error("NE e int", E_USER_WARNING);
			//~ }
			$this->m_state++;
		} else {
			// NOTICE
		}
	}
	
	function GetData() {
		$this->CheckVals();
		if ($this->m_state >= 1 ) {//Ако няма грешки при зареждането на xml-a			
			$this->m_state++;
			if( !$this->m_errCnt ){				
				$this->m_rowResults = $this->m_xpath->query($this->m_pubdata['row_select_xpath_query']);
				$this->m_recordCount = $this->m_rowResults->length;
				$this->m_pubdata['records'] = $this->m_recordCount;							
				if ($this->m_recordCount) {
					$this->m_pubdata['rownum'] = 1;					
				}
			}
		}
	}
	//Изпълнява завките за взимане на полетата от текущия ред
	private function GetRowFields($pCurrentRow) {
		foreach($this->m_pubdata['row_fields_query'] as $lFieldName => $lFieldData){
			$lFieldXPathQuery = $lFieldData['xpath'];
			if( !(int) $lFieldData['evaluate'] ){//Взимаме текста на възлите
				$lFieldResult = $this->m_xpath->query($lFieldXPathQuery, $pCurrentRow);
				$lValue = '';
				if( $lFieldResult->length ){
					$lValue = $lFieldResult->item(0)->textContent;
				}
				$this->m_pubdata[$lFieldName] = $this->m_currentRecord[$lFieldName] = $lValue;
			}else{//Взимаме резултата от заявката
				$this->m_pubdata[$lFieldName] = $this->m_currentRecord[$lFieldName] = $this->m_xpath->evaluate($lFieldXPathQuery, $pCurrentRow);
			}
		}		
	}
	
	private function GetRows() {
		for ($i = 0; $i < $this->m_recordCount; ++$i) {
			$lCurrentRow = $this->m_rowResults->item($i);
			$this->GetRowFields($lCurrentRow);
			
			if ($this->m_pubdata['templadd'])
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL , $this->m_currentRecord[$this->m_pubdata['templadd']]));
			else 
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
				
			$this->m_pubdata['rownum']++;			
		}
		return $lRet;
	}
	
	function Display() {
		if (!$this->m_dontgetdata)
			$this->GetData();
		
		if ($this->m_state < 2) {			
			return;
		}		
		
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_HEADER));
		if( $this->m_errCnt ){
			$this->m_pubdata['err_msg'] = $this->m_errMsg;
			$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ERR_ROW));
		}else{
			if ($this->m_recordCount == 0) {
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_NODATA));
			} else {
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_STARTRS));
				$lRet .= $this->GetRows();
				$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ENDRS));
			}
		}
		$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_FOOTER));
		
		return $lRet;
	}
}
?>