<?php
class cfield_facebookautocomplete extends cfield_base {
	var $m_rowTemplate;
	var $m_instanceId;
	var $m_fieldId;

	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);
		$this->m_rowTemplate = $pFieldTempl['autocomplete_row_template'];
		$this->m_instanceId = (int)$pFieldTempl['instance_id'];
		$this->m_fieldId = (int)$pFieldTempl['field_id'];

		if(!trim($this->m_rowTemplate)){
			$this->m_rowTemplate = HTML_DEFAULT_AUTOCOMPLETE_TEMPLATE;
		}
		$this->m_rowTemplate = $this->ReplaceHtmlFields($this->m_rowTemplate);
	}

	function GetToStringRepresentation() {
		$lSrcValues = getFieldSelectOptions($this->m_srcQuery, (int)$this->m_pubdata['document_id'], (int)$this->m_pubdata['instance_id']);
		return $lSrcValues[$this->m_parsedFieldValue];
	}


	function Display() {
		if((int)$this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_FACEBOOK_AUTOCOMPLETE_LABEL_TEMPL));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		$lSrcValues = getFieldSelectOptionsById($this->m_srcQuery, $this->m_parsedFieldValue);

		$lAutocompleteValues = '';
		$lInput = '';
		foreach( $lSrcValues as $k => $v){
			$lId = (int)$k;
			$lName = $v;
			$lInput .= '<input type="hidden" id="' . $lId . '_hiddenInp" name="' . $this->m_fieldHtmlIdentifier . '[]" value="' . $lId . '"/>';
			$lAutocompleteValues .= '{"id":"' . $lId . '","name":' . json_encode($lName) . '},';
		}
		//$lAutocompleteValues = substr($lAutocompleteValues, 0, -1);

		$lInput .= '<input  onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" type="text" id="' . $this->m_fieldHtmlIdentifier . '_autocomplete" name="' . $this->m_fieldHtmlIdentifier . '_autocomplete" value="' . h($lSrcValues[$this->m_fieldHtmlIdentifier]) . '" />';
		$lInput .= '<script type="text/javascript">
					//<![CDATA[

				$("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput(
					gAutocompleteAjaxSrv + "?action=get_autocomplete_options&instance_id=' . $this->m_instanceId . '&field_id=' . $this->m_fieldId . '",
					{
						minChars: 3,
						theme: "facebook",
						prePopulate: [' . $lAutocompleteValues . '],
						queryParam: "term",
						preventDuplicates: true,
						minChars: 3,
						onResult: function(data){
							return data;
						},
						onAdd: function(item){
							var input = \'<input id="\' + item.id + \'_hiddenInp"  name="' . $this->m_fieldHtmlIdentifier . '[]" value="\' + item.id + \'" type="hidden"></input>\';
							$(input).insertBefore( "#' . $this->m_fieldHtmlIdentifier . '_autocomplete" );
							PerformSingleFieldAutosave(' . $this->m_instanceId . ', ' . $this->m_fieldId . ');
						},
						onDelete: function(item){
							$( "#" + item.id + "_hiddenInp" ).remove();
							PerformSingleFieldAutosave(' . $this->m_instanceId . ', ' . $this->m_fieldId . ');
						},
						resultsFormatter: function(item)
						{
							return "<li><div style=\"float: left; width: 20%\">" + item.acronym + "</div><div style=\"float: right; width: 80%\">" + item.name + "</div><div style=\"clear:both\"></div></li>";
						}
					}
				);
				$("#token-input-' . $this->m_fieldHtmlIdentifier . '_autocomplete").bind(\'focus\', function(){
					changeFocus(1, this);
				});
				$("#token-input-' . $this->m_fieldHtmlIdentifier . '_autocomplete").bind(\'blur\', function(){
					changeFocus(2, this);
				});
			//]]>
		</script>';
		$this->m_pubdata['field'] = $lInput;
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_FACEBOOK_AUTOCOMPLETE_TEMPL));
	}

}

?>