<?php
// @formatter->off
$gTemplArr = array(
	'taskspopup.form' => '
		{task_detail_id}{event_ids}{state_id}{reviewers_email_flag}{document_id}{role_redirect}
		<div class="P-Registration-Content-Fields">
			<div class="loginFormRegErrHolder">{~}{~~}</div>
			<div class="input-reg-title">{*to} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W100 fieldHolder">
				{to}
			</div>
			<div class="P-Clear"></div>
			<div class="input-reg-title">{*subject} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W100 fieldHolder">
				{subject}
			</div>
			<div class="P-Clear"></div>
			<div class="P-Input-Full-Width P-W100 fieldHolder Tasks-PopUp-Content-Area with-label">
				<label id="content_label" for="content">{_getstr(pjs.template_notes_label)}</label>
				{template_notes}
			</div>
			<div class="P-Clear"></div>
			<div class="Tasks-PopUp-Content-Template-View">
				{@template}
			</div>
			<div class="P-Clear"></div>
		</div>
		{_showEditTaskActionButtons(state_id, recipients_count)}
		<script>
			$(\'.' . HIDDEN_EMAIL_ELEMENT . '\').remove();
			ShowHideLabel($(\'#content\'), \'#content_label\');
		</script>
	',
	
	'taskspopup.formview' => '
		{task_detail_id}{event_ids}{state_id}{reviewers_email_flag}{document_id}{role_redirect}
		<div class="P-Registration-Content-Fields">
			<div class="loginFormRegErrHolder">{~}{~~}</div>
			<div class="input-reg-title">{*to} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W100 fieldHolder">
				{to}
			</div>
			<div class="P-Clear"></div>
			<div class="input-reg-title">{*subject} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W100 fieldHolder">
				{subject}
			</div>
			<div class="P-Clear"></div>
			
			<div class="P-Input-Full-Width P-W100 fieldHolder Tasks-PopUp-Content-Area">
				{template_notes}
			</div>
			<div class="P-Clear"></div>
			<div class="Tasks-PopUp-Content-Template-View">
				{@template}
			</div>
			<div class="P-Clear"></div>
		</div>
		{_showEditTaskActionButtons(state_id, recipients_count)}
		<script>
			$(\'.' . HIDDEN_EMAIL_ELEMENT . '\').remove();
		</script>
	',
	
	'taskspopup.list_only' => '{list}',
	'taskspopup.form_only' => '{form}',
	
	'taskspopup.listheader' => '
		
	',
	
	'taskspopup.listrow' => '
		<div class="taskpopup-list-row-holder {_checkSelectedRecipient(id, selected)}">
			<div class="taskpopup-list-row-content" onclick="ChangeTaskRecipient(\'P-Registration-Content\', {_convertToJSArray(event_ids)}, {id})">
				<div class="taskpopup-list-row-content-pic">
					{_showPicIfExists(photo_id, c32x32y)}
				</div>
				<div class="taskpopup-list-row-content-right-main">
					<div class="taskpopup-list-row-content-right-main-title">
						{name}
					</div>
					<div class="taskpopup-list-row-content-right-main-role-holder">
						<div class="taskpopup-list-row-content-right-main-role">
							{role_name}
						</div>
					</div>
					<div class="taskpopup-list-row-content-right-main-role_right">
						<span class="red_txt_due_date" onclick="CancelReviewerInvitationInEmailForm(event, {_convertToJSArray(event_ids)}, {uid}, {id}, \'cancel_invitation\', \'P-Registration-Content\')">' . getstr('pjs.taskspopup_reviewers_cancel_review_text') . '</span>
					</div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'taskspopup.listfoot' => '
		<script>
			ShowHideReviewersCancelInvitationLink();
		</script>
	',
	
);

?>