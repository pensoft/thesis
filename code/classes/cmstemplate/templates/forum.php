<?php

$gTemplArr = array(

	'forum.normalform' => '
		{storyid}{dscid}{topicid}{sort}{dsc_name}{hascaptcha}
		<div class="comments_form">
			<div class="label">
				{*author}
			</div>
			{author}
			<div class="label">
				{*msg}
			</div>
			{msg}
			{_showCaptchaIfExists}
			{save}
			
		</div>
	',
	
	'forum.advancedform' => '
		{storyid}{dscid}{topicid}{sort}{dsc_name}{hascaptcha}
		<div class="comments_form">
			<div class="label">
				{*author}
			</div>
			{author}
			<div class="label">
				{*subject}
			</div>
			{subject}
			<div class="label">
				{*msg}
			</div>
			{msg}
			{_showCaptchaIfExists}
			{save}
			
		</div>
	',	
			

	'forum.closed' => '
		<div class="comments_form">
			<div class="label">'.getstr('forum.closed').'</div>
		</div>
	',

	'forum.msglist_start' => '
		<div class="comments">
			<div class="blacktitle">
				' . getstr('forum.comments') . '
			</div>
			<div class="content">		
	',
	
	'forum.msglist_single_start' => '
		<div class="comments">
			<div class="blacktitle">
				<span class="navlinkholder homelink"><a class="navlink" href="/index.php">' . getstr('global.home') . '</a></span>
				<span class="navlinkholder"><a class="navlink" href="/forum.php">' . getstr('global.forum') . '</a></span>
				<span class="navlinkholder"><a class="navlink" href="/forum.php?topicid={rootid}">{subject}</a></span>
			</div>
			<div class="content">		
	',
	
	'forum.msglist_end' => '
				
				
			</div>
			<div class="bottom"></div>
		</div>
		<div class="navigation noborder">
			<div class="pageing">{nav}</div>
			<div class="unfloat"></div>
		</div>
		{postform}
	',
	
	'forum.msglist_row' => '
							<div class="comments_row">
								<div class="date">{_getDateFromTS(mdate)} | <span class="author">{author}</span></div>
								{msg}
							</div>
	',
	
	'forum.msglist_hidden' => '
							<div class="comments_row">
								<div class="date">{_getDateFromTS(mdate)} | <span class="author">{author}</span></div>
									'.getstr('forum.hiddenmsg').' <a href="javascript:void(0);" onclick="javascript:openw(\'/forumpopup.php?msgid={id}&dsgid={dsggroup}\', \'proform\', \'location=no,menubar=yes,width=460,height=400,scrollbars=yes,resizable=yes,top=0,left=0\');return false;">'.getstr('forum.hiddenhere').'</a>
							</div>
	',
	
	'forum.msglist_nodata' => '<div class="comments_row">
		' . getstr('forum.nocomments') . '
		</div>',
	
	// Popup
	'forum.popup_row' => '
	<div class="comments">
		<div class="content">	
			<div class="comments_row">
				<div class="date">{_getDateFromTS(mdate)} | <span class="author">{author}</span></div>
				{msg}
			</div>
		</div>
	</div>
	',
	
	'forum.topiclist_head' => '
		<div class="blacktitle">
			' . getstr('global.forum') . '
		</div>
		<div class="newsbrowse">
			<div class="content">
	',
	
	'forum.topiclist_footer' => '
			</div>
			<div class="bottom"></div>
			<div class="navigation noborder">
				<div class="pageing">{nav}</div>
				<div class="unfloat"></div>
			</div>
		</div>
		{postform}
	',
	
	'forum.topiclist_nodata' => '
		<div class="news_rowbrowse">
			' . getstr('forum.notopics') . '
		</div>
	',
	
	'forum.topiclist_row' => '
		<div class="news_rowbrowse">
			<div class="linkholder"><a href="/forum.php?topicid={rootid}">{subject}</a></div>
			<div class="date">{_getDateFromTS(mdate)} | <span class="author">{author}</span> | {replies} ' . getstr('forum.replies') . '</div>
		</div>
	',
	
	'forum.topiclist_start' => '',
	
	'forum.topiclist_end' => '
		
	',







);
?>