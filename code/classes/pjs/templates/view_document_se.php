<?php
// @formatter->off
$gTemplArr = array(
	'view_document_se.document_in_review' => '
		{*view_document.view_document_head}
		<div class="document_view_holder">
			<div class="document_review_title">
				{_getstr(pjs.Invite_Reviewers)}
			</div>
			<div class="document_review_info">
				{_showInviteReviewersCorrectText(round_number, document_review_type_id)}
			</div>
			<!-- <div class="documentTitle">{name}</div> -->
			<!-- {assigned_dedicated_reviewers} -->
			{available_dedicated_reviewers}
			<!-- {decision} -->
			{_showAssignReviewersBackLink(document_id, editor_back)}
		</div>
		{*view_document.view_document_foot}
	',

	'view_document_se.document_se_only_decision' => '
		{*view_document.view_document_head}
		<div class="document_author_review_round_holder ed_holder_wrapper" id="doc_tab_1">
			{submission_notes}
			<div class="document_info_row_border_line"></div>
			{submission_actions}
			{_showSEProceedButton(waitnominatedflag, waitpanelflag, caninvitenominatedflag, reviews, round_user_id, document_id, round_number, reviewers_check, document_review_type_id, document_review_due_date, round_due_date, user_version_id, view_role, round_number, 1, 1, 1, reviewers_lock_flag)}
		</div>
		{*view_document.view_document_foot}
	',

	'view_document_se.document_se_decision' => '
		{*view_document.view_document_head}
		<div class="document_author_review_round_holder ed_holder_wrapper" id="doc_tab_1">
			{submission_notes}
			<div class="document_info_row_border_line"></div>
			{submission_actions}
			{assigned_reviewers}
			{_showSEProceedButton(waitnominatedflag, waitpanelflag, caninvitenominatedflag, reviews, round_user_id, document_id, round_number, reviewers_check, document_review_type_id, document_review_due_date, round_due_date, user_version_id, view_role, current_round_id, 1, check_invited_users, 1, reviewers_lock_flag)}
		</div>
		{*view_document.view_document_foot}
	',

	'view_document_se.document_se_decision_no_decision' => '
		{*view_document.view_document_head}
		<div class="document_author_review_round_holder ed_holder_wrapper" id="doc_tab_1">
			{submission_notes}
			<div class="document_info_row_border_line"></div>
			{submission_actions}
			{assigned_reviewers}
		</div>
		{*view_document.view_document_foot}
	',

	'view_document_se.document_cant_invite_reviewers_for_this_round' => '
		{*view_document.view_document_head}
		<div class="submission_notes_main_wrapper">
			<div class="document_author_holder_rev">
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
							<span class="yellow-green-txt">You\'re not in a round in wich you can assign reviewers</span>
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

	'view_document_se.document_in_review_cant_assign_reviewers' => '
		{*view_document.view_document_head}
		<div class="document_view_holder">
			{decision}
			<div class="back_link">
				&laquo; <a href="/view_document.php?id={document_id}&amp;view_role=' . SE_ROLE . '">back</a>
			</div>
		</div>
		{*view_document.view_document_foot}
	',

	'view_document_se.document_e_actions' => '
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
								{_showInviteReviewersButton(can_invite_reviewers, document_id, round_number, document_review_type_id, _1)}
								{_showRejectSEButtons(round_number, userid, document_id)}
								{_showSEProceedButton(waitnominatedflag, waitpanelflag, caninvitenominatedflag, reviews, round_user_id, document_id, round_number, reviewers_check, document_review_type_id, document_review_due_date, round_due_date, user_version_id, view_role, round_number, _0, 1, 1, can_invite_reviewers)}
								{_showInviteReviewersButton(can_invite_reviewers, document_id, round_number, document_review_type_id, _2)}
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
	
	'view_document_se.document_rejected' => '
		{*view_document.view_document_head}
		{submission_notes}
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
	
	'view_document_se.document_waiting_author_version_after_review' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">{_checkSERoundLabel(round_type, round_name, round_number, state_id)}</div>
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
								<span class="yellow-green-txt">{_getstr(pjs.waitingauthornewversion_rev)}</span>
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

	'view_document_se.document_approved_for_publish' => '
		{*view_document.view_document_head}
		{submission_notes}
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
							<span class="yellow-green-txt">The document is approved for publishing</span>
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

	'view_document_se.document_passed_review_state' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">{_showSERoundNumberInfo(round_type, round_name, round_number, state_id, round_number_accept)}</div>
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
								<span class="yellow-green-txt">Accepted for publication on {_formatDateDMY(decision_date, .)}</span>
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

	'view_document_se.document_in_layout_editing' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">Proof reading</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_rev">
					<div class="document_author_holder_line"></div>
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
								<span class="yellow-green-txt">In layout</span>
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

	'view_document_se.document_in_copy_editing' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">Copy editing</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_rev">
					<div class="document_author_holder_line"></div>
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
								<span class="yellow-green-txt">In copy editing</span>
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
	
	'view_document_se.document_passed_review_state_copyediting' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">Copy editing</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_rev">
					<div class="document_author_holder_line"></div>
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
								<span class="yellow-green-txt">In copy editing</span>
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
	
	'view_document_se.document_passed_review_state_layout' => '
		{*view_document.view_document_head}
		{submission_notes}
		<div class="document_info_row_border_line"></div>
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">Layout editing</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_rev">
					<div class="document_author_holder_line"></div>
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
								<span class="yellow-green-txt">In layout</span>
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

	'view_document_se.invited_reviewers_obj_list_row' => '{_showSeparatorReviewers(first_name, last_name, records, rownum, version_id)}',
	
	'view_document_se.public_panel_reviewers_holder' => '
		<table width="100%" cellspacing="0" cellpadding="0" class="reviewer_list_tbl reviewer_community_public_list_tbl">
			<colgroup>
				<col width="35%">
				<col width="35%">
				<col width="30%">
				<!--<col width="10%">-->
			</colgroup>
			<tr>
				<th align="left">Panel reviewers</th>
				<th align="left">Status</th>
				<th align="left" style="text-align:center">Review submitted by</th>
				<!--<th align="center">Action</th>-->
			</tr>
			<tr>
				<td align="left">
					{invited_reviewers}
				</td>
				<td align="left">
					{_checkCommunityPublicDueDate(document_review_type_id, panel_duedate, public_duedate)}
				</td>
				<td align="center">
					{reviewed_reviewers}
				</td>
				<!--<td align="center">
					{_showViewVersionIconPanelR(has_panel_reviews)}
				</td>-->
			</tr>
		</table>
	',
	
	'view_document_se.AssignedInvitedReviewersHolder' => '
		<div class="document_author_review_round_holder" id="doc_tab_1">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				{_showAddReviewersSection(review_lock, document_id)}
				<div class="document_author_holder_content">
					{dedicated_reviewers}
					{_showNoDedicatedReviewersData(no_data_type, document_review_type_id, document_id)}
					{panel_public_reviewers}
					{_showNoPanelsData(no_data_type, document_review_type_id, document_id, round_number)}
					<div class="document_author_holder_line"></div>
				</div>
			</div>
		</div>
	',
	
	'view_document_se.dedicatedReviewerAssignedListHeader' => '
	',
	
	'view_document_se.dedicatedReviewerAssignedListFooter' => '
	',
	
	'view_document_se.dedicatedReviewerAssignedListEnd' => '
		</table>
	',

	'view_document_se.dedicatedReviewerAssignedListStart' => '
		<table cellpadding="0" cellspacing="0" width="100%" class="reviewer_list_tbl">
		<colgroup>
			<col width="35%"></col>
			<col width="35%"></col>
			<col width="30%"></col>
		</colgroup>
		<tr>
			<th align="left">Nominated reviewers</td>
			<th align="left">Status</td>
			<th align="center" style="text-align:center">Actions</td>
		</tr>
	',

	'view_document_se.dedicatedReviewerAssignedListRow' => '
		<tr>
			<td align="left">{first_name} {last_name} <a href="mailto:{uname}"><img src="../i/mail.png"></a></td>
			<td align="left">
				{_DisplaySETextAboutDedicatedReviewer(invitation_state, usr_state, decision_id, due_date, decision_name, review_usr_due_date, round_id, id, reviwer_id)}
			</td>
			<td align="center">
				{_DisplaySEActionsAboutDedicatedReviewer(invitation_id, invitation_state, usr_state, decision_id, due_date, reviwer_id, round_id, document_id, id, round_number, reviwer_document_version_id)}
			</td>
			<!--<td align="center"><a href="#" class="history_link">View</a></td>-->
		</tr>
	',

	'view_document_se.dedicatedReviewerAvailableListEnd' => '
		</table>
	',

	'view_document_se.dedicatedReviewerAvailableListStart' => '
		<div class="reviewers_row_header">Select reviewers from the list:</div>
		<table cellpadding="0" cellspacing="0" class="reviewer_tbl" width="100%">
			<tr>
				<th align="left">{_getstr(pjs.reviewers_name_label)}</th>
				<th align="left">{_getstr(pjs.reviewers_email_label)}</th>
				<th align="left">{_getstr(pjs.reviewers_added_label)}</th>
				<th>{_getstr(pjs.reviewers_nominated_label)}</th>
				{_ReviewerOptionsHeader(round_number, review_process_type)}
			</tr>
	',

	'view_document_se.dedicatedReviewerAvailableListRow' => '
		<tr>
			<td><div class="suggestRevName">{first_name} {last_name}</div>
			<span class="suggestRevExpertise">
			{_unsafe_render_if(taxa, ,<br />)}{_unsafe_render_if(subjects, ,<br />)}{_unsafe_render_if(geo)}</span></td>
			<td class="sm_font">{email}</td>
			<td class="sm_font">{_suggestedBy(added)}</td>
			{_ReviewerOptions(role_id, id, round_number, review_process_type)}
		</tr>',
	'view_document_se.noSuggestedReviewersAvailable' =>
	'<p>{_getstr(pjs.no_suggestions)}</p>',
	
	'view_document_se.dedicatedReviewerAvailableListHeader' => '',
	
	'view_document_se.dedicatedReviewerAvailableListFooter' => '
	<script type="text/javascript">
	
			var check;
			$(\'input[type="radio"]\').hover(function() {
			    check = $(this).is(\':checked\');
			});
			$(\'input[type="radio"]\').click(function() {
			    check = !check;
			    $(this).attr("checked", check);
			});
		
		</script>
		<div class="reviewers_footer">
			<div class="reviewers_footer_txt">{_getstr(pjs.addreviewerstolist)}</div>
			<div class="reviewers_footer_content">
				<div class="reviewers_footer_content_left">
					<form name="reviewer_search_form" method="post" action="/view_document.php?id={document_id}&amp;view_role=3&amp;mode=1" id="reviewer_search_form">
						<div class="reviewers_footer_content_left_label">{_getstr(pjs.searchreviewers)}</div>
						<div class="reviewers_footer_content_left_inp_holder">
								<div class="fieldHolder">
									<input type="text" value="" name="reviewer_search" id="reviewer_search" />
									<script type="text/javascript">
									autoCompleteReviewers("' . SITE_URL .'", {document_id}, {current_round_id});
									
									</script>
								</div>
							<!--<div class="reviewers_footer_content_left_icon"></div>-->
							<div class="P-Clear"></div>
						</div>
					</form>
				</div>
				<div class="reviewers_footer_content_middle">{_getstr(pjs.or_text)}</div>
				<div class="reviewers_footer_content_right">
					<div class="reviewers_search_btn_left"></div>
					<div class="reviewers_search_btn_middle" style="cursor: pointer"
					onclick="window.location=\'/create_user?mode=' . SE_ROLE . '&amp;document_id={document_id}&amp;round_id={current_round_id}&amp;role=' . DEDICATED_REVIEWER_ROLE . '\'">
						Create new reviewer
					</div>
					<div class="reviewers_search_btn_right"></div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
		</div>
		<div class="under_footer">
			<div class="invite_reviewer_btn" onclick="DocumentInviteReviewers({document_id}, {view_role}, \'{_getstr(pjs.invite_at_least_one_nom_pan_reviewer)}\')">
				<div class="invite_reviewer_btn_left"></div>
				<div class="invite_reviewer_btn_middle">{_getstr(pjs.invite_reviewers_btn_txt)}</div>
				<div class="invite_reviewer_btn_right"></div>
				<div class="P-Clear"></div>
			</div>
			<div class="invite_reviewer_text">
				{_getstr(pjs.invite_reviewers_txt)}
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'view_document_se.decision_form' => '
			Make your decision:
			<form name="reviewer_decision_form">
				<div>
					Notes:
					<textarea name="notes" id="decision_notes"></textarea>
				</div>
				<a href="#" onclick="SaveSEDecision({round_user_id}, ' . (int)ROUND_DECISION_ACCEPT . ')">Accept</a> &nbsp;
				<a href="#" onclick="SaveSEDecision({round_user_id}, ' . (int)ROUND_DECISION_ACCEPT_WITH_MINOR_CORRECTIONS . ')">Accept with minor corrections</a> &nbsp;
				<a href="#" onclick="SaveSEDecision({round_user_id}, ' . (int)ROUND_DECISION_ACCEPT_WITH_MAJOR_CORRECTIONS . ')">Accept with major corrections</a> &nbsp;
				<a href="#" onclick="SaveSEDecision({round_user_id}, ' . (int)ROUND_DECISION_REJECT . ')">Reject</a> &nbsp;

			</form>
	',
	
	'view_document_se.submission_actions_non_peer_review' => '
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersion(version_num, author_version_id, document_id)}
				<!-- <div class="document_author_holder_content">
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
											<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
												<div class="invite_reviewer_btn_left"></div>
												<div class="invite_reviewer_btn_middle">Proceed</div>
												<div class="invite_reviewer_btn_right"></div>
												<div class="P-Clear"></div>
											</div>
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
				</div> -->
			</div>
		</div>
	',
	
	'view_document_se.se_tabs' => '
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
	
	'view_document_se.history_section' => '
		{*view_document.view_document_head}
			{review_round_1}
			{_showBorderLine(has_round2)}
			{review_round_2}
			{_showBorderLine(has_round3)}
			{review_round_3}
		{*view_document.view_document_foot}
	',
	
	'view_document_se.AssignedInvitedReviewersHolderView' => '
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
					{dedicated_reviewers}
					{panel_public_reviewers}
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
	
	'view_document_se.dedicatedReviewerAssignedOldListHeader' => '',
	
	'view_document_se.dedicatedReviewerAssignedOldListFooter' => '',
	
	'view_document_se.dedicatedReviewerAssignedOldListEnd' => '
				</table>
	',

	'view_document_se.dedicatedReviewerAssignedOldListStart' => '
			<table cellpadding="0" cellspacing="0" width="100%" class="reviewer_list_tbl">
			<colgroup>
				<col width="35%"></col>
				<col width="35%"></col>
				<col width="30%"></col>
				<!--<col width="10%"></col>-->
			</colgroup>
			<tr>
				<th align="left">{_getstr(pjs.nominated_reviewer_txt)}</td>
				<th align="left">{_getstr(pjs.nominated_reviewer_status_txt)}</td>
				<th align="left" style="text-align:center">{_getstr(pjs.nominated_reviewer_action_txt)}</td>
				<!--<th align="center">{_getstr(pjs.nominated_reviewer_history_txt)}</td>-->
			</tr>
	',

	'view_document_se.dedicatedReviewerAssignedOldListRow' => '
		<tr>
			<td align="left">{first_name} {last_name} <a href="mailto:{uname}"><img src="../i/mail.png"></a></td>
			<td align="left">
				{_DisplaySETextAboutDedicatedReviewer(invitation_state, usr_state, decision_id, due_date, decision_name, review_usr_due_date)}
			</td>
			<td align="center">
				{_DisplayReviewIcon(invitation_id, decision_id, reviwer_id, round_id, document_id, id, round_number, reviwer_document_version_id)}
			</td>
			<!--<td align="center"><a href="#" class="history_link">{_getstr(pjs.row_view_text)}</a></td>-->
		</tr>
	',
	
	'view_document_se.public_panel_reviewers_holder_view' => '
		<table width="100%" cellspacing="0" cellpadding="0" class="reviewer_list_tbl reviewer_community_public_list_tbl">
			<colgroup>
				<col width="35%">
				<col width="35%">
				<col width="30%">
				<!--<col width="10%">-->
			</colgroup>
			<tr>
				<th align="left">Panel reviewers</th>
				<th align="left">Status</th>
				<th align="left" style="text-align:center">Review submitted by</th>
				<!--<th align="center">Action</th>-->
			</tr>
			<tr>
				<td align="left">
					{invited_reviewers}
				</td>
				<td align="left">
					{_getstr(pjs.panel_public_review_ended)}
				</td>
				<td align="center">
					{reviewed_reviewers}
				</td>
				<!--<td align="center">
					{_showViewVersionIconPanelR(has_panel_reviews)}
				</td>-->
			</tr>
		</table>
	',
	
);
?>