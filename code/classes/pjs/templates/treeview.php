<?php

$gTemplArr = array(
	'treeview.treeviewtop' => '
		<ul>
	',
	
	'treeview.treeviewfoot' => '
		</ul>
	',
		
	'treeview.treeviewrowtempl' => '
		<li id="{unique}{id}" class="lazy folder" data="pos: \'{pos}\'">
			{_htmlentities(name)}
		</li>
	',
		
	'treeview.treescripttempl' => '
		<script type="text/javascript">
		//<![CDATA[
				var gAdded = -1;
				$("#tree{html_identifier}").dynatree({
						checkbox: true,
						selectMode: {is_multiple},
						debugLevel: 0,
						onLazyRead: function(node){
							node.appendAjax({url: "/lib/ajax_srv/autocomplete_srv.php",
											   data: {"key": node.data.pos, // Optional url arguments
													  "action": "get_tree_autocomplete",
													  "table_name": "{db_src_table}",
													  "filter_by_document_journal" : 1
													  },
											   success: function(node) {
												   // Called after nodes have been created and the waiting icon was removed.
												   var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
												   for ( var i = 0; i < lInputVals.length; ++i) {
														if($("#tree{html_identifier}").dynatree("getTree").getNodeByKey(lInputVals[i].id))
															$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(lInputVals[i].id).select();
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
								//$("#tree{html_identifier}").dynatree("getTree").visit(function(node){
									if( !node.bSelected ) {
										var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
										for ( var i = 0; i < lInputVals.length; ++i) {
											if(lInputVals[i].id == node.data.key){
												$("#{html_identifier}_autocomplete").tokenInput("remove", {
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
									{_getClearRowIfMultiple(is_multiple, html_identifier)}
									var lDontAdd = 0;
									var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
									for ( var i = 0; i < lInputVals.length; ++i) {
										if(lInputVals[i].id == node.data.key){
											lDontAdd = 1;
											break;
										}
									}
									if(!lDontAdd){
										$("#{html_identifier}_autocomplete").tokenInput("add", {
											id: node.data.key, name:  node.data.title
										});
									}
								}
								gAdded = -1;
							}
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
						cookieId: "dynatree-Cb-{html_identifier}",
						idPrefix: "dynatree-Cb-{html_identifier}"
				});
		
				{_disableTree(is_disabled, html_identifier)}
		
				$("#{html_identifier}_autocomplete").tokenInput(
					"/lib/ajax_srv/autocomplete_srv.php" + "?action=get_reg_autocomplete&filter_by_document_journal=1&table_name={db_src_table}",
					{
						theme: "facebook",
						queryParam: "term",
						preventDuplicates: true,
						minChars: 3,
						{_checkIsMultipleTokenInput(is_token_input_multiple)}
						onResult: function(data){
							return data;
						},
						onAdd: function(item){
							var input = \'<input id="{html_identifier}\' + item.id + \'_hiddenInp"  name="{html_identifier}[]" value="\' + item.id + \'" type="hidden"></input>\';
							$(input).insertBefore( "#{html_identifier}_autocomplete" );
							gAdded = item.id;

							if( $("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id) ) {
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).activate();
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).select();
							}
							else
							{
								console.log(item.id + " was not found in the currently loaded part of the tree");
							}
							
							if (typeof initComplete == "boolean" && initComplete)
							{
								//console.log("Init is complete; this is a user add");
								$("#filter_groups").submit();
							}
						},
						onDelete: function(item){
							$( "#{html_identifier}" + item.id + "_hiddenInp" ).remove();
							if( $("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id) ) {
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).select(false);
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).deactivate();
							}
							$("#filter_groups").submit();
						}
					}
				);
			//]]>
	</script>',
	
	'treeview.treescripttempl_reg' => '
		<script type="text/javascript">
		//<![CDATA[
				var gAdded = -1;
				$("#tree{html_identifier}").dynatree({
						checkbox: true,
						selectMode: {is_multiple},
						debugLevel: 0,
						onLazyRead: function(node){
							node.appendAjax({url: "/lib/ajax_srv/autocomplete_srv.php",
											   data: {"key": node.data.pos, // Optional url arguments
													  "action": "get_tree_autocomplete",
													  "table_name": "{db_src_table}",
													  "filter_by_document_journal" : 0
													  },
											   success: function(node) {
												   // Called after nodes have been created and the waiting icon was removed.
												   var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
												   for ( var i = 0; i < lInputVals.length; ++i) {
														if($("#tree{html_identifier}").dynatree("getTree").getNodeByKey(lInputVals[i].id))
															$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(lInputVals[i].id).select();
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
								//$("#tree{html_identifier}").dynatree("getTree").visit(function(node){
									if( !node.bSelected ) {
										var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
										for ( var i = 0; i < lInputVals.length; ++i) {
											if(lInputVals[i].id == node.data.key){
												$("#{html_identifier}_autocomplete").tokenInput("remove", {
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
									{_getClearRowIfMultiple(is_multiple, html_identifier)}
									var lDontAdd = 0;
									var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
									for ( var i = 0; i < lInputVals.length; ++i) {
										if(lInputVals[i].id == node.data.key){
											lDontAdd = 1;
											break;
										}
									}
									if(!lDontAdd){
										$("#{html_identifier}_autocomplete").tokenInput("add", {
											id: node.data.key, name:  node.data.title
										});
									}
								}
								gAdded = -1;
							}
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
						cookieId: "dynatree-Cb-{html_identifier}",
						idPrefix: "dynatree-Cb-{html_identifier}"
				});
		
				{_disableTree(is_disabled, html_identifier)}
		
				$("#{html_identifier}_autocomplete").tokenInput(
					"/lib/ajax_srv/autocomplete_srv.php" + "?action=get_reg_autocomplete&filter_by_document_journal=0&table_name={db_src_table}",
					{
						theme: "facebook",
						queryParam: "term",
						preventDuplicates: true,
						{_checkIsMultipleTokenInput(is_token_input_multiple)}
						onResult: function(data){
							return data;
						},
						onAdd: function(item){
							var input = \'<input id="{html_identifier}\' + item.id + \'_hiddenInp"  name="{html_identifier}[]" value="\' + item.id + \'" type="hidden"></input>\';
							$(input).insertBefore( "#{html_identifier}_autocomplete" );
							gAdded = item.id;

							if( $("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id) ) {
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).activate();
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).select();
							}
							else
							{
								console.log(item.id + " was not found in the currently loaded part of the tree");
							}
							
							if (typeof initComplete == "boolean" && initComplete)
							{
								//console.log("Init is complete; this is a user add");
								$("#filter_groups").submit();
							}
						},
						onDelete: function(item){
							$( "#{html_identifier}" + item.id + "_hiddenInp" ).remove();
							if( $("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id) ) {
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).select(false);
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).deactivate();
							}
							$("#filter_groups").submit();
						}
					}
				);
			//]]>
	</script>',
);/*
$lInput .= '<script type="text/javascript">
				var gAdded = -1;
				$("#tree{html_identifier}").dynatree({
						checkbox: true,
						selectMode: ' . ($this->m_isMultiple ? 2 : 1) . ',
						debugLevel: 0,
						onLazyRead: function(node){
							node.appendAjax({url: gAutocompleteAjaxSrv ,
											   data: {"key": node.data.key, // Optional url arguments
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
												   var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
												   for ( var i = 0; i < lInputVals.length; ++i) {
														if($("#tree{html_identifier}").dynatree("getTree").getNodeByKey(lInputVals[i].id))
															$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(lInputVals[i].id).select();
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
								//$("#tree{html_identifier}").dynatree("getTree").visit(function(node){
									if( !node.bSelected ) {
										var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
										for ( var i = 0; i < lInputVals.length; ++i) {
											if(lInputVals[i].id == node.data.key){
												$("#{html_identifier}_autocomplete").tokenInput("remove", {
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
									' . ($this->m_isMultiple ? '' : '$("#{html_identifier}_autocomplete").tokenInput("clear");') . '
									var lDontAdd = 0;
									var lInputVals = $("#{html_identifier}_autocomplete").tokenInput("get");
									for ( var i = 0; i < lInputVals.length; ++i) {
										if(lInputVals[i].id == node.data.key){
											lDontAdd = 1;
											break;
										}
									}
									if(!lDontAdd){
										$("#{html_identifier}_autocomplete").tokenInput("add", {
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
						cookieId: "dynatree-Cb-{html_identifier}",
						idPrefix: "dynatree-Cb-{html_identifier}"
				});

				' . ($this->m_isDisabled ?
						'$("#tree{html_identifier}").dynatree("disable");' :
						'' ) . '

				$("#{html_identifier}_autocomplete").tokenInput(
					gAutocompleteAjaxSrv + "?action=get_reg_autocomplete&amp;filter_by_document_journal=1&amp;instance_id=' . (int)$this->m_instanceId . '&amp;table_name=' . $this->m_dbSrcTbl . '",
					{
						theme: "facebook",
						queryParam: "term",
						preventDuplicates: true,
						' . ($this->m_isMultiple ? '' : 'tokenLimit: 1,') . '
						onResult: function(data){
							return data;
						},
						onAdd: function(item){
							var input = \'<input id="{html_identifier}\' + item.id + \'_hiddenInp"  name="{html_identifier}[]" value="\' + item.id + \'" type="hidden"></input>\';
							$(input).insertBefore( "#{html_identifier}_autocomplete" );
							gAdded = item.id;

							if( $("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id) ) {
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).activate();
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).select();
							}

						},
						onDelete: function(item){
							$( "#{html_identifier}" + item.id + "_hiddenInp" ).remove();
							if( $("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id) ) {
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).select(false);
								$("#tree{html_identifier}").dynatree("getTree").getNodeByKey(item.id).deactivate();
							}

						}
					}
				);

				' . ($this->m_isDisabled ?
						'$("#{html_identifier}_autocomplete").tokenInput("toggleDisabled");' :
						'' ) . '

				' . $lSelectedValues . '

		</script>';*/
?>