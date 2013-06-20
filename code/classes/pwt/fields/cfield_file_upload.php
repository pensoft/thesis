<?php
class cfield_file_upload extends cfield_base {
	function GetToStringRepresentation() {
		return $this->m_parsedFieldValue;
	}

	function Display() {
		$lInput = '';
		$lTempl = G_FILE_UPLOAD_TEMPL;
		$lFileData = getUploadedFileIdAndName((int) $this->m_pubdata['document_id'], (int) $this->m_pubdata['instance_id']);

		if(! empty($lFileData)){
			$this->m_pubdata['file_name'] = $lFileData['file_name'];
			$lFileId = (int) $lFileData['file_id'];
		}
		if((int) $this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_FILE_UPLOAD_LABEL_TEMPL));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		$lInput .= '
		<input type="hidden" id="field_' . $this->m_fieldHtmlIdentifier . '" fldattr="0" value="' . (int) $lFileId . '" name="' . $this->m_fieldHtmlIdentifier . '" onchange="getInputFileValue(this);">';
		$this->m_pubdata['field'] = $lInput;

		switch ($this->m_pubdata['html_control_type']) {
			case FIELD_HTML_FILE_UPLOAD_MATERIAL_TYPE :
				$lTempl = G_FILE_UPLOAD_MATERIAL_TEMPL;
				break;
			case FIELD_HTML_FILE_UPLOAD_CHECKLIST_TAXON_TYPE :
				$lTempl = G_FILE_UPLOAD_CHECKLIST_TAXON_TEMPL;
				break;
		}

		return $this->ReplaceHtmlFields($this->getObjTemplate($lTempl));
	}

}

?>