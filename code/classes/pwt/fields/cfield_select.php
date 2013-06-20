<?php 
class cfield_select extends cfield_base{
	var $m_selectedValues;
	var $m_is_multiple;
	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);
		
		switch ($this->m_htmlControlType){
			case FIELD_HTML_SELECT_TYPE:{				
				$this->m_is_multiple = false;
				$this->m_selectedValues = array($this->m_parsedFieldValue);
				break;
			}
			case FIELD_HTML_MULTIPLE_SELECT_TYPE:{
				$this->m_is_multiple = true;
				if(is_array($this->m_parsedFieldValue)){
					$this->m_selectedValues = $this->m_parsedFieldValue;	
				}else{
					$this->m_selectedValues = array($this->m_parsedFieldValue);
				}				
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
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_SELECT_LABEL_TEMPL));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		//Взимаме възможните стойности
		$lSrcValues = getFieldSelectOptions($this->m_srcQuery, (int)$this->m_pubdata['document_id'], (int)$this->m_pubdata['instance_id']);
		$lSelectedValuesArr = $this->m_selectedValues;		
		
		$lField = '<select ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" onkeyup="setDesignSelectValue(\'sel_' . $this->m_fieldHtmlIdentifier . '\', this)" id="sel_' . $this->m_fieldHtmlIdentifier . '" name="' . $this->m_fieldHtmlIdentifier . ($this->m_is_multiple ? '[]' : '') . '"' . ($this->m_is_multiple ? ' multiple="multiple" ' : '') . ' >';
		
		foreach ($lSrcValues as $lId => $lDisplayName) {
			if($lId == 'placeholder')
			{
				$lField .= '<option value="-1" selected="selected" disabled="disabled" style="display: none">' . $lSrcValues['placeholder'][-1] . '</option>';
				continue;
			}
			if(is_array($lDisplayName)) {
				$lOptGroupName = $lId;
				$lField .= '<optgroup label="' . $lOptGroupName . '">';
				foreach ($lDisplayName as $lOptId => $OptlDisplayName) {
					$lField .= '<option value="' . $lOptId . '"' . (in_array($lOptId, $lSelectedValuesArr)  ? ' selected="selected"' : '') . '>' . $OptlDisplayName . '</option>';
				}
				$lField .= '</optgroup>';
			} else {
				$lField .= '<option value="' . $lId . '"' . (in_array($lId, $lSelectedValuesArr)  ? ' selected="selected"' : '') . '>' . $lDisplayName . '</option>';
			}
		}
		$lField .= '</select>';
		$this->m_pubdata['field'] = $lField;
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_SELECT_TEMPL));
		
	}

	
}




?>