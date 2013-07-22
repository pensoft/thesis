<?php 
class cfield_input extends cfield_base {
	function GetToStringRepresentation() {
		return $this->m_parsedFieldValue;
	}


	function Display() {
		$lTemplate = G_INPUT_TEMPL;
		$lLabelTemplate = G_INPUT_LABEL_TEMPL;
		switch ($this->m_htmlControlType){
			case FIELD_HTML_VIDEO_YOUTUBE_LINK_TYPE:{
				$lTemplate = G_INPUT_VIDEO_YOUTUBE_LINK_TEMPL;
				$lLabelTemplate = G_INPUT_VIDEO_YOUTUBE_LINK_LABEL_TEMPL;
				break;
			}			
		}
		if((int)$this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate($lLabelTemplate));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		$lInput = '<input ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" type="text" name="' . $this->m_fieldHtmlIdentifier . '" id="' . $this->m_fieldHtmlIdentifier . '" value="' . h($this->m_parsedFieldValue) . '" />';
		$this->m_pubdata['field'] = $lInput;
		return $this->ReplaceHtmlFields($this->getObjTemplate($lTemplate));
	}

}

?>