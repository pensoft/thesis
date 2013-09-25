<?php

// @formatter:off
$gTemplArr = array(
	'view_poll.page' => '{content}',
	
	'view_poll.aof_poll_view_head' => '
		<div class="aof_poll_view_wrapper">
			<div class="aof_poll_view_header">Poll</div>
	',
	
	'view_poll.aof_poll_view_foot' => '
		</div>
	',
	
	'view_poll.aof_poll_view_start' => '<div class="aof_poll_view_list">',
	
	'view_poll.aof_poll_view_end' => '</div>',
	
	'view_poll.aof_poll_view_nodata' => '',
	
	'view_poll.aof_poll_view_row' => '
		<div class="aof_poll_view_list_row">
			<div class="aof_poll_view_list_label">{label}</div>
			<div class="aof_poll_view_list_label">{_getPollAnswerLabel(answer_id)}</div>
			<div class="P-Clear"></div>
		</div>
	', 
	
);
?>