<?php

/**
 * Взимаме всички референции и гледаме на кои от тях ще трябва да добавяме буква към годината
 * @author peterg
 *
 */
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static_xsl.php');
class cdocument_tables extends crs_array {
	function GetData() {
		if($this->m_dontgetdata){
			return;
		}
// 		echo 'Before ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
		parent::GetData();
// 		echo 'After ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
		if ($this->m_state >= 1) {
			$lPreviousRow = null;
// 			error_reporting(-1);
// 			$lHtml = getDocumentTablesPreview($this->m_pubdata['document_id']);

// 			$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);

// 			if($lDom->loadHTML($lHtml)){

// 				$lXPath = new DOMXPath($lDom);
// 				foreach($this->m_resultArr as &$lCurrentRow) {
// 					$lItemId = $lCurrentRow['id'];
// 					$lPreviewNode = $lXPath->query('//div[@id="Table-Preview-Wrapper' . $lItemId . '"]');

// 					if($lPreviewNode->length){
// // 						var_dump($lPreviewNode->item(0)->nodeValue);
// 						$lCurrentRow['preview'] = $lDom->saveXML($lPreviewNode->item(0));
// 					}
// 				}
// 			}
// 			echo 'After html ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
// 			var_dump($lHtml);
		}
// 		echo 'After parse ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
		$this->m_dontgetdata = true;
	}
}

?>