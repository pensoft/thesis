<?php
// @formatter:off
$gTemplArr = array(
	'comments.filter' => '
			<div class="Comments-Filter">
				<script>SetCommentsVersion({version_id}); SetDisplayUserChangeFilterEvent();</script>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_inline" onclick="FilterComments()" name="comments_filter_inline" value="1" checked="checked">
					<label for="comments_filter_inline" style="width:98px; cursor:pointer;">Inline</label>
				</div>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_general" onclick="FilterComments()" name="comments_filter_general" value="1" checked="checked">
					<label for="comments_filter_general" style="width:98px; cursor:pointer;">General</label>
				</div>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_resolved" onclick="FilterComments()" name="comments_filter_resolved" value="1" checked="checked">
					<label for="comments_filter_resolved" style="width:98px; cursor:pointer;">Resolved</label>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Comments-Expand-Collapse" id="Comments-Collapse-Expand-Top" style="display:none">
				<span class="Collapse-Expand unselectable"><img src="' . PWT_URL . '/i/double_arows_up.png" alt="" /><a href="javascript:void(0)" onclick="ExpandCollapseAll(0);">Collapse all</a></span>
				<span class="Collapse-Expand unselectable"><img src="' . PWT_URL . '/i/double_arrows_down.png" alt="" /><a href="javascript:void(0)" onclick="ExpandCollapseAll(1);">Expand all</a></span>
				<div class="P-Comment-Nav-BtnsNEW">		
					<span class="Comment-PrevNEW floatLeft{_displayPrevCommentVersionReadonlyClass(preview_is_readonly)}"><a style="cursor:pointer" onclick="SelectPreviousComment()"><img src="/i/docleftarrow.png" alt="" style="vertical-align:middle;" />&nbsp;Prev</a></span>
					<span class="Comment-NextNEW floatLeft"><a style="cursor:pointer" onclick="SelectNextComment()"><img src="/i/docrightarrow.png" alt="" style="vertical-align:middle;" />&nbsp;Next</a></span>
					<div class="P-Clear"></div>
				</div>
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
			<div class="P-Root-Comment-Wrapper">
				<div id="P-Root-Comment-{id}" class="P-Comments-Revisions-Item">
					{_displaySingleCommentInfo(id, rootid, has_editor_permissions, photo_id, is_disclosed, usr_id, current_user_id, fullname, undisclosed_user_fullname, lastmoddate, lastmoddate_in_seconds, 111, start_instance_id, start_field_id, end_instance_id, end_field_id)}			
					<script type="text/javascript">
						initComment({id}, {start_instance_id}, {start_field_id}, {start_offset}, {end_instance_id}, {end_field_id}, {end_offset}, \'{_DisplayCommentUserName(is_disclosed, usr_id, has_editor_permissions, current_user_id, fullname, undisclosed_user_fullname)}\', {_showFormatedPubDateInJSON(lastmoddate)});
					</script>
				</div>
				<div class="P-Comments-Revisions-Item-Content">					
					<div class="P-Comments-Container">
	',
	'comments.browseRow' => '
						{*comments.viewRow}
	',
	'comments.viewRow' => '
						<div id="P-Comment-{id}" class="P-Comments-Single-Row {_displayCommentSingleRowClass(id, rootid)}">
							{_displaySingleCommentInfo(id, rootid, has_editor_permissions, photo_id, is_disclosed, usr_id, current_user_id, fullname, undisclosed_user_fullname, lastmoddate, lastmoddate_in_seconds)}
							<div class="P-Comment-Msg" id="P-Comment-Msg-Holder_{id}" {_putCommentOnClickEvent(id, usr_id, current_user_id, version_is_readonly)}>
								{_nl2br(msg)}
							</div>
							<div id="P-Comment-Edit-Form_{id}" class="P-Comment-Edit-Form" style="display:none" >
								{_DisplayCommentEditForm(comment_edit_forms, id, usr_id, current_user_id, version_is_readonly)}							
							</div>
						</div>
	',
	'comments.browseSplitFoot' => '			
					</div>
					<!-- End P-Comments-Container -->
					{_displayRootCommentActions(rootid, original_id, is_resolved, resolve_uid, resolve_fullname, resolve_date, usr_id, comment_reply_forms, version_is_readonly)}	
				</div>
			</div>
		</div>
	',
	'comments.browseEnd' => '
			
		</div>		
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
	
	'comments.editform' => '
		{comment_id}{document_id}
		{msg}
	',
	
	'comments.new_form_wrapper' => '
			<div class="P-Clear"></div>
			<div class="P-Comment-Nav-Btns">
				{_displayNewCommentBtn(version_is_readonly)}
			</div>
			{_displayNewCommentForm(version_is_readonly, new_comment_form)}						
			<div class="P-Clear"></div>
	',

	/*'comments.new_form_wrapper' => '
						<div class="P-Clear"></div>
						{_displayNewCommentBtn(version_is_readonly)}											
						<div class="Comment-Prev floatLeft {_displayPrevCommentVersionReadonlyClass(version_is_readonly)}"><a onclick="SelectPreviousComment()"><img src="/i/docleftarrow.png" alt="" style="vertical-align:middle;" />&nbsp;Prev</a></div>
						<div class="Comment-Next floatLeft"><a onclick="SelectNextComment()"><img src="/i/docrightarrow.png" alt="" style="vertical-align:middle;" />&nbsp;Next</a></div> 
						<div class="P-Clear"></div>
						{_displayNewCommentForm(version_is_readonly, new_comment_form)}						
						<div class="P-Clear"></div>
	',*/


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
										<input type="submit" name="tAction" value="Comment" onmousedown="submitPreviewNewComment();return false;" class="P-Grey-Btn-Middle" />
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
				{*comments.viewRow}
	',
);
?>