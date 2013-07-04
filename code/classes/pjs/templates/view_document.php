<?php
// @formatter->off
$gTemplArr = array(
	
	'view_document.submitted_date_obj' => '
		<div class="submission_notes_main_wrapper authors_list_holder">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.date_submitted_doc)}: <span class="no_bold">{_formatDateDMY(submitted_date)}</span></div>
				<div class="P-Clear"></div>
			</div>
		</div>
		<div class="document_info_row_border_line"></div>
	',

	'view_document.ErrorsListHeader' => '
		<div class="submission_notes_main_wrapper">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">Errors</div>
				<div class="P-Clear"></div>
			</div>
			<div class="document_author_holder_rev">
				<div class="document_author_holder_line"></div>
				<div style="padding-top:0px" class="submission_notes_main_wrapper">
					<div style="padding-top:0px" class="document_author_holder_rev">
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
	',
	'view_document.ErrorsListFooter' => '
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
			</div>
		</div>',
	'view_document.ErrorsListStart' => '',
	'view_document.ErrorsListEnd' => '',
	'view_document.ErrorsListRow' => '<span class="yellow-green-txt">{err_msg}</span>',
	'view_document.ErrorsListNoData' => '',
	
	'view_document.document_info' => '
		<div class="document_info_holder_top">
			<div class="document_info_title">
				{name}
			</div>
			<div class="document_info_authors">
				{author_name}
			</div>
			<div class="document_info_bottom_info">
				<div class="document_info_bottom_info_left">
					Article type: <span class="document_info_bottom_info_left_rubr">{document_type_name}</span>
				</div>
				<div class="document_info_bottom_info_right">
					{_showSEDocumentInfo(document_id, se_uname, se_first_name, se_last_name)}
					<div>
						<div class="document_info_bottom_info_right_left">
							Type of review:
						</div>
						<div class="document_info_bottom_info_right_icon">
							<img src="../i/review_type3.png"></img>
						</div>
						<div class="document_info_bottom_info_right_right">
							{review_type_name}
							{_changeReviewType(state_id, document_id, document_review_type_id, role)}
						</div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Clear"></div>
			</div>
		</div>
		<div class="document_info_row_border_line"></div>
	',
	
	'view_document.view_document_head' => '
			
			<h1 class="dashboard-title has_tabs{has_tabs}">{_getstr(pjs.SubmissionLabel)} #{document_id}</h1>
			{tabs}
			<script type="text/javascript">
				if ("{has_tabs}" === "1")
					document.getElementById("dashboard-content").className = "header-with-tabs";
			</script>
			<!--<div class="border"></div>
			<div class="corners corners_grey">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="viewDocumentHolderTopWrapper">
				
				<div class="contentArticles viewDocumentHolderTop viewDocumentHolderTop_{has_tabs}">
					{_getstr(pjs.SubmissionLabel)} #{document_id}
				</div>
				<div class="viewDocumentHolderTopBorder"></div>
			</div>
			-->
			<div class="contentArticles viewDocumentBottom" style="padding-top: 0px;">
				<div id="articlesFullCol">
					{document_info}
	',
	
	'view_document.view_document_foot' => '
				</div>
				<div class="clear"></div>
			</div>
			<div class="border"></div>
			<div class="corners">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
			<div id="P-Ajax-Loading-Image-Main">
				<img src="./i/loading.gif" alt="" />
			</div>
	',
	
	'view_document.metadata_section' => '
		{*view_document.view_document_head}
			{submitted_date_obj}
			{authors_list}
			{abstract_keywords}
			{indexed_terms}
		{*view_document.view_document_foot}
	',
	
	'view_document.AuthorsListHeader' => '
		<div class="submission_notes_main_wrapper authors_list_holder">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.authors_list_label)}</div>
				<div class="P-Clear"></div>
			</div>
	',
	'view_document.AuthorsListFooter' => '
			<div class="document_only_submitting_author_can_edit">
				{_getstr(pjs.only_submitting_author_can_edit)}
			</div>
		</div>
		<div class="document_info_row_border_line"></div>
	',
	'view_document.AuthorsListStart' => '
		<table width="100%" cellspacing="0" cellpadding="0" class="tbl_editors">
		<colgroup>
			<col width="20%"></col>
			<col width="40%"></col>
			<col width="20%"></col>
			<col width="20%"></col>
		</colgroup>
		<tr>
			<th align="left">{_getstr(pjs.author_name_label)}</th>
			<th align="left">{_getstr(pjs.author_affiliation_label)}</th>
			<th align="center">{_getstr(pjs.author_country_label)}</th>
			<th align="center">{_getstr(pjs.author_co_author_label)}</th>
		</tr>
	',
	'view_document.AuthorsListEnd' => '
		</table>
	',
	'view_document.AuthorsListRow' => '
		<tr>
			<td align="left">{first_name} {last_name} {_showIfItemExists(submitting_author, *&nbsp;)}<a href="mailto:{uname}"><img src="../i/mail.png"></a></td>
			<td align="left">{affiliation}</td>
			<td align="center">{country}</td>
			<td align="center">{_showCoAuthorCheck(co_author)}</td>
		</tr>
	',
	'view_document.AuthorsListNoData' => '',
	
	'view_document.abstract_keywords' => '
		<div class="submission_notes_main_wrapper">
		{abstract_obj}
		{keywords_obj}
		</div>
	',
	
	'view_document.abstract' => '
		<div class="document_author_review_round_top">
			<div class="document_author_review_round_top_left">{_getstr(pjs.abstract_label)}</div>
			<div class="P-Clear"></div>
		</div>
		<div class="subm_notes_desc_holder">
			{abstract}
		</div>
	',
	
	'view_document.keywords' => '
		<div class="document_author_review_round_top">
			<div class="document_author_review_round_top_left">{_getstr(pjs.keywords_label)}</div>
			<div class="P-Clear"></div>
		</div>
		<div class="subm_notes_desc_holder">
			{keywords}
		</div>
	',
	
	'view_document.indexed_terms' => '
		{_showBorderLine(has_abstractkeyworddata)}
		<div class="submission_notes_main_wrapper authors_list_holder">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.indexed_terms_label)}</div>
				<!--<div class="document_author_review_round_top_right">
					<a href="#">{_getstr(pjs.abstract_keywords_section_edit_label)}</a>
				</div>-->
				<div class="P-Clear"></div>
			</div>
			<div class="smb_cat_obj_section">
				<table cellspacing="0" cellpadding="0" width="100%" class="tbl_editors">
					<colgroup>
						<col width="30%"></col>
						<col width="70%"></col>
					</colgroup>
					<tr>
						<td align="left">
							{_getstr(pjs.view_document_taxon_label)}
						</td>
						<td align="left">
							{taxon_categories}
						</td>
					</tr>
					<tr>
						<td align="left">
							{_getstr(pjs.view_document_gei_label)}
						</td>
						<td align="left">
							{geographical_categories}
						</td>
					</tr>
					<tr>
						<td align="left">
							{_getstr(pjs.view_document_subj_label)}
						</td>
						<td align="left">
							{subject_categories}
						</td>
					</tr>
					<tr>
						<td align="left">
							{_getstr(pjs.view_document_chrono_label)}
						</td>
						<td align="left">
							{chronological_categories}
						</td>
					</tr>
					<tr>
						<td align="left">
							{_getstr(pjs.view_document_supporting_a_label)}
						</td align="left">
						<td>
							{_showAgencies(agencies)}
							{customagencies}
						</td>
					</tr>
				</table>
			</div>
		</div>
	',
	
	'view_document.history_section' => '
		{*view_document.view_document_head}
			{review_round_1}
			{review_round_2}
			{review_round_3}
			{review_round_ce}
			{review_round_le}
		{*view_document.view_document_foot}
	',
	
	'view_document.view_review_rounds_row' => '
	<div class="submission_notes_main_wrapper authors_list_holder">
		<div class="document_author_review_round_top">
			<div class="document_author_review_round_top_left">{_checkRoundLabelHistory(round_type_id, round_name, round_number)}</div>
			<div class="P-Clear"></div>
		</div>
		<div class="document_author_holder_rev">
			{_showRoundVersionAndInfo(round_type_id, version_num, round_number)}
			<div class="document_author_holder_line"></div>
			<div class="doc_holder_reviewer_list">
				<table cellspacing="0" cellpadding="0" width="100%">
					<colgroup>
						<col width="33%"></col>
						<col width="33%"></col>
						<col width="34%"></col>
					</colgroup>
					<tbody>
						<tr>
							<td align="left"><span class="ed_decision_class_holder">Editorial decision</span></td>
							<td align="center"><span class="ed_decision_val_class_holder">{decision_name}</span></td>
							<td align="right">{_showViewersLink(round_type_id)}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="document_info_row_border_line"></div>
	',
	
	'view_document.submitted_files_section' => '
		{*view_document.view_document_head}
			{submitted_files_list}
		{*view_document.view_document_foot}
	',
	
	'view_document.SubmittedFilesListHeader' => '
		<div class="submission_notes_main_wrapper authors_list_holder">
			<div class="document_author_review_round_top">
				<div class="document_author_review_round_top_left">{_getstr(pjs.supplementary_files_label)}</div>
				<div class="P-Clear"></div>
			</div>
	',
	'view_document.SubmittedFilesListFooter' => '
		</div>
	',
	'view_document.SubmittedFilesListStart' => '
		<table cellspacing="0" cellpadding="0" width="100%" class="tbl_editors">
			<colgroup>
				<col width="20%"></col>
				<col width="60%"></col>
				<col width="20%"></col>
			</colgroup>
	',
	'view_document.SubmittedFilesListEnd' => '
		</table>
	',
	'view_document.SubmittedFilesListRow' => '
		<tr>
			<td>
				<a href="' . DOWNLOAD_SUPPLEMENTARY_FILE_URL . '{filename}.{type}">{title}</a>
			</td>
			<td>
				{description}
			</td>
			<td>
				<a href="#">{_getstr(pjs.edit_label)}</a>
			</td>
		</tr>
	',
	'view_document.SubmittedFilesListNodata' => '
		<table cellspacing="0" cellpadding="0" width="100%" class="tbl_editors">
			<tr>
				<td align="center">
					{_getstr(pjs.no_submitted_files_found)}
				</td>
			</tr>
		</table>
	',
	
	'view_document.seAssignedListStart' => '
		<div class="submission_notes_main_wrapper submission_notes_main_wrapper_E_view">
	',

	'view_document.seAssignedListRow' => '
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

	'view_document.seAssignedListEnd' => '
		</div>
	',
	
);
?>