<?php

// @formatter:off
$gTemplArr = array(
		'articles.header' => '
			<div class="documentHeader">
				<div class="P-Header">
					<div class="P-Logo-Search-Holder">
						<div class="P-Logo"><a href="/"><img src="/i/logo.jpg" alt="pjs logo" /></a></div>						
					</div>
					<div class="P-Head-Profile-Menu">
						<div class="userloggedmenu">
							<a class="userloggedimageA" href="/">
								{_showProfilePic(previewpicid)}
							</a>
							<div class="userloggedimage">
								<div class="username_left"></div>
								<div class="username">{fullname}</div>
								<div class="username_right"></div>
							</div>
							<div id="userLoggedMenu" class="userloggedmenulinks">
								<div class="userloggedmenulinksTop"></div>
								<div class="userloggedmenulinksMainInner">
									<div class="userLoggedMenuLink">
										<a id="userLoggedMenuLink_1" href="#" onclick="LayerProfEditFrm(\'P-Registration-Content\', 2, 1);">Account Settings</a>&nbsp;Login, Password, ...
									</div>
									<div class="userloggedmenuSep"></div>
									<div class="userLoggedMenuLink">
										<a id="userLoggedMenuLink_2" href="#" onclick="LayerProfEditFrm(\'P-Registration-Content\', 2, 2);">Profile</a>&nbsp;Name, Pictures, URL, ...
									</div>
									<div class="userloggedmenuSep"></div>
									<div class="userLoggedMenuLink">
										<a id="userLoggedMenuLink_3" href="#" onclick="LayerProfEditFrm(\'P-Registration-Content\', 2, 3);">Subscription</a>&nbsp;Name, Pictures, URL, ...
									</div>
									<div class="userloggedmenuSep"></div>
									<div class="userLoggedMenuLink">
										<a id="userLoggedMenuLink_4" href="/inbox.php">Messages</a>&nbsp;Inbox, Messages, ...
										<div class="P-Clear"></div>
									</div>
									<div class="userloggedmenuSep"></div>
									<div class="userLoggedMenuLink">
										<a id="userLoggedMenuLink_5" href="/login.php?logout=1">Logout</a>
										<div class="P-Clear"></div>
									</div>
									<div class="P-Clear"></div>
									<div class="userloggedmenuSep h15"></div>
									<div class="P-Clear"></div>
								</div>
								<div class="userloggedmenulinksBottom"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		',
		'articles.contents' => '
			{*articles.header}
			<script type="text/javascript">SetArticleId({id});</script>
			<div class="Main-Content">
				<div id="article-head">
					<img alt="" src="/i/bdj-eye.png" id="bdj-eye" />
					<div id="article-id">Biodiversity Data Journal 1:e957 (02.09.2013)<br />
						 doi: 10.3975/BDJ.1:e957
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
					<span class="P-Metrics-Value">1161</span>
				</div>
			</div>
		',
		
		'articles.share' => '
			<div class="P-Aritlce-Share">
				<div class="P-Article-Share-Row">
					<div class="P-Article-Share-Row-Icon">
						<img src="i/fb.png" />
						<span class="P-Article-Share-Row-Icon">
							Share with Facebook
						</span>
					</div>
				</div>
				<div class="P-Article-Share-Row">
					<div class="P-Article-Share-Row-Icon">
						<img src="i/tw.png" />
						<span class="P-Article-Share-Row-Icon">
							Share with Twitter
						</span>
					</div>
				</div>
				<div class="P-Article-Share-Row">
					<div class="P-Article-Share-Row-Icon">
						<img src="i/gplus.png" />
						<span class="P-Article-Share-Row-Icon">
							Share with Google+
						</span>
					</div>
				</div>
			</div>
		',
		
);

?>