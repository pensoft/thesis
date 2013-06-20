<?php

$gTemplArr = array(
	'article_html.taxonMenu' => '{general_menu}',
	'article_html.taxonLinksMenuStart' => '
		<div class="taxonMenu">
			<div class="taxonMenuLabel">
				<a target="_blank" href="' . TAXON_NAME_LINK . '{taxon_name}">{taxon_name}</a>
			</div>			
			<div class="taxonMenuRow">
				<div class="siteName"><a target="_blank" href="' . TAXON_NAME_LINK . '{taxon_name}">Pensoft Taxon Profile</a></div>				
			</div>
	',
	'article_html.taxonLinksMenuRow' => '
			<div class="taxonMenuRow">
				<div class="siteName"><a href="{_ParseTaxonExternalLink(taxon_name, href)}">{title}</a></div>				
			</div>
	',
	'article_html.taxonLinksMenuRowAjax' => '
			<div class="taxonMenuRow">
				<div class="siteName"><a href="{_ParseTaxonExternalLink(taxon_name, href)}">{title}</a></div>				
			</div>
	',
	'article_html.taxonLinksMenuAjax' => '
		<div id="article_link_{sitename}">
			<script>AjaxLoad(\'{ajax_link}\', \'article_link_{sitename}\')</script>
		</div>
	',
	'article_html.taxonLinksMenuEnd' => '
			<div class="taxonMenuRow noBackground">
				<div class="siteName"><a target="_blank" href="' . TAXON_NAME_LINK . '{taxon_name}">More</a></div>
			</div>
		</div>
	',
	
	'article_html.taxonBaloonDiv' => '
		<div id="taxon_{_parseTaxonNameForBaloon(taxon_name)}_baloon_div"> 
			{*article_html.taxonLinksMenuStart}
				<div class="taxonMenuRow">
					<div class="siteName"><a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, gbif_href)}">{gbif_title}</a></div>				
				</div>
				<div class="taxonMenuRow">
					<div class="siteName"><a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, ncbi_href)}">{ncbi_title}</a></div>				
				</div>
				<div class="taxonMenuRow">
					<div class="siteName"><a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, eol_href)}">{eol_title}</a></div>				
				</div>
				<div class="taxonMenuRow">
					<div class="siteName"><a target="_blank" target="_blank" href="{_ParseTaxonExternalLink(taxon_name, biodev_href)}">{biodev_title}</a></div>				
				</div>
				<div class="taxonMenuRow">
					<div class="siteName"><a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, wikipedia_href)}">{wikipedia_title}</a></div>				
				</div>
			{*article_html.taxonLinksMenuEnd}
		</div>
	',


);
?>