<?php
// @formatter->off
$gTemplArr = array(
	

	'view_document_le.document_not_in_layout_editing' => '
	{*view_document.view_document_head}
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					<div class="document_author_holder_content_no_review_yet">
						<div class="document_author_holder_content_no_review_yet_top">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tbody><tr>
									<td width="5"><div class="yellow-top-left"></div></td>
									<td><div class="yellow-top-middle"></div></td>
									<td width="5"><div class="yellow-top-right"></div></td>
								</tr>
							</tbody></table>
						</div>
						<div class="document_author_holder_content_no_review_yet_middle">
							<span class="yellow-green-txt"></span>
							<div class="document_btn_actions_editor_holder">
								<table cellpadding="0" cellspacing="0" width=100%>
									<tr>
										<td align="center">
											<span class="yellow-green-txt">The document is not in layout editing</span>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="document_author_holder_content_no_review_yet_bottom">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tbody><tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</tbody></table>
						</div>
					</div>
				</div>
			</div>
		</div>
		{*view_document.view_document_foot}
	',

	'view_document_le.document_approved_for_publish' => '
		{*view_document.view_document_head}
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					<div class="document_author_holder_content_no_review_yet">
						<div class="document_author_holder_content_no_review_yet_top">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tbody><tr>
									<td width="5"><div class="yellow-top-left"></div></td>
									<td><div class="yellow-top-middle"></div></td>
									<td width="5"><div class="yellow-top-right"></div></td>
								</tr>
							</tbody></table>
						</div>
						<div class="document_author_holder_content_no_review_yet_middle">
							<span class="yellow-green-txt"></span>
							<div class="document_btn_actions_editor_holder">
								<table cellpadding="0" cellspacing="0" width=100%>
									<tr>
										<td align="center">
											<span class="yellow-green-txt">The document is approved for publishing</span>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div class="document_author_holder_content_no_review_yet_bottom">
							<table width="100%" cellspacing="0" cellpadding="0">
								<tbody><tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</tbody></table>
						</div>
					</div>
				</div>
			</div>
		</div>
		{*view_document.view_document_foot}
	',


	'view_document_le.document_in_review' => '
	{*view_document.view_document_head}
		{le_note}
		<div class="document_info_row_border_line"></div>
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					<div class="document_author_holder_content_no_review_yet">
						<span class="yellow-green-txt"></span>
						<div class="document_btn_actions_editor_holder">
							<table cellpadding="0" cellspacing="0" width=100%>
								<tr>
									<td align="center">
										<div class="le_version_xml_holder">
											<div class="subm_textarea_holder_top">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tbody><tr>
														<td width="4">
															<div class="subm_textarea_holder_top_left"></div>
														</td>
														<td>
															<div class="subm_textarea_holder_top_middle"></div>
														</td>
														<td width="4">
															<div class="subm_textarea_holder_top_right"></div>
														</td>
													</tr>
												</tbody></table>
											</div>
											<div class="subm_textarea_holder_view_source">
												<textarea name="document_current_version_xml" id="document_current_version_xml">{document_le_xml}</textarea>
											</div>
											<div class="subm_textarea_holder">
												<table cellpadding="0" cellspacing="0" width="100%">
													<tbody><tr>
														<td width="4">
															<div class="subm_textarea_holder_top_left subm_textarea_holder_bottom_left"></div>
														</td>
														<td>
															<div class="subm_textarea_holder_top_middle subm_textarea_holder_bottom_middle"></div>
														</td>
														<td width="4">
															<div class="subm_textarea_holder_top_right subm_textarea_holder_bottom_right"></div>
														</td>
													</tr>
												</tbody></table>
											</div>
										</div>
									
										<div class="le_actions_holder">
											<div class="P-Green-Btn-Holder saveForm" onclick="SaveLEDecision({round_user_id}, ' . (int)ROUND_LAYOUT_DECISION_ACCEPT . ')">
												<div class="P-Green-Btn-Left"></div>
												<div class="P-Green-Btn-Middle">
													{_getstr(pjs.layout_approve_btn_text)}
												</div>
												<div class="P-Green-Btn-Right"></div>
											</div>
											<div class="P-Green-Btn-Holder saveForm" onclick="SaveLEXMLVersion({document_id}, \'#document_current_version_xml\')">
												<div class="P-Green-Btn-Left"></div>
												<div class="P-Green-Btn-Middle">
													{_getstr(admin.article_versions.save)}
												</div>
												<div class="P-Green-Btn-Right"></div>
											</div>
											<div class="P-Green-Btn-Holder saveForm" onclick="GenerateHTMLPreview({document_id})">
												<div class="P-Green-Btn-Left"></div>
												<div class="P-Green-Btn-Middle">
													{_getstr(pjs.layout_html_preview_btn_text)}
												</div>
												<div class="P-Green-Btn-Right"></div>
											</div>
											<div class="P-Green-Btn-Holder saveForm" onclick="GeneratePDFPreview({document_id})">
												<div class="P-Green-Btn-Left"></div>
												<div class="P-Green-Btn-Middle">
													{_getstr(pjs.layout_pdf_preview_btn_text)}
												</div>
												<div class="P-Green-Btn-Right"></div>
											</div>
											<div class="P-Green-Btn-Holder saveForm" onclick="RevertLEXMLVersion({document_id}, \'#document_current_version_xml\')">
												<div class="P-Green-Btn-Left"></div>
												<div class="P-Green-Btn-Middle">
													{_getstr(pjs.layout_revert_btn_text)}
												</div>
												<div class="P-Green-Btn-Right"></div>
											</div>
											<div class="P-Clear"></div>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	{*view_document.view_document_foot}
	',

	'view_document_le.document_waiting_for_author_after_review' => '
	{*view_document.view_document_head}
	{*view_document.view_document_foot}
	',
	
	'view_document_le.document_le_notes' => '
			<div id="collapse_smb_notes_closed" class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view">
				<table width="100%" cellspacing="0" cellpadding="0">
					<colgroup>
						<col width="33%"></col>
						<col width="33%"></col>
						<col width="33%"></col>
					</colgroup>
					<tbody>
						<tr>
							<td align="left">
								<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">Submission notes</div>
							</td>
							<td align="center">
								&nbsp;
							</td>
							<td align="right">
								<div><img src="../i/collapse_open.png"> <span onclick="Collapse(1, \'collapse_smb_notes_closed\', \'collapse_smb_notes_open\')" class="collapse_text">Expand</span></div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="submission_notes_main_wrapper" id="collapse_smb_notes_open" style="display:none">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">Submission notes</div>
					<div class="document_author_review_round_top_right">
						<img src="../i/collapse_close.png">
						<span onclick="Collapse(0, \'collapse_smb_notes_closed\', \'collapse_smb_notes_open\')" class="collapse_text">Collapse</span>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="subm_notes_desc_holder">
					can be used and seen only by Layout Editor
				</div>
				<div class="subm_textarea_holder">
					<div class="subm_textarea_holder_top">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="4">
									<div class="subm_textarea_holder_top_left"></div>
								</td>
								<td>
									<div class="subm_textarea_holder_top_middle"></div>
								</td>
								<td width="4">
									<div class="subm_textarea_holder_top_right"></div>
								</td>
							</tr>
						</table>
					</div>
					<div class="subm_textarea_holder_middle">
						<textarea name="note" id="ed_notes">{le_notes}</textarea>
					</div>
					<div class="subm_textarea_holder">
						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="4">
									<div class="subm_textarea_holder_top_left subm_textarea_holder_bottom_left"></div>
								</td>
								<td>
									<div class="subm_textarea_holder_top_middle subm_textarea_holder_bottom_middle"></div>
								</td>
								<td width="4">
									<div class="subm_textarea_holder_top_right subm_textarea_holder_bottom_right"></div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="reviewers_footer_content_right reviewers_footer_content_right_small_1" onclick="SaveLENotes(\'ed_notes\', {document_id})">
					<div class="reviewers_search_btn_left"></div>
					<div class="reviewers_search_btn_middle reviewers_search_btn_middle_small1">
						Save Note
					</div>
					<div class="reviewers_search_btn_right"></div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				<div class="subm_more_info_holder">
					
				</div>
			</div>
	',
	
	'view_document_le.le_tabs' => '
		<div class="tabHolder">
			<div class="tabRow {_showViewDocumentActiveSectionTab(active_tab, a' . (int)GET_CURSTATE_MANUSCRIPT_SECTION . ')}" onclick="window.location=\'view_document.php?id={document_id}&view_role={view_role}&section=' . GET_CURSTATE_MANUSCRIPT_SECTION . '\'">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.manuScript_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>	
			</div>
			<div class="tabRow {_showViewDocumentActiveSectionTab(active_tab, a' . GET_METADATA_SECTION . ')}" onclick="window.location=\'view_document.php?id={document_id}&view_role={view_role}&section=' . GET_METADATA_SECTION . '\'">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.metadata_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>	
			</div>
			<!--<div class="tabRow {_showViewDocumentActiveSectionTab(active_tab, a' . GET_SUBMITTED_FILES_SECTION . ')}" onclick="window.location=\'view_document.php?id={document_id}&view_role={view_role}&section=' . GET_SUBMITTED_FILES_SECTION . '\'">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.sybm_files_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>	
			</div>-->
			<div class="tabRow {_showViewDocumentActiveSectionTab(active_tab, a' . GET_DISCOUNTS_SECTION . ')}" onclick="window.location=\'view_document.php?id={document_id}&view_role={view_role}&section=' . GET_DISCOUNTS_SECTION . '\'">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.discounts_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>	
			</div>
			<div class="tabRow {_showViewDocumentActiveSectionTab(active_tab, a' . GET_SCHEDULING_SECTION . ')}" onclick="window.location=\'view_document.php?id={document_id}&view_role={view_role}&section=' . GET_SCHEDULING_SECTION . '\'">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.schedule_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>	
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'view_document_le.form' => '
		<div class="document_author_holder_content">
			<div class="document_author_holder_content_no_review_yet">
				<span class="yellow-green-txt"></span>
				<div class="document_btn_actions_editor_holder">
					<table cellpadding="0" cellspacing="0" width=100%>
						<tr>
							<td align="center">
								<div class="le_version_xml_holder">
									<div class="subm_textarea_holder_top">
										<table cellpadding="0" cellspacing="0" width="100%">
											<tbody><tr>
												<td width="4">
													<div class="subm_textarea_holder_top_left"></div>
												</td>
												<td>
													<div class="subm_textarea_holder_top_middle"></div>
												</td>
												<td width="4">
													<div class="subm_textarea_holder_top_right"></div>
												</td>
											</tr>
										</tbody></table>
									</div>
									<div class="subm_textarea_holder_view_source">
										{document_current_version_xml}
									</div>
									<div class="subm_textarea_holder">
										<table cellpadding="0" cellspacing="0" width="100%">
											<tbody><tr>
												<td width="4">
													<div class="subm_textarea_holder_top_left subm_textarea_holder_bottom_left"></div>
												</td>
												<td>
													<div class="subm_textarea_holder_top_middle subm_textarea_holder_bottom_middle"></div>
												</td>
												<td width="4">
													<div class="subm_textarea_holder_top_right subm_textarea_holder_bottom_right"></div>
												</td>
											</tr>
										</tbody></table>
									</div>
								</div>
							
								<div class="le_actions_holder">
									<div class="P-Green-Btn-Holder saveForm" onclick="SaveLEDecision({round_user_id}, ' . (int)ROUND_LAYOUT_DECISION_ACCEPT . ')">
										<div class="P-Green-Btn-Left"></div>
										<div class="P-Green-Btn-Middle">
											{_getstr(pjs.layout_approve_btn_text)}
										</div>
										<div class="P-Green-Btn-Right"></div>
									</div>
									<div class="P-Green-Btn-Holder saveForm">
										<div class="P-Green-Btn-Left"></div>
										<div class="P-Green-Btn-Middle">
											{save}
										</div>
										<div class="P-Green-Btn-Right"></div>
									</div>
									<div class="P-Green-Btn-Holder saveForm">
										<div class="P-Green-Btn-Left"></div>
										<div class="P-Green-Btn-Middle">
											{_getstr(pjs.layout_html_preview_btn_text)}
										</div>
										<div class="P-Green-Btn-Right"></div>
									</div>
									<div class="P-Green-Btn-Holder saveForm">
										<div class="P-Green-Btn-Left"></div>
										<div class="P-Green-Btn-Middle">
											{_getstr(pjs.layout_pdf_preview_btn_text)}
										</div>
										<div class="P-Green-Btn-Right"></div>
									</div>
									<div class="P-Green-Btn-Holder saveForm">
										<div class="P-Green-Btn-Left"></div>
										<div class="P-Green-Btn-Middle">
											{_getstr(pjs.layout_revert_btn_text)}
										</div>
										<div class="P-Green-Btn-Right"></div>
									</div>
									<div class="P-Clear"></div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	',
	
);
?>