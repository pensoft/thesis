<?php 
class cfield_radio extends cfield_base{
	var $m_selectedValues;
	var $m_isMultiple;
	var $m_inputType;
	var $m_separator;
	var $m_rowTempl;
	var $m_labelTempl;
	var $m_holderTempl;
	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);
		
		switch ($this->m_htmlControlType){
			case FIELD_HTML_RADIO_TYPE:{
				$this->m_isMultiple = false;
				$this->m_selectedValues = array($this->m_parsedFieldValue);
				$this->m_inputType = 'radio';	

				$this->m_rowTempl = G_RADIO_ROW_TEMPL;				
				$this->m_labelTempl = G_RADIO_LABEL_TEMPL;
				$this->m_holderTempl = G_RADIO_TEMPL;
				break;
			}
			case FIELD_HTML_CHECKBOX_TYPE:{
				$this->m_isMultiple = true;
				if(is_array($this->m_parsedFieldValue)){
					$this->m_selectedValues = $this->m_parsedFieldValue;	
				}else{
					$this->m_selectedValues = array($this->m_parsedFieldValue);
				}
				$this->m_inputType = 'checkbox';
				
				$this->m_rowTempl = G_CHECKBOX_ROW_TEMPL;				
				$this->m_labelTempl = G_CHECKBOX_LABEL_TEMPL;
				$this->m_holderTempl = G_CHECKBOX_TEMPL;
				break;
			}
		}
	}
	
	function GetToStringRepresentation() {
		$lSrcValues = getFieldSelectOptions($this->m_srcQuery, (int)$this->m_pubdata['document_id'], (int)$this->m_pubdata['instance_id']);
		
		$lResult = '';
		$lSelectedItems = 0;
		foreach ($lSrcValues as $lId => $lDisplayName) {
			if(in_array($lId, $this->m_selectedValues)){
				if($lSelectedItems > 0){
					$lResult .= ITEMS_STRING_REPRESENTATION_DELIMITER;
				}
				$lSelectedItems++;
				$lResult .= $lDisplayName;
			}	
		}
		return $lResult;		
	}

	
	function Display() {
		if((int)$this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate($this->m_labelTempl));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		//Взимаме възможните стойности
		$lSrcValues = getFieldSelectOptions($this->m_srcQuery, (int)$this->m_pubdata['document_id'], (int)$this->m_pubdata['instance_id']);		
		
		$lSelectedValuesArr = $this->m_selectedValues;		
		
		$lField = '';
		foreach ($lSrcValues as $lId => $lDisplayName) {
			$this->m_pubdata['input'] = '<input ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' id="' . $this->m_fieldHtmlIdentifier . '_' . $lId . '" name="' . $this->m_fieldHtmlIdentifier . ($this->m_isMultiple ? '[]' : '') . '" type=' . $this->m_inputType . ' value="' . $lId . '"' . (in_array($lId, $lSelectedValuesArr)  ? ' checked="checked"' : '') . '/>';
			$this->m_pubdata['label'] = $lDisplayName;
			$this->m_pubdata['label_for'] = $this->m_fieldHtmlIdentifier . '_' . $lId;
			$lField .= $this->ReplaceHtmlFields($this->getObjTemplate($this->m_rowTempl));
		}		
		$this->m_pubdata['field'] = $lField;
		return $this->ReplaceHtmlFields($this->getObjTemplate($this->m_holderTempl));
		
	}

	
}




?>