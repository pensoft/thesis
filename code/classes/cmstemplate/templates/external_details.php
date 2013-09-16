<?php

$gTemplArr = array(
	'external_details.leftcol' => '
		<div class="leftcol">
			<a class="logoLink" href="' . TAXON_NAME_LINK . '{taxon_name}"><img class="noBorder" alt="logo" src="/img/ext_details/logo.jpg"></img></a>
			{general_menu}
			{taxonomy_menu}
			{sequences_menu}
			{images_menu}
			{literature_menu}
			<div class="leftMenuLabel">
				Disclaimer
			</div>
			<div class="leftMenu disclaimerText">
				Some of the searched sites, particularly taxon-oriented databases, do not provide either "AND" or "Exact phrase" 
				search functions, or Application Programming Interface (API). This may lead to the listing of various homonyms, e.g., the taxon profile of spruce 
				(<span class="taxonName">Picea abies</span>) will also display the chilopod species <span class="taxonName">Lithobius obesus</span> picea Matic, 1957 
				in <a href="http://chilobase.bio.unipd.it/" target="_blank">Chilobase</a> and the fly <span class="taxonName">Tachina picea</span> Walker, 1853: 293 
				in <a href="http://diptera.org" target="_blank">diptera.org</a>.			
			</div>
		</div>
	',
	
	'external_details.defaultTaxonPage' => '
		<div class="leftcol">
			<a class="logoLink" href="' . TAXON_NAME_LINK . '{taxon_name}"><img class="noBorder" alt="logo" src="/img/ext_details/logo.jpg"></img></a>
		</div>
		<div class="rightcol">
			<div class="head">
				{topmenu}
				<div class="lLeftConthead">
					 
				</div>
				<div class="unfloat"></div>
			</div>
		</div>
		<div class="unfloat"></div>
		<div class="mainContentDefault">
			<div class="indexForm">
				<form action="' . TAXON_NAME_BASE_LINK . '" method="get">
					<div class="indexSrchInput">
						<div class="leftsrchinp"></div>
						<div class="mainsrchinp">
							<input id="query_label" type="text" name="query_label" value="Type taxon name here"></input>
							<input id="query" type="text" name="query" style="display:none"></input>
						</div>
						<div class="rightsrchinp"></div>
						<div class="unfloat"></div>
					</div>
					<input type="submit" class="indexSrchsmbbut" value=""></input>
					<div class="unfloat"></div>
				</form>
				<script>
					var lLabel = document.getElementById(\'query_label\');
					var lInput = document.getElementById(\'query\');
					if( lInput && lLabel ){
						lLabel.onfocus = function(){
							lLabel.style.display = \'none\';
							lInput.style.display = \'inline\';
							lInput.focus();
						}
						
						lInput.onblur = function(){
							if( lInput.value == \'\' ){
								lInput.style.display = \'none\';
								lLabel.style.display = \'inline\';
							}
						}
					}
				</script>
			</div>
		</div>
		<div class="copyrightFooter">Copyright © 2010 Pensoft Publishers. All rights reserved!</div>
	',
	
	'external_details.leftLinksMenuStart' => '
		<div class="leftMenuLabel">
			{label}
		</div>
		<div class="leftMenu">
	',
	
	'external_details.leftLinksMenuEnd' => '
		</div>
	',
	
	'external_details.leftLinksMenuRow' => '
			<div class="leftMenuRowHolder {_showLinksMenuLastRowClass(records, rownum, lastLeftMenuRowHolder)}">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
					<td class="leftMenuRowImage"><a href="{_ParseTaxonExternalLink(taxon_name, href, 0, postform, postfields)}">{_showImageIfSrcExists(picsrc)}</a></td>
					<td class="leftMenuRowLink"><a href="{_ParseTaxonExternalLink(taxon_name, href, 0, postform, postfields)}" {_displayMenuLink(results_exist)}>{title}</a></td>
				</table>
			</div>
	',
	
	'external_details.leftLinksMenuRowAjax' => '
			<div  id="leftMenuLink_{sitename}">
				<div class="leftMenuRowHolder {_showLinksMenuLastRowClass(records, rownum, lastLeftMenuRowHolder)}">
					<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
						<td class="leftMenuRowImage"><img src="/img/loading_small.gif"></img></td>
						<td class="leftMenuRowLink"></td>
					</table>
					
					
					<script>AjaxLoad(\'{ajax_link}\', \'leftMenuLink_{sitename}\', null, RearrangeMenu)</script>				
				</div>
			</div>
	',
	
	'external_details.ajaxMenuLinkRow' => '
			<div class="leftMenuRowHolder">
				<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
					<td class="leftMenuRowImage"><a href="{_ParseTaxonExternalLink(taxon_name, href, 0, postform, postfields)}">{_showImageIfSrcExists(picsrc)}</a></td>
					<td class="leftMenuRowLink"><a href="{_ParseTaxonExternalLink(taxon_name, href, 0, postform, postfields)}" {_displayMenuLink(results_exist)}>{title}</a></td>
				</table>
			</div>
	',
	
	'external_details.ajaxRightColIconLink' => '
			<a href="{_ParseTaxonExternalLink(taxon_name, href, 0, postform, postfields)}">{_showImageIfSrcExists(picsrc)}</a>
	',
	
	'external_details.topMenu' => '
		<div class="topMenu" >
			<table width="100%" height="100%" cellpadding="0" cellspacing="0" class="topMenuTable">
				<tr>
					<td>
						<a href="' . PENSOFT_SITE_URL . 'index.php">Home</a>
					</td>
					<td>
						<a href="' . PENSOFT_SITE_URL . 'about.php"">About Pensoft</a>
					</td>
					<td>
						<a href="' . PENSOFT_SITE_URL . 'books/"">Books</a>
					</td>
					<td>
						<a href="' . PENSOFT_SITE_URL . 'e-books/"">E-Books</a>
					</td>
					<td class="activeMenuItem">
						<a href="' . PENSOFT_SITE_URL . 'journals/">Journals</a>
					</td>
					<td>
						<a href="' . PENSOFT_SITE_URL . 'journal_home_page.php?journal_id=1&page=home">ZooKeys</a>
					</td>
					<td>
						<a href="' . PENSOFT_SITE_URL . 'journal_home_page.php?journal_id=3&page=home">PhytoKeys</a>
					</td>
					<td>
						<a href="' . PENSOFT_SITE_URL . 'newsall.php">News</a>
					</td>
					<td>
						<a href="' . PENSOFT_SITE_URL . 'contact_us.php">Contact</a>
					</td>
				</tr>
			</table>
		</div><!-- End of Top Menu -->
	',

	'external_details.RightColNoData' => '
		<div id="profile_nodata">
			<div class="rightColNoData">
				<img src="/img/ext_details/profile_nodata.jpg"></img>
			</div>
		</div>
	',

	'external_details.rightcol' => '
		<div class="rightcol">
			<div class="head">
				{topmenu}
				<div class="lLeftConthead">
					<h1 class="mainTitle">
						<span id="taxonName" style="white-space:nowrap;">{taxon_name}</span>
					</h1>
				</div>
				<div class="searchfrm">
					<form action="' . TAXON_NAME_BASE_LINK . '" method="get">
						<div class="searchtxt">
							Create your own taxon profile
						</div>
						<div class="srchInput">
							<div class="leftsrchinp"></div>
							<div class="mainsrchinp">
								<input id="query" type="text" onfocus="rldContent(\'query\', \'Type taxon name here\');" onblur="rldContent2(\'query\', \'Type taxon name here\');" name="query" value="Type taxon name here"></input>
							</div>
							<div class="rightsrchinp"></div>
							<div class="unfloat"></div>
						</div>
						<input type="submit" class="srchsmbbut" value=""></input>
						<div class="unfloat"></div>
					</form>
				</div>
				<div class="unfloat"></div>
			</div>
			<div class="mainContent">
				{profile_nodata}
				{map}
				{ncbiinfo}				
				{images}
				{bhl}
			</div>			
		</div>
		<div class="unfloat"></div>
		<script>
			autoReduceText(270, 24);
		</script>
	',
	
	'external_details.mapAjax' => '
		<div id="taxonMapAjax">
			<div class="contentSection generalInfoSection">
				<div class="centerDiv sectionBody smallMarginTop">
					<img src="/img/loading_large.gif"></img>
					<script>AjaxLoad(\'{ajax_link}\', \'taxonMapAjax\', \'taxonMapAjaxTemp\', LoadMap)</script>
				</div>
				
			</div>
		</div>
		<div id="taxonMapAjaxTemp" class="tempDiv"></div>
							
	',
	
	'external_details.ncbiAjax' => '
		<div id="ncbiAjax">
			<div class="contentSection imagesSection">
				<div class="centerDiv sectionBody smallMarginTop">
					<img src="/img/loading_large.gif"></img>
				</div>
				<script>AjaxLoad(\'{ajax_link}\', \'ncbiAjax\', \'ncbiAjaxTemp\', LoadNCBI )</script>
			</div>
		</div>
		<div id="ncbiAjaxTemp" class="tempDiv"></div>
							
	',
	
	'external_details.bhlAjax' => '
		<div id="bhlAjax">
			<div class="contentSection generalInfoSection">
				<div class="centerDiv sectionBody smallMarginTop">
					<img src="/img/loading_large.gif"></img>
				</div>
				<script>AjaxLoad(\'{ajax_link}\', \'bhlAjax\', \'bhlAjaxTemp\', LoadBHL)</script>
			</div>
		</div>
		<div id="bhlAjaxTemp" class="tempDiv"></div>
							
	',
	
	'external_details.wikimediaAjax' => '
		<div id="wikimediaAjax">
			<div class="contentSection imagesSection">
				<div class="centerDiv sectionBody smallMarginTop">
					<img src="/img/loading_large.gif"></img>
				</div>
				<script>AjaxLoad(\'{ajax_link}\', \'wikimediaAjax\', \'wikimediaAjaxTemp\', LoadWikimediaImages)</script>
			</div>
		</div>
		<div id="wikimediaAjaxTemp" class="tempDiv"></div>
							
	',
	
	'external_details.morphbankAjax' => '
		<div id="morphbankAjax">
			<div class="contentSection imagesSection">
				<div class="centerDiv sectionBody smallMarginTop">
					<img src="/img/loading_large.gif"></img>
				</div>
				<script>AjaxLoad(\'{ajax_link}\', \'morphbankAjax\', \'morphbankAjaxTemp\', LoadMorphbankImages)</script>
			</div>
		</div>
		<div id="morphbankAjaxTemp" class="tempDiv"></div>
							
	',
	
	'external_details.mapHead' => '
		<div class="contentSection generalInfoSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg"> <a href="{_ParseTaxonExternalLink(taxon_name, gbif_link, postform, postfields)}"><img class="noBorder" src="/img/ext_details/gbif_logo.jpg"></img></a></td>
					<td><h2 class="labelTitle">Global Biodiversity Information Facility</h2></td>
				</tr>
			</table>
			<div class="sectionBody">				
	',
	
	'external_details.mapFoot' => '		
				<div class="gbifLink">Click <a href="{_ParseTaxonExternalLink(taxon_name, gbif_link, postform, postfields)}">here</a> to go to the GBIF search results for this taxon</div>
			</div>
		</div>
	',
	
	'external_details.mapRow' => '		
			{result}			
	',
	
	'external_details.mapNoData' => '',
	
	'external_details.linksStart' => '
		<div class="contentSection geneSequenceSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg"><img class="noBorder" src="/img/ext_details/ncbi_logo.jpg"></img></td>
					<td><h2 class="labelTitle">Gene Sequences and PubMed Links</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
	',
	
	'external_details.linksEnd' => '		
			</div>
		</div>
	',
	
	'external_details.linksRow' => '		
			{result}
	',
	
	'external_details.imagesNoData' => '
		{*external_details.wikimediaAjax}
	',
	
	'external_details.imagesStart' => '		
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="{icon_div_id}"><script>AjaxLoad(\'{icon_ajax_url}\', \'{icon_div_id}\');</script></td>
					<td><h2 class="labelTitle">{title_label}</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
	',
	
	'external_details.imagesEnd' => '	
				<div class="unfloat"></div>
			</div>
		</div>
	',
	
	'external_details.imagesRow' => '		
			<div class="imageRow">
				<a href="{_ParseTaxonExternalLink(taxon_name, url)}"><img src="{url}" class="noBorder" alt="{title}"></img></a>
			</div>
			{_putUnfloat(rownum, itemsonrow)}
	',
	
	'external_details.ncbiStart' => '		
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="ncbiLink"><script>AjaxLoad(\'{icon_ajax_url}\', \'ncbiLink\');</script></td>
					<td><h2 class="labelTitle">Gene Sequences and PubMed links </h2></td>
				</tr>
			</table>
			<div class="sectionBody">
	',
	
	'external_details.ncbiEnd' => '	
				<div class="unfloat"></div>
			</div>
		</div>
	',
	
	'external_details.ncbiRow' => '
			<div class="ncbiDetails">
				<div class="ncbiDetail"><span class="label">Inherited blast name:</span> {division}</div>
				<div class="ncbiDetail"><span class="label">Rank:</span> {rank}</div>
				{lineage_object}
			</div>
			<div class="ncbiEntrezRecords">
				{entrez_records}
			</div>
			<div class="unfloat"></div>
			<div class="pubMedLinks">{pubmed_links}</div>			
			<div class="ncbiDisclaimer">
				Disclaimer: The NCBI taxonomy database is not an authoritative source for nomenclature or classification - please consult the relevant scientific literature for the most reliable information.
			</div>

	',
	
	'external_details.ncbiLineageStart' => '
		<div class="ncbiDetail">
			<div class="label">Lineage:</div><br/>
	',
	
	'external_details.ncbiLineageRow' => '
			<span class="ncbiLineageRow"><a href="{_ParseTaxonExternalLink(taxon_name, taxon_lineage_href)}">{scientific_name}</a>{_putColumnExceptOnLastRow(rownum, records)}</span>
	',
	
	'external_details.ncbiLineageEnd' => '
		</div>
	',
	
	'external_details.entrezRecordsStart' => '		
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
	
	'external_details.entrezRecordsEnd' => '	
			</table>
		</div>
	',
	
	'external_details.entrezRecordsRow' => '		
			<tr>
				<td class="entrezDbName">{menuname}</td>
				<td class="entrezSubtreeLink">{_ShowEntrezRecordsDbSubtreeLink(taxon_name, taxon_id, dbname, count)}</td>
			</tr>

	',
	
	'external_details.entrezRecordsAjax' => '		
			<div id="entrez_records_ajax">
				<img src="/img/loading_small.gif"></img>
				<script>AjaxLoad(\'{ajax_link}\', \'entrez_records_ajax\')</script>	
			</div>

	',
	
	'external_details.extLinkMenuStart' => '
		<div class="barImages">
	',
	
	'external_details.extLinkMenuEnd' => '
		</div>
	',
	
	'external_details.extLinkMenuRow' => '
			<div class="barIcon">
				<a href="{_ParseTaxonExternalLink(taxon_name, href, 0, postform, postfields)}">{_showImageIfSrcExists(picsrc)}</a>
			</div>
	',
	'external_details.extLinkMenuRowAjax' => '
			<div class="barIcon" id="extLink_{sitename}">
				<img src="/img/loading_small.gif"></img>
				<script>AjaxLoad(\'{ajax_link}\', \'extLink_{sitename}\', null, HideExtLinkDivIfLinkDoesNotExist)</script>
			</div>
	',
	
	'external_details.ajaxExtLinkRow'  => '
			<a href="{_ParseTaxonExternalLink(taxon_name, href, 0, postform, postfields)}">{_showImageIfSrcExists(picsrc)}</a>
	',
	
	'external_details.extLinkRow' => '
		<div class="wrapper" id="wrapper">
			<div class="leftcol">
				<a class="logoLink" href="' . TAXON_NAME_BASE_LINK . '?type=1&query={taxon_name}"><img class="noBorder" alt="logo" src="/img/ext_details/logo.jpg"></img></a>
			</div>
			<div class="rightcol">
				<div class="head">
					{topmenu}
					<div class="bar">
						<h1 class="mainTitle">
							<a href="' . TAXON_NAME_BASE_LINK . '?query={taxon_name}&type=' . (int) TAXON_NAME_SEARCH_TYPE . '"><span id="taxonName" style="white-space:nowrap;">{taxon_name}</span></a>
						</h1>
						<div class="lLinks">
							<div>
								<a href="' . TAXON_NAME_BASE_LINK . '?type=1&query={taxon_name}">Back to taxon profile</a></span>
							</div>
							<div class="unfloat"></div>
						</div>
						{general_menu}
					</div>
				</div>				
			</div>
			<div class="unfloat"></div>
		</div>
		{_getLinkIframe(url, postform, postfields)}
		<script>
			autoReduceText(270, 24);
		</script>
		<script>
			$(document).ready(function(){
				resizeIframe();
		   
			});

			$(window).resize(function() {
				resizeIframe();
			});

			function resizeIframe() {
				var lIframeHeight = $(window).height() - ($("#wrapper").height());				
				$("#ext_link_iframe").height(lIframeHeight);				
				return false;
			}

		</script>
		
	',
	
	'external_details.liasIframe' => '
		<frameset rows="174,*" frameborder="0" framespacing="0">

		    <frame name="LiasNamesMenu" src="' . LIAS_TOP_FRAME_URL. '" scrolling="no" frameborder="0" marginwidth="0" marginheight="0" noresize="noresize" />
		    <frameset cols="290,*" frameborder="0" framespacing="0">
			<frame name="LiasNamesQuery" src="' . LIAS_LEFT_FRAME_URL . '" scrolling="auto" frameborder="0" marginwidth="0" marginheight="0" noresize="noresize" />
			<frame name="LiasNamesContent" src="{url}" scrolling="auto" frameborder="0" marginwidth="0" marginheight="0" noresize="noresize" />
		    </frameset>
		    <noframes>
			<h1 class="title">LIAS names</h1>
		    </noframes>
		</frameset>
	',
	
	'external_details.extLinksStart' => '
		<div class="extLinksHolder">
			<div class="extLinksTitle">Related links found in database {database_title}</div>
	',
	
	'external_details.extLinksEnd' => '
			<div class="extLinksSeeAll">To get a complete list click <a href="{_ParseTaxonExternalLink(taxon_name, see_all_link)}">here</a>.</div>
		</div>
	',
	
	'external_details.extLinksRow' => '
			<div class="extLinkRow">
				<a href="{_ParseTaxonExternalLink(taxon_name, link)}" title="{title}">{_CutText(title, 100)}</a>
			</div>
	',
	
	'external_details.extLinksAjax' => '		
			<div id="extlinks_ajax">
				<img src="/img/loading_small.gif"></img>
				<script>AjaxLoad(\'{ajax_link}\', \'extlinks_ajax\')</script>	
			</div>

	',
	
	'external_details.googleMap' => '
		<body style="margin:0px; padding:0px;" onload=\'initialize("{coordinates}")\'>
		  <div id="map_canvas" style="width:100%; height:100%"></div>
		</body>
	',
	
	
	'external_details.bhlHead' => '
		<div class="contentSection generalInfoSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="4" border="0">
				<tr>
					<td class="labelImg" id="biodevLink"> <script>AjaxLoad(\'{icon_ajax_url}\', \'biodevLink\');</script></td>
					<td><h2 class="labelTitle">Biodiversity Heritage Library</h2></td>
				</tr>
			</table>
	',
	
	'external_details.bhlStart' => '
		
			<div class="sectionBody">	
				<p>{_bhl_showimage(taxon_name, FullSizeImageUrl, ThumbnailUrl, nodata)}
				<div class="BHLDetails_head">
					<div class="BHLDetail"><span class="label">Bibliography for <span class="ncbiLineageRow"><a href="{_ParseTaxonExternalLink(taxon_name, extlink)}">{taxon_name}</a></span></span></div>
					<!--<span class="bhl_pageslink"><a href="{_ParseTaxonExternalLink(taxon_name, extlink)}">{numpages}</a></span> pages found in <span class="bhl_pageslink"><a href="{_ParseTaxonExternalLink(taxon_name, extlink)}">{numtitles}</a></span> titles-->
				</div>
			<br>
	',
	
	'external_details.bhlEnd' => '
			<div class="unfloat"></div>
			<p class="extLinksSeeAll">To get a complete list click <a  href="{_ParseTaxonExternalLink(taxon_name, extlink)}">here</a></p>
			</div>
		
	',
	
	'external_details.bhlFoot' => '
		</div>
	',
	
	'external_details.bhl_title_row' => '		
		<div class="BHLDetails"><span class="bhl_title">{title}</span>
		<br>{items_pages}
		</div>
	',
	'external_details.bhl_volume' => '
		<span>{_bhl_showvolume(volume)}</span>
	',
	'external_details.bhl_page' => '
		<span class="bhl_pageslink"><a href="{_ParseTaxonExternalLink(taxon_name, pgurl)}">{pg}</a>{_bhl_writecomma(pgcount, pgcounter)}</span>
	',
	
	'external_details.bhl_nodata_wrong_xml' => '
		<p>It seems that this taxon name is present on a very large number of BHL pages.</p><br>
	',
	
	'external_details.bhl_nodata' => '
		<div class="sectionBody">
			<p>
				It seems that this taxon name is not present in any BHL page.
			</p>
			<br/>
		</div>
	',
);
?>