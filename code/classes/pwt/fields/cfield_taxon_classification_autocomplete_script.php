<?php 
class cfield_taxon_classification_autocomplete_script extends cfield_taxon_classification_autocomplete {
	
	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);
	}

	function BuildInput(){
		return '';
	}

	function BuildTree(){
		return '';
	}
	
	function getTreeSelectedValues(){
		return '';
	}
	
	function returnHtml(){
		return $this->m_pubdata['field'];
	}
}
//&instance_id=' . $this->m_instanceId . '&field_id=' . $this->m_fieldId . '

?>