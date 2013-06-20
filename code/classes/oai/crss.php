<?php

class crss extends cbase {
	function __construct($pFieldTempl) {
		$this->m_pubdata = array_change_key_case($pFieldTempl, CASE_LOWER);
		
		//~ parent::__construct($pFieldTempl);
	}

	function addNodeText($c, $elname, $elvalue) {
		$n = $this->d->CreateElement($elname);
		$n->AppendChild($this->d->CreateTextNode($elvalue));
		$c->AppendChild($n);
	}
	
	function addNodeCdata($c, $elname, $elvalue) {
		$n = $this->d->CreateElement($elname);
		$n->AppendChild($this->d->createCDATASection($elvalue));
		$c->AppendChild($n);
	}
	
	function GetData() {
		
	}
	function CheckVals() {
		
	}
	function Display() {
		$this->con = new DBCn;
		$this->con->Open();
		
		$this->d = new DOMDocument('1.0', ($this->m_pubdata['encoding'] ? $this->m_pubdata['encoding'] : 'UTF-8'));
		$this->d->formatOutput = true;
		
		$rss = $this->d->CreateElement("rss");
		$rss->SetAttribute("version", "2.0");
		$this->d->AppendChild($rss);
		
		$channel = $this->d->CreateElement("channel");
		$rss->AppendChild($channel);
		
		$this->addNodeText($channel, 'title', $this->m_pubdata['title']);
		$this->addNodeText($channel, 'link', ($this->m_pubdata['link'] ? $this->m_pubdata['link'] : 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
		$this->addNodeText($channel, 'description', $this->m_pubdata['description']);
		$this->addNodeText($channel, 'language', ($this->m_pubdata['language'] ? $this->m_pubdata['language'] : 'bg'));
		$this->addNodeText($channel, 'generator', 'CRSS generator');
		$this->addNodeText($channel, 'webMaster', ($this->m_pubdata['webMaster'] ? $this->m_pubdata['webMaster'] : $this->m_pubdata['authoremail']));
		
		$this->con->Execute($this->m_pubdata['sql']);
		$this->con->MoveFirst();
		while(!$this->con->Eof()) {
			$item = $this->d->CreateElement('item');
			
			$this->addNodeText($item, 'title', $this->con->mRs['title']);
			$this->addNodeText($item, 'link', $this->con->mRs['link']);
			$this->addNodeText($item, 'description', $this->con->mRs['description']);
			
			if ($this->con->mRs['rubrid']) {
				$this->addNodeText($item, 'category', $this->con->mRs['rubrname']);
			}
			
			$this->addNodeText($item, 'pubDate', rssDateTime($this->con->mRs['pubdate']));
			
			$channel->AppendChild($item);
			$this->con->MoveNext();
		}
		return $this->d->SaveXML();
	}
}

if (!function_exists('rssDateTime')) {
	function rssDateTime($pDateStr) {
		if (!preg_match('/(\d+)\/(\d+)\/(\d+) (\d+):(\d+):(\d+)/', $pDateStr, $a)) {
			return '';
		}
		return date('D, d M Y H:i:s O', mktime($a[4], $a[5], $a[6], $a[2], $a[1], $a[3]));
	}
}

?>