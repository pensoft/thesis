<?php

$gTemplArr = array(
	'references.single_reference_preview' => '
		<div id="P-Ref-Row-{id}" class="P-PopUp-Data-Table-Row">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td class="P-PopUp-Checkbox-Holder" align="left" valign="top">
						<input type="checkbox" name="ref-{id}" position="{rownum}" value="{id}"></input>
					</td>
					<td valign="top">
						<div class="P-Reference-Desc" id="Ref-Preview-{id}">{preview}</div>
						<div class="P-Clear"></div>
					</td>
				</tr>
			</table>
			<div class="P-Clear"></div>
		</div>
		<div class="P-Clear"></div>
	',

	'references.new_reference_popup' => '

	',

	'references.new_reference_in_popup_wrapper' => '
							<form name="newPopupForm" method="post">
								<input type="hidden" name="document_id" value="{document_id}"/>
								<input type="hidden" name="instance_id" value="{instance_id}"/>
								<input type="hidden" name="perform_save_action" value="1"/>
								<div class="horizontalContainer">
									<div class="container_item_wrapper halfWidth floatLeft">
										<div class="container_item_inner_wrapper">
											<div class="fieldWrapper">
												<div class="P-Data-Resources-Subsection-Title">
													<div class="input-title">Reference type</div>
													<div class="P-Input-Full-Width P-Select ">
														<div class="P-Input-Inner-Wrapper">
															<div class="P-Input-Holder">
																<div class="P-Input-Left"></div>
																<div class="P-Input-Middle">
																	<span class="P-Select-Value">Select reference type</span>
																	<select	id="referenceTypeSelect">
																		<option value="0" disabled="disabled" selected="selected" style="display:none">Select reference type</option>
																		<option value="1">Book</option>
																		<option value="2">Book chapter</option>
																		<option value="3">Journal article</option>
																		<option value="4">Conference paper</option>
																		<option value="5">Conference proceedings</option>
																		<option value="6">Thesis</option>
																		<option value="7">Software</option>
																		<option value="8">Website</option>
																	</select>
																	<div class="P-Select-Arrow"></div>
																</div>
																<div class="P-Input-Right"></div>
																<div class="P-Clear"></div>
															</div>
														</div>
													</div>
												</div>
												<script type="text/javascript">
													var lSelectBtn = new designSelect(\'referenceTypeSelect\', 0);
													$("#referenceTypeSelect option[value={reference_type}]").attr("selected", "selected");
													$("#referenceTypeSelect").siblings("." + gSelectedOptionClass ).html( $("#referenceTypeSelect").find("option:selected").text());
													$("#referenceTypeSelect").change(function(){
														if($(this).val() != 0){
															executeAction(35, 1, 95, $(this).val(), GetDocumentId());
															executeAction(gActionRemoveInstanceWithoutContainerReload, {instance_id}, {instance_id});
														}
													});
												</script>
											</div>
										</div>
									</div>
									<div class="P-Clear"></div>
								</div>

								<div class="P-Clear"></div>
								<div class="P-VSpace-10"></div>
								{instance}
							</form>
	',

	'references.empty_row' => '
		<div class="P-Empty-Content">' . getstr('pwt.references.nodata') . '</div>
	',

);

?>