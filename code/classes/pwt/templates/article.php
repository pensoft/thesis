<?php
$gTemplArr = array (
	// Authors
	'article.single_author_preview_row' => '
		<div class="AOF-Single-Author-Holder">
			<div class="AOF-Author-Pic">{_showPicIfExistsAOF(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a class="AOF-Author-Email" target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by this author in:&nbsp;
					<span class="AOF-Author-more-link"><a target="_blank" href="http://search.labs.crossref.org/?q={first_name}+{last_name}">CrossRef</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>	
				</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',
	
	'article.authors_preview_head' => '
		<div class="P-Authors-Label">' . getstr('pjs.articleAuthorsLabel') . '{_plural(records)}: </div>
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
		<div class="AOF-Single-Author-Preview" data-author-id="{usrid}">
			<div class="AOF-Author-Pic">{_showPicIfExistsAOF(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a class="AOF-Author-Email" target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a><span class="AOF-Author-Corr">{is_corresponding}</span></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by this author in:&nbsp; 
					<span class="AOF-Author-more-link"><a target="_blank" href="http://search.labs.crossref.org/?q={first_name}+{last_name}">CrossRef</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>	
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
			<div class="AOF-Author-Pic">{_showPicIfExistsAOF(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by the editor in:&nbsp;
					<span class="AOF-Author-more-link"><a target="_blank" href="http://search.labs.crossref.org/?q={first_name}+{last_name}">CrossRef</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>	
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
			
			
			{authors}
			<div class="P-SE-Label">' . getstr('pjs.articleSELabel') . ': </div>
			{se}
			
			
			<div class="copyrights">
			© 2013. This is an open access article distributed under the terms of the <a border="0" target="_blank" href="http://creativecommons.org/licenses/by/3.0/" rel="license">Creative Commons Attribution 3.0 (CC-BY)</a>,
			which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.
			</div>
		</div>
	',
	
	//Citation
	'article.citations_authors_preview_head' => '',
	'article.citations_authors_preview_foot' => '',
	'article.citations_authors_preview_start' => '',
	'article.citations_authors_preview_end' => '',
	'article.citations_authors_preview_nodata' => '',
	'article.citations_authors_preview_row' => '{last_name} {_GetAuthorFirstNameFirstLetter(first_name)}{_displayCitationsAuthorSeparator(records, rownum)}',
	
	'article.citation' => '
			<div class="P-Citation">
				
				<div class="P-Citation-Content">
					{author_names} ({pubyear}) {_GetArticleTitleForCitation(article_title)}
					{journal_name} {issue_number}: {start_page}.
					DOI: {doi}										
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
			<li id="i{instance_id}" >
				<div class="1" onclick="ScrollArticleToInstance({instance_id});return false;">{object_name}</div>
			</li>
	',
	
	'article.contents_list_row0' => '
			<li id="i{instance_id}" >
				<div class="2" onclick="ScrollArticleToInstance({instance_id});return false;">{display_name}</div>
			</li>
	',
	'article.contents_list_row1' => '
			<li id="i{instance_id}" >
				<div class="3" onclick="ScrollArticleToInstance({instance_id});return false;">{display_name}</div>
				<ul class="">
					{&}
				</ul>				
			</li>
	',
	
	// Localities
	'article.localities_list_head' => '
		<div class="P-Article-Structures">
			<div class="P-Localities-Map">
				<div class="P-Localities-Map-Inner" id="localitiesMap"></div>
			</div>						
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
					
			<div class="P-Localities-Menu">
				<div class="P-Localities-Menu-Row">
					<span class="P-Clear-Localities"> ' . getstr('pjs.articleNoLocalities') . '</span>
				</div>
			</div>
		</div>
	',
	
	//Taxa list
	'article.taxa_list_head' => '
	',
	'article.taxa_list_foot' => '
	',
	'article.taxa_list_start' => '				
	',
	'article.taxa_list_end' => '	
	',
	'article.taxa_list_nodata' => 'No taxa	
	',
	'article.taxa_list_row' => '
				<div class="taxalistAOF" tnu="INL">{html} {_showTaxaNameUsage(usage)}</div>
	',
	
	// Taxon previews
	// NCBI
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
				<span class="ncbiLineageRow"><a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, link)}">{scientific_name}</a></span>
	',
	'article.ncbi_lineage_nodata' => '',
	
	'article.ncbi_related_links_head' => '',
	'article.ncbi_related_links_foot' => '',
	'article.ncbi_related_links_start' => '
				<div class="extLinksHolder">
					<div class="extLinksTitle">Related links found in database</div>
	',
	'article.ncbi_related_links_end' => '
					<div class="extLinksSeeAll">To get a complete list click <a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, see_all_link)}">here</a>.</div>
				</div>
	',
	'article.ncbi_related_links_row' => '
					<div class="extLinkRow">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, link)}" title="{title}">{_CutText(title, 100)}</a>
					</div>
	',
	'article.ncbi_related_links_nodata' => '',
	//Entrez records
	'article.ncbi_entrez_records_title_head' => '',
	'article.ncbi_entrez_records_title_foot' => '',
	'article.ncbi_entrez_records_title_start' => '
				<th>Database name</th>
	',
	'article.ncbi_entrez_records_title_end' => '
	',
	'article.ncbi_entrez_records_title_row' => '
				<th class="entrezDbName">{db_display_name}</th>					
	',
	'article.ncbi_entrez_records_title_nodata' => '',
	
	'article.ncbi_entrez_records_head' => '',
	'article.ncbi_entrez_records_foot' => '',
	'article.ncbi_entrez_records_start' => '
		<div class="entrezRecordsHolder">
			<table class="entrezRecordsTable">
				<tr>
					{title}					
				</tr>		
				<tr>
					<td>Subtree links</td>
	',
	'article.ncbi_entrez_records_end' => '
				</tr>
			</table>
		</div>
	',
	'article.ncbi_entrez_records_row' => '
				<td class="entrezSubtreeLink">{_ShowEntrezRecordsDbSubtreeLink(taxon_name, taxon_ncbi_id, db_name, records)}</td>
	',
	'article.ncbi_entrez_records_nodata' => '',
	
	'article.ncbi_no_data' => 'No data on this taxon',
	
	'article.ncbi' => '
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="ncbiLink">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, ncbi_link)}"><img class="noBorder" src="' . PTP_URL . '/img/ext_details/ncbi_logo.jpg"></a>
					</td>
					<td><h2 class="labelTitle">Gene Sequences</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				<div class="ncbiEntrezRecords">
					{entrez_records}
				</div>
				<div class="P-Clear"></div>
			</div>
		</div>
	',
	// GBIF
	'article.gbif' => '
		<div class="contentSection generalInfoSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg"> <a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, gbif_link, postform, postfields)}"><img class="noBorder" src="' . PTP_URL . '/img/ext_details/gbif_logo.jpg"></img></a></td>
					<td><h2 class="labelTitle">Global Biodiversity Information Facility</h2></td>
				</tr>
			</table>
			<div class="sectionBody">	
				<script type="text/javascript">
					 function resizeGbifMap(){
						 var iframe = document.getElementById("gbifIframe");
						 var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
						 var mapi;
						 if (iframeDocument) {
						  mapi = iframeDocument.getElementById("map");
						  mapi.style.width="424px";
						  mapi.style.height="236px";
						 }
					 }
				</script>
				<iframe id="gbifIframe" name="gbifIframe" scrolling="no" height="410" frameborder="0" vspace="1" hspace="1" src="' . IFRAME_PROXY_URL . '?url={_rawurlencode(map_iframe_src)}"  onload="resizeGbifMap(); correctIframeLinks(\'gbifIframe\', \'{link_prefix}\')"></iframe>
			</div>
		</div>
	',
	
	'article.gbif_no_data' => 'No data on this taxon',
	// BHL
	'article.bhl_head' => '
		<div class="contentSection generalInfoSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="4" border="0">
				<tr>
					<td class="labelImg" id="biodevLink"> 
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, bhl_link)}"><img class="noBorder" src="' . PTP_URL . '/img/ext_details/biodev_logo.jpg"></a>
					</td>
					<td><h2 class="labelTitle">Biodiversity Heritage Library</h2></td>
				</tr>
			</table>
			<div class="sectionBody">	
				<p>{_bhl_showimage(taxon_name, fullsize_img_url, thumbnail_url, nodata)}
	',
	
	'article.bhl_foot' => '
			<div class="P-Clear"></div>
			<p class="extLinksSeeAll">To get a complete list click <a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, bhl_link)}">here</a></p>
			</div>
		</div>
	',
	
	'article.bhl_titles_head' => '
	',
	
	'article.bhl_titles_foot' => '
	',
	
	'article.bhl_titles_start' => '
	',
	
	'article.bhl_titles_end' => '
	',
	
	'article.bhl_titles_row' => '
				<div class="BHLDetails"><span class="bhl_title">{title}</span>
				<br>{_displayBHLItems(items, taxon_name)}
				</div>
	',
	
	'article.bhl_titles_nodata' => '
		<div class="sectionBody">
			<p>
				It seems that this taxon name is not present in any BHL pages.
			</p>
			<br/>
		</div>
	',
	
	'article.bhl_items_head' => '
	',
	
	'article.bhl_items_foot' => '
	',
	
	'article.bhl_items_start' => '
	',
	
	'article.bhl_items_end' => '
	',
	
	'article.bhl_items_row' => '
				<span>{_bhl_showvolume(volume)}</span>{_displayBHLPages(pages, taxon_name)}
	',
	
	'article.bhl_items_nodata' => '		
	',
	
	'article.bhl_pages_head' => '
	',
	
	'article.bhl_pages_foot' => '
	',
	
	'article.bhl_pages_start' => '
	',
	
	'article.bhl_pages_end' => '
	',
	
	'article.bhl_pages_row' => '
				<span class="bhl_pageslink"><a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, url)}">{number}</a>{_bhl_writecomma(rownum, records)}</span>
	',
	
	'article.bhl_pages_nodata' => '
	',
	
	'article.bhl_not_successfully_taken' => '
		{*article.bhl_head}
		<p>It seems that this taxon name is present on a very large number of BHL pages.</p><br>
		{*article.bhl_foot}
		
	',
	
	'article.bhl' => '
		{*article.bhl_head}
		{titles}
		{*article.bhl_foot}
	',
	// Wikimedia
	'article.wikimedia_no_data' => 'No wikimedia',
	'article.wikimedia' => '
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="{icon_div_id}">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, wikimedia_link)}">
							<img class="noBorder" src="' . PTP_URL . '/img/ext_details/wikimedia_logo.jpg">
						</a>
					</td>
					<td><h2 class="labelTitle">Images from Wikimedia</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				{images}
				<div class="P-Clear"></div>
			</div>
		</div>
	',
	
	'article.wikimedia_images_head' => '
	',
	
	'article.wikimedia_images_foot' => '
	',
	
	'article.wikimedia_images_start' => '
	',
	
	'article.wikimedia_images_end' => '
	',
	
	'article.wikimedia_images_row' => '
				<div class="imageRow">
					<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, src)}"><img src="{src}" class="noBorder" alt="{name}" style="width: 134px;"></img></a>
				</div>
				
	',
	
	'article.wikimedia_images_nodata' => 'No data on this taxon',
	
	// EOL
	'article.eol_no_data' => 'No EOL images',
	'article.eol' => '
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="{icon_div_id}">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, eol_link)}">
							<img class="noBorder" src="' . PTP_URL . '/img/ext_details/eol_logo.jpg">
						</a>
					</td>
					<td><h2 class="labelTitle">Images from EOL</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				{images}
				<div class="P-Clear"></div>
			</div>
		</div>
	',
	'article.eol_images_head' => '
	',
	
	'article.eol_images_foot' => '
	',
	
	'article.eol_images_start' => '
	',
	
	'article.eol_images_end' => '
	',
	
	'article.eol_images_row' => '
				<div class="imageRow">
					<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, url)}"><img src="{url}" class="noBorder" alt="" style="width: 134px;"></img></a>
				</div>	
	',
	
	'article.eol_images_nodata' => '
	',
	
	// Categories
	'article.category_special_sites_head' => '
	',
	
	'article.category_special_sites_foot' => '
	',
	
	'article.category_special_sites_start' => '
			<div class="P-Category-Special-Sites">
	',
	
	'article.category_special_sites_end' => '
			</div>
	',
	
	'article.category_special_sites_row' => '
				{preview}	
	',
	
	'article.category_special_sites_nodata' => '
	',
	// Regular sites
	'article.category_regular_sites_head' => '
	',
	
	'article.category_regular_sites_foot' => '
	',
	
	'article.category_regular_sites_start' => '
			<div class="P-Category-Regular-Sites">
	',
	
	'article.category_regular_sites_end' => '
				<div class="P-Clear"></div>
			</div>
	',
	
	'article.category_regular_sites_row' => '
				<div class="P-Regular-Site-Info-Holder {_displayRegularSiteHasResultsClass(has_results)} {_displayRegularSiteLastRowClass(rownum, records, items_on_row)}">
					<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
						<td class="leftMenuRowImage">
							<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, taxon_link, 0, use_post_action, fields_to_post)}">
								{_showImageIfSrcExists(picsrc)}
							</a>
						</td>
						<td class="leftMenuRowLink">
							<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, taxon_link, 0, use_post_action, fields_to_post)}">
								{display_title}
							</a>
						</td>
					</table>
				</div>
	
	',
	
	'article.category_regular_sites_nodata' => '
	',
	
	'article.category' => '
			<a href="#" id="category_{category_name}"></a>
			<div class="P-Category">
				<div class="P-Category-Title">{display_name}</div>
				{special_sites}
				{regular_sites}
			</div>
	',
	// Categories menu
	'article.categories_menu_head' => '
			<div class="P-Categories-Menu">
	',
	
	'article.categories_menu_foot' => '
				<div class="P-Clear"></div>
			</div>
	',
	
	'article.categories_menu_start' => '			
	',
	
	'article.categories_menu_end' => '			
	',
	
	'article.categories_menu_row' => '
				<div class="P-Categories-Menu-Element"><a href="#" onclick="ScrollToTaxonCategory(\'{category_name}\');return false;">{display_name}</a></div>
	
	',
	
	'article.categories_menu_nodata' => '
	',
	// Categories list
	'article.categories_list_head' => '
			<div class="P-Categories-List">
	',
	
	'article.categories_list_foot' => '
			</div>
	',
	
	'article.categories_list_start' => '
	',
	
	'article.categories_list_end' => '
	',
	
	'article.categories_list_row' => '
				{preview}
	
	',
	
	'article.categories_list_nodata' => '
	',
	
	'article.taxon_preview' => '
			<div class="P-Taxon">
				<div class="ptp-menu-holder">
					<div class="P-Taxon-Name">{taxon_name}</div>
					{categories_menu}
				</div>	
				{categories_list}
			</div>
	' 
);

?>