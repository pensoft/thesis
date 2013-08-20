<?php

$gTemplArr = array(
	'article.single_author_preview_row' => '
		<div class="P-Single-Author-Preview">
			<div class="P-Author-Pic">{_showPicIfExists(photo_id, c30x30y)}</div>
			<div class="P-Author-Details">
				<div class="P-Author-Name">{first_name} {middle_name} {last_name}</div>
				<div class="P-Author-Affiliation">{affiliation} {city} {country}</div>
				<div class="P-Author-Site">{website}</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'article.authors_preview_head' => '
		<div class="P-Authors-List">
			<div class="P-Label">Authors</div>
	',
	'article.authors_preview_foot' => '
		</div>
	',
	'article.authors_preview_start' => '
	
	',
	'article.authors_preview_end' => '
	
	',
	'article.authors_preview_nodata' => '
	
	',
	'article.authors_preview_row' => '
		{*article.single_author_preview_row}
	',

);

?>