<?php
/*
	Ще използваме този клас за да качва keys export. 	
*/
class ckeys_uploader{
	private $m_exportId;	
	private $m_errCnt;
	private $m_errMsg;
	private $m_reportMsg;
	private $m_checkIsSuccessful;
	private $m_checkError;
	private $m_apiKey;
	function __construct($pExportId){		
		$this->m_exportId = (int)$pExportId;
		$this->m_errCnt = 0;
		$this->m_errMsg = '';
		$this->m_reportMsg = '';
		$this->m_checkIsSuccessful = false;
		$this->m_checkError = '';
		$this->m_apiKey = $this->getJournalApiKey();
	}
	
	function getReportMsg(){
		return $this->m_reportMsg;
	}
	
	function getErrCnt(){
		return $this->m_errCnt;
	}
	
	function getErrMsg(){
		return $this->m_errMsg;
	}
	
	private function setErrMsg($pMsg){
		$this->m_errCnt++;
		$this->m_errMsg .= $pMsg;
	}
	
	function getCheckResult(){
		return $this->m_checkIsSuccessful;
	}
	
	function getCheckError(){
		return $this->m_checkError;
	}
	
	/**
	 * 	Гледаме дали апикей-а за това списание е попълнен. Ако не е попълнен - грешка.
		След това трябва да преглеждаме xml-a и да съобщаваме за грешка, ако някой от възлите е празен
	*/
	private function performChecks($pXml){
		if($this->m_apiKey == ''){
			$this->m_checkError = getstr('admin.keys_export.uploadEmptyApiKeyForThisJournal');
			return;
		}
		
		$lDom = new DOMDocument("1.0");
		$lDom->resolveExternals = true;		
		
		if( !$lDom->loadXML($pXml)){//Ako xml-a e greshen
			$this->m_checkError = getstr('admin.keys_export.uploadXmlIsNotValid');
			return;
		}
		$lXPath = new DOMXPath($lDom);
		$lElementsQuery = '//*';
		$lElements = $lXPath->query($lElementsQuery);
		for($i = 0; $i < $lElements->length; ++$i){
			$lCurrentElement = $lElements->item($i);
			if($lCurrentElement->nodeType != 1)
				return;
			if(trim($lCurrentElement->textContent) == ''){
				//Ако има празен възел - грешка
				$this->m_checkError = getstr('admin.keys_export.uploadEmptyElementNode') . $lCurrentElement->nodeName;
				return;
			}
		}
		
		
		$this->m_checkIsSuccessful = true;
	}
	
	/*
		Генерираме xml-а, качваме картинките и качваме самия xml
	*/
	function getData(){
		try{
			$lTransformedXml = $this->getExportTransformedXml();			
			//~ var_dump(GetFormattedXml($lTransformedXml));
			$this->performChecks($lTransformedXml);
			//Спираме, ако проверката открие грешка
			if(!$this->m_checkIsSuccessful)
				return;
						
			$this->uploadTransformedXml($lTransformedXml);
			
		}catch(Exception $pException){
			$this->setErrMsg($pException->getMessage());
		}
	}
	
	/*
		Взима xml-a на експорта от базата
	*/
	function getExportTransformedXml(){
		$lCon = Con();
		$lSql = 'SELECT xml, is_uploaded FROM keys_export WHERE id = ' . (int) $this->m_exportId;
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		if((int) $lCon->mRs['is_uploaded']){
			throw new Exception(getstr('admin.keys_export.thisExportHasBeenUploaded'));
		}
		return $lCon->mRs['xml'];
	}
	
	/*
		Качва xml-а в страницата на keys.		
	*/
	function uploadTransformedXml($pXml){
		$lUploadUrl = KEYS_EXPORT_UPLOAD_URL . $this->m_apiKey;
//		var_dump($lUploadUrl);
//		exit;
		$lCurlHandler = curl_init($lUploadUrl);
		curl_setopt($lCurlHandler, CURLOPT_HEADER, 1);	
		curl_setopt($lCurlHandler, CURLINFO_HEADER_OUT, 1);	
		
		curl_setopt($lCurlHandler, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($lCurlHandler, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($lCurlHandler, CURLOPT_MAXREDIRS, 3);
		curl_setopt($lCurlHandler, CURLOPT_TIMEOUT, 30);
		
		curl_setopt($lCurlHandler, CURLOPT_POST, 1);
		curl_setopt($lCurlHandler, CURLOPT_POSTFIELDS, $pXml);
		curl_setopt($lCurlHandler, CURLOPT_HTTPHEADER, array("Accept: application/xml","Content-Type: application/xml"));	
		//~ curl_setopt($lCurlHandler, CURLOPT_INFILE, $lReadStream);
		
		$lResult = curl_exec($lCurlHandler);	
		$lReturnCode = curl_getinfo($lCurlHandler, CURLINFO_HTTP_CODE); 
		//~ $lReturnCode1 = curl_getinfo($lCurlHandler, CURLINFO_HEADER_OUT); 
		//~ var_dump($lReturnCode1, $lResult, $lReturnCode);
		curl_close($lCurlHandler);
		
		if($lReturnCode == 404 ){
			throw new Exception(getstr('admin.keys_export.uploadIncompleteXml')); 
		}
		if($lReturnCode == 403 ){
			throw new Exception(getstr('admin.keys_export.uploadWrongApiKey')); 
		}
		if($lReturnCode != 200 && $lReturnCode != 201 ){//Това са възможните кодове за успех. Всичко друго е грешка
			throw new Exception(getstr('admin.keys_export.uploadUnknownError')); 
		}
		$this->m_reportMsg = getstr('admin.keys_export.uploadSuccessfulUpload');		
	}
	
	/**
	 * 
	 * Взимаме apikey-а за съответното списание от базата. Ако за това списание не е въведен apikey
	 * ползваме default-ен
	 */
	private function getJournalApiKey(){
		$lCon = Con();
		$lSql = 'SELECT j.keys_apikey as apikey
			FROM keys_export e
			JOIN articles a ON a.id = e.article_id
			JOIN journals j ON j.id = a.journal_id 
			WHERE e.id = ' . (int) $this->m_exportId;
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lApiKey = trim($lCon->mRs['apikey']);		
		return $lApiKey;
	}

}





?>