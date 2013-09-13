<?php

// @formatter:off
$gTemplArr = array(		
		
		'articles.contents' => '
			{*articles.header}
			<script type="text/javascript">SetArticleId({id});</script>
			<div class="Main-Content">
				<div id="article-head">
					<a class="AOF-journal-logo" href="/">
						<img alt="" src="/i/bdj-eye.png" id="bdj-eye" />
					</a>	
					<div id="article-id">
					
						Biodiversity Data Journal 1: e{article_id} ({publish_date})<br />
						doi: 10.3897/BDJ.1.e{article_id}
						 
					</div>
					<a href=""><img alt="" src="/i/print-icon.png" /></a>
					<a href=""><img alt="" src="/i/xml-icon.png" /></a>
					<a href=""><img alt="" src="/i/pdf-icon.png" /></a>
					<div class="line"></div>
					<div class="P-Clear"></div>
				</div>
				<div id="article-preview">
					<iframe src="/article_preview.php?id={id}" id="articleIframe" style="height: 1600px;"></iframe>
					<script type="text/javascript">
						SetArticleOnLoadEvents();
					</script>
					<div class="P-Baloon" id="ArticleBaloon"></div>
				</div>
				<div class="P-Article-Info-Bar" id="P-Article-Info-Bar">
					<div class="P-Article-Info-header">
						<ul class="P-Info-Menu leftBar">' .
							infoMenu(array(						
								ARTICLE_MENU_ELEMENT_TYPE_CONTENTS   => 'Contents',
								ARTICLE_MENU_ELEMENT_TYPE_AUTHORS   => 'Article info',
								ARTICLE_MENU_ELEMENT_TYPE_CITATION   => 'Citation',
							)) . '
						</ul>
						<ul class="P-Info-Menu rightBar">' .
							infoMenu(array(	
								ARTICLE_MENU_ELEMENT_TYPE_RELATED  => 'Related',
								ARTICLE_MENU_ELEMENT_TYPE_METRICS  => 'Metrics',
								ARTICLE_MENU_ELEMENT_TYPE_SHARE  => 'Share',
							)) . '
						</ul>
						{_createArticleObjectMenu(object_existence)}					
					</div>
					<div class="P-Info-Content">
						{contents_list}
					</div>
					<script type="text/javascript">InitArticleMenuEvents()</script>
				</div>	
			</div> 
			<div class="P-Article-References-For-Baloon">
			</div>								
						
		',	
		
		'articles.related' => '
			<div class="P-Related-Articles">
				<div class="P-Related-Articles-Row">
					<a href="#">Article 1</a>
				</div>
				<div class="P-Related-Articles-Row">
					<a href="#">Article 2</a>
				</div>
				<div class="P-Related-Articles-Row">
					<a href="#">Article 3</a>
				</div>
				<div class="P-Related-Articles-Row">
					<a href="#">Article 4</a>
				</div>
			</div>
		',
		
		'articles.article_metrics' => '
			<div class="P-Metrics">
				<div class="P-Metrics-Label">Article views</div>
				<table class="P-Metric-Table" cellspacing="0" cellpadding="0">
					<tr class="P-Metrics-Headers-Row">
						<th>
							Version
						</th>
						<th>
							Unique
						</th>
						<th>
							All
						</th>
					</tr>
					<tr class="P-Metrics-Row">
						<td style="text-align: center">
							HTML
						</td>
						<td>
							{html_unique_views_cnt}
						</td>
						<td>
							{html_views_cnt}
						</td>
					</tr>
					<tr class="P-Metrics-Row">
						<td style="text-align: center">
							PDF
						</td>
						<td>
							{pdf_unique_views_cnt}
						</td>
						<td>
							{pdf_views_cnt}
						</td>
					</tr>
					<tr class="P-Metrics-Row">
						<td style="text-align: center">
							XML
						</td>
						<td>
							{xml_unique_views_cnt}
						</td>
						<td>
							{xml_views_cnt}
						</td>
					</tr>
					<tr class="P-Metrics-Row">
						<td style="text-align: center">
							Total
						</td>
						<td>
							{total_unique_views_cnt}
						</td>
						<td>
							<b>{total_views_cnt}</b>
						</td>
					</tr>
				</table>				
			</div>
		',
		
		'articles.metrics' => '
			{*articles.article_metrics}	
			{figures_metrics}
			{tables_metrics}
			{suppl_files_metrics}
		',
		
		'article.figures_metrics_head' => '',
		'article.figures_metrics_foot' => '',
		'article.figures_metrics_start' => '
			<div class="P-Metrics">
				<div class="P-Metrics-Label">Figures</div>
				<table class="P-Metric-Table" cellspacing="0" cellpadding="0">
					<tr class="P-Metrics-Headers-Row">
						<th>
							Item
						</th>
						<th>
							Views
						</th>
						<th>
							Downloads
						</th>
					</tr>		
		',
		'article.figures_metrics_end' => '
				</table>				
			</div>		
		',
		'article.figures_metrics_row' => '
					<tr class="P-Metrics-Row">
						<td>
							<span class="fig" rid="{instance_id}">{display_label}</span>
						</td>
						<td>
							{view_cnt}({view_unique_cnt})
						</td>
						<td>
							{download_cnt}({download_unique_cnt})
						</td>
					</tr>		
		',
		'article.figures_metrics_nodata' => '',
		
		'article.tables_metrics_head' => '',
		'article.tables_metrics_foot' => '',
		'article.tables_metrics_start' => '
			<div class="P-Metrics">
				<div class="P-Metrics-Label">Tables</div>
				<table class="P-Metric-Table" cellspacing="0" cellpadding="0">
					<tr class="P-Metrics-Headers-Row">
						<th>
							Item
						</th>
						<th>
							Views
						</th>
						<th>
							Downloads
						</th>
					</tr>				
		',
		'article.tables_metrics_end' => '{*article.figures_metrics_end}',
		'article.tables_metrics_row' => '
					<tr class="P-Metrics-Row">
						<td>
							<span class="table" rid="{instance_id}">{display_label}</span>
						</td>
						<td>
							{view_cnt}({view_unique_cnt})
						</td>
						<td>
							{download_cnt}({download_unique_cnt})
						</td>
					</tr>			
		',
		'article.tables_metrics_nodata' => '',
		
		'article.suppl_files_metrics_head' => '',
		'article.suppl_files_metrics_foot' => '',
		'article.suppl_files_metrics_start' => '
			<div class="P-Metrics">
				<div class="P-Metrics-Label">Supplementary files</div>
				<table class="P-Metric-Table" cellspacing="0" cellpadding="0">
					<tr class="P-Metrics-Headers-Row">
						<th>
							Item
						</th>
						<th>
							Views
						</th>
						<th>
							Downloads
						</th>
					</tr>			
		',
		'article.suppl_files_metrics_end' => '{*article.figures_metrics_end}',
		'article.suppl_files_metrics_row' => '
					<tr class="P-Metrics-Row">
						<td>
							<span class="suppl" rid="{instance_id}">{display_label}</span>
						</td>
						<td>
							{view_cnt}({view_unique_cnt})
						</td>
						<td>
							{download_cnt}({download_unique_cnt})
						</td>
					</tr>			
		',
		'article.suppl_files_metrics_nodata' => '',
		
		'articles.share' => '
			<div class="P-Aritlce-Share">
				<div class="P-Article-Share-Row">
					<a target="_blank" {_generateFBLink(article_id)}>
						<div class="P-Article-Share-Row-Icon">
							<img src="i/fb.png" />
							<span class="P-Article-Share-Row-Icon">
								Share with Facebook
							</span>
						</div>
					</a>	
				</div>
				<div class="P-Article-Share-Row">
					<a target="_blank" {_generateTwitterLink(article_id)}>
						<div class="P-Article-Share-Row-Icon">
							<img src="i/tw.png" />
							<span class="P-Article-Share-Row-Icon">
								Share with Twitter
							</span>
						</div>
					</a>	
				</div>
				<div class="P-Article-Share-Row">
					<div class="P-Article-Share-Row-Icon">
						<a target="_blank" {_generateGPlusLink(article_id)}>
							<img src="i/gplus.png" />
							<span class="P-Article-Share-Row-Icon">
								Share with Google+
							</span>
						</a>	
					</div>
				</div>
				<div class="P-Article-Share-Row">
					<div class="P-Article-Share-Row-Icon">
						<a target="_blank" {_generateMendeleyLink(article_id)}>
							<img src="i/mendeley.png" />
							<span class="P-Article-Share-Row-Icon">
								Share with Mendeley
							</span>
						</a>	
					</div>
				</div>
				<div class="P-Article-Share-Row">
					<div class="P-Article-Share-Row-Icon">
						<a {_generateEmailLink(article_id, document_name, journal_name, journal_short_name, doi, authors, publish_date)}>
							<img src="i/icon_email.gif" />
							<span class="P-Article-Share-Row-Icon">
								Notify a colleague
							</span>
						</a>	
					</div>
				</div>
			</div>
		',
		
		'articles.error_row' => '<div class="P-Article-Error">
					<div class="P-Error-Message">{err_msg}</div>
				</div>
		
		',
		
);

?>