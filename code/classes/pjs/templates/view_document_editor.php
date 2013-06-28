<?php
// @formatter->off
$gTemplArr = array(	
//red_btn_background.jpg
	'view_document_editor.document_approved_for_publish' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
		{*view_document_editor.view_all_rounds}
		{ce_obj}
		{le_obj}
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">Publish</div>
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

	'view_document_editor.document_ese_notes' => '
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
								<div>
									<img src="../i/collapse_open.png"> 
									<span onclick="Collapse(1, \'collapse_smb_notes_closed\', \'collapse_smb_notes_open\')" class="collapse_text">Expand</span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="submission_notes_main_wrapper" id="collapse_smb_notes_open" style="display:none;">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">Submission notes</div>
					<div class="document_author_review_round_top_right">
						<img src="../i/collapse_close.png">
						<span onclick="Collapse(0, \'collapse_smb_notes_closed\', \'collapse_smb_notes_open\')" class="collapse_text">Collapse</span>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="subm_notes_desc_holder">
					can be used and seen only by Editor and/or Subject Editor(s) of this submission
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
						<textarea name="note" id="ed_notes">{editor_notes}</textarea>
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
				<div class="reviewers_footer_content_right reviewers_footer_content_right_small_1" onclick="SaveESENotes(\'ed_notes\', {document_id})">
					<div class="reviewers_search_btn_left"></div>
					<div class="reviewers_search_btn_middle reviewers_search_btn_middle_small1">
						Save Note
					</div>
					<div class="reviewers_search_btn_right"></div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				<div class="subm_more_info_holder">
					<div class="subm_more_info_holder_first">
						<div class="subm_more_info_holder_first_left">{_getstr(pjs.authors_notes_to_editor)}:</div>
						<div class="subm_more_info_holder_first_right">{notes_to_editor}</div>
						<div class="P-Clear"></div>
					</div>
					<!-- <div class="subm_more_info_holder_sec">
						<div class="subm_more_info_holder_sec_left">Fees:</div>
						<div class="subm_more_info_holder_sec_middle">
							<div class="subm_more_info_holder_sec_middle_label">Discount requested:</div>
							<div class="subm_more_info_holder_sec_middle_val">15%</div>
						</div>
						<div class="subm_more_info_holder_sec_right">
							<div class="subm_more_info_holder_sec_right_label">Reason:</div>
							<div class="subm_more_info_holder_sec_right_val">I would appreciate a discount of 15 % because of one of the following reasons: I am an editor in this journal </div>
						</div>
						<div class="P-Clear"></div>
					</div> -->
				</div>
			</div>
	',

	'view_document_editor.view_all_rounds' => '
		{review_round_1}
		{_showReviewRoundDelimiters(has_round_2)}
		{review_round_2}
		{_showReviewRoundDelimiters(has_round_3)}
		{review_round_3}
		{_showReviewRoundDelimiters(has_round_1)}
	',
	
	'view_document_editor.document_e_actions' => '
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">Waiting assignment to Subject Editor</div>
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
								<span class="yellow-green-txt">You can assign this manuscript to a Subject Editor</span>
								<div class="document_btn_actions_editor_holder">
									<table cellpadding="0" cellspacing="0" width=100%>
										<tr>
											<td align="right">
												<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first" onclick="window.location=\'/view_document.php?id={document_id}&amp;view_role=2&amp;mode=1&amp;suggested=1\'">
													<div class="invite_reviewer_btn_left"></div>
													<div class="invite_reviewer_btn_middle">Assign Subject Editor</div>
													<div class="invite_reviewer_btn_right"></div>
													<div class="P-Clear"></div>
												</div>
											</td>
											<td align="left">
												<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_sec" onclick="DocumentAddSe({document_id}, {userid}, {journal_id});">
													<div class="invite_reviewer_btn_left"></div>
													<div class="invite_reviewer_btn_middle">Assign Self as Subject Editor</div>
													<div class="invite_reviewer_btn_right"></div>
													<div class="P-Clear"></div>
												</div>
											</td>
										</tr>
									</table>
								</div>
								<div class="subm_reject_or_holder">
									<table cellpadding="0" cellspacing="0" width="100%">
										<colgroup>
											<col width="45%"></col>
											<col width="10%"></col>
											<col width="45%"></col>
										</colgroup>
										<tr>
											<td><div class="or_line"></div></td>
											<td align="center"><span class="or_text">OR</span></td>
											<td><div class="or_line"></div></td>
										</tr>
									</table>
								</div>
								<div class="subm_reject_reasons_txt">
									<span class="yellow-green-txt">You can reject this manuscript prior to peer review by explaining your reasoning below:</span>
								</div>
								<div class="subm_textarea_holder subm_textarea_holder_E">
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
										<textarea onkeyup="ChangeRejectBtn(this, \'btn_rej_1\', \'btn_rej_2\', \'btn_rej_1_active\', \'btn_rej_2_active\')" name="notes_reject" id="ed_notes_reject"></textarea>
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
									<div class="document_btn_actions_editor_holder">
										<table cellpadding="0" cellspacing="0" width=100%>
											<colgroup>
												<col width="50%"></col>
												<col width="50%"></col>
											</colgroup>
											<tr>
												<td align="right" style="">
													<div style="margin-left: -30px; margin-left: 125px; width: 300px; float: left;">
														<div id="btn_rej_1_active" class="btn_rej_1_active" style="display:none" onclick="SaveEditorDecision({userid}, ' . ROUND_DECISION_REJECT . ', {document_id})">
															<div class="btnContentHolder">' . getstr('pjs.reject') . '</div>
														</div>
														<div id="btn_rej_1" class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
															<div class="rejBtnMid" style="width: 158px;">
																' . getstr('pjs.reject') . '
															</div>
															<!-- <img src="../i/btn_rej.jpg" /> -->
														</div>
													</div>
												</td>
												<td>
													<div style="margin-left: -50px; margin-left: -60px; width: 300px; float: left;">
														<div id="btn_rej_2_active" class="btn_rej_2_active" style="display:none" onclick="SaveEditorDecision({userid}, ' . ROUND_DECISION_REJECT_BUT_RESUBMISSION . ', {document_id})">
															<div class="btnContentHolder">' . getstr('pjs.reject.but') . '</div>
														</div>
														<div id="btn_rej_2" class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_sec">
															<!-- <img src="../i/btn_rej_but.jpg" /> -->
															<div class="rejBtnMid" style="width: 300px;">
																' . getstr('pjs.reject.but') . '
															</div>
														</div>
													</div>
												</td>
												<td align="left">
													
												</td>
											</tr>
										</table>
									</div>
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
	',
	
	'view_document_editor.document_se_decision' => '
		{*view_document.view_document_head}
		<div class="document_author_review_round_holder ed_holder_wrapper" id="doc_tab_1">
			{submission_notes}
			{*view_document_editor.view_all_rounds}
			<!-- <div class="document_info_row_border_line"></div> -->
			{form}
			{submission_actions}
		</div>
		{*view_document.view_document_foot}
	',
	'view_document_editor.scheduling_form' => '
		<div class="leftMar10">
			<div class="formErrors">{~}{~~}</div>
			<div class="input-reg-title">{*journal}</div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{issue_id}
			</div>
			<div class="input-reg-title">{*startpage}</div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{startpage}
			</div>
			<div class="input-reg-title">{*endpage}</div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{endpage}
			</div>		
			<div class="input-reg-title">{*colorpage}</div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{colorpage}
			</div>
			<div class="input-reg-title">{*price}</div>
			<div class="P-Input-Full-Width P-W300 fieldHolder">
				{price}
			</div>
			<div class="input-reg-title">' . getstr('pjs.scheduling.form.autoprice') . '</div>
				<div id="autoprice">{autoprice}</div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle">{save}</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
		</div>
		<script type="text/javascript">
			$(function(){
				updateDocumentAutoPrice(this)
				var lJournalValue = $(\'.journals option:first\').attr(\'disabled\', \'disabled\');
				var lJournalValue = $(\'.journals option\').attr(\'value\');
				//~ if( $(\'.journals option[value=\'\']\') ) {
					console.log($(this));
					// $(this).attr(\'disabled\', \'disabled\');
				//~ }
				
			});
		</script>
	',
	
	'view_document_editor.document_view_source' => '
		{*view_document.view_document_head}
		<div class="document_author_review_round_holder ed_holder_wrapper" id="doc_tab_1">
			{submission_notes}
			
				<div class="subm_textarea_holder_view_source">
					<textarea name="note" id="ed_notes">{document_current_version_xml}</textarea>
				</div>
				
			
			<div class="document_info_row_border_line"></div>
			{submission_actions}	
		</div>
		{*view_document.view_document_foot}
	',

	'view_document_editor.document_rejected' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
		{*view_document_editor.view_all_rounds}
		<div class="document_author_review_round_holder" id="doc_tab_1">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					<div class="document_author_holder_content_no_review_yet">
						<div class="document_author_holder_content_no_review_yet_top">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5"><div class="yellow-top-left"></div></td>
									<td><div class="yellow-top-middle"></div></td>
									<td width="5"><div class="yellow-top-right"></div></td>
								</tr>
							</table>
						</div>
						<div class="document_author_holder_content_no_review_yet_middle">
							<span class="yellow-green-txt">{_showRejectStatus(state_id)}</span>
							{_showRejectNotes(reject_round_decision_notes)}
						</div>
						<div class="document_author_holder_content_no_review_yet_bottom">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		{*view_document.view_document_foot}
	',
	
	'view_document_editor.document_waiting_author_decision_copyedit' => '
		{*view_document.view_document_head}
		{*view_document_editor.view_all_rounds}
		<div class="document_author_review_round_holder" id="doc_tab_1">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.copyeditinground_label)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					<div class="document_author_holder_content_no_review_yet">
						<div class="document_author_holder_content_no_review_yet_top">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5"><div class="yellow-top-left"></div></td>
									<td><div class="yellow-top-middle"></div></td>
									<td width="5"><div class="yellow-top-right"></div></td>
								</tr>
							</table>
						</div>
						<div class="document_author_holder_content_no_review_yet_middle">
							<span class="yellow-green-txt">Waiting author decision for copyediting</span>
						</div>
						<div class="document_author_holder_content_no_review_yet_bottom">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		{*view_document.view_document_foot}
	',
	
	'view_document_editor.document_waiting_author_decision_layout' => '
		{*view_document.view_document_head}
		{*view_document_editor.view_all_rounds}
		<div class="document_author_review_round_holder" id="doc_tab_1">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">Layout Editing</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					<div class="document_author_holder_content_no_review_yet">
						<div class="document_author_holder_content_no_review_yet_top">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5"><div class="yellow-top-left"></div></td>
									<td><div class="yellow-top-middle"></div></td>
									<td width="5"><div class="yellow-top-right"></div></td>
								</tr>
							</table>
						</div>
						<div class="document_author_holder_content_no_review_yet_middle">
							<span class="yellow-green-txt">Waiting author decision for layout editing</span>
						</div>
						<div class="document_author_holder_content_no_review_yet_bottom">
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		{*view_document.view_document_foot}
	',
	
	'view_document_editor.document' => '
		{*view_document.view_document_head}
		<div class="document_view_holder">
			{*view_document_editor.view_all_rounds}
			<div class="document_review_title">
				{_getstr(pjs.Invite_SE)}
			</div>
			{available_se}
		</div>
		{*view_document.view_document_foot}
	',
	
	'view_document_editor.document_editor_assigned_se' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
		{*view_document_editor.view_all_rounds}
		{assigned_reviewers}
		{_showBorderLine(border)}
		{se_can_take_decision}
		{*view_document.view_document_foot}
	',

	'view_document_editor.seAssignedListStart' => '
		<div class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view">
				
	',

	'view_document_editor.seAssignedListRow' => '
		<table cellpadding="0" cellspacing="0" width="100%">
			<colgroup>
				<col width="33%"></col>
				<col width="33%"></col>
				<col width="33%"></col>
			</colgroup>
			<tr>
				<td align="left">
					<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">Subject Editor</div>
				</td>
				<td align="center">
					<span class="subj_editor_name_class">{first_name} {last_name}</span> <a href="mailto:{uname}"><img src="../i/mail.png" /></a>
				</td>
				<td align="right">
					<img src="../i/edit.png"/> <a href="/view_document.php?id={document_id}&amp;view_role=2&amp;mode=1&suggested=1">Change</a>
				</td>
			</tr>
		</table>
	',

	'view_document_editor.seAssignedListEnd' => '
		</div>
	',

	'view_document_editor.assigned_reviewers_list' => '
		<div class="document_author_review_round_holder document_author_review_round_holder_E_reviewers">
			{*view_document_editor.view_all_rounds}
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					{_showAssignmentSEDueDate(subject_editor_name, reviewers_assignment_duedate, document_id, current_round_id, round_user_id, role_id)}
				</div>
			</div>
		</div>
	',

	'view_document_editor.seAvailableListStart' => '
		<table class="tbl_editors no_bold_class" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<th align="left">{_getstr(pjs.editors_name_label_col)}</th>
				<th align="left">{_getstr(pjs.editors_name_email_col)}</th>
				<th align="center">{_getstr(pjs.editors_name_action_col)}</th>
			</tr>
	',
	'view_document_editor.seAvailableListNodata' => '
		<p>No subject editors match your criteria</p>
		<div class="back_link editor_back_link">
			&laquo; <a href="/view_document.php?id={document_id}&amp;view_role=2">back</a>
		</div>
	',
	'view_document_editor.seAvailableListRow' => '
	<tr>
		<td align="left">{first_name} {last_name}  <br />
			<span class="editor_more_info">({_SEexpertise(taxons, subjects)})</span>
		</td>
		<td align="left">{email}</td>
		<td align="center">{_showSEAddEvent(document_id, id, assigned_se_uid, journal_id)}</td>
	</tr>',
	
	'view_document_editor.seAvailableListEnd' => '
		</table>
		<div class="back_link editor_back_link">
			&laquo; <a href="/view_document.php?id={document_id}&amp;view_role=2">back</a>
		</div>
	',

	'view_document_editor.document_in_layout' => '
		{*view_document.view_document_head}
		EDITOR
		<div class="documentTitle">{name}</div>
		{assigned_le}
		{available_le}
		{*view_document.view_document_foot}
	',

	'view_document_editor.seAvailableListHeader' => '
		<div class="reviewers_footer_content se_header_cont">
			<div class="reviewers_footer_content_left">
				<div class="reviewers_footer_content_left_label">{_getstr(pjs.search_pensoft_db)}:</div>
				<form name="reviewer_search_form" method="post" action="/view_document.php?id={document_id}&amp;view_role=2&amp;mode=1" id="reviewer_search_form">
					<div class="reviewers_footer_content_left_inp_holder">
						<div class="reviewers_footer_content_left_inp">
							<div class="fieldHolder" style="width: 278px">
								<input type="text" name="subject_editor_search" value="" id="subject_editor_search" />
									<script type="text/javascript">
									//<![CDATA[
									$.ui.autocomplete.prototype._renderMenu = function(ul, items) {
									  var self = this;
									  ul.append("<table width=\"100%\"><tbody></tbody></table>");
									  $.each( items, function( index, item ) {
									    self._renderItem( ul.find("table tbody"), item );
									  });
									};
									$.ui.autocomplete.prototype._renderItem = function ( table, row ) {															
										var TR =  $( "<tr></tr>" )
											.data( "item.autocomplete", row )
											.append( 
													"<td class=\"name\">" + row.name + "</td>" + 
													"<td class=\"affiliation\">" + row.affiliation + "</td>" +
													"<td class=\"affiliation\">" + row.email + "</td>" +
													"<td class=\"center\">" + 
														(row.role_id == 3 
															? "<a href=\"javascript:DocumentAddSe({document_id}, " + row.id + ", {journal_id});\"><b>Assign</b></a>"
															: "<a href=\"javascript:LayerUserExpertisesFrm(\'P-Registration-Content\', {journal_id}, {document_id}, " + row.id + ");\">Make SE</a>"
														) +  "</td>"
													)
											.appendTo( table );
										return TR;
									};
									
									$(document).ready(function () {
									    $("#subject_editor_search").autocomplete({
									    source: "' . SITE_URL . 'lib/ajax_srv/usr_autocomplete_srv.php?action=get_subject_editors",
									    autoFocus: true,
									    minLength: 3,
									    select: function(){
									    	$("#subject_editor_search").val("");
									    	return false;
									    }
									    
										}
										);
									});
									//]]>
									</script>
							</div>
						</div>
						<div class="P-Clear"></div>
					</div>
				</form>
			</div>
			<div class="reviewers_footer_content_middle">or</div>
			<div class="reviewers_footer_content_right">
				<div class="reviewers_search_btn_left"></div>
				<div class="reviewers_search_btn_middle" style="cursor: pointer;" onclick="window.location=\'/create_user?mode=' . SE_ROLE . '&amp;document_id={document_id}&amp;role=' . SE_ROLE . '\'">
					{_getstr(pjs.create_new_sub_editor)}
				</div>
				<div class="reviewers_search_btn_right"></div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
		</div>
		<div class="editors_list_voc_list_holder">
			<div class="reviewers_footer_content_left_label">{_getstr(pjs.sel_from_subjEList)}:</div>
			<div class="letter_holder">
					<div class="letter_row_filter letter_row_filter_big"><a class="{suggested}" href="/view_document.php?id={document_id}&amp;view_role=2&amp;mode=1&amp;suggested=1">{_getstr(pjs.suggest_txt)}</a></div>
					<div class="letter_row_filter_sep">|</div> 
					<div class="letter_row_filter letter_row_filter_big2"><a class="{all}" href="/view_document.php?id={document_id}&amp;view_role=2&amp;mode=1">{_getstr(pjs.letter_all_txt)}</a></div>
					<div class="letter_row_filter_sep">|</div>
					{_AlphabetFilter(document_id)}
				<div class="P-Clear"></div>
			</div>
		</div>
	',
	
	'view_document_editor.seAvailableListFooter' => '',

	'view_document_editor.document_in_copy_review' => '
		{*view_document.view_document_head}
		EDITOR
		<div class="documentTitle">{name}</div>
		{assigned_ce}
		{available_ce}
		<a href="#" onclick="EditorProceedDocumentToLayout({document_id})">Proceed document to layout editing</a>
		{*view_document.view_document_foot}
	',

	'view_document_editor.dedicatedReviewerAssignedListHeader' => '
	',
	
	'view_document_editor.dedicatedReviewerAssignedListFooter' => '
	',
	
	'view_document_editor.dedicatedReviewerAssignedListEnd' => '
		</table>
	',

	'view_document_editor.dedicatedReviewerAssignedListStart' => '
		<table cellpadding="0" cellspacing="0" width="100%" class="reviewer_list_tbl">
		<colgroup>
			<col width="30%"></col>
			<col width="30%"></col>
			<col width="30%"></col>
			<col width="10%"></col>
		</colgroup>
		<tr>
			<th align="left">Nominated reviewers</td>
			<th align="left">Status</td>
			<th align="left">Actions</td>
			<th align="center">History</td>
		</tr>
	',

	'view_document_editor.dedicatedReviewerAssignedListRow' => '
		<tr>
			<td align="left">{first_name} {last_name} <a href="mailto:{uname}"><img src="../i/mail.png"></a></td>
			<td align="left">
				{_DisplaySETextAboutDedicatedReviewer(invitation_state, usr_state, decision_id, due_date, decision_name, review_usr_due_date, round_id, id, reviwer_id)}
			</td>
			<td align="left">
				{_DisplaySEActionsAboutDedicatedReviewer(invitation_id, invitation_state, usr_state, decision_id, due_date, reviwer_id, round_id, document_id, id, round_number, reviwer_document_version_id)}
			</td>
			<td align="center"><a href="#" class="history_link">View</a></td>
		</tr>
		<!--<div class="">{first_name} {last_name} State:{invitation_state_name} {_DisplaySEActionsAboutDedicatedReviewer(invitation_id, invitation_state)} Decision:{decision_name}</div>-->
	',
	
	'view_document_editor.document_waiting_ce_assign' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
		{*view_document_editor.view_all_rounds}
		{ce_rounds}
		{ce_assign_obj}
		{*view_document.view_document_foot}
	',
	
	'view_document_editor.document_waiting_le_obj' => '
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
							<table cellpadding="0" cellspacing="0" width="100%">
								<colgroup>
									<col width="33%"></col>
									<col width="33%"></col>
									<col width="33%"></col>
								</colgroup>
								<tr>
									<td align="left" style="padding:0px 0px 0px 20px">
										<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{first_name} {last_name}</div>
									</td>
									<td align="center">
										{_checkEditorLEDecisionDueDate(round_due_date, current_round_id, user_round_id)}
									</td>
									<td align="right" style="padding:0px 20px 0px 0px">
										{_showELEDecisionActions(round_due_date, document_id)}
									</td>
								</tr>
							</table>
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
	',
	
	'view_document_editor.document_waiting_ce_decision' => '
		<div class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view" id="collapse_closed_ce">
			<table width="100%" cellspacing="0" cellpadding="0">
				<colgroup>
					<col width="33%"></col>
					<col width="33%"></col>
					<col width="33%"></col>
				</colgroup>
				<tbody>
					<tr>
						<td align="left">
							<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{_getstr(pjs.copyeditinground_label)}</div>
						</td>
						<td align="center">
							<a href="#" onclick="openPopUp(\'/view_version.php?version_id={copy_editor_version_id}&id={document_id}&view_role=' . (int)CE_ROLE . '\')">View copyedited version</a>
						</td>
						<td align="right">
							<div><img src="../i/collapse_open.png"></img> <span class="collapse_text" onclick="Collapse(1, \'collapse_closed_ce\', \'collapse_opened_ce\')">Expand</span></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="submission_notes_main_wrapper" id="collapse_opened_ce" style="display:none;">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.copyeditinground_label)}</div>
				<div class="document_author_review_round_top_right">
					<img src="../i/collapse_close.png"></img>
					<span class="collapse_text" onclick="Collapse(0, \'collapse_closed_ce\', \'collapse_opened_ce\')">Collapse</span>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				{_showCurrentAuthorVersionCERound(document_id, copy_editor_version_id)}
			</div>
		</div>
		<div class="document_info_row_border_line"></div>
	',
	
	'view_document_editor.document_waiting_le_decision' => '
		<div class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view" id="collapse_closed_le">
			<table width="100%" cellspacing="0" cellpadding="0">
				<colgroup>
					<col width="33%"></col>
					<col width="33%"></col>
					<col width="33%"></col>
				</colgroup>
				<tbody>
					<tr>
						<td align="left">
							<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{_getstr(pjs.layoutround_label)}</div>
						</td>
						<td align="center">
							&nbsp;
						</td>
						<td align="right">
							<div><img src="../i/collapse_open.png"></img> <span class="collapse_text" onclick="Collapse(1, \'collapse_closed_le\', \'collapse_opened_le\')">Expand</span></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="submission_notes_main_wrapper" id="collapse_opened_le" style="display:none;">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.layoutround_label)}</div>
				<div class="document_author_review_round_top_right">
					<img src="../i/collapse_close.png"></img>
					<span class="collapse_text" href="javascript:void(0)" onclick="Collapse(0, \'collapse_closed_le\', \'collapse_opened_le\')">Collapse</span>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev" style="padding-bottom:25px;">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					<div class="doc_holder_reviewer_list" style="padding-top:0px;">
						<table width="100%" cellspacing="0" cellpadding="0">
							<colgroup>
								<col width="33%"></col>
								<col width"33%"></col>
								<col width"34%"></col>
							</colgroup>
							<tbody><tr>
								<td align="left">
									<span class="ed_decision_class_holder">
										First Proof
									</span>
								</td>
								<td align="center">&nbsp;</td>
								<td align="right">
									<a href="javascript:void(0);">Download as PDF</a>
								</td>
							</tr>
						</tbody></table>
					</div>
				</div>
			</div>
		</div>
		<div class="document_info_row_border_line"></div>
	',
	
	'view_document_editor.document_waiting_ce_obj' => '
		<div class="submission_notes_main_wrapper">
			{_showEditorCurrentRoundLabel(state_id, ce_rounds_count)}
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
							<table cellpadding="0" cellspacing="0" width="100%">
								<colgroup>
									<col width="33%"></col>
									<col width="33%"></col>
									<col width="33%"></col>
								</colgroup>
								<tr>
									<td align="left" style="padding:0px 0px 0px 20px">
										<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{first_name} {last_name}</div>
									</td>
									<td align="center">								
										{_checkEditorCEDecisionDueDate(round_due_date, current_round_id, user_round_id)}
									</td>
									<td align="right" style="padding:0px 20px 0px 0px">
										{_showECEDecisionActions(round_due_date, document_id)}
									</td>
								</tr>
							</table>
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
	',
	
	'view_document_editor.document_waiting_le_assign_obj' => '
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
							<table cellpadding="0" cellspacing="0" width="100%">
								<colgroup>
									<col width="33%"></col>
									<col width="33%"></col>
									<col width="33%"></col>
								</colgroup>
								<tr>
									<td align="left" style="padding:0px 0px 0px 20px">
										<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">Editorial office</div>
									</td>
									<td align="center">
										{_checkEditorLEAssignDueDate(round_due_date)}
									</td>
									<td align="right" style="padding:0px 20px 0px 0px">
										<a href="view_document.php?id={document_id}&amp;view_role=2&amp;mode=1">Assign Layout Editor</a>
									</td>
								</tr>
							</table>
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
	',
	
	'view_document_editor.document_waiting_ce_assign_obj' => '
		<div class="submission_notes_main_wrapper">
			{_showEditorCurrentRoundLabel(state_id, ce_rounds_count)}
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
							<table cellpadding="0" cellspacing="0" width="100%">
								<colgroup>
									<col width="33%"></col>
									<col width="33%"></col>
									<col width="33%"></col>
								</colgroup>
								{_showCopyEditorHolder(round_number, round_due_date, document_id)}
							</table>
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
	',
	
	'view_document_editor.document_waiting_le_assign' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
		{*view_document_editor.view_all_rounds}
		{ce_obj}
		{le_assign_obj}
		{*view_document.view_document_foot}
	',

	'view_document_editor.ceAvailableListStart' => '<div class="">Available CE</div>',

	'view_document_editor.ceAvailableListRow' => '<div class="">{first_name} {last_name} <a href="#" onclick="DocumentAddCE({document_id}, {id}, current_round_id);">add</a></div>',

	'view_document_editor.leAvailableListStart' => '<div class="">Available LE</div>',

	'view_document_editor.leAvailableListRow' => '<div class="">{first_name} {last_name} <a href="#" onclick="DocumentAddLE({document_id}, {id});">add</a></div>',

	'view_document_editor.ceAssignedListStart' => '
		<div class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view">
				
	',

	'view_document_editor.ceAssignedListRow' => '
		<table cellpadding="0" cellspacing="0" width="100%">
			<colgroup>
				<col width="33%"></col>
				<col width="33%"></col>
				<col width="33%"></col>
			</colgroup>
			<tr>
				<td align="left">
					<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">Copy Editor</div>
				</td>
				<td align="center">
					<span class="subj_editor_name_class">{first_name} {last_name}</span> <img src="../i/mail.png" />
				</td>
				<td align="right">
					<img src="../i/edit.png"/> <a href="/view_document.php?id={document_id}&amp;view_role=2&amp;mode=1">Change</a>
				</td>
			</tr>
		</table>
	',

	'view_document_editor.ceAssignedListEnd' => '
		</div>
	',

	'view_document_editor.leAssignedListStart' => '
		<div class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view">
				
	',

	'view_document_editor.leAssignedListRow' => '
		<table cellpadding="0" cellspacing="0" width="100%">
			<colgroup>
				<col width="33%"></col>
				<col width="33%"></col>
				<col width="33%"></col>
			</colgroup>
			<tr>
				<td align="left">
					<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">Layout Editor</div>
				</td>
				<td align="center">
					<span class="subj_editor_name_class">{first_name} {last_name}</span> <img src="../i/mail.png" />
				</td>
				<td align="right">
					<img src="../i/edit.png"/> <a href="/view_document.php?id={document_id}&amp;view_role=2&amp;mode=1">Change</a>
				</td>
			</tr>
		</table>
	',

	'view_document_editor.leAssignedListEnd' => '
		</div>
	',

	'view_document_editor.allceavailableListStart' => '
		<table class="tbl_editors no_bold_class" cellpadding="0" cellspacing="0" width="100%">
			<colgroup>
				<col width="45%"></col>
				<col width="45%"></col>
				<col width="10%"></col>
			</colgroup>
			<tr>
				<th align="left">{_getstr(pjs.editors_name_label_col)}</th>
				<th align="left">{_getstr(pjs.editors_name_email_col)}</th>
				<th align="center">{_getstr(pjs.editors_name_action_col)}</th>
			</tr>
	',

	'view_document_editor.allceavailableListRow' => '
	<tr>
		<td align="left">
			{first_name} {last_name}
		</td>
		<td align="left">
			<a href="mailto:{uname}">{uname}</a>
		</td>
		<td align="center">
			{_showCEAddEvent(document_id, id, assigned_ce_uid, current_round_id)}
		</td>
	</tr>',
	
	'view_document_editor.allceavailableListEnd' => '
		</table>
		<div class="back_link editor_back_link">
			&laquo; <a href="/view_document.php?id={document_id}&view_role=2">back</a>
		</div>
	',

	'view_document_editor.allceavailableListHeader' => '
		<div class="reviewers_footer_content_left_label">Assign copy editor:</div>
	',
	'view_document_editor.allceavailableListFooter' => '',

	'view_document_editor.document_ce_assign_list' => '
		{*view_document.view_document_head}
		<div class="submission_notes_main_wrapper">
			{*view_document_editor.view_all_rounds}
			{ce_list}
		</div>
		{*view_document.view_document_foot}
	',
	
	'view_document_editor.allleavailableListStart' => '
		<table class="tbl_editors no_bold_class" cellpadding="0" cellspacing="0" width="100%">
			<colgroup>
				<col width="45%"></col>
				<col width="45%"></col>
				<col width="10%"></col>
			</colgroup>
			<tr>
				<th align="left">{_getstr(pjs.editors_name_label_col)}</th>
				<th align="left">{_getstr(pjs.editors_name_email_col)}</th>
				<th align="center">{_getstr(pjs.editors_name_action_col)}</th>
			</tr>
	',

	'view_document_editor.allleavailableListRow' => '
	<tr>
		<td align="left">
			{first_name} {last_name}
		</td>
		<td align="left">
			<a href="mailto:{uname}">{uname}</a>
		</td>
		<td align="center">
			{_showLEAddEvent(document_id, id, assigned_le_uid)}
		</td>
	</tr>',
	
	'view_document_editor.allleavailableListEnd' => '
		</table>
		<div class="back_link editor_back_link">
			&laquo; <a href="/view_document.php?id={document_id}&amp;view_role=2">back</a>
		</div>
	',

	'view_document_editor.allleavailableListHeader' => '
		<div class="reviewers_footer_content_left_label">Assign layout editor:</div>
	',
	'view_document_editor.allleavailableListFooter' => '',

	'view_document_editor.document_le_assign_list' => '
		{*view_document.view_document_head}
		<div class="submission_notes_main_wrapper">
			{*view_document_editor.view_all_rounds}
			{le_list}
			{ce_obj}
		</div>
		{*view_document.view_document_foot}
	',
	
	'view_document_editor.document_in_ce_state' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
		{*view_document_editor.view_all_rounds}
		{ce_rounds}
		{ce_assigned}
		{ce_obj}
		{waiting_author}
		{*view_document.view_document_foot}
	',
	
	'view_document_editor.document_in_le_state' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
		{*view_document_editor.view_all_rounds}
		{ce_obj}
		{le_assigned}
		{*view_document.view_document_foot}
	',

	'view_document_editor.dedicatedReviewerAssignedOldListHeader' => '
		
				
	',
	
	'view_document_editor.dedicatedReviewerAssignedOldListFooter' => '
					
	',
	
	'view_document_editor.dedicatedReviewerAssignedOldListEnd' => '
				</table>
	',

	'view_document_editor.dedicatedReviewerAssignedOldListStart' => '
			<table cellpadding="0" cellspacing="0" width="100%" class="reviewer_list_tbl">
			<colgroup>
				<col width="30%"></col>
				<col width="30%"></col>
				<col width="30%"></col>
				<col width="10%"></col>
			</colgroup>
			<tr>
				<th align="left">{_getstr(pjs.nominated_reviewer_txt)}</td>
				<th align="left">{_getstr(pjs.nominated_reviewer_status_txt)}</td>
				<th align="left">{_getstr(pjs.nominated_reviewer_action_txt)}</td>
				<th align="center">{_getstr(pjs.nominated_reviewer_history_txt)}</td>
			</tr>
	',

	'view_document_editor.dedicatedReviewerAssignedOldListRow' => '
		<tr>
			<td align="left">{first_name} {last_name} <a href="mailto:{uname}"><img src="../i/mail.png"></a></td>
			<td align="left">
				{_DisplaySETextAboutDedicatedReviewer(invitation_state, usr_state, decision_id, due_date, decision_name, review_usr_due_date)}
			</td>
			<td align="left">
				{_DisplayReviewIcon(invitation_id, decision_id, reviwer_id, round_id, document_id, id, round_number, reviwer_document_version_id)}
			</td>
			<td align="center"><a href="#" class="history_link">{_getstr(pjs.row_view_text)}</a></td>
		</tr>
	',

	'view_document_editor.assigned_reviewers_list_se_can_take_decision' => '
		<div class="document_author_holder_content_no_review_yet" style="margin:0px 20px 0px 20px">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
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
						<table cellpadding="0" cellspacing="0" width="100%">
							<colgroup>
								<col width="33%"></col>
								<col width="33%"></col>
								<col width="33%"></col>
							</colgroup>
							<tr>
								<td align="left" style="padding:0px 0px 0px 20px">
									<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{subject_editor_name}</div>
								</td>
								<td align="center">
									{_checkReviewRoundDate(round_due_date_main, current_round_id, round_user_id)}
								</td>
								<td align="right" style="padding:0px 20px 0px 0px">
									{_checkReviewRoundDateLinks(round_due_date_main, user_version_id, role_id, round_number, document_id, round_user_id, current_round_id, check_invited_users, 1, can_invite_reviewers, document_review_type_id)}
								</td>
							</tr>
						</table>
					</div>
					
					<div class="document_author_holder_content_no_review_yet_bottom">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	',
	
	'view_document_editor.assigned_reviewers_list_se_can_take_decision_closed_peer' => '
				<div class="document_author_holder_content" style="margin:0px 40px 0px 40px; padding-top:0px;">
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
						<table cellpadding="0" cellspacing="0" width="100%">
							<colgroup>
								<col width="33%"></col>
								<col width="33%"></col>
								<col width="33%"></col>
							</colgroup>
							<tr>
								<td align="left" style="padding:0px 0px 0px 20px">
									<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{subject_editor_name}</div>
								</td>
								<td align="center">
									{_checkReviewRoundDate(round_due_date_main, current_round_id, round_user_id)}
								</td>
								<td align="right" style="padding:0px 20px 0px 0px">
								
									{_checkReviewRoundDateLinks(round_due_date_main, user_version_id, ' . JOURNAL_EDITOR_ROLE . ', round_number, document_id, round_user_id, current_round_id, check_invited_users, 1, can_invite_reviewers, document_review_type_id)}
								</td>
							</tr>
						</table>
					</div>
					
					<div class="document_author_holder_content_no_review_yet_bottom">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
	',
	
	'view_document_editor.assigned_reviewers_list_waiting_reviewers' => '
		<div class="document_author_holder_content_no_review_yet" style="margin:0px 40px 0px 40px">
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
				<span class="yellow-green-txt">You can proceed with your decision 
				after the due date for panel review has passed and all nominated reviewers have completed their reviews.<br>
				If nominated reviewers fail to complete their tasks on time, you may remove them from the process.</span>
				<table cellpadding="0" cellspacing="0" width="100%" style="padding-top:10px;">
					<tr>
						<td align="center">
							<img src="./i/SE_decision_not_allowed.png"></img>
						</td>
					</tr>
				</table>
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
	',
	
	'view_document_editor.e_tabs' => '
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
			<div class="tabRow {_showViewDocumentActiveSectionTab(active_tab, a' . GET_SUBMITTED_FILES_SECTION . ')}" onclick="window.location=\'view_document.php?id={document_id}&view_role={view_role}&section=' . GET_SUBMITTED_FILES_SECTION . '\'">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.sybm_files_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>	
			</div>
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

	'view_document_editor.document_editor_waiting_author_version_after_review' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
		{*view_document_editor.view_all_rounds}
		{waiting_author}
		{*view_document.view_document_foot}
	',

	'view_document_editor.document_editor_waiting_author_version_after_review_object' => '
		<div class="document_author_holder_content_no_review_yet" style="margin:0px 20px 0px 20px">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.dashboards.ReviewRound)} {round_number}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
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
						<table cellpadding="0" cellspacing="0" width="100%">
							<colgroup>
								<col width="33%"></col>
								<col width="33%"></col>
								<col width="33%"></col>
							</colgroup>
							<tr>
								<td align="left" style="padding:0px 0px 0px 20px">
									<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{first_name} {last_name}</div>
								</td>
								<td align="center">
									{_checkAReviewRoundDate(round_due_date, current_round_id, user_round_id)}
								</td>
								<td align="right" style="padding:0px 20px 0px 0px">
									{_checkAReviewRoundDateReminder(round_due_date)}
								</td>
							</tr>
						</table>
					</div>
					
					<div class="document_author_holder_content_no_review_yet_bottom">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	',
	
	'view_document_editor.document_editor_waiting_author_version_after_review_rounds_object' => '
		<div class="document_author_holder_content_no_review_yet" style="margin:0px 20px 0px 20px">
			{_showEditorCurrentRoundLabel(state_id, ce_rounds_count)}
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
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
						<table cellpadding="0" cellspacing="0" width="100%">
							<colgroup>
								<col width="33%"></col>
								<col width="33%"></col>
								<col width="33%"></col>
							</colgroup>
							<tr>
								<td align="left" style="padding:0px 0px 0px 20px">
									<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{first_name} {last_name}</div>
								</td>
								<td align="center">
									{_checkAReviewRoundDate(round_due_date, current_round_id, user_round_id)}
								</td>
								<td align="right" style="padding:0px 20px 0px 0px">
									{_checkAReviewRoundDateReminder(round_due_date)}
								</td>
							</tr>
						</table>
					</div>
					
					<div class="document_author_holder_content_no_review_yet_bottom">
						<table width="100%" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td width="5"><div class="yellow-bottom-left"></div></td>
									<td><div class="yellow-bottom-middle"></div></td>
									<td width="5"><div class="yellow-bottom-right"></div></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	',
	
	'view_document_editor.AssignedInvitedReviewersHolderView' => '
		<div class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view" id="collapse_closed_review_round_{round_number}">
			<table width="100%" cellspacing="0" cellpadding="0">
				<colgroup>
					<col width="33%"></col>
					<col width="33%"></col>
					<col width="33%"></col>
				</colgroup>
				<tbody>
					<tr>
						<td align="left">
							<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{round_name} round {round_number}</div>
						</td>
						<td align="center">
							<span class="middle_col_collapsed_label">{decision_round_name}</span>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="openPopUp(\'/view_version.php?version_id={se_version_id}&id={document_id}&view_role=' . (int)SE_ROLE . '\')">View reviews</a>
						</td>
						<td align="right">
							<div><img src="../i/collapse_open.png"></img> <span class="collapse_text" onclick="Collapse(1, \'collapse_closed_review_round_{round_number}\', \'collapse_opened_review_round_{round_number}\')">Expand</span></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="document_author_review_round_holder" id="collapse_opened_review_round_{round_number}" style="display:none">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{round_name} round {round_number}</div>
				<div class="document_author_review_round_top_right">
					<img src="../i/collapse_close.png"></img>
					<span class="collapse_text" onclick="Collapse(0, \'collapse_closed_review_round_{round_number}\', \'collapse_opened_review_round_{round_number}\')">Collapse</span>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="reviewholder_top" {_showHideReviewersText(hide_reviewers_text)}>
					<div class="reviewholder_top_left">Reviewers</div>
					<div class="reviewholder_top_right">
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_content">
					{dedicated_reviewers}
					{_showNoDedicatedReviewersData(no_data_type, document_review_type_id, document_id)}
					{panel_public_reviewers}
					{_showNoPanelsData(no_data_type, document_review_type_id, document_id, round_number)}
					<div class="document_author_holder_line" {_showHideReviewersText(hide_reviewers_text)}></div>
					<div class="doc_holder_reviewer_list" {_checkManagePaddings(hide_reviewers_text)}>
						<table cellpadding="0" cellspacing="0" width="100%">
							<colgroup>
								<col width"33%"></col>
								<col width"33%"></col>
								<col width"34%"></col>
							</colgroup>
							<tr>
								<td align="left"><span class="ed_decision_class_holder">Editor decision</span></td>
								<td align="center"><span class="ed_decision_val_class_holder">{decision_round_name}</span></td>
								<td align="right"><img src="../i/eye.png"></img> <a href="javascript:void(0);" onclick="openPopUp(\'/view_version.php?version_id={se_version_id}&id={document_id}&view_role=' . (int)SE_ROLE . '\')">View reviews</a></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	',

	'view_document_editor.document_ce_round_row' => '
		<div class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view" id="collapse_closed_ce_round_{rownum}">
			<table width="100%" cellspacing="0" cellpadding="0">
				<colgroup>
					<col width="33%"></col>
					<col width="33%"></col>
					<col width="33%"></col>
				</colgroup>
				<tbody>
					<tr>
						<td align="left">
							<div class="document_author_review_round_top_left document_author_review_round_top_left_editor">{_getstr(pjs.copyeditinground_label)} {rownum}</div>
						</td>
						<td align="center">
							<a href="#" onclick="openPopUp(\'/view_version.php?version_id={copy_editor_version_id}&id={document_id}&view_role=' . (int)CE_ROLE . '\')">View copyedited version</a>
						</td>
						<td align="right">
							<div><img src="../i/collapse_open.png"></img> <span class="collapse_text" onclick="Collapse(1, \'collapse_closed_ce_round_{rownum}\', \'collapse_opened_ce_round_{rownum}\')">Expand</span></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="submission_notes_main_wrapper" id="collapse_opened_ce_round_{rownum}" style="display:none;">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.copyeditinground_label)} {rownum}</div>
				<div class="document_author_review_round_top_right">
					<img src="../i/collapse_close.png"></img>
					<span class="collapse_text" onclick="Collapse(0, \'collapse_closed_ce_round_{rownum}\', \'collapse_opened_ce_round_{rownum}\')">Collapse</span>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_round_version_id, document_id)}
				{_showCurrentAuthorVersionCERound(document_id, copy_editor_version_id)}
			</div>
		</div>
		<div class="document_info_row_border_line"></div>
	',

);
?>