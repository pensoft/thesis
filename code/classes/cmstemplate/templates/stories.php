<?php

$gTemplArr = array(

	'stories.browse_row' => '
				<div class="news_browse_row">
					<div class="title"><a href="news_show.php?storyid={guid}">{title}</a></div>					
					<div class="content">
						{_showPicIfExists(previewpicid, sg198, browsepic)}
						{description}
					</div>
				</div>
	',
	
	'stories.browse_nodata' => '
	
	',
	
	'stories.browse_head' => '
		<div class="tittlemidcol">
			{_getstr(global.stories)}
		</div>
	',
	
	'stories.browse_foot' => '
	
	',
	
	'stories.browse_start' => '
	
	',
	
	'stories.browse_end' => '
		{*stories.browsepageing}
	',
	
	'stories.browsepageing' => '
				<div class="pager">
					{nav}
				</div>
	',
	
	'stories.browsenodata' => '
				<div class="box">
					<p style="text-align: center;font-weight: bold;">В тази рубрика няма статии.</p>
				</div>
	',
	'stories.show' => '
		<div class="holder">
			{_showItemIfExists(storysuptitle, <div class="suptitle">, </div>)}
			<div class="title">{storytitle}</div>
			{_showItemIfExists(storysubtitle, <div class="subtitle">, </div>)}
			<div class="author">{author}</div>
			<div class="content">
				<div class="bigpic">{photosf}</div>
				<div class="picsleft">{photostl}</div>
				<div class="picsright">{photostr}</div>
				{storycontent}
				<div class="unfloat"></div>
				<div class="bottompic">{photosb}</div>
				<div class="unfloat"></div>
			</div>
			
			<div class="more">
				{relstories}
				{relinks}
				{attachments}
			</div>
		</div>
	',
	
	'stories.attachmentshead' => '
		<div class="relelements">' . getstr('stories.attachments') . '</div>
		<div class="relatedelements">
	',
	
	'stories.relsthead' => '
		<div class="relelements">' . getstr('stories.relatedstories') . '</div>
		<div class="relatedelements">
	',
	
	'stories.mediahead' => '
		<div class="relelements">' . getstr('stories.relatedmedia') . '</div>
		<div class="relatedelements">
	',
	
	'stories.relinkhead' => '
		<div class="relelements">' . getstr('stories.relatedlinks') . '</div>
		<div class="relatedelements">
	',
	
	'stories.relfoot' => '
		</div>
	',
	
	'stories.relstrow'=>'
	<a href="show.php?gstoryid={relstoryid}&rubrid={rubrid}">{relsttitle}</a><br/>
	',
	
	'stories.rellinkrow'=>'
	<a href="{relinkurl}">{relinktitle}</a><br/>
	',
	
	'stories.attmp3'=>'
	<a href="atthref"> 	{title} </a>
	',
	
	'stories.showphoto' => '
		{piczlstart}<img src="{photofname}" "border="0" alt="" />{piczlend}
		{_showItemIfExists(zoomlink, &nbsp;, &nbsp;)}
		<div class="subphototext">{photodesc}</div>
	',
	
	'stories.showattrow' => '<a href="/getatt.php?filename=oo_{imgname}">{title}</a><br/>',
);
?>