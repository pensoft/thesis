<?php

$gTemplArr = array(
	'comments.filter' => '
			<div class="Comments-Filter">
				<script>SetCommentsDocument({document_id}); SetDisplayUserChangeFilterEvent();</script>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_general" onclick="FilterComments()" name="comments_filter_general" value="1" checked="checked">
					<label for="comments_filter_general" style="width:100px">General</label>
				</div>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_inline" onclick="FilterComments()" name="comments_filter_inline" value="1" checked="checked">
					<label for="comments_filter_inline" style="width:100px">Inline</label>
				</div>
				<div class="commentsSingleFilter">
					<input type="checkbox" id="comments_filter_resolved" onclick="FilterComments()" name="comments_filter_resolved" value="1" checked="checked">
					<label for="comments_filter_resolved" style="width:100px">Resolved</label>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Comments-Expand-Collapse" id="Comments-Collapse-Expand-Top" style="display:none">
				<span><img src="/i/double_arows_up.png" alt="" /><a href="javascript:void(0)" onclick="ExpandCollapseAll(0);">Collapse All</a></span>
				<span><img src="/i/double_arrows_down.png" alt="" /><a href="javascript:void(0)" onclick="ExpandCollapseAll(1);">Expand All</a></span>
			</div>
			<div class="P-Clear"></div>

	',

	'comments.new_form_wrapper' => '
			<div class="P-Clear"></div>
			<div class="P-Comment-Nav-Btns">
				{_displayNewCommentBtn(preview_is_readonly)}			
				<div class="Comment-Prev floatLeft{_displayPrevCommentVersionReadonlyClass(preview_is_readonly)}"><a onclick="SelectPreviousComment()">Prev</a></div>
				<div class="Comment-Next floatLeft"><a onclick="SelectNextComment()">Next</a></div>
				<div class="P-Clear"></div>
			</div>
			{_displayNewCommentForm(preview_is_readonly, commentform)}						
			<div class="P-Clear"></div>
	',

	'comments.form' => '
		{instanceid}{documentid}
		{start_instance_id}{start_field_id}{start_offset}
		{end_instance_id}{end_field_id}{end_offset}
		{msg}		
	',

	'comments.answerform' => '
		{instanceid}{documentid}{rootmsgid}{commentid}
		{msg}
	',
		
	'comments.editform' => '
		{comment_id}{document_id}
		{msg}
	',
		
	'comments.editform_wrapper' => '
		{edit_form}
	',

	'comments.wrapper' => '
		{comments}
	',

	'comments.commentform' => '
		{commentform}
	',

	'comments.singlecomment' => '
			{*comments.viewRow}			
	',
	'comments.browseHead' => '<script type="text/javascript">setCommentsPreviewMode({in_preview_mode});InitFreezeResizeEvent();</script>',
	'comments.browseStart' => '
							<script>ShowExpandCollapseBtns();</script>
							<div class="P-Comments-Revisions-Content" id="P-Root-Comments-Holder">
	',
	'comments.browseSplitHead' => '
		<div id="P-Root-Comment-Holder-{id}" class="P-Root-Comment">
			<div class="P-Root-Comment-Wrapper">
				<div id="P-Root-Comment-{id}" class="P-Comments-Revisions-Item">
					{_displaySingleCommentInfo(id, rootid, photo_id, is_disclosed, usr_id, current_user_id, fullname, undisclosed_user_fullname, lastmoddate, lastmoddate_in_seconds, 111, start_instance_id, start_field_id, end_instance_id, end_field_id)}										
					<script type="text/javascript">
						initComment({id}, {start_instance_id}, {start_field_id}, {start_offset}, {end_instance_id}, {end_field_id}, {end_offset}, \'{_DisplayCommentUserName(is_disclosed, usr_id, current_user_id, fullname, undisclosed_user_fullname)}\', \'{_showFormatedPubDate(lastmoddate)}\');
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
							{_displaySingleCommentInfo(id, rootid, photo_id, is_disclosed, usr_id, current_user_id, fullname, undisclosed_user_fullname, lastmoddate, lastmoddate_in_seconds)}								
							<div class="P-Comment-Msg" id="P-Comment-Msg-Holder_{id}" {_putCommentOnClickEvent(id, usr_id, preview_is_readonly)}>
								{_nl2br(msg)}
							</div>
	                      	 {_showCommentEditForm(id, usr_id, document_id, preview_is_readonly)}
			
						</div>
	',
	'comments.browseSplitFoot' => '						
					</div>
					<!-- End P-Comments-Container -->
					{_displayRootCommentActions(rootid, document_id, instance_id, is_resolved, resolve_uid, resolve_fullname, resolve_date, usr_id, preview_is_readonly)}															
				</div>
			</div>
		</div>
	',
	'comments.browseEnd' => '
			  <div class="P-Inline-Line"></div>		
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
);
?>