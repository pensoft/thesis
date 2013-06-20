<?php
// @formatter->off
$gTemplArr = array(

	'view_document_ce.document_not_in_copy_editing' => '
		{*view_document.view_document_head}
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				<div class="document_author_holder_rev_info_top">
					<div class="document_author_holder_rev_info_top_left">
						{_showCurrentVersion(version_num)}
					</div>
					<div class="document_author_holder_rev_info_top_right">
						<img src="../i/eye.png">
						<a href="#">View manuscript</a>
					</div>
					<div class="P-Clear"></div>	
				</div>
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
							<span class="yellow-green-txt"></span>
							<div class="document_btn_actions_editor_holder">
								<table cellpadding="0" cellspacing="0" width=100%>
									<tr>
										<td align="center">
											<span class="yellow-green-txt">The document is not in copy editing</span>
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

	'view_document_ce.document_in_review' => '
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
											<!-- <div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first" onclick="SaveCEDecision({round_user_id}, ' . (int)ROUND_COPY_EDITING_DECISION_ACCEPT . ')"> -->
											<div class="invite_reviewer_btn invite_reviewer_btn_E invite_reviewer_btn_E_first">
												<div class="invite_reviewer_btn_left"></div>
												<div class="invite_reviewer_btn_middle" onclick="openPopUp(\'/view_version.php?version_id={user_version_id}\');">Approve</div>
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

	'view_document_ce.document_approved_for_publish' => '
		{*view_document.view_document_head}
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_showRoundNumberInfo(round_type, round_name, round_number, state_id)}</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				<div class="document_author_holder_rev_info_top">
					<div class="document_author_holder_rev_info_top_left">
						{_showCurrentVersion(version_num)}
					</div>
					<div class="document_author_holder_rev_info_top_right">
						<img src="../i/eye.png">
						<a href="#">View manuscript</a>
					</div>
					<div class="P-Clear"></div>	
				</div>
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
							<span class="yellow-green-txt">This document is approved for publishing</span>
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
	
	'view_document_ce.ce_tabs' => '
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
			<div class="P-Clear"></div>
		</div>
	',

);
?>