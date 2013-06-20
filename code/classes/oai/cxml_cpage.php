<?php
/**
 * Почти същия клас като cpage, само че изпраща header-и 
 * и стрипрва whitespace-овете в xml-a - за да може да го форматираме както искаме в темплейтите
 */
class cxml_cpage extends cpage {
	
	function Display() {
		header("Content-type: text/xml");
		$templ = $this->getObjTemplate(G_MAINBODY);
		$lXml = $this->ReplaceHtmlFields($templ);
		//var_dump($lXml);
		$lXmlDom = new DOMDocument('1.0', 'utf-8');
		$lXmlDom->preserveWhiteSpace = false;
		$lXmlDom->formatOutput = true;
		$lXmlDom->loadXML($lXml);		
		echo $lXmlDom->saveXML();
	}
}
?>