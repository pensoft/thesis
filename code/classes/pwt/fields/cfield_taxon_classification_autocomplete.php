<?php
class cfield_taxon_classification_autocomplete extends cfield_base {
	var $m_rowTemplate;
	var $m_instanceId;
	var $m_fieldId;
	var $m_isDisabled;
	var $m_isMultiple;
	var $m_dbSrcTbl;

	function __construct($pFieldTempl){
		parent::__construct($pFieldTempl);
		$this->m_rowTemplate = $pFieldTempl['autocomplete_row_template'];
		$this->m_instanceId = (int)$pFieldTempl['instance_id'];
		$this->m_fieldId = (int)$pFieldTempl['field_id'];

		if(!trim($this->m_rowTemplate)){
			$this->m_rowTemplate = HTML_DEFAULT_AUTOCOMPLETE_TEMPLATE;
		}
		$this->m_rowTemplate = $this->ReplaceHtmlFields($this->m_rowTemplate);
		$this->m_isMultiple = false;

		switch($pFieldTempl['html_control_type']){
			case (int) FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_TYPE :
				$this->m_isMultiple = true;
			case (int) FIELD_HTML_TAXON_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
				$this->m_dbSrcTbl = TAXON_NOMENCLATURE_TABLE_NAME;
				break;
			case (int) FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_TYPE :
				$this->m_isMultiple = true;
			case (int) FIELD_HTML_SUBJECT_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE :
				$this->m_dbSrcTbl = 'subject_categories';
				break;
			case (int) FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE :
				$this->m_isMultiple = true;
			case (int) FIELD_HTML_CHRONOLOGICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE :
				$this->m_dbSrcTbl = 'chronological_categories';
				break;
			case (int) FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_TYPE :
				$this->m_isMultiple = true;
			case (int) FIELD_HTML_GEOGRAPHICAL_CLASSIFICATION_AUTOCOMPLETE_SINGLE_TYPE:
				$this->m_dbSrcTbl = 'geographical_categories';
				break;

		}




		$this->m_isDisabled = false;
		if($this->m_isReadOnly){
			$this->m_isDisabled = true;
		}

	}

	function GetToStringRepresentation() {
		$lSrcValues = getFieldSelectOptions($this->m_srcQuery, (int)$this->m_pubdata['document_id'], (int)$this->m_pubdata['instance_id']);
		return $lSrcValues[$this->m_parsedFieldValue];
	}

	function BuildInput(){
		return '<input  onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" type="text" id="' . $this->m_fieldHtmlIdentifier . '_autocomplete" name="' . $this->m_fieldHtmlIdentifier . '_autocomplete" value="" />';
	}

	function BuildTree(){
		return '<div id="tree' . $this->m_fieldHtmlIdentifier . '">
													' . getRegTreeCategoriesByRootNodes( $this->m_dbSrcTbl, true, false, true, $this->m_instanceId ) . '
												</div>';
	}

