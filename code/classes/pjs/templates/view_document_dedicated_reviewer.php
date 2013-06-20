<?php
// @formatter->off
$gTemplArr = array(	
	'view_document_dedicated_reviewer.review_round_has_passed' => '
		{*view_document.view_document_head}
		<div class="document_author_review_round_holder" id="doc_tab_1">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersionReviewer(version_num, author_version_num, author_version_id, document_id)}
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
							<span class="yellow-green-txt">The document has passed the review state when you were reviewer</span>
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

	'view_document_dedicated_reviewer.document_approved_for_publish' => '
		{*view_document.view_document_head}
		<div class="document_author_review_round_holder" id="doc_tab_1">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersionReviewer(version_num, author_version_num, author_version_id, document_id)}
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
							<span class="yellow-green-txt">Approved for publish</span>
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
	
	'view_document_dedicated_reviewer.review_round_has_removed' => '
		{*view_document.view_document_head}
		<div class="document_author_review_round_holder" id="doc_tab_1">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersionReviewer(version_num, author_version_num, author_version_id, document_id)}
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
							<span class="yellow-green-txt">You were removed from reviewers by the Section editor</span>
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
	
	'view_document_dedicated_reviewer.new_invitation' => '
		{*view_document.view_document_head}
			<div class="submission_notes_main_wrapper" id="doc_tab_1">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_rev">
					{_showCurrentAuthorVersionReviewer(version_num, author_version_num, author_version_id, document_id)}
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
								<span class="yellow-green-txt">Respond to this review request. The review is due in 21 days</span>
								<div class="document_btn_actions_editor_holder">
									<table cellpadding="0" cellspacing="0" width=100%>
										<tr>
											<td align="right">
												<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first" onclick="ConfirmReviewerInvitation({document_id}, {invitation_id});">
													<div class="invite_reviewer_btn_left"></div>
													<div class="invite_reviewer_btn_middle">Will do</div>
													<div class="invite_reviewer_btn_right"></div>
													<div class="P-Clear"></div>
												</div>
											</td>
											<td align="left">
												<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_sec" onclick="CancelReviewerInvitation({document_id}, {invitation_id});">
													<div class="invite_reviewer_btn_left"></div>
													<div class="invite_reviewer_btn_middle">Unable</div>
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
					</div>
				</div>
			</div>
		{*view_document.view_document_foot}
	',

	'view_document_dedicated_reviewer.canceled_invitation' => '
		{*view_document.view_document_head}
			<div class="document_author_review_round_holder" id="doc_tab_1">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_rev">
					{_showCurrentAuthorVersionReviewer(version_num, author_version_num, author_version_id, document_id)}
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
								<span class="yellow-green-txt">You have canceled your invitation to participate in the current review round of this document.</span>
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

	'view_document_dedicated_reviewer.confirmed_invitation' => '
		{*view_document.view_document_head}
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_rev">
					{_showCurrentAuthorVersionReviewer(version_num, author_version_num, author_version_id, document_id)}
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
												<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
													<div class="invite_reviewer_btn_left"></div>
													<div class="invite_reviewer_btn_middle" onclick="
													openPopUp(\'/view_version.php?version_id={document_version_id}&view_role={view_role}&id={document_id}&round={round_number}&round_user_id={round_user_id}\')">Proceed with review</div>
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
					</div>
				</div>
			</div>
		{*view_document.view_document_foot}
	',

	'view_document_dedicated_reviewer.confirmed_invitation_decision_taken' => '
		{*view_document.view_document_head}
			<div class="submission_notes_main_wrapper">
				<div class="document_author_review_round_top">
					<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
					<div class="P-Clear"></div>
				</div>
				<div class="document_author_holder_rev">
					{_showCurrentAuthorVersionReviewer(version_num, author_version_num, author_version_id, document_id)}
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
								<table width="100%" cellspacing="0" cellpadding="0">
									<tbody><tr>
										<td align="center">
											<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
												<div class="invite_reviewer_btn_left"></div>
												<div class="invite_reviewer_btn_middle" onclick="openPopUp(\'/view_version.php?version_id={document_version_id}&id={document_id}&view_role={view_role}&round={round_number}&round_user_id={round_user_id}\')">See Review</div>
												<div class="invite_reviewer_btn_right"></div>
												<div class="P-Clear"></div>
											</div>
										</td>
									</tr>
								</tbody></table>
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

	'view_document_dedicated_reviewer.r_tabs' => '
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
			{_showHistoryTab(active_tab, document_id, view_role, has_history)}
			<div class="P-Clear"></div>
		</div>
	',

	'view_document_dedicated_reviewer.assigned_reviewer_veiw' => '
		{_showBorderReviewer(cnt_rounds, last_round)}
		<div class="document_author_review_round_holder">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{round_name} round {round_number}</div>
				<div class="document_author_review_round_top_right">
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				{_showCurrentAuthorVersionReviewer(version_num, author_version_num, author_version_id, document_id)}
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
							{_showReviewerRoundStateObjs(decision_id, invitation_state_id,usr_role_name, user_version_id, view_role, round_number, round_user_id, document_id)}
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

);
?>