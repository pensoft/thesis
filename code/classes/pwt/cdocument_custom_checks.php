<?php
/**
	Този клас ще реализира проверките за по-сложни връзки между полетата
*/
require_once PATH_CLASSES . SITE_NAME. '/custom_checks.php';

class cdocument_custom_checks extends csimple {
	var $m_documentId;
	var $m_instanceId;
	var $m_documentXml;
	var $m_errorsArr;
	var $m_xmlDom;
	var $m_xpath;
	var $m_dontGetData;
	//Валидация или след save
	var $m_mode;

	var $m_rules;
	var $m_xmlIsLoaded;
	var $m_useExistingDbConnection;

	function __construct($pFieldArr){
		$this->m_documentId = (int)$pFieldArr['document_id'];
		$this->m_instanceId = (int)$pFieldArr['instance_id'];
		$this->m_mode = (int)$pFieldArr['mode'];
		$this->m_documentXml = $pFieldArr['xml'];

		parent::__construct($pFieldArr);

		$this->m_errorsArr = array();
		$this->m_dontGetData = false;
		$this->m_useExistingDbConnection = (int)$pFieldArr['use_existing_db_connection'];
		$this->GetDocumentXml();
		$this->GetRules();
	}

	protected function GetDocumentXml(){
		if($this->m_documentXml == ''){
			if(!$this->m_useExistingDbConnection){
				$lCon = new DBCn();
				$lCon->Open();
			}else{
				$lCon = Con();
				$lCon->CloseRs();
			}
			$lCon->Execute('SELECT doc_xml FROM pwt.documents WHERE id = ' . (int)$this->m_documentId);
			$this->m_documentXml = $lCon->mRs['doc_xml'];
// 			var_dump('SELECT doc_xml FROM pwt.documents WHERE id = ' . (int)$this->m_documentId);
		}
		$this->m_xmlDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
		$this->m_xmlIsLoaded = $this->m_xmlDom->loadXML($this->m_documentXml);
		$this->m_xpath = new DOMXPath($this->m_xmlDom);
	}

	function GetErrors(){
		return $this->m_errorsArr;
	}

	protected function GetRules(){
		if(!$this->m_useExistingDbConnection){
			$lCon = new DBCn();
			$lCon->Open();
		}else{
			$lCon = Con();
			$lCon->CloseRs();
		}
		if($this->m_instanceId){
			$lJoinSql = 'JOIN pwt.document_object_instances p ON p.document_id = i.document_id
			AND p.pos = substring(i.pos, 1, char_length(p.pos)) AND p.id = ' . (int) $this->m_instanceId . '
			';
		}
		$lSql = 'SELECT r.*, i.id as instance_id
		FROM pwt.document_object_instances i
		JOIN pwt.document_template_objects dto ON dto.id = i.document_template_object_id
		JOIN pwt.custom_validation_rules r ON r.template_object_id = dto.template_object_id
		' . $lJoinSql . '
		WHERE i.document_id = ' . (int) $this->m_documentId . ' AND i.is_confirmed = true
		AND ' . (int)$this->m_mode . ' = ANY (r.perform_in_modes)
		ORDER BY i.pos DESC, r.ord ASC
		';

// 		var_dump($lSql);
		$lCon->Execute($lSql);
		while(!$lCon->Eof()){
			$this->m_rules[] = array(
					'instance_id' => $lCon->mRs['instance_id'],
					'function_name' => $lCon->mRs['function_name'],
			);
			$lCon->MoveNext();
		}

	}

	function GetData(){
		if($this->m_dontGetData){
			return;
		}

		$this->m_dontGetData = true;
		if(!$this->m_documentId || !$this->m_mode || !$this->m_xmlIsLoaded){
			trigger_error('Could not perform custom checks!', E_USER_NOTICE);
			return;
		}



		try{

			foreach ($this->m_rules as $lCurrentRule) {
				$this->ExecuteSingleCustomCheck($lCurrentRule['instance_id'], $lCurrentRule['function_name']);
			}

			//Пращаме ексепшън само когато сме обработили breakable грешка
		}catch(Exception $pException){

		}
	}

	function ExecuteSingleCustomCheck($pInstanceId, $pCheckFunctionName){
		$lNode = $this->m_xpath->query('/document/objects//*[@instance_id="' . $pInstanceId . '"]');
		if(!$lNode->length)
			return;
		if(function_exists($pCheckFunctionName)){
			$lResult = call_user_func($pCheckFunctionName, $lNode->item(0), $this->m_mode);
			if(is_array($lResult)){
				$this->m_errorsArr = array_merge($this->m_errorsArr, $lResult);
			}
			foreach($lResult as $lCurrentError){
				if($lCurrentError['error_type']== (int) CUSTOM_CHECK_BREAKABLE_ERROR_TYPE){
					throw new Exception();
				}
			}
		}
	}


}

?>