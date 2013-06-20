<?php
// @formatter->off
$gTemplArr = array(
	// Browse Journal Stories List Templates
	'browse.head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_about_pages') . '</h1>
	',
	'browse.startrs' => '
	<table width="88%" cellpadding="5" cellspacing="0">
		<!--<ul class="journalArticlesTree">-->
	',
	'browse.row' => '
			<li class="{_getBrowseRowClass(pos, showmode, rownum)}">{_addSpace(pos)}<a href="/show.php?mode=1&storyid={guid}#{title}">{title}</a></li>
	',
	'browse.row_show' => '
			<li class="{_getBrowseRowClass(pos, showmode, rownum)}">{_addSpace(pos)}<a href="#{title}">{title}</a></li>
	',
	'browse.row_edit' =>
	'<tr class="pageName">
		<td width="30" class="editOptions"><a title="Add sub-page" href="/edit.php?tAction=showedit&amp;journal_id={journal_id}&amp;parent_id={guid}" class="journalArticleAction"><img src="/i/addpage.png" alt="add new article" /></a></td>
		<td width="30" class="editOptions"><a title="Delete page" href="javascript: void(0);" onclick="deleteStoryAjax(this, {journal_id}, {guid});" class="journalArticleAction"><img src="/i/removepage.png" alt="delete article" /></a></td>
		<td width="20" class="editOptions"><div class="arrows">
				<div class="goup">
					<a title="Move up" href="javascript: void(0);" class="journalArticleAction" onclick="moveStoryAjax(this, {journal_id}, {guid}, 1);">
						<img src="/i/toparrow.png" alt="go up" /></a></div>
				<div class="godown">
					<a title="Move down" href="javascript: void(0);" class="journalArticleAction" onclick="moveStoryAjax(this, {journal_id}, {guid}, 0);">
						<img src="/i/bottomarrow.png" alt="go down" class="vote" /></a></div>
			</div></td>
		<td class="title">{_addSpace(pos)}<a title="Edit page" href="/edit.php?tAction=showedit&amp;journal_id={journal_id}&amp;guid={guid}" class="journalArticleTitle">{title}</a></td></tr>',
	'browse.foot' => '',
	
	'browse.endrs_edit' => '
	<tr class="pageName">
		<td style="border: none"></td>
		<td style="border: none"></td>
		<td style="border: none"><a href="/edit.php?tAction=showedit&amp;journal_id={journal_id}" class="journalArticleAction">
				<img src="/i/addpage.png" alt="add new article" />
			</a></td>
		<td class="title">
			<a href="/edit.php?tAction=showedit&amp;journal_id={journal_id}" class="journalArticleTitle">
			 Add new page
			</a>
		</td></tr>
		</table>
	',
	
	'browse.stories_list_head' => '
		
	',
	'browse.stories_list_row' => '
			{story}
	',
	'browse.stories_list_foot' => '
		
	',
	'browse.tree_list_empty' => '
		<br/>
		<br/>
		<div style="text-align: center;">This article does not exist.</div>
	',
	'browse.journal_fetures_head' => '	
						<div class="leftSiderBlock">
							<h3>' . getstr('pjs.journal_features') . '</h3>
							<div class="siderBlockLinksHolder">',
	'browse.journal_fetures_row' => '
								{_getJournalFeaturesLinks(journal_id, guid, title, type)}',
	'browse.journal_fetures_foot' => '
							<a href="/contacts" class="link">
								<span></span>
								<span class="content">{_getstr(pjs.contacts)}</span>
							</a>
							<a href="/board" class="link">
								<span></span>
								<span class="content">{_getstr(pjs.editorial_team)}</span>
							</a>
							</div>
							<div class="P-Clear"></div>
					</div>
	',
	'browse.left_head' => '
						<div class="leftSiderBlock" style="position: fixed;">
									<h3>' . getstr('pjs.journal_about_title') . '</h3>
						
						<div class="siderBlockLinksHolder">
	',
	
	'browse.left_row_show' => '
							<a href="#{_strip_invalid(title)}" class="link" id="about_panel_story_{guid}" onclick="getStoryChildrens(this, {guid}, {journal_id});">
								<span></span>
								<span class="content">{_addSpace(pos)}{title}</span>
							</a>
	',
	
	'browse.left_foot' => '
							<div class="clear"></div>
						</div>
						</div>
	',
	
	// Browse Journal Issues List Templates
	'browse.journal_issue_head' => '
			<div style="margin: 10px;">
				<h1 style="font-size: 20px;"> {journal_name} {volume} ({year}){_getSpecialIssueTxt(is_regular_issue)}</h1>
				<h3  class="bold" style="font-size: 14px;">
					{issue_name}
				</h3>
				<p class="padding">
					<br/>
					by Vladimir Blagoderov &amp; Vincent Smith
				</p>
				<p class="bold">
					Table of contents
				</p>
				<div class="P-Clear"></div>
	',
	
	'browse.journal_issue_row' => '
				<div class="article" style="border-top: none;">
					<div class="starHover"></div>
					<div class="articleHeadline">
						<a href="#">
							{name}
						</a>
					</div>
					<p>
						{editors_names}
					</p>
					<img src="i/researchLeft.png" alt="Research Left Corner" class="floatLeft"></img>
					<div class="research">
						{journal_section_name}
					</div>
					<img src="i/researchRight.png" alt="Research Left Corner" class="floatLeft"></img>
					&nbsp;&nbsp;&nbsp;
					<a href="#" class="subLink">doi: {doi}</a>
					<span class="price"><span>Reprint price:</span> <b>&euro; {price}</b> <img src="i/cart.png" alt="cart"></img></span>
					<div class="info">
						<span><img src="i/paper.png" alt="paper"></img> {start_page}-{end_page}</span>
						<span><img src="i/articleCalendar.png" alt="Calendar"></img> {_getOnlyDatePart(publish_date)}</span>
						<span><img src="i/eye.png" alt="eye"></img> 465</span>
						<div>
							<a href="#">Abstract</a>
							<a href="#">HTML</a>
							<a href="#">XML</a>
							<a href="#" class="clearBorder">PDF</a>
						</div>
					</div>
				</div>
	',
	
	'browse.journal_issue_foot' => '
			</div>
	',
	
	'browse.journal_issue_empty' => '<br/>' . getstr('pjs.journal_issue_empty') . '<br/>',
	
	// Browse Journal Documents List Templates
	'browse.journal_documents_head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_documents') . '</h1>
	',
	'browse.journal_documents_start' => '
		<table class="dashboard">
			<tr>
				<th class="center">' . getstr('pjs.id') . '</th>
				<th class="left">' . getstr('pjs.section') . '</th>
				<th class="left">' . getstr('pjs.authors') . '</th>
				<th class="left">' . getstr('pjs.title') . '</th>
				<th class="left">' . getstr('pjs.action') . '&nbsp;&nbsp;&nbsp;</th>
			</tr>
	',
	
	'browse.journal_documents_row' => '
			<tr>
				<td class="right id">{id}</td>
				<td class="left"><abbr title="{section_title}">{abr}</abbr></td>
				<td class="left">{*dashboard.authors.all}</td>
				<td class="left">{*pjs.submission}</td>
				<td class="left"><a href="javascript: void(0);" onclick="confirmDelete(\'' . getstr('pjs.are_you_sure') . '\', \'manage_journal_documents.php?document_id={id}&amp;delete=1\');">' . getstr('pjs.delete') . '</a>&nbsp;&nbsp;&nbsp;</td>
			</tr>
	',
	
	'browse.journal_documents_endrs' => '
		</table>
		<br />
	',
	
	'browse.journal_documents_foot' => '',
	'browse.journal_documents_empty' => '<br/>' . getstr('pjs.journal_documents_empty') . '<br/>',
	
	// Browse Journal Users List Templates
	'browse.journal_users_head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_users') . '</h1>
	',
	'browse.journal_users_start' => '
		<table class="dashboard">
			<tr>
				<th class="left">' . getstr('pjs.name') . '</th>
				<th class="left">' . getstr('JM') . '</th>
				<th class="left">' . getstr('E') . '</th>
				<th class="left">' . getstr('SE') . '</th>
				<th class="left">' . getstr('LE') . '</th>
				<th class="left">' . getstr('CE') . '</th>
				<th class="left">' . getstr('R') . '</th>
				<th class="left">' . getstr('A') . '</th>
				<th class="left">' . getstr('pjs.expertises') . '</th>
				<th class="left">' . getstr('pjs.actions') . '</th>
				<th class="left">' . getstr('pjs.update') . '</th>
			</tr>
	',
	
	'browse.journal_users_row' => '
			<tr>
				<td class="left">{user_names}</td>
				<td class="left"><input type="checkbox" name="jm_{id}" id="jm_{id}" value="1" {_isChecked(jm)} /></td>
				<td class="left"><input type="checkbox" name="e_{id}" id="e_{id}" value="1" {_isChecked(e)} /></td>
				<td class="left"><input type="checkbox" name="se_{id}" id="se_{id}" value="1" {_isChecked(se)} /></td>
				<td class="left"><input type="checkbox" name="le_{id}" id="le_{id}" value="1" {_isChecked(le)} /></td>
				<td class="left"><input type="checkbox" name="ce_{id}" id="ce_{id}" value="1" {_isChecked(ce)} /></td>
				<td class="left"><input type="checkbox" name="r_{id}" id="r_{id}" disabled="disabled" value="1" {_isChecked(r)} /></td>
				<td class="left"><input type="checkbox" name="a_{id}" id="a_{id}" disabled="disabled" value="1" {_isChecked(a)} /></td>
				<td class="left" id="expertise_{id}">{_getUserExpertisesLink(journal_id, id, se)}</td>
				<td class="left"><a href="javascript: void(0);">' . getstr('pjs.loginas') . '</a></td>
				<td class="left"><a href="javascript: void(0);" onclick="updateUserRoles(this, {journal_id}, {id});">' . getstr('pjs.update') . '</a></td>
			</tr>
	',
	
	'browse.journal_users_endrs' => '
		</table>
		<br />
	',
	
	'browse.journal_users_foot' => '',
	'browse.journal_users_empty' => '<br/>No results.<br/>',
	
	// Browse journal issues templates
	'browse.journal_issues_head' => '',
	'browse.journal_issues_startrs' => '
		<h1 class="pageTitle">{_getMatchingIssuesCount(records)}</h1>
		<div style="padding: 5px;">
			{nav}
			<table width="100%">
	',
	'browse.journal_issues_row' => '
				<tr>
					<td style="padding:15px 7px 13px 0px">
						<table width="100%" style="font-size: 11px;">
							<tr>
								<td valign="top" class="issuePicture" rowspan="6">
									{_showPicIfExists(previewpicid, d80x)}
								</td>
								<td valign="top" colspan="2">
									<a class="green issueTitle" href="/browse_journal_issue_documents.php?journal_id={journal_id}&issue_id={id}">{journal_name} {volume} ({year}){_getSpecialIssueTxt(is_regular_issue)}</br>{issue_title}</a>
								</td>
							</tr>
							<tr>
								<td colspan="2"> 
									<div class="issueTotals">
										' . getstr('pjs.papers_published') . ': <span>{_intThis(count_documents)}</span> &nbsp;&nbsp;|&nbsp;&nbsp;
										' . getstr('pjs.total_pages') . ': <span>{_intThis(count_pages)}</span> &nbsp;&nbsp;|&nbsp;&nbsp;
										' . getstr('pjs.color_pages') . ': <span>{_intThis(count_color_pages)}</span>
									</div>
								</td>
							</tr>
							<tr>
								<td width="80px" valign="top"><b>' . getstr('pjs.taxa') . ':</b></td>
								<td class="categories">{_getCategoriesAndCount(taxon_names, taxon_cnt)}</td>
							</tr>
							<tr>
								<td width="80px" valign="top"><b>' . getstr('pjs.subjects') . ':</b></td>
								<td class="categories">{_getCategoriesAndCount(subject_names, subject_cnt)}</td>
							</tr>
							<tr>
								<td width="80px" valign="top"><b>' . getstr('pjs.regions') . ':</b></td>
								<td class="categories">{_getCategoriesAndCount(geographical_names, geographical_cnt)}</td>
							</tr>
							<tr>
								<td colspan="2">
									<div id="price" class="issuePrice">
										<div class="floatLeft">&euro; {price}</div><img class="floatLeft" src="i/cart.png" alt="cart">
										<span class="floatLeft">' . getstr('pjs.order_reprint') . '</span>
										<a href="/browse_journal_issue_documents.php?journal_id={journal_id}&issue_id={id}" style="float: right;">' . getstr('pjs.table_of_contents') . '</a>
										<div class="P-Clear"></div>
									</div>
								</td>
							</tr>
							<tr> 
								<td colspan="3" bgcolor="#D1CDBB" height="2px"></td>
							</tr>
						</table>
					</td>
				</tr>
	',
	'browse.journal_issues_endrs' => '
			</table>
			{nav}
		</div>
	',
	'browse.journal_issues_foot' => '',
	'browse.journal_issues_empty' => '<br/>' . getstr('pjs.no_issues_matching_your_criteria') . '<br/>',
);
?>