	function getTreeSelectedValues(){
		$lSrcValues = getTaxonTreeSelectedValues( $this->m_dbSrcTbl, $this->m_parsedFieldValue, 0, true, $this->m_instanceId);
		$lSelectedValues = '';
		foreach( $lSrcValues as $k => $v){
			$lSelectedValues .= '
				$("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput("add", {id:' . (int) $k . ', name:"' . $v . '"});
			';
		}
		$lSelectedValues .= '$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").isInitted = 1;';
		return $lSelectedValues;
	}

	function Display() {
		if((int)$this->m_pubdata['display_label']){
			$this->m_pubdata['field_label'] = $this->ReplaceHtmlFields($this->getObjTemplate(G_TAXON_CLASSIFICATION_AUTOCOMPLETE_LABEL_TEMPL));
		}else{
			$this->m_pubdata['field_label'] = '';
		}

		$lInput = '';
		$lSelectedValues = $this->getTreeSelectedValues();

		$lInput .= $this->BuildInput();
		$lInput .= $this->BuildTree();
		$lInput .= '<script>
				var gAdded = -1;
				$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree({
						checkbox: true,
						selectMode: ' . ($this->m_isMultiple ? 2 : 1) . ',
						debugLevel: 0,
						isInitted : 0,
						onLazyRead: function(node){
							node.appendAjax({url: gAutocompleteAjaxSrv ,
											   data: {"key": node.data.pos, // Optional url arguments
													  "action": "get_tree_autocomplete",
													  "table_name": "' . $this->m_dbSrcTbl . '",
													  "filter_by_document_journal" : 1,
													  "instance_id" : ' . (int)$this->m_instanceId . '
													  },
											   // (Optional) use JSONP to allow cross-site-requests
											   // (must be supported by the server):
											   //  dataType: "jsonp",
											   success: function(node) {
												   // Called after nodes have been created and the waiting icon was removed.
												   var lInputVals = $("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput("get");
												   for ( var i = 0; i < lInputVals.length; ++i) {
														if($("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").getNodeByKey(lInputVals[i].id))
															$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").getNodeByKey(lInputVals[i].id).select();
												   }
												},
											   error: function(node, XMLHttpRequest, textStatus, errorThrown) {
													// Called on error, after error icon was created.
												   },
											   cache: false
											  });
						},
						onSelect: function(flag, node) {
							if (!flag) { // Deselected item
								//$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").visit(function(node){
									if( !node.bSelected ) {
										var lInputVals = $("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput("get");

										for ( var i = 0; i < lInputVals.length; ++i) {
											if(lInputVals[i].id == node.data.key){
												$("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput("remove", {
													id: lInputVals[i].id
												});
												break;
											}
										}
									}
								//});
							}else{
								if( gAdded == node.data.key ){

								}else{
									' . ($this->m_isMultiple ? '' : '$("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput("clear");') . '
									var lDontAdd = 0;
									var lInputVals = $("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput("get");
									for ( var i = 0; i < lInputVals.length; ++i) {
										if(lInputVals[i].id == node.data.key){
											lDontAdd = 1;
											break;
										}
									}
									if(!lDontAdd){
										$("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput("add", {
											id: node.data.key, name:  node.data.title
										});
									}
								}
								gAdded = -1;
							}
								';

			foreach ($this->m_actions as $lCurrentAction){
				if($lCurrentAction['event'] == 'classification_change'){
					$lInput .= 'var lTempFunction = new Function(' . json_encode($lCurrentAction['js_action']) . ');
					lTempFunction();
					';
				}
			}
			$lInput .= '
						},
						onDblClick: function(node, event) {
							node.toggleSelect();
						},
						onKeydown: function(node, event) {
							if( event.which == 32 ) {
								node.toggleSelect();
								return false;
							}
						},
						onActivate: function(node) {

						},
						onDeactivate: function(node) {

						},
						// The following options are only required, if we have more than one tree on one page:
						//initId: "treeData",
						cookieId: "dynatree-Cb-' . $this->m_fieldHtmlIdentifier . '",
						idPrefix: "dynatree-Cb-' . $this->m_fieldHtmlIdentifier . '"
				});
				$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").isInitted = 0;
				' . ($this->m_isDisabled ?
					'$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("disable");' :
					'' ) . '

				$("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput(
					gAutocompleteAjaxSrv + "?action=get_reg_autocomplete&filter_by_document_journal=1&instance_id=' . (int)$this->m_instanceId . '&table_name=' . $this->m_dbSrcTbl . '",
					{
						minChars: 3,
						theme: "facebook",
						queryParam: "term",
						preventDuplicates: true,
						' . ($this->m_isMultiple ? '' : 'tokenLimit: 1,') . '
						onResult: function(data){
							return data;
						},
						onAdd: function(item){
							var input = \'<input id="' . $this->m_fieldHtmlIdentifier . '\' + item.id + \'_hiddenInp"  name="' . $this->m_fieldHtmlIdentifier . '[]" value="\' + item.id + \'" type="hidden"></input>\';
							$(input).insertBefore( "#' . $this->m_fieldHtmlIdentifier . '_autocomplete" );
							gAdded = item.id;

							if( $("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").getNodeByKey(item.id) ) {
								$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").getNodeByKey(item.id).activate();
								$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").getNodeByKey(item.id).select();

							}
							if($("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").isInitted){
								PerformSingleFieldAutosave(' . $this->m_instanceId . ', ' . $this->m_fieldId . ');
							}

						},
						onDelete: function(item){
							$( "#' . $this->m_fieldHtmlIdentifier . '" + item.id + "_hiddenInp" ).remove();
							if( $("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").getNodeByKey(item.id) ) {
								$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").getNodeByKey(item.id).select(false);
								$("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").getNodeByKey(item.id).deactivate();
							}
							if($("#tree' . $this->m_fieldHtmlIdentifier . '").dynatree("getTree").isInitted){
								PerformSingleFieldAutosave(' . $this->m_instanceId . ', ' . $this->m_fieldId . ');
							}

						}
					}
				);

				' . ($this->m_isDisabled ?
					'$("#' . $this->m_fieldHtmlIdentifier . '_autocomplete").tokenInput("toggleDisabled");' :
					'' ) . '

				' . $lSelectedValues . '

		</script>';
		$this->m_pubdata['field'] = $lInput;
		return $this->returnHtml();
	}

	function returnHtml(){
		return $this->ReplaceHtmlFields($this->getObjTemplate(G_TAXON_CLASSIFICATION_AUTOCOMPLETE_TEMPL));
	}

}
//&instance_id=' . $this->m_instanceId . '&field_id=' . $this->m_fieldId . '

?>