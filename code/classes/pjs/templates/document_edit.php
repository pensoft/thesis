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
				{*global.profile_pic_and_name}
			</div>
			<div class="P-Clear"></div>
			{_displayReadonlyVersionHeaderBox(version_is_readonly)}
		</div>
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
			<div class="P-Clear"></div>
		</div>
	</div>
	',
);

?>