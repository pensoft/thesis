<?php
$gTemplArr = array (
	// Authors
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
	
	// Contents
	
	'article.contents_list_head' => '
		<div class="P-Article-Structures">
			<div class="P-Article-StructureHead">Contents</div>
			<ul id="articleMenu">
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
				<div class="{_displayDocumentTreeDivClass(11111, 0, level, has_children, 0, instance_id, document_id)}">
					<a onclick="ScrollArticleToInstance({instance_id});return false;">{object_name}</a>
				</div>
			</li>
	',
	
	'article.contents_list_row0' => '
			<li id="{instance_id}" >
				<div class="{_displayDocumentTreeDivClass(11111, 0, level, has_children, 0, instance_id, document_id)}">
					<a onclick="ScrollArticleToInstance({instance_id});return false;">{display_name}</a>
				</div>
			</li>
	',
	'article.contents_list_row1' => '
			<li id="{instance_id}">
				<div class="{_displayDocumentTreeDivClass(11111, 0, level, has_children, 0, instance_id, document_id)}">
					<a onclick="ScrollArticleToInstance({instance_id});return false;">{display_name}</a>
				</div>
				<ul class="{_displayShowHideClass(instance_id)}">
					{&}
				</ul>				
			</li>
	',
)
;

?>