<?php

class csitemap extends cbase {
	protected $mDBC;
	public $mDoc;
	public $mURLSet;
	
	function __construct($pFieldTempl) {
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
	}
	
	function CheckVals() {
		
	}
	
	function GetData() {
		
	}
	
	function GetXml($sql) {
		$this->mDBC = new DBCn;
		$this->mDBC->Open();
		$lValidVals = array(
			'changefreq' => array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'),
			'priority' => array('from' => 0.0, 'to' => 1.0),
		);
		$this->mDBC->Execute($sql);
		$this->mDBC->MoveFirst();
		while (!$this->mDBC->Eof()) {
			$lURL = $this->mDoc->createElement('url');
				$lLoc = $this->mDoc->createElement('loc');
				$lLoc->appendChild($this->mDoc->createTextNode($this->EncodeUrl($this->mDBC->mRs['url'])));
				$lURL->appendChild($lLoc);
				
				if (in_array($this->mDBC->mRs['changefreq'], $lValidVals['changefreq'])) {
					$lChangeFreq = $this->mDoc->createElement('changefreq');
					$lChangeFreq->appendChild($this->mDoc->createTextNode($this->mDBC->mRs['changefreq']));
					$lURL->appendChild($lChangeFreq);
				}
				
				if ((float)$this->mDBC->mRs['priority'] >= $lValidVals['priority']['from'] && (float)$this->mDBC->mRs['priority'] <= $lValidVals['priority']['to']) {
					$lPriority = $this->mDoc->createElement('priority');
					$lPriority->appendChild($this->mDoc->createTextNode((float)$this->mDBC->mRs['priority']));
					$lURL->appendChild($lPriority);
				}
			$this->mURLSet->appendChild($lURL);
			$this->mDBC->MoveNext();
		}
		$this->mDBC->Close();
	}
	
	function Display() {
		$this->mDoc = new DOMDocument('1.0', 'UTF-8');
		$this->mDoc->formatOutput = true;
		$this->mURLSet = $this->mDoc->createElement('urlset');
		$this->mURLSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$this->mURLSet->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->mURLSet->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
		
		if (!is_array($this->m_pubdata['sql'])) {
			$this->GetXml($this->m_pubdata['sql']);
		} else {
			foreach ($this->m_pubdata['sql'] as $sql) {
				$this->GetXml($sql);
			}
		}
		$this->mDoc->appendChild($this->mURLSet);
		return $this->mDoc->saveXML();
	}
}

?>