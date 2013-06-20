<?php
// @formatter->off
$gTemplArr = array(
	'document_edit.document_header' => '
	<div class="documentHeader">
		<div class="P-Header">
			<div class="P-Logo-Search-Holder">
				<div class="P-Logo"><a href="/"><img src="/i/logo.jpg" alt="pjs logo" /></a></div>
				<div class="P-Head-Search">
					<form action="/search.php" method="get">
						<div class="input-left"></div>
						<input type="submit" class="input-right" value="" />
						<input type="hidden" class="input-right" value="{document_id}" name="document_id"/>
						<div class="input-middle">
							<input class="iw260" type="text" value="{_getSearchStr(search_str)}" name="stext" onfocus="rldContent(this, \'Search in...\');" onblur="rldContent2(this, \'Search in...\');" />
						</div>
						<div class="P-Clear"></div>
					</form>
				</div>
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
		<div id="P-Registration-Content" style="display: none;"></div>
		<!-- End P-Header -->
		<div id="docEditHeader" class="docEditHeaderHolder {_checkReadOnlyAndHasLegend(readonly, user_legend)}" style="{_changeHeaderSize(readonly)}">
			{_decisionFormPreviewMode(readonly, role, name, decision, user_legend, author_version_num, author_name)}
			{_displayFilterBox(user_legend)}
			<div class="box buttons">
				<div class="P-Grey-Btn-Holder" {_showHideByRole(role, se_opening)}>
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><a href="" onclick="scrollToForm(); return false;">{_changeReviwerDecisionBtnText(role)}</a></div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
				{_ReturnSaveOrCloseBtn(previewmode)}
			</div>
		</div>
	</div>
	',
);

?>