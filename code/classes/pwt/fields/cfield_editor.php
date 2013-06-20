<?php 
class cfield_editor extends cfield_base {
	function GetToStringRepresentation() {
		return $this->m_parsedFieldValue;
	}


	function Display() {		
		if((int)$this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_EDITOR_LABEL_TEMPL));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		
		switch(	$this->m_htmlControlType ){
			case (int)FIELD_HTML_EDITOR_TYPE_ONLY_REFERENCE_CITATIONS:
				$lFieldTemplate = G_EDITOR_REFERENCE_CITATIONS_TEMPL;
				break;
			default:
				$lFieldTemplate = G_EDITOR_TEMPL;
				break;
		}
		$lField = '<textarea id="' . $this->m_fieldHtmlIdentifier . '_textarea" ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' class="h380" name="' . $this->m_fieldHtmlIdentifier . '">' . h($this->m_parsedFieldValue) . '</textarea>';
		$this->m_pubdata['field'] = $lField;
		return $this->ReplaceHtmlFields($this->getObjTemplate($lFieldTemplate));
	}

}

?>