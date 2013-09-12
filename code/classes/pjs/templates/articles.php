<?php

// @formatter:off
$gTemplArr = array(		
		
		'articles.contents' => '
			{*articles.header}
			<script type="text/javascript">SetArticleId({id});</script>
			<div class="Main-Content">
				<div id="article-head">
					<img alt="" src="/i/bdj-eye.png" id="bdj-eye" />
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
		
		'articles.metrics' => '
			<div class="P-Article-Metrics">
				<div class="P-Article-Metrics-Row">
					<span class="P-Metric-Label">Total HTML views:</span>
					<span class="P-Metrics-Value">1023</span>
				</div>
				<div class="P-Article-Metrics-Row">
					<span class="P-Metric-Label">Total PDF views:</span>
					<span class="P-Metrics-Value">121</span>
				</div>
				<div class="P-Article-Metrics-Row">
					<span class="P-Metric-Label">Total XML views:</span>
					<span class="P-Metrics-Value">37</span>
				</div>
				<div class="P-Article-Metrics-Row">
					<span class="P-Metric-Label">Total views:</span>
					<span class="P-Metrics-Value">1181</span>
				</div>
			</div>
		',
		
		'articles.share' => '
			<div class="P-Aritlce-Share">
				<div class="P-Article-Share-Row">
					<a target="_blank" href="https://www.facebook.com/BiodiversityDataJournal">
						<div class="P-Article-Share-Row-Icon">
							<img src="i/fb.png" />
							<span class="P-Article-Share-Row-Icon">
								Share with Facebook
							</span>
						</div>
					</a>	
				</div>
				<div class="P-Article-Share-Row">
					<a target="_blank" href="https://twitter.com/BioDataJournal">
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
						<a target="_blank" href="https://plus.google.com/114819936210826038991?prsrc=3">
							<img src="i/gplus.png" />
							<span class="P-Article-Share-Row-Icon">
								Share with Google+
							</span>
						</a>	
					</div>
				</div>
				<div class="P-Article-Share-Row">
					<div class="P-Article-Share-Row-Icon">
						<a target="_blank" href="http://www.mendeley.com/groups/3621351/biodiversity-data-journal/">
							<img src="i/mendeley.png" />
							<span class="P-Article-Share-Row-Icon">
								Share with Mendeley
							</span>
						</a>	
					</div>
				</div>
			</div>
		',
		
);

?>