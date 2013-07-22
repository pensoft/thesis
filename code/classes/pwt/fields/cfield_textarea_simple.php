<?php
class cfield_textarea_simple extends cfield_base {
	function GetToStringRepresentation() {
		return $this->m_parsedFieldValue;
	}
	function Display() {
		if (( int ) $this->m_pubdata ['display_label']) {
			$lLabelTempl = G_TEXTAREA_SIMPLE_LABEL_TEMPL;
			switch ($this->m_htmlControlType) {
				case ( int ) FIELD_HTML_ROUNDED_SIMPLE_TEXTAREA :
					$lLabelTempl = G_TEXTAREA_SIMPLE_ROUNDED_LABEL_TEMPL;
					break;
				case ( int ) FIELD_HTML_TEXTAREA_PLATE_DESCRIPTION_TYPE :
					$lLabelTempl = G_TEXTAREA_PLATE_DESCRIPTION_LABEL_TEMPL;
					break;
			}
			$this->m_pubdata ['field_label'] = $this->ReplaceHtmlFields( $this->getObjTemplate( $lLabelTempl ) );
		} else {
			$this->m_pubdata ['field_label'] = '';
		}
		$lInput = '<textarea ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' name="' . $this->m_fieldHtmlIdentifier . '" >' . h( $this->m_parsedFieldValue ) . '</textarea>';
		$this->m_pubdata ['field'] = $lInput;
		$lTemplate = G_TEXTAREA_SIMPLE_TEMPL;
		switch ($this->m_htmlControlType) {
			case ( int ) FIELD_HTML_ROUNDED_SIMPLE_TEXTAREA :
				$lTemplate = G_TEXTAREA_SIMPLE_ROUNDED_TEMPL;
				break;
			case ( int ) FIELD_HTML_TEXTAREA_PLATE_DESCRIPTION_TYPE :
				$lTemplate = G_TEXTAREA_PLATE_DESCRIPTION_TEMPL;
				break;
		}
		return $this->ReplaceHtmlFields( $this->getObjTemplate( $lTemplate ) );
	}
}

?>