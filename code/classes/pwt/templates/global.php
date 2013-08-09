<?php

$gTemplArr = array(
	'global.empty' => '',

	'global.htmlonlyheader' =>
	   '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
			<head>
				<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
				<title>{title}Pensoft Writing Tool</title>
				<meta name="description" content="" />
				<meta name="keywords" content="" />
				<meta name="distribution" content="global" />
				<meta name="robots" content="index, follow, all" />
				<link rel="shortcut icon" href="/favicon.ico" />
				<meta name="application-name" content="Pensoft Writing Tool" />
				<meta name="description" content="Online, collaborative, article-authoring tool" />
				<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=IE8" />
				<link type="text/css" rel="stylesheet" href="/lib/css/def.css" media="all" title="default" />
				<link type="text/css" href="/lib/css/ui.dynatree.css" id="skinSheet" rel="stylesheet" />
				<link type="text/css" rel="stylesheet" href="/lib/css/editor_rewrite.css" media="all" title="default" />
				<link type="text/css" rel="stylesheet" href="/lib/css/article_preview.css" media="all" title="default" />
				<link type="text/css" rel="stylesheet" href="/lib/css/editable_preview.css" media="all" title="default" />
				<link type="text/css" rel="stylesheet" href="/lib/css/comments.css" media="all" title="default" />
				<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" rel="stylesheet" />
				<script src="/lib/js/jquery.js" type="text/javascript"></script>
				<script src="/lib/js/jquery_ui.js" type="text/javascript"></script>
				<script src="/lib/js/jquery.tinyscrollbar.min.js" type="text/javascript" ></script>
				<script src="/lib/js/jquery.collapse.js" type="text/javascript" ></script>
				<script src="/lib/js/jquery.dynatree.min.js" type="text/javascript"></script>
				<script src="/lib/js/jquery.cookie.js" type="text/javascript"></script>
				<script src="/lib/js/jquery.simplemodal.js" type="text/javascript"></script>
				<script src="/lib/js/jquery_form.js" type="text/javascript"></script>
				<script src="/lib/js/jquery.tokeninput.js" type="text/javascript" ></script>
				<script src="/lib/js/jquery.dragsort.js" type="text/javascript" ></script>
				<script src="/lib/js/jquery.ba-resize.js" type="text/javascript" ></script>
				<script src="/lib/js/jquery.youtubepopup.min.js" type="text/javascript" ></script>
				<script src="/lib/js/ajaxupload.3.5.js" type="text/javascript" ></script>
				<script src="/lib/js/def.js" type="text/javascript"></script>
				<script src="/lib/js/comments_common.js" type="text/javascript"></script>
				<script src="/lib/js/comments.js" type="text/javascript"></script>
				<script src="/lib/js/changes_common.js" type="text/javascript"></script>
				<script src="/lib/js/popup.js" type="text/javascript"></script>
				<script src="/lib/js/figures.js" type="text/javascript"></script>
				<script src="/lib/js/editable_preview.js" type="text/javascript"></script>
		
				<script src="' . PJS_SITE_URL . '/lib/js/ice/lib/rangy-1.2/rangy-core.js"></script>
				<script src="' . PJS_SITE_URL . '/lib/js/ice/lib/rangy-1.2/rangy-cssclassapplier.js"></script>
				<script src="' . PJS_SITE_URL . '/lib/js/ice/lib/rangy-1.2/rangy-selectionsaverestore.js"></script>
				<script src="' . PJS_SITE_URL . '/lib/js/ice/lib/rangy-1.2/rangy-serializer.js"></script>

				<script type="text/javascript" src="/lib/editors/ckeditor_4.1/ckeditor.js"></script>
				<script type="text/javascript" src="/lib/editors/ckeditor/adapters/jquery.js"></script>

				<script type="text/javascript">
					$(document).ready(function(){
						setCommentsWrapEvents();
					});
					$(window).load(function(){
						gWindowIsLoaded = true;
					});
				</script>
			</head>
			<body onload="resizeMiddleContainer()">
	',

	'global.htmlonlyfooter' => '
				{*global.email_popup}
				<div id="layerbg" style="display: none;"></div>
				<script type="text/javascript">
					resizeMiddleContainer();
					autoSaveField();

					/* Google Analytics */
					var _gaq = _gaq || [];
					_gaq.push([\'_setAccount\', \'UA-34109634-1\']);
					_gaq.push([\'_trackPageview\']);
					(function() {
						var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
						ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
						var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
					})();
				</script>
			</body>
		</html>
	',

	'global.htmlonlyfooter_login' => '
				<div id="layerbg" style="display: none;"></div>
				<script type="text/javascript">
					/* Google Analytics */
					var _gaq = _gaq || [];
					_gaq.push([\'_setAccount\', \'UA-34109634-1\']);
					_gaq.push([\'_trackPageview\']);
					(function() {
						var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
						ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
						var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
					})();
				</script>
			</body>
		</html>
	',

	'global.header' => '{_checkDocumentMenuAndColumnsState(document_id)}
					<div class="P-Header">
						<div class="P-Logo-Search-Holder">
							<div class="P-Logo"><a href="/"><img src="' . PENSOFT_LOGO_IMG . '" alt="{_getstr(pwt)}" /></a></div>
							<div class="P-Head-Search">
								<form action="/search.php" method="get">
									<div class="input-left"></div>
									<input type="submit" class="input-right" value="" />
									<input type="hidden" class="input-right" value="{document_id}" name="document_id"/>
									<div class="input-rightdropdown" id="PensoftSearch">
										<div class="P-Option-Selected"></div>
										<select id="PensoftSearchSelect" name="catsearch">
											{_getSearchSelectItems(document_id)}
										</select>
										<div class="P-Options-Holder" id="PensoftSearchSelectOptions" style="display: none;">
											<div class="P-Options-Top"></div>
											<div class="P-Options-Middle"></div>
											<div class="P-Options-Bottom"></div>
											<div class="P-Options-Arrow"></div>
											<div class="P-Clear"></div>
										</div>
									</div>
									<div class="input-middle">
										<input class="iw260" type="text" value="{_getSearchStr(search_str)}" name="stext" onfocus="rldContent(this, \'Search in...\');" onblur="rldContent2(this, \'Search in...\');" />
									</div>
									<div class="P-Clear"></div>
								</form>
							</div>
							<script type="text/javascript">
								var penSearch = new searchDropDown(\'PensoftSearch\', \'PensoftSearchSelectOptions\');
							</script>
						</div>
						<div class="P-Head-Profile-Menu">
							<div class="userloggedmenu">
								<a class="userloggedimageA" href="/">
									{_showProfilePic()}
								</a>
								<div class="userloggedimage">
									<div class="username_left"></div>
									<div class="username">{_getUserName()}</div>
									<div class="username_right"></div>
								</div>
								<div onmouseout="" onmouseover="" id="userLoggedMenu" class="userloggedmenulinks">
									<div class="userloggedmenulinksTop"></div>
									<div class="userloggedmenulinksMainInner">
										<div class="userLoggedMenuLink">
											<a id="userLoggedMenuLink_1" href="#" onclick="LayerProfEditFrm(\'P-Registration-Content\', 2, 1);">Account settings</a>&nbsp;Login, Password
										</div>
										<div class="userloggedmenuSep"></div>
										<div class="userLoggedMenuLink">
											<a id="userLoggedMenuLink_2" href="#" onclick="LayerProfEditFrm(\'P-Registration-Content\', 2, 2);">Profile</a>&nbsp;Name, Pictures, URL, ...
										</div>
										<div class="userloggedmenuSep"></div>
										<div class="userLoggedMenuLink">
											<a id="userLoggedMenuLink_3" href="#" onclick="LayerProfEditFrm(\'P-Registration-Content\', 2, 3);">Email/RSS alerts</a>&nbsp;Name, Pictures ...
										</div>
										<div class="userloggedmenuSep"></div>
										<div class="userLoggedMenuLink">
											<a id="userLoggedMenuLink_5" href="/inbox.php">Messages</a>&nbsp;Inbox, Messages, ...
											<div class="P-Clear"></div>
										</div>
										<div class="userloggedmenuSep"></div>
										<div class="userLoggedMenuLink">
											<a id="userLoggedMenuLink_6" href="/login.php?logout=1">Logout</a>
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
						<script type="text/javascript">
							//<![CDATA[
								$(document).ready(function(){
									ShowHideProfileMenu();
								});
							//]]>
						</script>
					</div>
					<div id="P-Registration-Content" style="display: none;"></div>
					<!-- End P-Header -->
	',

	'global.htmlstartcontent' =>
		   '{*global.htmlonlyheader}
				<div class="P-Wrapper {_showLockedErrorClassMain(document_is_locked, document_lock_usr_id, without_warning)} {_showValidationErrorClassMain(xml_errors, xml_validation)}">
					{*global.header}					
					<div class="P-Bread-Crumbs {_showLockedErrorClass(document_is_locked, document_lock_usr_id, without_warning)} {_showValidationErrorClass(xml_errors, xml_validation)}">
						{_showDocumentLockWarning(document_is_locked, document_lock_usr_id, without_warning)}						
						{_showValidationErrorDiv(xml_errors, xml_validation)}
						<div class="P-Path"><a href="/index.php"><img class="pathHomeImg" src="./i/home_path.png" alt="My manuscripts"/></a>{path}</div>
						<div class="P-SavePreview-Btns">
							<div class="P-RevHistory"><a href="/preview_revisions.php?document_id={document_id}">Revision History</a></div>
							{_displayTopRightButtons( document_is_locked, preview_mode, document_id)}
						</div>
						<div class="P-Clear"></div>
					</div><!-- End P-Bread-Crumbs -->
					<div class="P-Wrapper-Container">
	',
		
	'global.htmlstartcontent_preview' =>
		'{*global.htmlonlyheader}
				<div class="P-Wrapper {_showLockedErrorClassMain(document_is_locked, document_lock_usr_id, without_warning, preview_is_readonly)} {_showValidationErrorClassMain(xml_errors, xml_validation)}">
					{*global.header}
					<div class="P-Bread-Crumbs {_showLockedErrorClass(document_is_locked, document_lock_usr_id, without_warning, preview_is_readonly)} {_showValidationErrorClass(xml_errors, xml_validation)}">
						{_showDocumentLockWarning(document_is_locked, document_lock_usr_id, without_warning, preview_is_readonly)}
						{_showValidationErrorDiv(xml_errors, xml_validation)}
						<div class="P-Path"><a href="/index.php"><img class="pathHomeImg" src="./i/home_path.png" alt="My manuscripts"/></a>{path}</div>
						<div class="P-SavePreview-Btns">
							<div class="P-RevHistory"><a href="/preview_revisions.php?document_id={document_id}">Revision History</a></div>
							{_displayTopRightButtons( document_is_locked, preview_mode, document_id)}
						</div>
						<div class="P-Clear"></div>
					</div><!-- End P-Bread-Crumbs -->
					<div class="P-Wrapper-Container">
	',

	'global.htmlendcontent' => '
						<div id="P-Ajax-Loading-Image">
							<img src="./i/loading.gif" alt="" />
						</div>
					</div><!-- End P-Wrapper-Container -->
					<div class="P-Footer">
						<div class="P-Footer-Left">
							<a href="http://www.pensoft.net/" target="_blank">' . getstr('global.pensoft_main_site') . '</a>
							<a href="http://ptp.pensoft.eu/" target="_blank">' . getstr('global.pensoft_taxon_profile') . '</a>
							<a href="http://www.pensoft.net/contact_us.php" target="_blank">' . getstr('global.contact_us') . '</a>
						</div>
						<div class="P-Footer-Right">
							<span style="float: right">' . getstr('global.copyright') . date('Y') . '&nbsp;' . getstr('pwt.pensoft') . '</span>
							<div class="P-Fixed-Bottom-Line-Item" onclick="openEmailPopup(\'P-Project-Participants\');"><div class="P-Icon-Message"></div> ' . getstr('global.email') . '</div>
							<!-- <div class="P-Fixed-Bottom-Line-Separator"></div>
							<div class="P-Fixed-Bottom-Line-Item"></div> -->
						</div>
					</div>
				</div>
				<div id="P-Ajax-Loading-Image" style="display: none;">
					<img src="' . PJS_SITE_URL . '/i/loading.gif" alt="">
				</div>
			{*global.htmlonlyfooter}
	',

	'global.email_popup' => '
		<div style="display: none;" class="P-PopUp P-Email-PopUp" id="compose-new-message">
			<div class="P-PopUp-Main-Holder">
				<div class="P-PopUp-Content">
					<div class="P-PopUp-Title">' . getstr('global.composeMessage') . '</div>

					<div class="P-PopUp-Content-Inner-Email">
						<div class="input-title">' . getstr('global.subject') . ' <span class="txtred">*</span></div>
						<div class="P-Input-Full-Width" >
							<div class="P-Input-Inner-Wrapper">
								<div class="P-Input-Holder">
									<div class="P-Input-Left"></div>
									<div class="P-Input-Middle">
										<input id="email_subject" type="text" fldattr="0" onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" value="" name="inputname"></input>
									</div>
									<div class="P-Input-Right"></div>
									<div class="P-Clear"></div>
								</div>
							</div>
						</div>
						<div class="P-Clear"></div>
						<div class="P-VSpace-10"></div>
						<div class="P-Clear"></div>
						<div class="input-title">' . getstr('global.recipients') . ' <span class="txtred">*</span></div>
						<div class="P-Input-Full-Width" >
							<div class="P-Input-Inner-Wrapper">
								<div class="P-Input-Holder">
									<input type="text" id="email_recipients" fldattr="0" onblur="changeFocus(2, this)" onfocus="changeFocus(1, this)" value="" name="inputname1"></input>
									<script type="text/javascript">
										//<![CDATA[
										$(document).ready(function () {
											$("#email_recipients").tokenInput(gAutocompleteAjaxSrv + "?action=get_email_recipients",
												{
													minChars: 3,
													theme: "facebook",
													preventDuplicates: true,
													queryParam: "term",
													onResult: function(data){
														return data;
													},
													onAdd: function(item){
														$("#" + item.id).attr("checked", true);
														var input = \'<input class="email_recipients" id="email_recipients\' + item.id + \'_hiddenInp"  name="email_recipients[]" value="\' + item.id + \'" type="hidden"></input>\';
														$(input).insertBefore( "#email_recipients" );
													},
													onDelete: function(item){
														$("#email_recipients" + item.id + "_hiddenInp").remove();
														$("#" + item.id).attr("checked", false);
													}
												}
											);
										});
										//]]>
									</script>
									<div class="P-Clear"></div>
								</div>
							</div>
						</div>
						<div class="P-Clear"></div>
						<div class="P-VSpace-10"></div>
						<div class="P-Clear"></div>
						<div class="P-Data-Resources-Control-Txt">
							<div class="P-Data-Resources-Control-Left">
								' . getstr('global.message') . ' <span class="txtred">*</span>
							</div>
						</div>
						<div class="P-Control-Textarea-Holder">
							<textarea id="email_content_textarea" rows="10" cols="80"></textarea>
						</div>
						{_createHtmlEditorBase(email_content, 220, 0, ' . EDITOR_MODERATE_TOOLBAR_NAME . ', 0, "", 2)}
						<div class="P-Clear"></div>
						<div class="P-VSpace-20"></div>
						<div class="P-Clear"></div>
						<div class="P-Green-Btn-Holder P-90" onclick="ajaxSendMessage(\'email_subject\',\'email_recipients\',\'email_content_textarea\');"
						 	style="position: absolute; bottom: 10px">
							<div class="P-Green-Btn-Left"></div>
							<div class="P-Green-Btn-Middle">' . getstr('global.send') . '</div>
							<div class="P-Green-Btn-Right"></div>
						</div>
						<div class="P-HSpace-10"></div>
						<div class="P-Grey-Btn-Holder" onclick="popUp(POPUP_OPERS.close, \'compose-new-message\', \'compose-new-message\');" 
						style="position: absolute; bottom: -10px; left: 121px">
							<div class="P-Grey-Btn-Left"></div>
							<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>' . getstr('global.close') . '</div>
							<div class="P-Grey-Btn-Right"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Project-Participants">


				</div>
			</div>
		</div>
	',

	'global.participants_head' => '
		<div class="P-PopUp-Title">' . getstr('global.projectParticipans') . '</div>
		<div class="P-Project-Participants-Holder">
	',
	'global.participants_foot' => '
		</div>
		<div class="P-Clear"></div>
		{_getAddParticipantsButton(records)}
		<div class="P-Clear"></div>
	',

	'global.participants_empty' => '
		<div class="P-Empty-Content">' . getstr('pwt.email_popup_participants_empty') . '</div>
	',

	'global.participants_row' => '
		<div class="P-Project-Participants-Item">
			<div class="P-Project-Participant-Checkbox">
				<input type="checkbox" onchange="AddMailRecipients(this, \'P-Recipient-Username_{id}\', \'email_recipients\');" value="{id}" id="{id}" name="participants" ></input>
			</div>
			<img style="float:left" src="{_getUserProfileImg(photo_id)}" alt="" />
			<div class="P-Activity-Fieed-Item-Details">
				<div id ="P-Recipient-Username_{id}" class="P-Username">{usernames}</div>
				<div class="P-Clear"></div>
				<div class="P-Activity-Fieed-Content">Corresponding author</div>
				<div class="P-Clear"></div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',

	'global.index_page' =>
		   '{*dashboard.htmlstartcontent}
				{*dashboard.content}
			{*global.htmlendcontent}
	',

	'global.simple_page' =>
		   '{*global.htmlstartcontent}
					{contents}
			{*global.htmlendcontent}
	',

	'global.document_page' =>
		   '{*global.htmlstartcontent}
						{document}
			{*global.htmlendcontent}
	',

	'global.document_under_construction_page' =>
		   '{*global.htmlonlyheader}
				<div class="P-Wrapper">
					{*global.header}
					<div class="P-Wrapper-Container">
						<div class="P-Under-Construction">Under construction</div>
			{*global.htmlendcontent}
	',

	'global.editdocument_page' =>
		   '{*global.htmlstartcontent_preview}
						<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
							{tree}
							<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
							<script  type="text/javascript">
								var articleStructure = new articleMenu(\'articleMenu\', \'P-Right-Arrow\' , \'P-Down-Arrow\');
							</script>
						</div><!-- End P-Wrapper-Container-Left -->
						<div class="P-Wrapper-Container-Right {_getContainerHideClass(2)}">
							{*document.rightcol}
							<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						</div><!-- End P-Wrapper-Container-Right -->
						<div class="P-Wrapper-Container-Middle {_getContainerHideClass(1)} {_getContainerHideClass(2)}">
							{content}
						</div><!-- End P-Wrapper-Container-Middle -->
						<script type="text/javascript">
							window.onbeforeunload = function(){
								unlock_document();
							};
						</script>
						{_hideTreeIfDocumentIslocked(document_is_locked, P-Wrapper-Container-Left, document_lock_usr_id)}
			{*global.htmlendcontent}
	',
		
	'global.editdocument_page_ajax_tree' =>
		'{*global.htmlstartcontent_preview}
						<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
							{*document.tree_ajax_wrapper}
							<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
							<script  type="text/javascript">
								var articleStructure = new articleMenu(\'articleMenu\', \'P-Right-Arrow\' , \'P-Down-Arrow\');
							</script>
						</div><!-- End P-Wrapper-Container-Left -->
						<div class="P-Wrapper-Container-Right {_getContainerHideClass(2)}">
							{*document.rightcol}
							<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						</div><!-- End P-Wrapper-Container-Right -->
						<div class="P-Wrapper-Container-Middle {_getContainerHideClass(1)} {_getContainerHideClass(2)}">
							{content}
						</div><!-- End P-Wrapper-Container-Middle -->
						<script type="text/javascript">
							window.onbeforeunload = function(){
								unlock_document();
							};
						</script>
						{_hideTreeIfDocumentIslocked(document_is_locked, P-Wrapper-Container-Left, document_lock_usr_id)}
			{*global.htmlendcontent}
	',

	'global.document_revisions_page' =>
		   '{*global.htmlstartcontent}
						<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
							{tree}
							<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
							<script type="text/javascript">
								var articleStructure = new articleMenu(\'articleMenu\', \'P-Right-Arrow\' , \'P-Down-Arrow\');
							</script>
						</div><!-- End P-Wrapper-Container-Left -->
						<div class="P-Wrapper-Container-Right {_getContainerHideClass(2)}">
							{*document.revisions_rightcol}
							<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						</div><!-- End P-Wrapper-Container-Right -->
						<div class="P-Wrapper-Container-Middle {_getContainerHideClass(1)} {_getContainerHideClass(2)}">
							{content}
						</div><!-- End P-Wrapper-Container-Middle -->
						<script  type="text/javascript">
							window.onbeforeunload = function(){
								unlock_document();
							};
						</script>
						{_hideTreeIfDocumentIslocked(document_is_locked, P-Wrapper-Container-Left, document_lock_usr_id)}
			{*global.htmlendcontent}
	',

	'global.loginpage' =>
	   '{*global.htmlonlyheader}
				{contents}
				<div id="P-Registration-Content" style="display: none;"></div>
		{*global.htmlonlyfooter_login}
	',


	'global.registerpage' => '
		{contents}
	',

	'global.htmlstartcontent_without_bread_crumbs' =>
		   '{*global.htmlonlyheader}
				<div class="P-Wrapper P-Without-Bread-Crumbs">
					{*global.header}
					<div class="P-Wrapper-Container">
	',

	'global.createdocument_page' =>
		   '{*global.htmlstartcontent_without_bread_crumbs}
				<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
					<div class="P-Article-Structures">
						<div class="P-Article-Structure-Start-Desc">' . getstr('pwt_article.create_document_article_desc') . '</div>
					</div>
				</div>

				<div class="P-Wrapper-Container-Middle clearBorder">
					<div class="P-Data-Resources">
						{content}
					</div>
				</div>
			{*global.htmlendcontent}
	',

	'global.search_page' =>
	   '{*dashboard.htmlstartcontent}
			{content}
		{*global.htmlendcontent}
	',

	'global.search_in_document_page' =>
		   '{*global.htmlstartcontent}
						<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
							{tree}
							<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
							<script  type="text/javascript">
								var articleStructure = new articleMenu(\'articleMenu\', \'P-Right-Arrow\' , \'P-Down-Arrow\');
							</script>
						</div><!-- End P-Wrapper-Container-Left -->
						<div class="P-Wrapper-Container-Right {_getContainerHideClass(2)}">
							{*document.rightcol}
							<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						</div><!-- End P-Wrapper-Container-Right -->
						<div class="P-Wrapper-Container-Middle P-Wrapper-Container-Search-In-Document {_getContainerHideClass(1)} {_getContainerHideClass(2)}">
							{content}
						</div><!-- End P-Wrapper-Container-Middle -->
						<script type="text/javascript">
							window.onbeforeunload = function(){
								unlock_document();
							};
							toggleRightContainer();
						</script>
						{_hideTreeIfDocumentIslocked(document_is_locked, P-Wrapper-Container-Left, document_lock_usr_id)}
			{*global.htmlendcontent}
	',

	'global.zoomed_figure_page' =>
	   '{*global.htmlonlyheader}
		{content}
		{*global.htmlonlyfooter}
	',

	'global.sendmail' =>
	   '<html>
		<body>
			{mailbody}
		</body>
		</html>
	',

	'global.inbox' =>
	   '{*dashboard.htmlstartcontent}
			{content}
		{*global.htmlendcontent}
	',

	'global.setcookie' =>
	   '{*global.htmlonlyheader}
			{contents}
		{*global.htmlonlyfooter}
	',

	'global.empty' => '

	',
);
?>