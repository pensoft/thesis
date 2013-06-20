<?php

$gTemplArr = array(

	'search.row' => '
				<div class="news_browse_row">
					<div class="title"><a href="show.php?storyid={guid}">{title}</a></div>					
					<div class="content">
						{_showPicIfExists(previewpicid, sg198, browsepic)}
						{description}
					</div>
				</div>
	',
	'search.nodata' => '
				<div class="news_browse_row">
					<div class="content">
						{_getstr(search.noresults)}
					</div>
				</div>
	',
	
	'search.head' => '
		<div class="title">
			' . getstr('global.search'). '
		</div>
	',
	
	'search.foot' => '
	
	',
	
	'search.startrs' => '
	
	',
	
	'search.endrs' => '
		{*search.pageing}
	',
	
	'search.pageing' => '
				<div class="pager">
					{nav}
				</div>
	',
	



);
?>	