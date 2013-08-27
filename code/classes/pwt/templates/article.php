<?php
$gTemplArr = array (
	// Authors
	'article.single_author_preview_row' => '
		<div class="AOF-Single-Author-Holder">
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a class="AOF-Author-Email" target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a><span class="AOF-Author-Corr">{is_corresponding}</span></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by this author in:&nbsp;&nbsp;
					<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>	
				</div>
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
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a class="AOF-Author-Email" target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a><span class="AOF-Author-Corr">{is_corresponding}</span></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by this author in:&nbsp;&nbsp; 
					<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>	
				</div>
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
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by the editor in:&nbsp;&nbsp;
					<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>	
				</div>
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
			<div class="P-Date-holder">
				<span class="P-Date-Label">' . getstr('pjs.articleDateReceivedLabel') . '</span>
				<span class="P-Date"> {create_date}</span> | 
				<span class="P-Date-Label">' . getstr('pjs.articleDateApprovedLabel') . '</span>
				<span class="P-Date"> {approve_date}</span> | 
				<span class="P-Date-Label">' . getstr('pjs.articleDatePublishedLabel') . '</span>
				<span class="P-Date"> {publish_date}</span>
			</div>
			
			<div class="P-Authors-Label">' . getstr('pjs.articleAuthorsLabel') . '{_plural(records)}: </div>
			{authors}
			<div class="P-SE-Label">' . getstr('pjs.articleSELabel') . ': </div>
			{se}
			
			
			<div class="copyrights">
			© 2013. This is an open access article distributed under the terms of the <a border="0" target="_blank" href="http://creativecommons.org/licenses/by/3.0/" rel="license">Creative Commons Attribution 3.0 (CC-BY)</a>,
			which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.
			</div>
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
					<input type="checkbox" name="active-localities" id="all" value="-2" /><label for="all">All</label> 
				</div>
	',
		'article.localities_list_foot' => '
				<div class="P-Localities-Menu-Row-Clear">
					<span class="P-Clear-Localities"> ' . getstr('pjs.articleLocalitiesClear') . '</span>
				</div>
			</div>
		</div>
		<script>PlaceLocalitiesMenuEvents();</script>
	',
		'article.localities_list_start' => '
				<div class="P-Localities-Menu-Row">
					<input type="checkbox" name="active-localities" id="alltaxa" value="-1"/><label for="alltaxa"> ' . getstr('pjs.articleLocalitiesAllTaxa') . '</label>
				</div>
	',
		'article.localities_list_end' => '
	
	',
		'article.localities_list_nodata' => '
	
	',
		'article.localities_list_row' => '
				<div class="P-Localities-Menu-Row-taxa">
					<input type="checkbox" name="active-localities" value="{id}" id="xy{id}"/><label for="xy{id}"> {display_name}</label>
				</div>
	',
	
	'article.localities_nolocalities' => '
		<div class="P-Article-Structures">
			<div class="P-Article-StructureHead">Localities</div>			
			<div class="P-Localities-Menu">
				<div class="P-Localities-Menu-Row">
					<span class="P-Clear-Localities"> ' . getstr('pjs.articleNoLocalities') . '</span>
				</div>
			</div>
		</div>
	',
)	
;

?>