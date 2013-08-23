<?php
$gTemplArr = array (
	// Authors
	'article.single_author_preview_row' => '
		<div class="AOF-Single-Author-Preview">
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, singlefigmini)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name">{first_name} {middle_name} {last_name}</div>
				<div class="AOF-Author-Email">{email}</div>
				<div class="AOF-Author-ZooBankId">{zoobank_id}</div>			
				<div class="AOF-Author-Affiliation">{affiliation} {city} {country}</div>				
				<div class="AOF-Author-Site">{website}</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'article.authors_preview_head' => '
		<div class="P-Authors-List">			
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
		<div class="AOF-Single-Author-Preview">
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, singlefigmini)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name">{first_name} {middle_name} {last_name}</div>
				<div class="AOF-Author-Email">{email}</div>
				<div class="AOF-Author-ZooBankId">{zoobank_id}</div>			
				<div class="AOF-Author-Affiliation">{affiliation} {city} {country}</div>
				<div class="AOF-Author-Is-Corresponding">{is_corresponding}</div>
				<div class="AOF-Author-Site">{website}</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'article.authors_se_preview_head' => '
		<div class="P-Authors-List">			
	',
		'article.authors_se_preview_foot' => '
		</div>
	',
		'article.authors_se_preview_start' => '
	
	',
		'article.authors_se_preview_end' => '
	
	',
		'article.authors_se_preview_nodata' => '
	
	',
		'article.authors_se_preview_row' => '
		<div class="AOF-Single-Author-Preview AOF-Single-SE-Preview">
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, singlefigmini)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name">{first_name} {middle_name} {last_name}</div>
				<div class="AOF-Author-Email">{email}</div>
				<div class="AOF-Author-ZooBankId">{zoobank_id}</div>			
				<div class="AOF-Author-Affiliation">{affiliation} {city} {country}</div>
				<div class="AOF-Author-Site">{website}</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'article.authors_list_template' => '
		<div class="P-Authors-Whole-List">
			<div class="P-Journal-Meta">
				{journal_name} {issue_number}: {start_page}-{end_page}
			</div>
			<div class="P-Doi-Meta">
				<span class="P-Doi-Label">' . getstr('pjs.articleDoiLabel') . '</span> {doi}
			</div>
			<div class="P-Date-Received">
				<span class="P-Date-Label">' . getstr('pjs.articleDateReceivedLabel') . '</span>
				{create_date}
			</div>
			<div class="P-Date-Approved">
				<span class="P-Date-Label">' . getstr('pjs.articleDateApprovedLabel') . '</span>
				{approve_date}
			</div>
			<div class="P-Date-Published">
				<span class="P-Date-Label">' . getstr('pjs.articleDatePublishedLabel') . '</span>
				{publish_date}
			</div>
			<div class="P-Authors-Label">' . getstr('pjs.articleAuthorsLabel') . '</div>
			{authors}
			<div class="P-SE-Label">' . getstr('pjs.articleSELabel') . '</div>
			{se}
			© 2013 {AUTHORS}

			This is an open access article distributed under the terms of the Creative Commons Attribution License 3.0 (CC-BY),
			which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.
		</div>
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
					<input type="checkbox" name="active-localities" value="-2"/>' . getstr('pjs.articleLocalitiesAllLocalities') . '
				</div>				
	',
		'article.localities_list_foot' => '
				<div class="P-Localities-Menu-Row">
					<span class="P-Clear-Localities">' . getstr('pjs.articleLocalitiesClear') . '</span>
				</div>
			</div>
		</div>
		<script>PlaceLocalitiesMenuEvents();</script>
	',
		'article.localities_list_start' => '
				<div class="P-Localities-Menu-Row">
					<input type="checkbox" name="active-localities" value="-1"/>' . getstr('pjs.articleLocalitiesAllTaxa') . '
				</div>
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
	
	'article.localities_nolocalities' => '
		<div class="P-Article-Structures">
			<div class="P-Article-StructureHead">Localities</div>			
			<div class="P-Localities-Menu">
				<div class="P-Localities-Menu-Row">
					<span class="P-Clear-Localities">' . getstr('pjs.articleNoLocalities') . '</span>
				</div>
			</div>
		</div>
	',
)	
;

?>