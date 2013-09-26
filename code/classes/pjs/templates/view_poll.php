<?php

// @formatter:off
$gTemplArr = array(
	'view_poll.page' => '{content}',
	
	'view_poll.aof_poll_view_head' => '
		<div class="aof_poll_view_wrapper">
			<div class="aof_poll_view_header">Review form</div>
	',
	
	'view_poll.aof_poll_view_foot' => '
		</div>
	',
	
	'view_poll.aof_poll_view_start' => '<table class="aof_poll_view_list">',
	
	'view_poll.aof_poll_view_end' => '</table>',
	
	'view_poll.aof_poll_view_nodata' => '',
	
	'view_poll.aof_poll_view_row' => '
		<tr class="aof_poll_view_list_row">
			<td class="aof_poll_view_list_label">{label}</td>
			<td class="aof_poll_view_list_label">{_getPollAnswerLabel(answer_id)}</td>
		</tr>
	', 
	
);
?>