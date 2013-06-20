<?php

$gTemplArr = array(
	'journalsection.edit_journal_section_form' => '
	<h1 class="dashboard-title">' . getstr('pjs.manage_journal_sections') . '</h1>
	<div class="leftMar10">
		<div class="P-Left-Col-Fields">
			{~}{~~}{journal_id}{guid}
			<div class="input-reg-title">
				{*title}
			</div>
			<div class="fieldHolder">
				{title}
			</div>
			<div class="input-reg-title">
				{*abbreviation}
			</div>
			<div class="fieldHolder">
				{abbreviation}
			</div>
			
			<div class="input-reg-title">
				{*policy}
			</div>
			<div class="fieldHolder">
				{policy}
			</div>
			<br />
			<div class="P-User-Type-Radios">
				{review_type}
			</div>
			<div class="clear"></div>
			<div class="input-reg-title">
				{*paper_type}
			</div>
			<div class="fieldHolder">
				{paper_type}
			</div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle">{save}</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
	<div class="clear"></div>
	',
	
	'journalsection.browse_head' => '
				<h1 class="dashboard-title">' . getstr('pjs.manage_journal_sections') . '</h1>
	',
	
	'journalsection.browse_startrs' => '
				<table width="100%" class="dashboard">
					<tr>
						<th class="left">' . getstr('pjs.title') . '</th>
						<th class="left">' . getstr('pjs.abbreviation') . '</th>
						<th class="left">' . getstr('pjs.policy') . '</th>
						<th class="left">' . getstr('pjs.review_type') . '</th>
						<th class="left">' . getstr('pjs.paper_type') . '</th>
						<th class="left">' . getstr('pjs.delete') . '</th>
					</tr>
	',
	'journalsection.browse_row' => '
					<tr>
						<td class="left"><a href="/edit_journal_section?tAction=showedit&amp;guid={id}">{title}</a></td>
						<td class="left">{abr}</td>
						<td class="left">{policy}</td>
						<td class="left">{_reviewTypesPics(review_types, review_types_names)}</td>
						<td class="left">{paper_type}</td>
						<td class="left">
							
							<a href="javascript: void(0);" onclick="confirmDelete(\'' . getstr('pjs.are_you_sure') . '\', \'/edit_journal_section.php?tAction=delete&amp;guid={id}\'); return false;"><img alt="' . getstr('pjs.delete') . '" src="/i/removepage.png" /></a>
						</td>
					</tr>
	',
	'journalsection.browse_endrs' => '
				</table>
	',
	'journalsection.browse_foot' => '
				<div class="submitLink">
					<a class="leftMar10" href="/edit_journal_section?tAction=showedit"><img src="/i/addpage.png" alt="add new section" /> ' . getstr('pjs.addNewSection') . '</a>
				</div>
				<div class="clear"></div>
	',
	'journalsection.browse_empty' => 'No sections in this journal.',
);
?>