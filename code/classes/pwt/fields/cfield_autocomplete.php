<?php 
class cfield_autocomplete extends cfield_base {
	var $m_rowTemplate;
	var $m_instanceId;
	var $m_fieldId;
	var $m_onSelectFunction;
	
	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);
		$this->m_rowTemplate = $pFieldTempl['autocomplete_row_template'];
		$this->m_onSelectFunction = $pFieldTempl['autocomplete_onselect'];
		
		$this->m_instanceId = (int)$pFieldTempl['instance_id'];
		$this->m_fieldId = (int)$pFieldTempl['field_id'];
		
		if(!trim($this->m_rowTemplate)){
			$this->m_rowTemplate = HTML_DEFAULT_AUTOCOMPLETE_TEMPLATE;
		}	
		
		if(!trim($this->m_onSelectFunction)){
			$this->m_onSelectFunction = HTML_DEFAULT_AUTOCOMPLETE_ONSELECT_FUNCTION;
		}
		
		$this->m_rowTemplate = $this->ReplaceHtmlFields($this->m_rowTemplate);
		$this->m_onSelectFunction = $this->ReplaceHtmlFields($this->m_onSelectFunction);
	}
	
	function GetToStringRepresentation() {
		$lSrcValues = getFieldSelectOptions($this->m_srcQuery, (int)$this->m_pubdata['document_id'], (int)$this->m_pubdata['instance_id']);	
		return $lSrcValues[$this->m_parsedFieldValue];
	}


	function Display() {
		if((int)$this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_AUTOCOMPLETE_LABEL_TEMPL));
		}else{
			$this->m_pubdata['field_label'] = '';
		}
		$lSrcValues = getFieldSelectOptions($this->m_srcQuery, (int)$this->m_pubdata['document_id'], (int)$this->m_pubdata['instance_id']);		
		$lInput = '<input ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' type="hidden" id="' . $this->m_fieldHtmlIdentifier . '" name="' . $this->m_fieldHtmlIdentifier . '" value="' . h($this->m_parsedFieldValue) . '"/>';		
		$lInput .= '<input ' . ($this->m_isReadOnly ? ' disabled="disabled" ' : '') . ' onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" type="text" id="' . $this->m_fieldHtmlIdentifier . '_autocomplete" name="' . $this->m_fieldHtmlIdentifier . '_autocomplete" value="' . h($lSrcValues[$this->m_parsedFieldValue]) . '" />';
		$lInput .= '<script type="text/javascript">
					//<![CDATA[
				$("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").autocomplete({
					minLength: 3,
					source: function( request, response ) {
						var lSearchTerm = request.term;												
						$.ajax({
							url : gAutocompleteAjaxSrv,
							dataType : \'json\',
							data :{
								action : \'get_autocomplete_options\',
								instance_id : \'' . $this->m_instanceId . '\',
								field_id : \'' . $this->m_fieldId . '\',
								term : lSearchTerm
							},
							success : function(pAjaxResult){
								response(pAjaxResult);								//
							}
						});
					},
					focus: function( event, ui ) {
						$( "#' . $this->m_fieldHtmlIdentifier . '_autocomplete" ).val( ui.item.name );
						return false;
					},
					select: function( event, ui ) {						
						' . $this->m_onSelectFunction . '
					}
				})
				.data( "autocomplete" )._renderItem = function( ul, item ) {															
					var lLi =  $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a class=\"P-Row-Data-Holder\"><div class=\"P-Row-Data-Holder-Inner\">"+' . $this->m_rowTemplate . '+"</div></a>" )
						.appendTo( ul );
					return lLi;
				};
				//]]>
		</script>';
		$this->m_pubdata['field'] = $lInput;
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_AUTOCOMPLETE_TEMPL));
	}

}

?>