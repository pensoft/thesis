<?php 
class cfield_textarea extends cfield_base {
	function GetToStringRepresentation() {
		return $this->m_parsedFieldValue;
	}


	function Display() {		
		$this->m_pubdata['height'] = (int)EDITOR_SMALL_DEFAULT_HEIGHT;
		$this->m_pubdata['width'] = 0;
		$this->m_pubdata['create_common_toolbar_holder'] = 0;
		$this->m_pubdata['use_common_toolbar'] = 0;
		$this->m_pubdata['use_floating_tools'] = 0;
		$this->m_pubdata['common_toolbar_holder_id'] = '';
		$this->m_pubdata['toolbar_name'] = EDITOR_SMALL_TOOLBAR_NAME;
		$this->m_pubdata['floating_tools_toolbar_name'] = EDITOR_FLOATING_TOOLBAR_NAME_BASIC;
		$lTemplate = G_TEXTAREA_TEMPL;
		$lTemplateLabel = G_TEXTAREA_LABEL_TEMPL;
		switch($this->m_htmlControlType ){
			case (int)FIELD_HTML_TEXTAREA_THESIS_TYPE:
				$this->m_pubdata['create_common_toolbar_holder'] = 1;
			case (int)FIELD_HTML_TEXTAREA_ANTITHESIS_TYPE:				
			case (int)FIELD_HTML_TEXTAREA_THESIS_NEXT_COUPLET_TYPE:
			case (int)FIELD_HTML_TEXTAREA_THESIS_TAXON_NAME_TYPE:{
				$this->m_pubdata['use_common_toolbar'] = 1;
				$this->m_pubdata['common_toolbar_holder_id'] = $this->m_pubdata['instance_id'] . '_toolbar';
				$this->m_pubdata['height'] = EDITOR_NEXT_COUPLET_HEIGHT;
				$this->m_pubdata['toolbar_name'] = EDITOR_FULL_TOOLBAR_NAME_NO_MAXIMIZE;
				break;
			}
			case FIELD_HTML_TEXTAREA_TABLE:
				$this->m_pubdata['toolbar_name'] = EDITOR_MODERATE_TABLE_TOOLBAR_NAME;
				$lTemplate = G_TEXTAREA_TABLE_TEMPL;
				$lTemplateLabel = G_TEXTAREA_TABLE_LABEL_TEMPL;
				break;
			case FIELD_HTML_TEXTAREA_MATERIAL_FIELD:
				$this->m_pubdata['use_floating_tools'] = 1;
				$this->m_pubdata['toolbar_name'] = EDITOR_EMPTY_TOOLBAR_NAME;
				$this->m_pubdata['height'] = EDITOR_MATERIAL_FIELD_HEIGHT;
				$this->m_pubdata['floating_tools_toolbar_name'] = EDITOR_FLOATING_TOOLBAR_NAME_MATERIAL;
				break;
			case FIELD_HTML_TEXTAREA_REFERENCE_FIELD:
				$this->m_pubdata['use_floating_tools'] = 1;
				$this->m_pubdata['toolbar_name'] = EDITOR_EMPTY_TOOLBAR_NAME;
				$this->m_pubdata['height'] = EDITOR_REFERENCE_FIELD_HEIGHT;
				$this->m_pubdata['floating_tools_toolbar_name'] = EDITOR_FLOATING_TOOLBAR_NAME_REFERENCE;
				break;
				
		}
		
		switch($this->m_htmlControlType ){
			case (int)FIELD_HTML_TEXTAREA_THESIS_TYPE:				
			case (int)FIELD_HTML_TEXTAREA_ANTITHESIS_TYPE:{
				$this->m_pubdata['height'] = EDITOR_THESIS_HEIGHT;
				$this->m_pubdata['width'] = 0;
			}
		}
		
		
		
		if((int)$this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate($lTemplateLabel));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		$lInput = '<textarea id="' . $this->m_fieldHtmlIdentifier . '_textarea" ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' name="' . $this->m_fieldHtmlIdentifier . '" >' . h($this->m_parsedFieldValue) . '</textarea>';
		$this->m_pubdata['field'] = $lInput;
		return $this->ReplaceHtmlFields($this->getObjTemplate($lTemplate));		
	}

}

?>