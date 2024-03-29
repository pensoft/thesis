<?php
/*
	Ще използваме този клас за да качва wiki export в страницата на уикито. 
	За целта използваме помощни уики класове. 
*/
class cwiki_uploader{
	private $m_articleId;
	private $m_exportId;
	private $m_wikiObject;
	/**
		Понеже ще ъплоудваме снимките на друго апи - пазим 2 обекта
	 */
	private $m_wikiObjectPhotos;
	private $m_errCnt;
	private $m_errMsg;
	private $m_reportMsg;
	function __construct($pExportId){
		$lCon = Con();
		$lSql = 'SELECT article_id FROM wiki_export WHERE id = ' . (int)$pExportId;
		$lCon->Execute($lSql);
		
		$this->m_articleId = (int)$lCon->mRs['article_id'];
		$this->m_exportId = (int)$pExportId;
		$this->m_wikiObject = new wikipedia(WIKI_ADDRESS);
		$this->m_wikiObjectPhotos = new wikipedia(WIKI_PHOTOS_ADDRESS);
		$this->m_errCnt = 0;
		$this->m_errMsg = '';
		$this->m_reportMsg = '';
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
	
	/*
		Генерираме xml-а, качваме картинките и качваме самия xml
	*/
	function getData(){
		try{
			if( !(int) $this->m_articleId ){
				throw new Exception(getstr('admin.wiki_export.noArticleSelected'));
			}	
			$lLoginData = $this->getWikiLoginData();			
			$this->m_wikiObject->login($lLoginData['username'], $lLoginData['password']);
			$this->m_wikiObjectPhotos->login($lLoginData['username'], $lLoginData['password']);
			$lArticleXml = getArticleXml($this->m_articleId);
			$lXmlImages = transformXmlWithXsl($lArticleXml, PATH_CLASSES . 'xsl/mediawiki_images.xsl');			
			$this->uploadWikiImages($lXmlImages);
			//Взимаме xml-a
			$lTransformedXml = $this->getExportTransformedXml();
			$this->uploadWikiXml($lTransformedXml);
			
			
		}catch(Exception $pException){
			$this->setErrMsg($pException->getMessage());
		}
	}
	
	/*
		Взима от базата потребителското име и паролата, които са указани да се ползват за този експорт
	*/
	function getWikiLoginData(){
		$lCon = Con();
		$lSql = 'SELECT u.username, u.password 
			FROM wiki_login u
			JOIN wiki_export e ON e.wiki_username_id = u.id
			WHERE e.id = ' . (int) $this->m_exportId;
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		return array('username' => $lCon->mRs['username'], 'password' => $lCon->mRs['password']);
	}
	
	/*
		Взима xml-a на експорта от базата
	*/
	function getExportTransformedXml(){
		$lCon = Con();
		$lSql = 'SELECT xml FROM wiki_export WHERE id = ' . (int) $this->m_exportId;
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		return $lCon->mRs['xml'];
	}
	
	
	/*
		Получава снимките във xml формат и ги качва в уикито
		Формата на xml-a e :
		<images>
			<image name="Локално име на снимката">Url адрес на снимката</image>
		
	*/	
	function uploadWikiImages($pImagesXml){
		$lXML = new DOMDocument("1.0");
		
		if (!$lXML->loadXML($pImagesXml)) {	
			return;
		}	
		$lXPath = new DOMXPath($lXML);
		$lImgQuery = '//image';
		$lImgNodes = $lXPath->query($lImgQuery);
		
		/*
			В този темп файл ще държим картинките. Ползваме го понеже в уикито не могат да се качват картинки от интернет адрес, а трябва да е локално
		*/
		$lTempFile = tempnam(sys_get_temp_dir(), 'img');
		for($i = 0; $i < $lImgNodes->length; ++$i){
			$lCurrentImg = $lImgNodes->item($i);
			$lImgName = trim($lCurrentImg->getAttribute('name'));
			$lPicUrl = trim($lCurrentImg->textContent);
			
			if( downloadImage($lTempFile, $lPicUrl) !== false ){
				$lUploadStatus = $this->m_wikiObjectPhotos->upload($lImgName, $lTempFile);				
				if( !$lUploadStatus['upload']['result'] == 'Success' ){//Ако е имало някаква грешка при качването на снимката - трием темп файла и съобщаваме за грешката
					unlink($lTempFile);
					throw new Exception(getstr('admin.wiki_export.couldNotUploadPhoto') . $lPicUrl . getstr('admin.wiki_export.errMsg') . $lUploadStatus['upload']['result']);
				}				
			}else{//Ако е имало някаква грешка при свалянето на снимката - трием темп файла и съобщаваме за грешката
				unlink($lTempFile);
				throw new Exception(getstr('admin.wiki_export.couldNotDownloadPhoto') . $lPicUrl);
			}
		}
		unlink($lTempFile);
	}
	
	/*
		Качва xml-а в уикито.
		При успех попълва кои страници са качени.
	*/
	function uploadWikiXml($pXml){
		// Понеже уикито работи с файл а не със съдържание - правим си темп файл
		$lTempFile = tempnam(sys_get_temp_dir(), 'xml');
		if( $lTempFile === false || file_put_contents($lTempFile, $pXml) === false){
			unlink($lTempFile);//Triem faila i syobshtavame za greshka
			throw new Exception(getstr('admin.wiki_export.couldNotCreateXmlTempFile'));
		}
		$lImportRes = $this->m_wikiObject->import($lTempFile);
		unlink($lTempFile);
		if( $lImportRes === false ){
			throw new Exception(getstr('admin.wiki_export.couldNotExportXmlFile')); 
		}
		if( $lImportRes['error']['code'] ){//Ако е станала грешка
			throw new Exception(getstr('admin.wiki_export.couldNotExportXmlFile') . getstr('admin.wiki_export.error') . $lImportRes['error']['info']); 
		}
		$this->m_reportMsg = getstr('admin.wiki_export.successfulExport') . getstr('admin.wiki_export.pageTitlesExported');
		$lPageTitles = array();
		foreach($lImportRes['import'] as $lCurrentPage){
			$lPageTitles[] = $lCurrentPage['title'];
		}
		$this->m_reportMsg .= implode($lPageTitles, ", \n");		
	}

}





?>