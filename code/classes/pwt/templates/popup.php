<?php

$gTemplArr = array(
	'popup.default_popup' => '
		<div class="P-PopUp New-Element-Popup" id="newElementPopup" tabindex="-1" class="hiddenElement">
		<div id="simplemodal-container" onclick="CancelNewElementPopup({instance_id});HideNewPopup();"><a title="Close" class="modalCloseImg simplemodal-close"></a></div>
			<div class="P-PopUp-Main-Holder">
				<div class="P-PopUp-Content">
					<div class="P-PopUp-Title">New {_strtolower(popup_title)}</div>
					<div class="P-PopUp-Content-Inner-Tables" id="newElementPopupContent">
							<form name="newPopupForm" method="post">
								<input type="hidden" name="document_id" value="{document_id}"/>
								<input type="hidden" name="instance_id" value="{instance_id}"/>
								<input type="hidden" name="perform_save_action" value="1"/>
								{popup_content}
							</form>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-PopUp-Footer-Holder">
					<div class="P-Green-Btn-Holder P-90" id="newElementPopupSave"
						onclick="SaveNewElementPopup({instance_id}, {parent_instance_id}, {container_id}, {display_in_tree});">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-HSpace-10"></div>
					<div class="P-Grey-Btn-Holder"
						onclick="CancelNewElementPopup({instance_id});" id="newElementPopupCancel">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle">
							<div class="P-Btn-Icon"></div>
							Cancel
						</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
			</div>
		</div>
	',
	
	'popup.default_popup_with_margin' => '
		<div class="P-PopUp New-Element-Popup" id="newElementPopup" tabindex="-1" class="hiddenElement">
			<div id="simplemodal-container" onclick="CancelNewElementPopup({instance_id});HideNewPopup();"><a title="Close" class="modalCloseImg simplemodal-close"></a></div>
			<div class="P-PopUp-Main-Holder">
				<div class="P-PopUp-Content">
					<div class="P-PopUp-Title">New {_strtolower(popup_title)}</div>
					<div class="P-PopUp-Content-Inner-Tables New-Element-Reference-Popup" id="newElementPopupContent">
							<form name="newPopupForm" method="post">
								<input type="hidden" name="document_id" value="{document_id}"/>
								<input type="hidden" name="instance_id" value="{instance_id}"/>
								<input type="hidden" name="perform_save_action" value="1"/>
								{popup_content}
							</form>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-PopUp-Footer-Holder">
					<div class="P-Green-Btn-Holder P-90" id="newElementPopupSave"
						onclick="SaveNewElementPopup({instance_id}, {parent_instance_id}, {container_id}, {display_in_tree});">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-HSpace-10"></div>
					<div class="P-Grey-Btn-Holder"
						onclick="CancelNewElementPopup({instance_id});" id="newElementPopupCancel">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle">
							<div class="P-Btn-Icon"></div>
							Cancel
						</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
			</div>
		</div>
	',

	'popup.references_popup' => '
		<div class="P-PopUp New-Element-Popup" id="newElementPopup" tabindex="-1" class="hiddenElement">
			<div id="simplemodal-container" onclick="CancelNewReferencePopup();HideNewPopup();"><a title="Close" class="modalCloseImg simplemodal-close"></a></div>
			<div class="P-PopUp-Main-Holder">
				<div class="P-PopUp-Content">
					<div class="P-PopUp-Title">Add reference</div>
					<div class="P-PopUp-Content-Inner-Tables New-Element-Reference-Popup" id="newElementPopupContent">
						<div class="horizontalContainer">
							<div class="container_item_wrapper halfWidth floatLeft">
								<div class="container_item_inner_wrapper">
									<div class="fieldWrapper">
										<form name="newPopupForm" method="post">
											<div class="P-Data-Resources-Subsection-Title">

												<div class="input-title">Reference type</div>

												<div class="P-Input-Full-Width P-Select ">
													<div class="P-Input-Inner-Wrapper">
														<div class="P-Input-Holder">
															<div class="P-Input-Left"></div>
															<div class="P-Input-Middle">
																<span class="P-Select-Value">Select reference type</span>
																<select	id="referenceTypeSelect">
																	<option value="0" disabled="disabled" selected="selected" style="display: none">Select reference type</option>
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
												$("#referenceTypeSelect").change(function(){
													if($(this).val() != 0)
														executeAction(35, 1, 95, $(this).val(), GetDocumentId());
												});
											</script>
										</form>
									</div>
								</div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-PopUp-Footer-Holder">
					<div class="P-Green-Btn-Holder P-90 hiddenElement" id="referenceSaveBtn"
						onclick="SaveReferencePopup();">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-HSpace-10"></div>
					<div class="P-Grey-Btn-Holder"
						onclick="CancelNewReferencePopup();">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle">
							<div class="P-Btn-Icon"></div>
							Cancel
						</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
			</div>
		</div>
	',

	'popup.references_edit_popup' => '
		<div class="P-PopUp New-Element-Popup" id="newElementPopup" tabindex="-1" class="hiddenElement">
			<div id="simplemodal-container" onclick="HideNewPopup();"><a title="Close" class="modalCloseImg simplemodal-close"></a></div>
			<div class="P-PopUp-Main-Holder">
				<div class="P-PopUp-Content">
					<div class="P-PopUp-Title">Edit Reference</div>
					<div class="P-PopUp-Content-Inner-Tables New-Element-Reference-Popup" id="newElementPopupContent">
						<form name="newPopupForm" method="post">
								<input type="hidden" name="document_id" value="{document_id}"/>
								<input type="hidden" name="instance_id" value="{instance_id}"/>
								<input type="hidden" name="perform_save_action" value="1"/>
								<input type="hidden" name="in_popup" value="1"/>
								{popup_content}
							</form>
						<div class="P-Clear"></div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-PopUp-Footer-Holder">
					<div class="P-Green-Btn-Holder P-90" id="referenceSaveBtn"
						onclick="SaveReferencePopup();">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-HSpace-10"></div>
					<div class="P-Grey-Btn-Holder" onclick="HideNewPopup();">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle">
							<div class="P-Btn-Icon"></div>
							Cancel
						</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
			</div>
		</div>
	',

	'popup.default_edit_popup' => '
		<div class="P-PopUp New-Element-Popup" id="newElementPopup"  class="hiddenElement">
			<div class="P-PopUp-Main-Holder">
				<div class="P-PopUp-Content">
					<div class="P-PopUp-Title">Edit {popup_title}</div>
					<div class="P-PopUp-Content-Inner-Tables New-Element-Reference-Popup" id="newElementPopupContent">
						<form name="newPopupForm" method="post">
							<input type="hidden" name="document_id" value="{document_id}"/>
							<input type="hidden" name="instance_id" value="{instance_id}"/>
							<input type="hidden" name="perform_save_action" value="1"/>
							<input type="hidden" name="in_popup" value="1"/>
							{popup_content}
						</form>
						<div class="P-Clear"></div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-PopUp-Footer-Holder">
					<div class="P-Green-Btn-Holder P-90" id="popupEditSaveBtn"
						onclick="SaveEditPopup();">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle">Save</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-HSpace-10"></div>
					<div class="P-Grey-Btn-Holder" onclick="HideEditPopup();">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle">
							<div class="P-Btn-Icon"></div>
							Cancel
						</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
			</div>
		</div>
	',

);


?>