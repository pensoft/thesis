<?php

$gTemplArr = array(
	'stories.show' => '
		<div class="holder">
			{_showItemIfExists(storysuptitle, <div class="suptitle">, </div>)}
			<div class="storyTitle"><h1>{storytitle}</h1></div>
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
	
	'stories.show_list' => '
		<div class="holder">
			<a name="{_strip_invalid(storytitle)}"></a>
			{_showItemIfExists(storysuptitle, <div class="suptitle">, </div>)}
			<div class="storyRowTitle {_hideStoryTitle(hidetitle)}"><h1>{storytitle}</h1></div>
			{_showItemIfExists(storysubtitle, <div class="subtitle">, </div>)}
			<div class="author">{author}</div>
			<div class="content journalListStories">
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

	'stories.show_index' => '
		<div class="holder">
			<div class="content">
				<div class="bigpic">{photosf}</div>
				<div class="picsleft">{photostl}</div>
				<div class="picsright">{photostr}</div>
				{storycontent}
				<div class="unfloat"></div>
				<div class="bottompic">{photosb}</div>
				<div class="unfloat"></div>
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

	'stories.relstrow' => '
	<a href="show.php?storyid={relstoryid}">{relsttitle}</a><br/>
	',

	'stories.rellinkrow' => '
	<a href="{relinkurl}">{relinktitle}</a><br/>
	',

	'stories.attmp3' => '
	<a href="atthref">{title}</a>
	',

	'stories.showphoto' => '
		{piczlstart}<img src="{photofname}" "border="0" alt="" />{piczlend}
		{_showItemIfExists(zoomlink, &nbsp;, &nbsp;)}
		<div class="subphototext">{photodesc}</div>
	',

	'stories.showattrow' => '<a href="/getatt.php?filename=oo_{imgname}">{title}</a><br/>'
);
?>