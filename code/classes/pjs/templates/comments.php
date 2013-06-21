<?php
// @formatter:off
$gTemplArr = array(
	'comments.filter' => '
			<div class="Comments-Filter">
				<script>SetCommentsVersion({version_id}); SetDisplayUserChangeFilterEvent();</script>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_general" onclick="FilterComments()" name="comments_filter_general" value="1" checked="checked">
					<label for="comments_filter_general">General comments</label>
				</div>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_inline" onclick="FilterComments()" name="comments_filter_inline" value="1" checked="checked">
					<label for="comments_filter_inline">Inline comments</label>
				</div>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_resolved" onclick="FilterComments()" name="comments_filter_resolved" value="1" checked="checked">
					<label for="comments_filter_resolved">Resolved comments</label>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Comments-Expand-Collapse" id="Comments-Collapse-Expand-Top" style="display:none">
				<span><img src="' . PWT_URL . '/i/double_arows_up.png" alt="" /><a href="javascript:void(0)" onclick="ExpandCollapseAll(0);">Collapse All</a></span>
				<span><img src="' . PWT_URL . '/i/double_arrows_down.png" alt="" /><a href="javascript:void(0)" onclick="ExpandCollapseAll(1);">Expand All</a></span>
			</div>
			<div class="P-Clear"></div>

	',

	'comments.browseHead' => '<script type="text/javascript">setCommentsPreviewMode(1); InitFreezeResizeEvent();</script>
		',
	'comments.browseStart' => '

							<script>ShowExpandCollapseBtns();</script>
							<div class="P-Comments-Revisions-Content" id="P-Root-Comments-Holder">
	',



	'comments.browseSplitHead' => '
		<div id="P-Root-Comment-Holder-{id}" class="P-Root-Comment">
			<div id="P-Root-Comment-{id}" class="P-Comments-Revisions-Item {_GetRootCommentStyle(start_instance_id, start_field_id, end_instance_id, end_field_id)}">
				{_showCommentPic(photo_id, is_disclosed, usr_id, has_editor_permissions, current_user_id)}
				<div class="P-Comments-Revisions-Item-Details">
					<div class="username">{_DisplayCommentUserName(is_disclosed, usr_id, has_editor_permissions, current_user_id, fullname, undisclosed_user_fullname)} {rootid} {_DisplayDeleteCommentLink(id, rootid, original_id, usr_id)}</div>
					<div class="commentdate">Comment / {_showFormatedPubDate(lastmoddate)}</div>
				</div>
				<script type="text/javascript">
					initComment({id}, {start_instance_id}, {start_field_id}, {start_offset}, {end_instance_id}, {end_field_id}, {end_offset}, {_json_encode(fullname)}, {_showFormatedPubDateInJSON(lastmoddate)});
				</script>
			</div>
			<div class="P-Comments-Revisions-Item-Content">
				{_displayResolvedInfo(id, is_resolved, resolve_uid, resolve_fullname, resolve_date)}
				<div class="P-Comments-Container">
	',
	'comments.browseRow' => '
					<div class="P-Inline-Line"></div>
					<div id="P-Comment-{id}" class="P-Comments-Revisions-History">
						<a href="#">{_DisplayCommentUserName(is_disclosed, usr_id, has_editor_permissions, current_user_id, fullname, undisclosed_user_fullname)}</a>&nbsp;commented: <span class="P-Comments-Reviosions-History-Date">{_showFormatedPubDate(lastmoddate, 1)}</span>
						<div class="P-Comment-Msg">{msg}</div>
					</div>
	',
	'comments.browseSplitFoot' => '
					<div class="P-Inline-Line"></div>
				</div>

				<div onclick="showCommentForm({rootid});" class="comment_btn" id="P-Comment-Btn-{rootid}"></div>
				<div id="P-Comment-Form_{rootid}" style="display: none;">
					{_DisplayCommentAnswerForm(comment_reply_forms, rootid)}
					<div class="P-Grey-Btn-Holder">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle">
							<div class="P-Comment">
								<div class="P-Btn-Icon"></div>
								<div class="P-Grey-Btn-Middle" onclick="SubmitCommentReplyForm({rootid});">Comment</div>
							</div>
						</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
				</div>
				<div class="P-Clear"></div>

			</div>
		</div>
	',
	'comments.browseEnd' => '
			<div class="P-Inline-Line"></div>
		</div>
		<script type="text/javascript">positionComments()</script>
	',
	'comments.browseFoot' => '',
	'comments.browseNoData' => '',
	'comments.commentanswerform' => '
			{commentanswerform}
	',

	'comments.previewCommentAjax' => '
		{*comments.browseSplitHead}
		{*comments.browseRow}
		{*comments.browseSplitFoot}
	',

	'comments.firstPreviewCommentAjax' => '
		{*comments.browseStart}
		{*comments.browseSplitHead}
		{*comments.browseRow}
		{*comments.browseSplitFoot}
		{*comments.browseEnd}
	',

	'comments.reply_form' => '
	{rootid}
	{msg}
	',

	'comments.new_form_wrapper' => '
						<div class="P-Clear"></div>
						<div class="comment_btn floatLeft" onclick="showCommentForm2();"></div>
						<div class="Comment-Prev floatLeft"><a onclick="SelectPreviousComment()">Prev</a></div>
						<div class="Comment-Next floatLeft"><a onclick="SelectNextComment()">Next</a></div>
						<div class="P-Clear"></div>
						{new_comment_form}
						<div class="P-Clear"></div>
	',


	'comments.new_comment_form' => '
						<div id="popupNewComment" style="display: none;">
							{version_id}
							{start_instance_id}{start_field_id}{start_offset}
							{end_instance_id}{end_field_id}{end_offset}
							{msg}
							<div class="P-Grey-Btn-Holder">
								<div class="P-Grey-Btn-Left"></div>
								<div class="P-Grey-Btn-Middle">
									<div class="P-Comment">
										<div class="P-Btn-Icon"></div>
										<input type="submit"  name="tAction" value="Comment" onclick="submitPreviewNewComment();return false;" class="P-Grey-Btn-Middle" />
									</div>
								</div>
								<div class="P-Grey-Btn-Right"></div>
							</div>
						</div>
						<div class="P-Clear"></div>
	',

	'comments.newCommentRow' => '
		{*comments.browseSplitHead}
		{*comments.browseRow}
		{*comments.browseSplitFoot}
		<script>clearNewReplyCommentForm({rootid})</script>
	',



	'comments.newCommentRowFirst' => '
		{*comments.browseStart}
		{*comments.browseSplitHead}
		{*comments.browseRow}
		{*comments.browseSplitFoot}
		{*comments.browseEnd}
		<script>clearNewReplyCommentForm({rootid})</script>
	',

	'comments.replyCommentRow' => '
			<div class="P-Comments-Revisions-History" id="P-Comment-{id}">
				<a href="#">{fullname}</a>&nbsp;commented: <span class="P-Comments-Reviosions-History-Date">{_showFormatedPubDate(lastmoddate, 1)}</span>
				<div class="P-Comment-Msg">{msg}</div>
			</div>
			<div class="P-Inline-Line"></div>
	',
);
?>