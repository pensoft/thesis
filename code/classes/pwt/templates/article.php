<?php
$gTemplArr = array (
	// Authors
	'article.single_author_preview_row' => '
		<div class="AOF-Single-Author-Preview">
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, authors)}</div>
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
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, authors)}</div>
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
			<div class="AOF-Author-Pic">{_showPicIfExists(photo_id, authors)}</div>
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
			
			
			<div class="copyrights">
			© 2013. This is an open access article distributed under the terms of the Creative Commons Attribution License 3.0 (CC-BY),
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
	
	//Taxon previews
	//NCBI
	'article.ncbi_lineage_head' => '',
	'article.ncbi_lineage_foot' => '',
	'article.ncbi_lineage_start' => '
			<div class="ncbiDetail">
				<div class="label">Lineage:</div><br/>
	',
	'article.ncbi_lineage_end' => '
			</div>
	',
	'article.ncbi_lineage_row' => '
				<span class="ncbiLineageRow"><a href="{_ParseTaxonExternalLink(taxon_name, link)}">{scientific_name}</a></span>
	',
	'article.ncbi_lineage_nodata' => '',
	
	'article.ncbi_related_links_head' => '',
	'article.ncbi_related_links_foot' => '',
	'article.ncbi_related_links_start' => '
				<div class="extLinksHolder">
					<div class="extLinksTitle">Related links found in database</div>
	',
	'article.ncbi_related_links_end' => '
					<div class="extLinksSeeAll">To get a complete list click <a href="{_ParseTaxonExternalLink(taxon_name, see_all_link)}">here</a>.</div>
				</div>
	',
	'article.ncbi_related_links_row' => '
					<div class="extLinkRow">
						<a href="{_ParseTaxonExternalLink(taxon_name, link)}" title="{title}">{_CutText(title, 100)}</a>
					</div>
	',
	'article.ncbi_related_links_nodata' => '',
	
	'article.ncbi_entrez_records_head' => '',
	'article.ncbi_entrez_records_foot' => '',
	'article.ncbi_entrez_records_start' => '
		<div class="entrezRecordsHolder">
			<table class="entrezRecordsTable">
				<tr>
					<th colspan="2">Entrez records</th>
				</tr>
				<tr>
					<th>Database name</th>
					<th>Subtree links</th>
				</tr>		
	',
	'article.ncbi_entrez_records_end' => '
			</table>
		</div>
	',
	'article.ncbi_entrez_records_row' => '
				<tr>
					<td class="entrezDbName">{db_display_name}</td>
					<td class="entrezSubtreeLink">{_ShowEntrezRecordsDbSubtreeLink(taxon_name, taxon_ncbi_id, db_name, records)}</td>
				</tr>
	',
	'article.ncbi_entrez_records_nodata' => '',
	
	'article.ncbi' => '
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="ncbiLink">
						<a href="{_ParseTaxonExternalLink(taxon_name, ncbi_link)}"><img class="noBorder" src="' . PTP_URL . '/img/ext_details/ncbi_logo.jpg"></a>
					</td>
					<td><h2 class="labelTitle">Gene Sequences and PubMed links </h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				<div class="ncbiDetails">
					<div class="ncbiDetail"><span class="label">Inherited blast name:</span> {division}</div>
					<div class="ncbiDetail"><span class="label">Rank:</span> {rank}</div>
					{lineage}
				</div>
				<div class="ncbiEntrezRecords">
					{entrez_records}
				</div>
				<div class="unfloat"></div>
				<div class="pubMedLinks">{related_links}</div>			
				<div class="ncbiDisclaimer">
					Disclaimer: The NCBI taxonomy database is not an authoritative source for nomenclature or classification - please consult the relevant scientific literature for the most reliable information.
				</div>
				<div class="unfloat"></div>
			</div>
		</div>
	',
)	
;

?>