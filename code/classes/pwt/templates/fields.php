<?php

$gTemplArr = array(
	'fields.head' => '
											<div class="fieldWrapper {css_class} {_getFieldErrorClass(has_validation_error)}" id="field_wrapper_{instance_id}_{field_id}">
	',

	'fields.foot' => '
												<script type="text/javascript">
													autoSaveField(\'field_wrapper_{instance_id}_{field_id}\');
												</script>
											</div>
	',

	'fields.foot_popup' => '
											</div>
	',

	'fields.label' => '
													<div class="input-title">{label}{_displayFieldRequiredSign(allow_nulls)}</div>
	',

	'fields.label_radio' => '
													<div class="input-title">
														<div class="Radio-Label-Holder">{label} {_displayFieldRequiredSign(allow_nulls)}</div>
														<div class="Radio-Help-Holder">{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}</div>
														<div class="unfloat"></div>
													</div>
	',

	'fields.label_checkbox' => '
													<div class="input-title">
														<div class="Radio-Label-Holder">{label} {_displayFieldRequiredSign(allow_nulls)}</div>
														<div class="Radio-Help-Holder">{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}</div>
														<div class="unfloat"></div>
													</div>
	',

	'fields.label_editor' => '
													<div class="P-Data-Resources-Control-Left">{label}{_displayFieldRequiredSign(allow_nulls)}</div>
	',

	'fields.texarea_simple_rounded_label' => '{label}{_displayFieldRequiredSign(allow_nulls)}',

	'fields.label_file_upload' => '
													<div class="input-title">{label}{_displayFieldRequiredSign(allow_nulls)}</div>
	',


	//Field
	'fields.input' => '
												<div class="P-Data-Resources-Subsection-Title">
													{field_label}

													<div class="P-Input-Full-Width {_getInputWrapperClass(has_help_label, has_validation_error, help_label_display_style)}">
														<div class="P-Input-Inner-Wrapper">
															<div class="P-Input-Holder">
																<div class="P-Input-Left"></div>
																<div class="P-Input-Middle">
																	{field}
																	{actions_content}
																</div>
																<div class="P-Input-Right"></div>
																<div class="P-Clear"></div>
															</div>
															{_displayFieldExampleLabel(has_example_label, example_label)}
														</div>
														{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}
													</div>
												</div>

	',

	'fields.file_upload' => '

												<div class="P-Data-Resources-Subsection-Title">
													{field_label}
													<div class="P-Input-Full-Width P-File {_getInputWrapperClass(has_help_label, has_validation_error, help_label_display_style)}">
														<div class="P-Input-Inner-Wrapper">
															<div class="P-Input-Holder">
																<div class="P-Input-Left"></div>
																<div class="P-Input-Middle">
																	<div class="P-File-Name">{file_name}</div><!-- Zadyljitelno!!! -->
																		{field}
																		{actions_content}
																</div>
																<div class="P-Input-Right"></div>
																<div class="P-Clear"></div>
															</div>
															{_displayFieldExampleLabel(has_example_label, example_label)}
														</div>
														<div class="P-Browse-Btn-Holder" id="field_ajax_{field_id}__{instance_id}" onclick="triggerClick(\'input_file_name123\');">
															<div class="P-Browse-Btn-Left"></div>
															<div class="P-Browse-Btn-Middle">Browse</div>
															<div class="P-Browse-Btn-Right"></div>
															{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}
														</div>
														<script type="text/javascript">UploadFile(\'field_ajax_{field_id}__{instance_id}\', {document_id}, {instance_id}, {field_id});</script>
													</div>

												</div>

	',

	'fields.fbautocomplete' => '
												<div class="P-Data-Resources-Subsection-Title">
													{field_label}
													<div class="P-Input-Full-Width P-FB-Autocomplete {_getInputWrapperClass(has_help_label, has_validation_error, help_label_display_style)}">
														<div class="P-Input-Inner-Wrapper">
															{field}
															{actions_content}
															<div class="P-Clear"></div>
															{_displayFieldExampleLabel(has_example_label, example_label)}
														</div>
														{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}
													</div>
												</div>

	',

	'fields.taxon_classification_autocomplete' => '
												<div class="P-Data-Resources-Subsection-Title">
													{field_label}
													<div class="P-Input-Full-Width P-FB-Autocomplete {_getInputWrapperClass(has_help_label, has_validation_error, help_label_display_style)}">
														<div class="P-Input-Inner-Wrapper">
															{field}
															{actions_content}
															<div class="P-Clear"></div>
															{_displayFieldExampleLabel(has_example_label, example_label)}
														</div>
														{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}
													</div>
												</div>


	',

	'fields.select' => '
												<div class="P-Data-Resources-Subsection-Title">
													{field_label}
													<div class="P-Input-Full-Width P-Select {_getInputWrapperClass(has_help_label, has_validation_error, help_label_display_style)}">
														<div class="P-Input-Inner-Wrapper">
															<div class="P-Input-Holder">
																<div class="P-Input-Left"></div>
																<div class="P-Input-Middle">
																	<span class="P-Select-Value"></span>
																	{field}
																	{actions_content}
																	<div class="P-Select-Arrow"></div>
																</div>
																<div class="P-Input-Right"></div>
																<div class="P-Clear"></div>
															</div>
															{_displayFieldExampleLabel(has_example_label, example_label)}
														</div>
														{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}
													</div>
												</div>
												<script type="text/javascript">
													var selectJournal{field_html_identifier} = new designSelect( \'sel_{field_html_identifier}\', 0 );
												</script>
	',

	'fields.radio' => '
												<div class="P-Data-Resources-Subsection-Title P-Radio-Section">
													{field_label}
													{field}
													{actions_content}
													<div class="unfloat"></div>
												</div>
	',

	'fields.radio_row' => '
							<div class="P-Check-Row P-Radio-Row">
								<div class="P-CheckBox-Holder">
									{input}
								</div>
								<label class="P-CheckBox-Label-Holder" for="{label_for}">{label}</label>
								<div class="P-Clear"></div>
							</div>
	',

	'fields.checkbox' => '
												<div class="P-Data-Resources-Subsection-Title">
													{field_label}
													{field}
													{actions_content}
												</div>
	',

	'fields.checkbox_row' => '
							<div class="P-Check-Row">
								<div class="P-CheckBox-Holder">
									{input}
								</div>
								<label class="P-CheckBox-Label-Holder" for="{label_for}">{label}</label>
								<div class="P-Clear"></div>
							</div>
	',

	'fields.textarea' => '
												<div class="P-Data-Resources-Control">
													<div class="P-Data-Resources-Control-Txt">
														{field_label}
														<div class="P-Data-Resources-Control-Right"></div>
														<div class="P-Clear"></div>
													</div>

													<div class="P-Data-Resources-Textarea SmallTextArea">
														{_createEditorToolbarHolder(create_common_toolbar_holder, common_toolbar_holder_id)}
														{field}
														{actions_content}
														{_createSmallHtmlEditor(field_html_identifier, height, width, toolbar_name, use_common_toolbar, common_toolbar_holder_id)}
													</div>
												</div>
	',

	'fields.textarea_simple' => '
												<div class="P-Data-Resources-Control">
													<div class="P-Data-Resources-Control-Txt">
														{field_label}
														<div class="P-Data-Resources-Control-Right"></div>
														<div class="P-Clear"></div>
													</div>
													<div class="P-Data-Resources-Textarea SmallTextArea">

														{field}
														{actions_content}
													</div>
													{_displayFieldExampleLabel(has_example_label, example_label)}
												</div>
	',

	'fields.textarea_rounded_simple' => '

												<div class="P-Data-Resources-Subsection-Title P-Textarea-Rounded">
													<div class="input-title">{field_label}</div>
													<div class="P-Input-Full-Width">
														<table class="P-Input-Holder" width="100%" cellspacing="0" cellpadding="0" border="0">
															<tbody>
															<tr>
																<td class="P-ResTextarea-Top-Left"></td>
																<td class="P-ResTextarea-Top-Middle"></td>
																<td class="P-ResTextarea-Top-Right"></td>
															</tr>
															<tr>
																<td class="P-ResTextarea-Middle-Left">&nbsp;</td>
																<td class="P-ResTextarea-Middle-Middle">
																	<textarea fldattr="0" onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)"></textarea>
																</td>
																<td class="P-ResTextarea-Middle-Right">&nbsp;</td>
															</tr>
															<tr>
																<td class="P-ResTextarea-Bottom-Left"></td>
																<td class="P-ResTextarea-Bottom-Middle"></td>
																<td class="P-ResTextarea-Bottom-Right"></td>
															</tr>
														</tbody></table>
														<div class="P-Clear"></div>
													</div>
													<div class="P-Clear"></div>
													{_displayFieldExampleLabel(has_example_label, example_label)}
													<div class="P-Clear"></div>
												</div>
	',


	'fields.editor' => '
												<div class="P-Data-Resources-Control">
													<div class="P-Data-Resources-Control-Txt">
														{field_label}
														<div class="P-Clear"></div>
													</div>
													<div class="P-Data-Resources-Textarea NormalTextArea">
														{field}
														{actions_content}
														{_createHtmlEditor(field_html_identifier)}

													</div>
												</div>
	',


	'fields.editor_reference_citations' => '
												<div class="P-Data-Resources-Control">
													<div class="P-Data-Resources-Control-Txt">
														{field_label}
														<div class="P-Clear"></div>
													</div>
													<div class="P-Data-Resources-Textarea">
														{field}
														{actions_content}
														{_createHtmlEditorReferenceCitation(field_html_identifier)}
													</div>
												</div>
	',

	'fields.editor_no_citation' => '
												<div class="P-Data-Resources-Control">
													<div class="P-Data-Resources-Control-Txt">
														{field_label}
														<div class="P-Clear"></div>
													</div>
													<div class="P-Data-Resources-Textarea NormalTextArea">
														{field}
														{actions_content}
														{_createHtmlEditorNoCitation(field_html_identifier)}

													</div>
												</div>
	',

	'fields.file_upload_material' => '

												<div class="P-Data-Resources-Subsection-Title">
													{field_label}
													<div id="field_ajax_input_{field_id}__{instance_id}" class="P-Input-Full-Width P-File {_getInputWrapperClass(has_help_label, has_validation_error, help_label_display_style)}">
														<div class="P-Input-Inner-Wrapper">
															<div class="P-Input-Holder">
																<div class="P-Input-Left"></div>
																<div class="P-Input-Middle">
																	<div class="P-File-Name">{file_name}</div><!-- Zadyljitelno!!! -->
																		{field}
																		{actions_content}
																</div>
																<div class="P-Input-Right"></div>
																<div class="P-Clear"></div>
															</div>
															{_displayFieldExampleLabel(has_example_label, example_label)}
														</div>
														<div class="P-Browse-Btn-Holder" id="field_ajax_{field_id}__{instance_id}" onclick="triggerClick(\'input_file_name123\');">
															<div class="P-Browse-Btn-Left"></div>
															<div class="P-Browse-Btn-Middle">Browse</div>
															<div class="P-Browse-Btn-Right"></div>
															{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}
														</div>
														<script type="text/javascript">UploadMaterialFile(\'field_ajax_{field_id}__{instance_id}\', {document_id}, {instance_id});</script>
														<script type="text/javascript">UploadMaterialFile(\'field_ajax_input_{field_id}__{instance_id}\', {document_id}, {instance_id});</script>
													</div>

												</div>

	',

	'fields.file_upload_checklist_taxon' => '
												<div class="P-Data-Resources-Subsection-Title">
													{field_label}
													<div id="field_ajax_input_{field_id}__{instance_id}" class="P-Input-Full-Width P-File {_getInputWrapperClass(has_help_label, has_validation_error, help_label_display_style)}">
														<div class="P-Input-Inner-Wrapper">
															<div class="P-Input-Holder">
																<div class="P-Input-Left"></div>
																<div class="P-Input-Middle">
																	<div class="P-File-Name">{file_name}</div><!-- Zadyljitelno!!! -->
																		{field}
																		{actions_content}
																</div>
																<div class="P-Input-Right"></div>
																<div class="P-Clear"></div>
															</div>
															{_displayFieldExampleLabel(has_example_label, example_label)}
														</div>
														<div class="P-Browse-Btn-Holder" id="field_ajax_{field_id}__{instance_id}" onclick="triggerClick(\'input_file_name123\');">
															<div class="P-Browse-Btn-Left"></div>
															<div class="P-Browse-Btn-Middle">Browse</div>
															<div class="P-Browse-Btn-Right"></div>
															{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}
														</div>
														<script type="text/javascript">UploadChecklistTaxonFile(\'field_ajax_{field_id}__{instance_id}\', {document_id}, {instance_id});</script>
														<script type="text/javascript">UploadChecklistTaxonFile(\'field_ajax_input_{field_id}__{instance_id}\', {document_id}, {instance_id});</script>
													</div>

												</div>
	',
		
	'fields.file_upload_taxon_coverage_taxa' => '
												<div class="P-Data-Resources-Subsection-Title">
													{field_label}
													<div id="field_ajax_input_{field_id}__{instance_id}" class="P-Input-Full-Width P-File {_getInputWrapperClass(has_help_label, has_validation_error, help_label_display_style)}">
														<div class="P-Input-Inner-Wrapper">
															<div class="P-Input-Holder">
																<div class="P-Input-Left"></div>
																<div class="P-Input-Middle">
																	<div class="P-File-Name">{file_name}</div><!-- Zadyljitelno!!! -->
																		{field}
																		{actions_content}
																</div>
																<div class="P-Input-Right"></div>
																<div class="P-Clear"></div>
															</div>
															{_displayFieldExampleLabel(has_example_label, example_label)}
														</div>
														<div class="P-Browse-Btn-Holder" id="field_ajax_{field_id}__{instance_id}" onclick="triggerClick(\'input_file_name123\');">
															<div class="P-Browse-Btn-Left"></div>
															<div class="P-Browse-Btn-Middle">Browse</div>
															<div class="P-Browse-Btn-Right"></div>
															{_displayFieldHelpLabel(has_help_label, help_label, help_label_display_style)}
														</div>
														<script type="text/javascript">UploadTaxonomicCoverageTaxaFile(\'field_ajax_{field_id}__{instance_id}\', {document_id}, {instance_id});</script>
														<script type="text/javascript">UploadTaxonomicCoverageTaxaFile(\'field_ajax_input_{field_id}__{instance_id}\', {document_id}, {instance_id});</script>
													</div>

												</div>
	',

);

?>