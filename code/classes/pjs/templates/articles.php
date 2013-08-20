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
										<a id="userLoggedMenuLink_2" href="#" onclick="LayerProfEditFrm(\'P-Registration-Content\', 2, 3);">Subscription</a>&nbsp;Name, Pictures, URL, ...
									</div>
									<div class="userloggedmenuSep"></div>
									<div class="userLoggedMenuLink">
										<a id="userLoggedMenuLink_2" href="/inbox.php">Messages</a>&nbsp;Inbox, Messages, ...
										<div class="P-Clear"></div>
									</div>
									<div class="userloggedmenuSep"></div>
									<div class="userLoggedMenuLink">
										<a id="userLoggedMenuLink_2" href="/login.php?logout=1">Logout</a>
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
			<script>SetArticleId({id});</script>
			<div class="Main-Content">
				<div class="P-Article-Preview">
					<iframe src="/article_preview.php?id={id}" id="articleIframe"></iframe>
					<script>
						SetArticleOnLoadEvents();
					</script>
				</div>
				<div class="P-Article-Info-Bar">
					<ul class="P-Info-Menu">						
						<li class="P-Active-Menu" data-info-type="' . ARTICLE_MENU_ELEMENT_TYPE_CONTENTS . '">Contents</li>
						<li data-info-type="' . ARTICLE_MENU_ELEMENT_TYPE_FIGURES . '">Figures</li>
						<li data-info-type="' . ARTICLE_MENU_ELEMENT_TYPE_TABLES . '">Tables</li>
						<li data-info-type="' . ARTICLE_MENU_ELEMENT_TYPE_REFERENCES . '">References</li>
						<li data-info-type="' . ARTICLE_MENU_ELEMENT_TYPE_SUP_FILES . '">Supp. files</li>
						<li data-info-type="' . ARTICLE_MENU_ELEMENT_TYPE_LOCALITIES . '">Localities</li>
						<li data-info-type="' . ARTICLE_MENU_ELEMENT_TYPE_TAXON . '">Taxa</li>
					</ul>
					<div class="P-Info-Content">
				
					</div>
					<script>InitArticleMenuEvents()</script>
				</div>	
			</div> 
		',	
		
);

?>