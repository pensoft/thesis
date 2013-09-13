<?php
// @formatter->off
$gTemplArr = array(
	'global.empty' => '',

	'global.htmlonlyheader' =>
'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	{*global.metadata}
	<meta name="author" content="Etaligent.NET"/>
	<meta name="distribution" content="global"/>
	<meta name="robots" content="index, follow, all"/>
	<link rel="SHORTCUT ICON" href="/favicon.ico" />	
	{CSS}{JS}
	{share_metadata}
</head>
<body>',

	'global.htmlonlyheader_version' => '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<title>{pagetitle}</title>
			<meta name="description" content="{description}" />
			<meta name="keywords" content="{keywords}" />
			<meta name="author" content="Etaligent.NET"/>
			<meta name="distribution" content="global"/>
			<meta name="robots" content="index, follow, all"/>

			<link rel="SHORTCUT ICON" href="/favicon.ico" />
			<link type="text/css" rel="stylesheet" href="/lib/def.css?v={_getCommit()}" media="all" title="default" />
			<link type="text/css" rel="stylesheet" href="/lib/version_preview.css?v={_getCommit()}" media="all" title="default" />
			<link type="text/css" rel="stylesheet" href="/lib/ui.dynatree.css?v={_getCommit()}" media="all" title="default" />
			<!--link type="text/css" rel="stylesheet" href="' . PWT_URL . '/lib/css/article_preview.css?v={_getCommit()}" media="all" title="default"/-->
			<link type="text/css" rel="stylesheet" href="' . PWT_URL . '/lib/css/article_preview2.css?v={_getCommit()}" media="all" title="default"/>
			<link type="text/css" rel="stylesheet" href="/lib/editor.css?v={_getCommit()}" media="all" />
			<link type="text/css" rel="stylesheet" href="' . PWT_URL . '/lib/css/comments.css?v={_getCommit()}" media="all" />
			<style type="text/css">
				{_IncludeVersionCss(users_with_changes)}
			</style>

			<script src="/lib/js/jquery.js?v={_getCommit()}" type="text/javascript"></script>
			<script src="/lib/js/jquery_form.js?v={_getCommit()}" type="text/javascript"></script>
			<script src="/lib/js/jquery.tokeninput.js?v={_getCommit()}" type="text/javascript" ></script>
			<script src="/lib/js/jquery.dragsort.js?v={_getCommit()}" type="text/javascript" ></script>
			<script src="' . PWT_URL . '/lib/js/jquery.ba-resize.js?v={_getCommit()}" type="text/javascript"></script>

			<script src="/lib/js/ajaxupload.3.5.js?v={_getCommit()}" type="text/javascript" ></script>
			<!-- CKEditor BEGIN -->
			<script type="text/javascript" src="/lib/ckeditor/ckeditor.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/ckeditor/adapters/jquery.js?v={_getCommit()}"></script>
			<script src="/lib/js/def.js?v={_getCommit()}" type="text/javascript"></script>
					
					
			<script src="' . PWT_URL . '/lib/js/comments_common.js?v={_getCommit()}" type="text/javascript"></script>
			<script src="/lib/js/comments.js?v={_getCommit()}" type="text/javascript"></script>
			<script src="' . PWT_URL . '/lib/js/changes_common.js?v={_getCommit()}" type="text/javascript"></script>
			
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-core.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-cssclassapplier.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-selectionsaverestore.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-serializer.js?v={_getCommit()}"></script>	

			
					
			{share_metadata}
			<script type="text/javascript">
				$(document).ready(function(){
					setCommentsWrapEvents();
				});
				$(window).load(function(){
					gWindowIsLoaded = true;
				});
			</script>
		</head>
	<body>
	',
	
	'global.htmlonlyheader_preview' => '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<title>{pagetitle}</title>
			<meta name="description" content="{description}" />
			<meta name="keywords" content="{keywords}" />
			<meta name="author" content="Etaligent.NET"/>
			<meta name="distribution" content="global"/>
			<meta name="robots" content="index, follow, all"/>
	
			<link rel="SHORTCUT ICON" href="/favicon.ico" />
			<link type="text/css" rel="stylesheet" href="/lib/version_preview.css?v={_getCommit()}" media="all" title="default" />
			<link type="text/css" rel="stylesheet" href="/lib/ui.dynatree.css?v={_getCommit()}" media="all" title="default" />
			<!--link type="text/css" rel="stylesheet" href="' . PWT_URL . '/lib/css/article_preview.css?v={_getCommit()}" media="all" title="default"/-->
			<link type="text/css" rel="stylesheet" href="' . PWT_URL . '/lib/css/article_preview2.css?v={_getCommit()}" media="all" title="default"/>
			<link type="text/css" rel="stylesheet" href="/lib/editor.css?v={_getCommit()}" media="all" />
			<style type="text/css">
				{_IncludeVersionCss(users_with_changes)}
			</style>
				
	
			<script src="/lib/js/jquery.js?v={_getCommit()}" type="text/javascript"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-core.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-cssclassapplier.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-selectionsaverestore.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-serializer.js?v={_getCommit()}"></script>		
					
			
			
	
			
			
			<script type="text/javascript" src="/lib/js/ice/ice.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/dom.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/icePlugin.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/icePluginManager.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/bookmark.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/selection.js?v={_getCommit()}"></script>
				
			
			<script type="text/javascript" src="/lib/js/ice/plugins/IceAddTitlePlugin/IceAddTitlePlugin.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/plugins/IceCopyPastePlugin/IceCopyPastePlugin.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/plugins/IceEmdashPlugin/IceEmdashPlugin.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/plugins/IceSmartQuotesPlugin/IceSmartQuotesPlugin.js?v={_getCommit()}"></script>
			<script src="/lib/js/version_preview.js?v={_getCommit()}" type="text/javascript"></script>
			
			{share_metadata}
			<script type="text/javascript">				
				$(window).load(function(){
					gWindowIsLoaded = true;
					
				});
			</script>
		</head>
	<body>
	',
	
	'global.htmlonlyheader_article' => '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
			<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
			<title>{pagetitle}</title>
			<meta name="description" content="{description}" />
			<meta name="keywords" content="{keywords}" />
			<meta name="author" content="Etaligent.NET"/>
			<meta name="distribution" content="global"/>
			<meta name="robots" content="index, follow, all"/>
	
			<link rel="SHORTCUT ICON" href="/favicon.ico" />
			<link type="text/css" rel="stylesheet" href="/lib/def.css?v={_getCommit()}" media="all" title="default" />
			<link type="text/css" rel="stylesheet" href="/lib/version_preview.css?v={_getCommit()}" media="all" title="default" />
			<link type="text/css" rel="stylesheet" href="/lib/ui.dynatree.css?v={_getCommit()}" media="all" title="default" />
			<link type="text/css" rel="stylesheet" href="/lib/article.css?v={_getCommit()}" media="all" title="default" />		
	
			<script src="/lib/js/jquery.js?v={_getCommit()}" type="text/javascript"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-core.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-cssclassapplier.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-selectionsaverestore.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-serializer.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/def.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="/lib/js/article.js?v={_getCommit()}"></script>
			<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>			
			{share_metadata}
			<script type="text/javascript">
				$(window).load(function(){
					gWindowIsLoaded = true;
			
				});
			</script>
		</head>
		<body>
	',
	
	'global.htmlonlyheader_article_preview' => '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="bg" lang="bg">
			<head>
				<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
				<title>{pagetitle}</title>
				<meta name="description" content="{description}" />
				<meta name="keywords" content="{keywords}" />
				<meta name="author" content="Etaligent.NET"/>
				<meta name="distribution" content="global"/>
				<meta name="robots" content="index, follow, all"/>
		
				<link rel="SHORTCUT ICON" href="/favicon.ico" />
				<link type="text/css" rel="stylesheet" href="/lib/version_preview.css?v={_getCommit()}" media="all" title="default" />
				<link type="text/css" rel="stylesheet" href="/lib/ui.dynatree.css?v={_getCommit()}" media="all" title="default" />
				<!--link type="text/css" rel="stylesheet" href="' . PWT_URL . '/lib/css/article_preview.css?v={_getCommit()}" media="all" title="default"/-->
				<!--link type="text/css" rel="stylesheet" href="' . PWT_URL . '/lib/css/article_preview2.css?v={_getCommit()}" media="all" title="default"/-->
				<link type="text/css" rel="stylesheet" href="/lib/editor.css?v={_getCommit()}" media="all" />
				<link type="text/css" rel="stylesheet" href="' . PWT_URL . '/lib/css/articleAOF.css?v={_getCommit()}" media="all" title="default" />	
				
				<script src="/lib/js/jquery.js?v={_getCommit()}" type="text/javascript"></script>
				<script src="/lib/js/article2.js?v={_getCommit()}" type="text/javascript"></script>
				<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-core.js?v={_getCommit()}"></script>
				<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-cssclassapplier.js?v={_getCommit()}"></script>
				<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-selectionsaverestore.js?v={_getCommit()}"></script>
				<script type="text/javascript" src="/lib/js/ice/lib/rangy-1.2/rangy-serializer.js?v={_getCommit()}"></script>		
				{share_metadata}	
				<script type="text/javascript">				
					$(window).load(function(){
						gWindowIsLoaded = true;
						
					});
				</script>
			</head>
			<body>
	',

	'global.htmlonlyfooter' => '
	{_phpGetLoggedErrors()}
	
</body>
</html>',

	'global.metadata' =>
	'<title>Biodiversity Data Journal</title>
	<meta name="description" content="" />
	<meta name="keywords" content="" />',


	'global.profile_pic_and_name' => '
						<a href="/login.php?logout=1" class="logoutbtn" title="Logout"></a>
						<a href="/profile.php" target="_blank" id="profile">
							{_getProfilePicSmall(previewpicid)}
							<span>{fullname}</span>
							<!-- <img src="/i/Barrow.png" alt="buttom arrow" /> -->
						</a>
	',

	'global.login_register' => '
						<div class="loginOrRegister">
							<a href="javascript: void(0);" onclick="LayerRegFrm(\'P-Registration-Content\', 1);">' . getstr('pjs.register') . '</a> |
							<a href="/login.php">' . getstr('pjs.login') . '</a>
						</div>
	',

	'global.article_search_form_templ' => '
		<div id="searchField">
			<img src="i/leftSearch.png" style="float: left;" alt="search Field" />
			<div class="searchWrapper">
				{stext}
			</div>
			<img src="i/rightSearch.png" style="float: left;" alt="search Field" />
			{search}
			<!--<input type="submit" name="submit" value="" />-->
			<div class="P-Clear"></div>
			<table class="article_search_radio_holder">
				<colgroup>
					<col width="33%"></col>
					<col width="33%"></col>
					<col width="34%"></col>
				</colgroup>
				{search_in}
			</table>
		</div>
	', 

/*
									<img src="i/leftSearch.png" style="float: left;" alt="search Field" />
									<div class="searchWrapper">
										<input type="text" name="search" value="Search ..." />
									</div>
									<img src="i/rightSearch.png" style="float: left;" alt="search Field" />
									<input type="submit" name="submit" value="" />
									<div class="P-Clear"></div>
									<p>
										<input type="radio" name="filter" value="All" />All
										<input type="radio" name="filter" value="Author" />Author
										<input type="radio" name="filter" value="Title" />Title
										<span>
											<a href="#">Advanced search</a>
										</span>
									</p>
								</div>
*/
	'global.journal_header' => '
					<div class="header-content">
						<div class="constrained">
							<div id="bioRiskLogo">
								<a href="/">
									<img src="/i/BDJLogo.jpg" alt="BDJ" width="402" height="104" />
								</a>
							</div>
							{article_search}
							<div class="wrapper">
								<div id="button">
									<button class="button_green" onclick=\'window.open("' . PWT_URL . '", "_blank");\'>Start a manuscript</button>
									{_getYourTasksBtn(show_your_tasks, journal_id)}
									<a href="/about#Howitworks">How it works</a>
								</div>

								{journal_menu}

							</div>'
							 /*<form method="post" action="">
								<div id="searchField">
									<img src="/i/leftSearch.png" style="float: left;" alt="search Field" />
									<div class="searchWrapper">
										<input type="text" name="search" value="Search ..." />
									</div>
									<img src="/i/rightSearch.png" style="float: left;" alt="search Field" />
									<input type="submit" name="submit" value="" />
									<div class="P-Clear"></div>
									<p>
										<input type="radio" name="filter" value="All" />All
										<input type="radio" name="filter" value="Author" />Author
										<input type="radio" name="filter" value="Title" />Title
										<span>
											<a href="#">Advanced search</a>
										</span>
									</p>
								</div>
							</form>*/
						. '</div>
						<div class="P-Clear"></div>
					</div>
	',
	'global.htmlstartcontent' =>
	'{*global.htmlonlyheader}
	<div id="container">
		<div id="header">
			<div id="nav">
				<div class="constrained">
					<a target="_blank" href="http://www.pensoft.net"><img src="/i/logo.jpg" alt="logo" class="logo" /></a>

					{mainmenu}
					{login_register_or_profile}

					<!--<div id="cart">
						<a href="#">
							<img src="/i/cartMenu.png" alt="cart" />
							<span>14</span>
						</a>
					</div>-->
				</div>
			</div>
			<div class="P-Clear"></div>

			{journal_header}
			<div class="P-Clear"></div>
		</div><!-- END header -->
		<div class="contentWrapper constrained" id="contentWrapper">
	',

	'global.htmlendcontent' => '
				</div><!-- END contentWrapper -->
			</div><!-- END container -->
			<div id="P-Registration-Content"></div>
		{*global.htmlonlyfooter}',

	'global.most_visited_papers' => '
					<div class="leftSiderBlock withoutHeader">
						<div class="siderBlockLinksHolder">
							<div class="mostVisited">
								<div class="P-Clear"></div>
								<a href="#">
									Most visited papers
								</a>
							</div>
						</div>
					</div>
	',

	'global.follow_us' => '
					<div class="leftSiderBlock">
						<h3>' . getstr('pjs.followus') . '</h3>
						<div class="P-Clear"></div>
						<div class="siderBlockLinksHolder">
							<div id="social">
								' . //<a href="#"><img src="/i/rss.png" alt="rss"></img></a>
								'
								<a target="_blank" href="https://www.facebook.com/BiodiversityDataJournal"><img src="/i/fb.png" alt="facebook" /></a>
								<a target="_blank" href="https://twitter.com/BioDataJournal"><img src="/i/tw.png" alt="twitter" /></a>
								'. //<a href="#"><img src="/i/m.png" alt="" /></a>
								'
								<a target="_blank" href="https://plus.google.com/114819936210826038991?prsrc=3"><img src="/i/gplus.png" alt="google plus" /></a>
								<a target="_blank" href="http://www.mendeley.com/groups/3621351/biodiversity-data-journal/"><img src="/i/mendeley.png" alt="mendeley" /></a>
								{_showRSSLink()}
								<div class="P-Clear"></div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
	',

	'global.left_col_browse' => '
					<div class="leftSiderBlock">
						<h3>' . getstr('pjs.browse') . '</h3>
						<div class="siderBlockLinksHolder">
							<a class="link" href="/browse_journal_articles.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.articles') . '</span>
							</a>
							<a class="link" href="/browse_journal_issues.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.issues') . '</span>
							</a>
							<a class="link" href="/browse_journal_authors.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.authors') . '</span>
							</a>
						</div>
						<div class="P-Clear"></div>
					</div>
	',

	'global.sidebar_left' => '
					<div id="leftSider" style="width: 185px;">
						{journal_features}'.
						//<div class="P-Clear"></div>
						//{*global.most_visited_papers}
						'<div class="P-Clear"></div>
						{*global.follow_us}
						'
						//<div class="P-Clear"></div>
						//{*global.left_col_browse}
						.'<div class="P-Clear"></div>
						<div id="siderFooter">
							<a href="http://www.doaj.org/"><img src="/i/openAccess.png" alt="open access" /></a>
							<div class="P-Clear"></div>
							<div>
								This work is licensed under the <a href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 (CC-BY)</a>.
							</div>
						</div>
						<div class="P-Clear"></div>
					</div>
	',

	'global.sidebar_left_journals' => '
					<div id="leftSider" style="width: 185px;">
						{journal_features}
						<div class="P-Clear"></div>
						{*global.most_visited_papers}
						<div class="P-Clear"></div>
						{*global.follow_us}
						<div class="P-Clear"></div>
						<div id="siderFooter">
							<img src="/i/openAccess.png" alt="open access"></img>
							<div class="P-Clear"></div>
							<div>
								This work is licensed under the
								<span>
									Creative Commons Attribution 3.0 (CC-BY).
								</span>
							</div>
						</div>
						<div class="P-Clear"></div>
					</div>
	',

	'global.sidebar_left_profile' => '
					<div id="leftSider" style="width: 185px;">
						<div class="leftSiderBlock">
							<h3>' . getstr('pjs.myprofile') . '</h3>
							<div class="P-Clear"></div>
							<div class="siderBlockLinksHolder">
								<a href="/editprofile.php?tAction=showedit&amp;step=1&amp;editprofile=1" class="link">
									<span class="content" style="padding-left: 13px;">' . getstr('pjs.account_settings') . '</span>
								</a>
								<a href="/editprofile.php?tAction=showedit&amp;step=2&amp;editprofile=1" class="link">
									<span class="content" style="padding-left: 13px;">' . getstr('pjs.profile') . '</span>
								</a>
								<a href="/editprofile.php?tAction=showedit&amp;step=3&amp;editprofile=1" class="link">
									<span class="content" style="padding-left: 13px;">' . getstr('pjs.subscription') . '</span>
								</a>
								<a href="/editprofile.php?tAction=showedit&amp;my_expertise=1" class="link">
									<span class="content" style="padding-left: 13px;">' . getstr('pjs.myexpertise') . '</span>
								</a>
								<div class="P-Clear"></div>
							</div>
						</div>
						<div class="P-Clear"></div>
					</div>
	',

	'global.sidebar_left_show' => '
					<div id="leftSider" style="width: 185px;">
						{stories_tree}
					</div>
	',

	'global.sidebar_left_issues' => '
					<div id="leftSider">
					<div class="leftSiderBlock">
							<h3>Issue summary</h3>
					</div>
					<div id="browse">
						<div class="whiteBackground" style="width: 374px;">
							<div class="P-Clear"></div>
							<div id="book">
								<div class="floatLeft">
									<img src="/i/book.png" alt="book"></img>
									<div class="P-Clear"></div>
								</div>
								<div id="bookInfo">
									<p>
										Papers published: <span>5</span>
									</p>
									<p>
										Total pages: <span>83</span>
									</p>
									<p>
										Color pages: <span>23</span>
									</p>
									<p>
										Printed version: <span>Paperback</span>
									</p>
									<img src="/i/openAccess.png" alt="open access" style="margin-left: 10px; margin-top: 25px;"></img>
									<div id="price">
										<div class="floatLeft">&euro; 28.00</div><img class="floatLeft" src="/i/cart.png" alt="cart"></img>
										<span class="floatLeft">Order reprint(s)</span>
										<div class="P-Clear"></div>
									</div>
								</div>
								<div class="P-Clear"></div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
						<div class="boxFooter">
							<div class="bottomLeftCorner" style="margin-top: -4px;"></div>
							<div class="bottomRightCorner" style="margin-right: 4px;"></div>
						</div>
					</div>
					<div class="P-Clear"></div>
					<div class="headlineHolder">
						<div class="left_ANG"></div>
						<div class="MID">
							<h3>View issue</h3>
						</div>
						<div class="right_ANG"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="whiteBackground" id="selectISS">
						<div id="issue">
							<div class="pagesWrapper">
								<div class="floatLeft">
									{_getPrevIssueBtn(journal_id, prev_issue_id)}
								</div>
								<div class="floatLeft">
									<div class="issueNo">Issue {issue_id}</div>
								</div>
								<div class="floatLeft">
									{_getNextIssueBtn(journal_id, next_issue_id)}
								</div>
							</div>
							<div class="P-Clear"></div>
							<div class="selectIssue"></div>
							<div id="issueNo">Issue No: </div>
							<div id="inputBox">
								<div class="floatLeft">
									<div class="left_ANG"></div>
									<div class="MID">
										<input type="text" name="goToIssue" id="go_to_issue_input"></input>
									</div>
									<div class="right_ANG"></div>
								</div>
								<div id="submitWrapper">
									<div class="left_ANG"></div>
									<div class="MID">
										<a href="javascript: goToIssue({journal_id}, \'go_to_issue_input\');">GO</a>
									</div>
									<div class="right_ANG"></div>
									<div class="list">
										from {min_issue_num} to {max_issue_num}
									</div>
								</div>
								<div class="P-Clear"></div>
							</div>
						</div>
					</div>
					<div class="siderCorners">
						<div class="bottomLeftCorner"></div>
						<div class="bottomRightCorner"></div>
					</div>
				</div>
	',

	'global.htmlendcontent' => '
				</div><!-- END contentWrapper -->
			</div><!-- END container -->
			<div id="P-Registration-Content"></div>
		{*global.htmlonlyfooter}
	',
	'global.edit_journal_page' => '
	{*global.htmlstartcontent}
		{leftcol}
		<div id="content">
			<div class="border"></div>
			<div class="corners">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="content-main">
				{form}
			</div>
			<div class="border"></div>
			<div class="corners bottom">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
			<div class="P-Clear"></div>
			{*global.footer}
		</div>
	{*global.htmlendcontent}',

	// Language templates
	'global.clangheader' => '
	<div style="text-align:right;margin:5px;color:#C26A4F;font-size:10px;">
	',
	'global.clangrowtempl' => '

		{name}&nbsp<a href="{url}"><img src="./i/{code}.gif" alt="language" style="border:none;"/></a>&nbsp;
	',

	'global.clangfooter' => '
	</div>
	',

	'global.system_msg' => '
		<h2>System message</h2>
		{msg}
	',

	// Templates for pages
	'global.simplepage' =>
	'{*global.htmlstartcontent}
		{*global.sidebar_left}
		<div id="content" >
			<div class="border"></div>
			<div class="corners">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="contentArticles" style="padding-top: 0px;">
				<div id="articlesFullCol">
					{contents}
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="border"></div>
			<div class="corners">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
		</div>
		{*global.htmlendcontent}',

	'global.submissionpage' =>
	'{*global.htmlstartcontent}

		<div id="content" style="margin: 0px;">
			<div id="dashboard-content" style="margin-left: 0; margin-right: 9px">
				{submission_step_title}
				<div class="content-main small-font" id="submission_step">
					{document_info}
					{document_reviewers}
					{contents}
					<div class="P-Clear"></div>
				</div>
			</div>
			<div class="P-Clear"></div>
			{*global.footer}
		</div>
		{*global.htmlendcontent}',

	'global.version_page' =>
		'{*global.htmlonlyheader_version}
				{preview}
			</div>
		{*global.htmlonlyfooter}
	',
	
	'global.article_page' =>'
		{*global.htmlonlyheader_article}
			<div id="container">
				<div id="header">
					<div id="nav" class="P-Nav-Article">
						<div class="constrained">
							<a target="_blank" href="http://www.pensoft.net"><img src="i/logo.jpg" alt="logo" class="logo" /></a>
		
							{mainmenu}
							{login_register_or_profile}
		
						
						</div>
					</div>
					<div class="P-Clear"></div>
				</div><!-- END header -->
				{contents}	
			</div>	
		{*global.htmlonlyfooter}
	',
	
	'global.article_preview_page' =>'
		{*global.htmlonlyheader_article_preview}
			{contents}
		{*global.htmlonlyfooter}
	',

	'global.story_childrens' => '{stories_tree}',

	'global.simplepage_withleftcol' =>
	'{*global.htmlstartcontent}
		{leftcol}
		<div id="content" >
			<div class="border"></div>
			<div class="corners">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="contentArticles" style="padding-top: 0px;">
				<div id="articlesFullCol">
					{contents}
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="border"></div>
			<div class="corners">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
		</div>
		{*global.htmlendcontent}',

	'global.loginpage' =>
	'{*global.htmlstartcontent}
		{*global.sidebar_left}
		<div id="content" >
			<div class="border"></div>
			<div class="corners">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="contentArticles content-main">
				<div class="loginformWrapper">
					<br/><div style="text-align: center;">{success_reg_message}{mail_confirmed_message}</div>
					<div class="loginformWrapperInnner">
						<br/><br/>
						{form}
					</div>
				</div>
			</div>
			<div class="border"></div>
			<div class="corners bottom">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
			<div class="P-Clear"></div>
			{*global.footer}
		</div>
	{*global.htmlendcontent}',

	'global.fpasspage' =>
	'{*global.htmlstartcontent}
		{*global.sidebar_left}
		<div id="content" >
			<div class="border"></div>
			<div class="corners">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="contentArticles content-main">
				<div class="loginformWrapper">
					<br/><div style="text-align: center;">{success_reg_message}{mail_confirmed_message}</div>
					<div class="loginformWrapperInnner">
						<br/><br/>
						{form}
					</div>
				</div>
			</div>
			<div class="border"></div>
			<div class="corners bottom">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
			<div class="P-Clear"></div>
			{*global.footer}
		</div>
	{*global.htmlendcontent}',

	'global.profilepage' =>
	'{*global.htmlstartcontent}
		{*global.sidebar_left_profile}
		<div id="content" style="margin: 0;">
			<div id="dashboard-content">
				<h1 class="dashboard-title">' . getstr('pjs.viewprofile') . '</h1>
				<div class="content-main">
					{content}
				</div>
				<div class="border"></div>
				<div class="corners bottom">
					<div class="bottomLeftCorner"></div>
					<div class="bottomRightCorner"></div>
				</div>
			</div>
			<div class="P-Clear"></div>
			{*global.footer}
		</div>
	{*global.htmlendcontent}',

	'global.registerpage' => '
		{form}
	',

	'global.stories_list' => '{content}',

	'global.show' =>
	'{*global.htmlstartcontent}
		{*global.sidebar_left_show}
		<div id="content" >
			<div class="border"></div>
			<div class="corners">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="content-main">
				{content}
			</div>
			<div class="border"></div>
			<div class="corners bottom">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
			<div class="P-Clear"></div>
			{*global.footer}
		</div>
	{*global.htmlendcontent}',

	'global.browse_journal_special_issues' =>
	'{*global.htmlstartcontent}
		{*global.sidebar_left}
		<div id="content" >
			<div class="border"></div>
			<div class="corners">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="contentArticles issues">

				{journal_special_issues_list}

			</div>
			<div class="border"></div>
			<div class="corners bottom">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
			<div class="P-Clear"></div>
			{*global.footer}
		</div>
	{*global.htmlendcontent}',

	'global.editprofilepage' =>
	'{*global.htmlstartcontent}
		{*global.sidebar_left_profile}
		<div id="content" >
			<div class="content-main">
				{form}
			</div>
			<div class="border"></div>
			<div class="corners bottom">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
			<div class="P-Clear"></div>
			{*global.footer}
		</div>
	{*global.htmlendcontent}',

	'global.index_page' => '',

	'global.document_tabs' => '
		<div class="tabHolder">
			<div class="tabRow viewdoc_activetab" onclick="ChangeActiveTab(this, \'viewdoc_activetab\', \'doc_tab_1\', \'document_author_review_round_holder\')">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.manuScript_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>
			</div>
			<div class="tabRow" onclick="ChangeActiveTab(this, \'viewdoc_activetab\', \'doc_tab_2\', \'document_author_review_round_holder\')">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.metadata_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>
			</div>
			<div class="tabRow" onclick="ChangeActiveTab(this, \'viewdoc_activetab\', \'doc_tab_3\', \'document_author_review_round_holder\')">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.sybm_files_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>
			</div>
			<div class="tabRow" onclick="ChangeActiveTab(this, \'viewdoc_activetab\', \'doc_tab_4\', \'document_author_review_round_holder\')">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.discounts_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>
			</div>
			<div class="tabRow" onclick="ChangeActiveTab(this, \'viewdoc_activetab\', \'doc_tab_5\', \'document_author_review_round_holder\')">
				<div class="tabRowLeft_Inactive"></div>
				<div class="tabRowMiddle_Inactive">
					{_getstr(pjs.schedule_label_tab)}
				</div>
				<div class="tabRowRight_Inactive"></div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',

	'global.right_sidebar' => '<!--
			<div class="rightSider" id="rightSidebar">
				<div class="rightSiderBox">
					<div class="rightSiderBoxCorners">
						<div class="topLeftCorner1"></div>
						<div class="topRightCorner2"></div>
					</div>
					<div id="picture">
						<img src="/i/pic.png" alt="pic"></img>
						<p>
							Authors are thus encouraged to post the pdf files of
							published papers on their homepages or elsewhere to
							expedite distribution.
						</p>
					</div>
					<div class="corners">
						<div class="bottomLeftCorner1"></div>
						<div class="bottomRightCorner2"></div>
					</div>
				</div>
				<div class="P-Clear"></div>
				<div class="headlineHolder">
					<div class="left_ANG"></div>
					<div class="MID">
						<h3>Community feed</h3>
					</div>
					<div class="right_ANG"></div>
					<div class="P-Clear"></div>
				</div>
				<div class="rightSiderBox">
					<div class="feed">
						<div class="img">
							<img src="/i/feed1.png" alt="feed image"></img>
						</div>
						<div class="title">
							<a href="#">
								Scorpio rising - An elusive new scorpion species from
								California lives underground
							</a>
							<div class="date">23.03.2012</div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="feed">
						<div class="img">
							<img src="/i/feed2.png" alt="feed image"></img>
						</div>
						<div class="title">
							<a href="#">
								new, giant wasp comes from Indonesia
								A new pipe
							</a>
							<div class="date">23.03.2012</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="feed">
						<div class="img"><img src="/i/feed3.png" alt="feed image"></img></div>
						<div class="title">
							<a href="#">
								A new pipewort specices from a unique,
								but fraglie habilat in indiana
							</a>
							<div class="date">23.03.2012</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="feed">
						<div class="img"><img src="/i/feed4.png" alt="feed image"></img></div>
						<div class="title">
							<a href="#">
								A new pipewort species from a unique, but fragile habitat in India
							</a>
							<div class="date">23.03.2012</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="feed">
						<div class="img"><img src="/i/feed5.png" alt="feed image"></img></div>
						<div class="title">
							<a href="#">
								Scorpio rising - An elusive new scorpion species from
								California lives underground
							</a>
							<div class="date">23.03.2012</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="moreFeeds">
						<a href="#">
							All feeds <img src="/i/rightArrows.png" alt=""></img>
						</a>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				<div class="corners">
					<div class="bottomLeftCorner1"></div>
					<div class="bottomRightCorner2"></div>
				</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>-->
	',

	'global.journal_home_page' =>
	'{*global.htmlstartcontent}
		{*global.sidebar_left}
		<div id="dashboard-content" class="withoutHeader">
			{contents}
			' . //{*global.right_sidebar}
			'<div class="P-Clear"></div>
		</div>
		{*global.journal_footer_page}
	{*global.htmlendcontent}',

	'global.dashboard' =>
		'{*global.htmlstartcontent}
			{leftcol}
			<div id="dashboard-content">
				{contents}
				{form}
				{users_list}
			</div>
			{*global.footer}
			<script type="text/javascript">
				resizeMainContent(\'dashboard-content\', \'dashboard-content\', \'dashboard-menu\');
			</script>
			{_showTasksPopUp(event_ids, url_redirect)}
		{*global.htmlendcontent}',

	'global.big_right_col' =>
		'{*global.htmlstartcontent}
			<div id="dashboard-content" style="margin-left:0px">
				{contents}
				{form}
				{users_list}
			</div>
			{*global.footer}
			<script type="text/javascript">
				resizeMainContent(\'dashboard-content\', \'dashboard-content\', \'dashboard-menu\');
			</script>
			{_showTasksPopUp(event_ids, url_redirect)}
		{*global.htmlendcontent}',

	'global.big_left_col_page' =>
		'{*global.htmlstartcontent}
			{leftcol}
			<div id="contentSmallHolder">
				<div id="contentSmall">
					{contents}

					{users_list}
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				{*global.footer}
			</div>
			<div class="P-Clear"></div>
			<script type="text/javascript">
				resizeMainContent(\'contentSmall\', \'contentSmallHolder\', \'leftSider\');
			</script>
		{*global.htmlendcontent}',

	'global.browse_articles' =>
		'{*global.htmlstartcontent}
			{journal_features}
			<div id="contentSmallHolder" style="margin-left: 200px;">
				<div id="contentSmall">
					{contents}
					{users_list}
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				{*global.footer}
			</div>
			<div class="P-Clear"></div>
			<script type="text/javascript">
				resizeMainContent(\'contentSmall\', \'contentSmallHolder\', \'leftSider\');
			</script>
		{*global.htmlendcontent}',

	'global.picture' => '
		{picture}
	',

	'global.profile_picture' => '
		<img class="P-Prof-Pic" src="/showimg.php?filename={pic_pref}_{pic_id}.jpg"></img>
	',

	'global.default_profile_picture' => '
		<img src="/i/add_photo.png"></img>
	',
	'global.footer' => '<div id="footer"><p>{_getstr(pjs.footer1)}
	 						<span class="bold">{_getstr(pjs.footer.copyright)}</span> |
							<a href="http://www.pensoft.net/contact_us.php">{_getstr(pjs.footer.contact)}</a></p></div>',

	'global.journal_footer_page' => '
		<div id="footer">
	        <ul class="link_list clearfix">
	            <li>
	                <a href="http://vbrant.eu/" target="_blank"><img src="/i/logo_vibrant.png"></a>
	            </li>
	            <li>
	                <a href="http://www.pensoft.net/" target="_blank"><img src="/i/logo_pensoft.png"></a>
	            </li>
	            <li>
	                <a href="http://cordis.europa.eu/fp7/home_en.html" target="_blank"><img src="/i/logo_sfp.png"></a>
	            </li>
	        </ul>
	    </div>
	',

	'global.document_edit' =>
	'{*global.htmlonlyheader}
		{*document_edit.document_header}
		<div class="clear"></div>

		<div id="content" >
			<div class="border"></div>
			<div class="corners">
				<div class="topLeftCorner"></div>
				<div class="topRightCorner"></div>
			</div>
			<div class="contentArticles" style="padding-top: 0px;">
				<div id="articlesFullCol">
					{contents}
				</div>
				<div class="clear"></div>
			</div>
			<div class="border"></div>
			<div class="corners">
				<div class="bottomLeftCorner"></div>
				<div class="bottomRightCorner"></div>
			</div>
		</div>
		{*global.htmlendcontent}',

	'global.setcookie' => '
		{*global.htmlonlyheader}
			{contents}
		{*global.htmlendcontent}
	',
	
	'global.preview_page' => '
		{*global.htmlonlyheader_preview}
			{contents}
		{*global.htmlonlyfooter}
	',

	'global.taskspopuppage' => '
		<div class="taskspopup-leftcol">
			<div class="taskspopup-header">
				{_getstr(pjs.taskspopup.formtitle)}
			</div>
			<div class="taskspopup-formcontent" id="tasksPopUpFormContent">
				{form}
			</div>
		</div>
		<div class="taskspopup-rightcol">
			<div class="taskspopup-header taskspopup-header-right">
				{_getstr(pjs.taskspopup.listtitle)}
			</div>
			<div class="taskspopup-listcontent">
				<div class="taskspopup-listheadmain">
					<div class="taskspopup-listheadmain-skip-section">
					</div>
					<div class="taskspopup-listheadmain-content-section">
						{_getstr(pjs.taskspopup.content_section_title)}
					</div>
					<div class="P-Clear"></div>
				</div>
				<div id="tasksPopUpListContent">
					{list}
				</div>
			</div>
		</div>
		<div class="P-Clear"></div>
	',

	'global.userexpertisespopuppage' => '
		<div class="taskspopup-leftcol user_expertises_big_col">
			<div class="taskspopup-header">
				{_getstr(pjs.userexpertisespopup.formtitle)}
			</div>
			<div class="taskspopup-formcontent userexpertises-formcontent">
				{form}
			</div>
		</div>
		<div class="P-Clear"></div>
	',

	'global.duedatepopup' => '
		{contents}
	',
	'global.reviewtypepopup' => '
		{contents}
	',
	
	'global.pdf_htmlonlyheader' =>
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		{*global.metadata}
	</head>
	<body>',
	
		'global.pdf_htmlonlyfooter' =>
	'</body>
	</html>',
	
	'global.generate_pdf' => '{contents}',
	
	'global.rss_page' => '{contents}'
);
?>