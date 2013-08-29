<?php

/**
 * Взимаме всички референции и гледаме на кои от тях ще трябва да добавяме буква към годината
 * @author peterg
 *
 */
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static_xsl.php');

class cdocument_references extends crs_array {
	function GetData() {
		if($this->m_dontgetdata){
			return;
		}
// 		echo 'Before ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
		parent::GetData();
// 		echo 'After ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
		if ($this->m_state >= 1) {
			$lPreviousRow = null;
			$lFieldsToMatch = array('is_website_citation', 'first_author_combined_name', 'authors_count', 'authors_combined_names', 'pubyear');
			foreach($this->m_resultArr as &$lCurrentRow) {
				if($lPreviousRow){
					$lReferencesAreFromTheSameGroup = true;
					foreach($lFieldsToMatch as $lCurrentField){
						if($lCurrentRow['authors_count'] <= 2) {
							if($lPreviousRow[$lCurrentField] != $lCurrentRow[$lCurrentField]){
								$lReferencesAreFromTheSameGroup = false;
								break;
							}
						} else {
							if($lCurrentField != 'authors_combined_names') {
								if($lPreviousRow[$lCurrentField] != $lCurrentRow[$lCurrentField]){
									$lReferencesAreFromTheSameGroup = false;
									break;
								}
							}
						}
					}
					if($lReferencesAreFromTheSameGroup){
						$lPreviousRow['group_has_more_elements'] = 1;
						$lPreviousRow['parsed_pubyear'] = $lPreviousRow['pubyear'] . chr(ord('a') - 1 +  $lPreviousRow['element_in_group_idx']);

						$lCurrentRow['element_in_group_idx'] = $lPreviousRow['element_in_group_idx'] + 1;
						$lCurrentRow['group_has_more_elements'] = 1;
						$lCurrentRow['parsed_pubyear'] = $lCurrentRow['pubyear'] . chr(ord('a') - 1 +  $lCurrentRow['element_in_group_idx']);
					}else{
						$lCurrentRow['element_in_group_idx'] = 1;
						$lCurrentRow['group_has_more_elements'] = 0;
						$lCurrentRow['parsed_pubyear'] = $lCurrentRow['pubyear'];
					}
				}else{
					$lCurrentRow['element_in_group_idx'] = 1;
					$lCurrentRow['parsed_pubyear'] = $lCurrentRow['pubyear'];
					$lCurrentRow['group_has_more_elements'] = 0;
				}
				$lPreviousRow = &$lCurrentRow;
			}
// 			echo 'Before html ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
			getReferenceYearLetter(0, 0, 1, $this);
// 			error_reporting(-1);
			$lHtml = getDocumentReferencesPreview($this->m_pubdata['document_id']);

			$lDom = new DOMDocument('1.0', DEFAULT_XML_ENCODING);
			if(@$lDom->loadHTML($lHtml)){

				$lXPath = new DOMXPath($lDom);
				foreach($this->m_resultArr as &$lCurrentRow) {
					$lReferenceId = $lCurrentRow['id'];
					$lPreviewNode = $lXPath->query('//div[@id="Reference-Preview-Wrapper' . $lReferenceId . '"]');

					if($lPreviewNode->length){
// 						var_dump($lPreviewNode->item(0)->nodeValue);
						$lCurrentRow['preview'] = $lDom->saveXML($lPreviewNode->item(0));
					}
				}
			}
// 			echo 'After html ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
// 			var_dump($lHtml);
		}
// 		echo 'After parse ' .  date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6) . "\n";
		$this->m_dontgetdata = true;
	}
}

?>