<?php

$gTemplArr = array(
	'email_notifications.email_notification_form' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_email_templates') . '</h1>
		<div class="leftMar10 rightMar10">
			<div class="P-Left-Col-Fields P-Left-Col-Fields-FullWidth">
				{~}{~~}{journal_id}{event_id}{success}
				<div class="input-reg-title">
					<b>{*recipients}</b>
				</div>
					{recipients}
				<div class="input-reg-title">
					<b>{*subject}</b>
				</div>
					{subject}
				<div class="input-reg-title">
					<b>{*template_body}</b>
				</div>
					{template_body}
				<div class="clear"></div>
				<div class="br"></div>
				<div class="br"></div>
				<div class="buttonsHolder clearMargin">
					<div class="P-Green-Btn-Holder clearMargin">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-80">{send}</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<script>
			if($(\'#success\').val() > 0) {
				alert(\'message sent successful !\');
			}
		</script>
	',
);

?>