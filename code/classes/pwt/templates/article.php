<?php
$gTemplArr = array (
	// Authors
	'article.single_author_preview_row' => '
		<div class="AOF-Single-Author-Preview">
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, singlefigmini)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name">{first_name} {middle_name} {last_name}</div>
				<div class="AOF-Author-Affiliation">{affiliation} {city} {country}</div>
				<div class="AOF-Author-Site">{website}</div>
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
	
	// Contents
	
	'article.contents_list_head' => '
		<div class="AOF-Content-holder">
			<ul id="AOF-articleMenu">
	',
	'article.contents_list_foot' => '
			</ul>			
		</div>		
	',
	'article.contents_list_start' => '
	
	',
	'article.contents_list_end' => '
	
	',
	'article.contents_list_nodata' => '
	
	',
	'article.contents_list_row' => '
			<li id="{instance_id}" >
				<div class="1" onclick="ScrollArticleToInstance({instance_id});return false;">{object_name}</div>
			</li>
	',
	
	'article.contents_list_row0' => '
			<li id="{instance_id}" >
				<div class="2" onclick="ScrollArticleToInstance({instance_id});return false;">{display_name}</div>
			</li>
	',
	'article.contents_list_row1' => '
			<li id="{instance_id}" >
				<div class="3" onclick="ScrollArticleToInstance({instance_id});return false;">{display_name}</div>
				<ul class="">
					{&}
				</ul>				
			</li>
	',
	
	//Localities
	'article.localities_list_head' => '
		<div class="P-Article-Structures">
			<div class="P-Article-StructureHead">Localities</div>
			<div class="P-Localities-Map" id="localitiesMap"></div>			
			<script>LoadMapScript()</script>
			<div class="P-Localities-Menu">
				<div class="P-Localities-Menu-Row">
					<input type="checkbox" name="active-localities" value="-2"/>All
				</div>
				<div class="P-Localities-Menu-Row">
					<input type="checkbox" name="active-localities" value="-1"/>All taxa
				</div>
	',
		'article.localities_list_foot' => '
				<div class="P-Localities-Menu-Row">
					<span class="P-Clear-Localities">Clear</span>
				</div>
			</div>
		</div>
		<script>PlaceLocalitiesMenuEvents();</script>
	',
		'article.localities_list_start' => '
	
	',
		'article.localities_list_end' => '
	
	',
		'article.localities_list_nodata' => '
	
	',
		'article.localities_list_row' => '
				<div class="P-Localities-Menu-Row">
					<input type="checkbox" name="active-localities" value="{id}"/>{display_name}
				</div>
	',
)	
;

?>