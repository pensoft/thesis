<?php
// @formatter->offf
$gTemplArr = array(
	'view_document_author.document_waiting_se' => '
		{*view_document.view_document_head}
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
							<span class="yellow-green-txt">Pre-review evaluation</span>
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

	'view_document_author.document_rejected' => '
		{*view_document.view_document_head}
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
	
	'view_document_author.document' => '
		{*view_document.view_document_head}
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
							<span class="yellow-green-txt">Pre-review evaluation</span>
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

	'view_document_author.document_submit_review_version' => '
		{*view_document.view_document_head}
		<div class="document_info_row_border_line"></div>
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					{se_decision}
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
							<span class="yellow-green-txt">Revise your manuscript and respond to Editor\'s comments</span>
							<div class="document_btn_actions_editor_holder">
								<table cellpadding="0" cellspacing="0" width=100%>
									<tr>
										<td align="center">
											{_ShowHideAuthorAction(createuid, pwt_id)}
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
	
	'view_document_author.document_in_copy_review' => '
		{*view_document.view_document_head}
		<div class="document_info_row_border_line"></div>
		<div class="document_author_review_round_holder" id="doc_tab_1">
			{_showAuthorCurrentRoundLabel(state_id, ce_rounds_count)}			
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
							<span class="yellow-green-txt">{_showCopyEditingText(state_id, ce_rounds_count)}</span>
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

	'view_document_author.document_approved_for_publish' => '
		{*view_document.view_document_head}
		<div class="document_info_row_border_line"></div>
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

	'view_document_author.document_waiting_to_proceed_to_copyedit' => '
		{*view_document.view_document_head}
		<div class="document_info_row_border_line"></div>
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showSERoundNumberInfo(round_type, round_name, round_number, state_id, round_number_accept)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					{se_decision}
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
											<span class="yellow-green-txt">Revise your manuscript and submit version to be used for copy editing</span>
											{_ShowHideAuthorAction(createuid, pwt_id)}
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

	'view_document_author.document_waiting_to_proceed_to_layout' => '
		{*view_document.view_document_head}
		<div class="document_info_row_border_line"></div>
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showSERoundNumberInfo(round_type, round_name, round_number, state_id, round_number_accept, ce_rounds_count)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<div class="document_author_holder_content">
					{se_decision}
					{ce_obj}
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
											<span class="yellow-green-txt">
												{_getstr(pjs.revise_author_version_for_ce_le_round)}
											</span>
											{_ShowHideAuthorAction(createuid, pwt_id)}
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

	'view_document_author.document_in_layout' => '
		{*view_document.view_document_head}
		<div class="document_info_row_border_line"></div>
		<div class="document_author_review_round_holder" id="doc_tab_1">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">Proof reading</div>
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
							<span class="yellow-green-txt">{_showAuthorLELabel(state_id)}</span>
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

	'view_document_author.document_submit_layout_version' => '
		AUTHOR
		<div class="documentTitle">{name}</div>
		<a href="#" onclick="SaveAuthorLayoutDecision({document_id}, ' . (int)ROUND_LAYOUT_DECISION_ACCEPT . ')">Accept</a> &nbsp;
		<a href="#" onclick="SaveAuthorLayoutDecision({document_id}, ' . (int)ROUND_LAYOUT_DECISION_RETURN_TO_AUTHOR . ')">Return to LEs</a> &nbsp;

	',

	'view_document_author.author_tabs' => '
		<div class="tabHolder">
			<div class="tabRow {_showViewDocumentActiveSectionTab(active_tab, a' . (int)GET_CURSTATE_MANUSCRIPT_SECTION . ')}" onclick="window.location=\'view_document.php?id={document_id}&view_role={view_role}&section=' . GET_CURSTATE_MANUSCRIPT_SECTION . '\'">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.curStat_label_tab)}
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
			{_showHistoryTab(active_tab, document_id, view_role, has_history)}
			<div class="P-Clear"></div>
		</div>
	',

	'view_document_author.document_in_review' => '
		{*view_document.view_document_head}
		<div class="document_info_row_border_line"></div>
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
							<span class="yellow-green-txt">{_getstr(pjs.document_in_review_author_state)}</span>
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

	'view_document_author.seAssignedListRow' => '
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
				<td align="right">&nbsp;</td>
			</tr>
		</table>
	',

	'view_document_author.se_decision' => '
		<div class="document_author_holder_content document_author_holder_content_decision">
			<div class="doc_holder_reviewer_list doc_holder_reviewer_list_decision">
				<table width="100%" cellspacing="0" cellpadding="0">
					<colgroup>
						<col width="33%"></col>
						<col width="33%"></col>
						<col width="34%"></col>
					</colgroup>
					<tbody><tr>
						<td align="left"><span class="ed_decision_class_holder">Editorial decision</span></td>
						<td align="center"><span class="ed_decision_val_class_holder">{decision}</span></td>
						<td align="right"><img src="../i/eye.png"> <a href="javascript:void(0);" onclick="openPopUp(\'/view_version.php?version_id={se_version_id}&id={document_id}&view_role=' . (int)SE_ROLE . '\')">View reviews</a></td>
					</tr>
				</tbody></table>
			</div>
			<div class="document_author_holder_line"></div>
		</div>
	',

	'view_document_author.ce_decision' => '
		<div class="document_author_holder_content document_author_holder_content_decision">
			<div class="doc_holder_reviewer_list doc_holder_reviewer_list_decision">
				<table width="100%" cellspacing="0" cellpadding="0">
					<colgroup>
						<col width="33%"></col>
						<col width="33%"></col>
						<col width="34%"></col>
					</colgroup>
					<tbody><tr>
						<td align="left"><span class="ed_decision_class_holder">Copy editor version</span></td>
						<td align="center">&nbsp;</td>
						<td align="right"><img src="../i/eye.png"> <a href="javascript:void(0);" onclick="openPopUp(\'/view_version.php?version_id={copy_editor_version_id}\')">View review</a></td>
					</tr>
				</tbody></table>
			</div>
			<div class="document_author_holder_line"></div>
		</div>
	',

	'view_document_author.history_section' => '
		{*view_document.view_document_head}
			{_showBorderLine(has_se)}
			{review_round_1}
			{_showBorderLine(has_round2)}
			{review_round_2}
			{_showBorderLine(has_round3)}
			{review_round_3}
			{_showBorderLine(has_ce)}
			{review_round_ce}
		{*view_document.view_document_foot}
	',
	
	'view_document_author.AssignedInvitedReviewersHolderView' => '
		<div class="document_author_review_round_holder">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{round_name} round {round_number}</div>
				<div class="document_author_review_round_top_right">
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

	'view_document_author.document_ce_round_row' => '
		<div class="submission_notes_main_wrapper" id="collapse_opened_ce_round_{rownum}">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.copyeditinground_label)} {rownum}</div>
				<div class="document_author_review_round_top_right">
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