<?php

$gTemplArr = array(
	// PJS Email Templates Manage
	'emailtemplates.browse_head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_email_templates') . '</h1>
	',
	'emailtemplates.browse_startrs' => '
		<p>{_displayErrorIfExist(error)}</p>
		<table width="100%" class="dashboard">
			<tr>
				<th class="left">' . getstr('pjs.name') . '</th>
				<th class="left">' . getstr('pjs.eventtype') . '</th>
				<th class="left">' . getstr('pjs.tmp.state') . '</th>
				<th colspan="3">' . getstr('pjs.action') . '</th>
				
				
			</tr>
	',
	'emailtemplates.browse_row' => '
		<tr>
			<td width="20%">{tmpname}</td>
			<td class="left">{eventtype}</td>
			<td class="left">{is_automated}</td>
			<td  style="vertical-align: middle;">
				&nbsp;&nbsp;&nbsp;<a href="/edit_email_template.php?journal_id={journal_id}&tAction=showedit&id={id}">' . getstr('pjs.edit') . '</a>
			</td>
		</tr>
	',
	'emailtemplates.browse_endrs' => '
				</table>
	',
	'emailtemplates.browse_foot' => '
				<div class="clear"></div>
	',
	'emailtemplates.browse_empty' => '<p>No email templates in this journal.</p>',
	
	'emailtemplates.template_edit_form' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_email_templates') . '</h1>
		<div class="leftMar10">
			<div class="P-Left-Col-Fields">
				{~}{~~}{journal_id}{id}{parent_id}{name}{event_type_id}
				<div class="input-reg-title">
					<b>{*template_name}</b>
				</div>
					{template_name}
				<div class="input-reg-title">
					<b>{*event_type}</b>
				</div>
					{event_type}
				<div class="input-reg-title">
					<b>{*recipients}</b>
				</div>
					{_returnArrField(recipients)}	
				<div class="input-reg-title">
					<b>{*type}</b>
				</div>
					{type}
				<div class="input-reg-title">
					<b>{*subject}</b>
				</div>
				<div class="fieldHolder">
					{subject}<div id="subject_default_select">{default_subject}</div>
				</div>
				<div class="input-reg-title">
					<b>{*template_body}</b>
				</div>
				<div class="fieldHolder">
					{template_body}<div id="body_default_select">{default_body}</div>
				</div>
				<script type="text/javascript">
					var lDefaultSubject = $(\'#subject_default_select input\').attr(\'checked\');
					var lDefaultBody = $(\'#body_default_select input\').attr(\'checked\');
					$("#subject_default_select input").addClass("defSubject");
					$("#body_default_select input").addClass("defBody");
					$(\'.defSubject\').bind(\'click\', function() {
						activateFieldCheckbox(\'template_subject\');
					});	
					$(\'.defBody\').bind(\'click\', function() {
						activateFieldCheckbox(\'template_body\');
					});
					if (lDefaultSubject != \'checked\')
						$(\'.template_subject\').removeAttr(\'disabled\');
					if (lDefaultBody != \'checked\')
						$(\'.template_body\').removeAttr(\'disabled\');
				</script>
				<div class="clear"></div>
				<div class="br"></div>
				<div class="br"></div>
				<div class="buttonsHolder clearMargin">
					<div class="P-Green-Btn-Holder clearMargin">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-80">{save}</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	'
);

?>