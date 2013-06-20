<?php 
class cfield_input extends cfield_base {
	function GetToStringRepresentation() {
		return $this->m_parsedFieldValue;
	}


	function Display() {
		if((int)$this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_INPUT_LABEL_TEMPL));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		$lInput = '<input ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" type="text" name="' . $this->m_fieldHtmlIdentifier . '" value="' . h($this->m_parsedFieldValue) . '" />';
		$this->m_pubdata['field'] = $lInput;
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_INPUT_TEMPL));
	}

}

?>