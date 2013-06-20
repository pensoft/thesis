<?php 
class cfield_textarea_simple extends cfield_base {
	function GetToStringRepresentation() {
		return $this->m_parsedFieldValue;
	}


	function Display() {
		if((int)$this->m_pubdata['display_label']){
			if($this->m_htmlControlType == FIELD_HTML_ROUNDED_SIMPLE_TEXTAREA){
				$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_TEXTAREA_SIMPLE_ROUNDED_LABEL_TEMPL));
			}else{
				$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_TEXTAREA_SIMPLE_LABEL_TEMPL));
			}			
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		$lInput = '<textarea ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' name="' . $this->m_fieldHtmlIdentifier . '" >' . h($this->m_parsedFieldValue) . '</textarea>';
		$this->m_pubdata['field'] = $lInput;
		
		if($this->m_htmlControlType == FIELD_HTML_ROUNDED_SIMPLE_TEXTAREA){
			return $this->ReplaceHtmlFields($this->getObjTemplate(G_TEXTAREA_SIMPLE_ROUNDED_TEMPL));
		}else{
			return $this->ReplaceHtmlFields($this->getObjTemplate(G_TEXTAREA_SIMPLE_TEMPL));
		}		
	}

}

?>