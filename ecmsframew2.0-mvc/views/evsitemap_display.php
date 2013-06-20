<?php
	class evSiteMap_Display extends evbase_view {
	
	function __construct($pData) {
		parent::__construct($pData);
		$this->m_controllerData = $pData['controller_data'];
	}
	function Display() {	
		header('Content-type: text/xml; charset=UTF-8');
		$this->mDoc = new DOMDocument('1.0', 'UTF-8');
		$this->mDoc->formatOutput = true;
		$this->mURLSet = $this->mDoc->createElement('urlset');
		$this->mURLSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$this->mURLSet->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->mURLSet->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
		
		$this->mDoc->appendChild($this->mURLSet);
		$this->GetXml();
		return $this->mDoc->saveXML();
	}
	function GetXml() {
			$lURL = $this->mDoc->createElement('url');
			$lLoc = $this->mDoc->createElement('loc');
			$lURL->appendChild($lLoc);
		foreach ($this->m_controllerData as $data) {
			$lURL = $this->mDoc->createElement('url');
			$lLoc = $this->mDoc->createElement('loc');
			$lLoc->appendChild($this->mDoc->createTextNode($this->EncodeUrl(''.$data['url'].$data['title'], 1))); // за да превежда българските имена на рубриките
			$lURL->appendChild($lLoc);
			
			if ($data['changefreq']) {
				$changefreq = $this->mDoc->CreateElement('changefreq');
				$changefreq->AppendChild($this->mDoc->CreateTextNode($data['changefreq']));
				$lURL->AppendChild($changefreq);
			}
			if ($data['priority'] >= $lValidVals['priority']['from'] && (float)$this->mDBC->mRs['priority'] <= $lValidVals['priority']['to']) {
				$lPriority = $this->mDoc->createElement('priority');
				$lPriority->appendChild($this->mDoc->createTextNode($data['priority']));
				$lURL->appendChild($lPriority);
			}
			$this->mURLSet->appendChild($lURL);
		}
	}
}
?>
