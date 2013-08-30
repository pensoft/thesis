<?php

$gTemplArr = array(
	'journalauthors.sidebar_left_browse_authors' => '
				<div id="leftSider">
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.browse') . '</h3>
						<div class="P-Clear"></div>
						<div class="siderBlockLinksHolder">
							<a class="link" href="/browse_journal_articles.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.articles') . '</span>
							</a>
							<!-- <a class="link" href="/browse_journal_issues.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.issues') . '</span>
							</a> -->
							<a class="link active" href="/browse_journal_authors.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.authors') . '</span>
							</a>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.filter') . '</h3>
						<div class="P-Clear"></div>
						<div class="filterBlock">
							<div class="filterBlockTitle">' . getstr('pjs.byalphabet') . '</div>
							<div class="filterBlockContent">
								<input id="author_first_letter" type="hidden" name="author_letter" value="{author_letter}" />
								<a href="javascript: filterAuthorsLetter(\'affiliation_input\', {journal_id}, \'\')" class="green letter">All</a>&nbsp;&nbsp;&nbsp;<span style="color: #b0ada2;">|</span>
								<div class="lettersHolder">
									{_getLetters(journal_id, \'affiliation_input\')}
								</div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<!--
						<div class="filterBlock">
							<div class="filterBlockTitle">' . getstr('pjs.byaffiliation') . '</div>
							<div class="filterBlockContent">
								<div class="fieldHolder bigColField">
									<input id="affiliation_input" type="text" name="affiliation" value="{affiliation}" />
								</div>
							</div>
							<div class="P-Clear"></div>
						</div>
						-->
						
						<div class="buttonsHolder">
							<div class="P-Green-Btn-Holder">
								<div class="P-Green-Btn-Left"></div>
								<a class="P-Green-Btn-Middle P-80" onclick="filterAuthors(\'affiliation_input\', {journal_id}, \'author_first_letter\');">Filter</a>
								<div class="P-Green-Btn-Right"></div>
							</div>
							<div class="P-Grey-Btn-Holder">
								<div class="P-Grey-Btn-Left"></div>
								<a class="P-Grey-Btn-Middle" href="/browse_journal_authors.php?journal_id={journal_id}">' . getstr('pjs.clear_filters') . '</a>
								<div class="P-Grey-Btn-Right"></div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
				</div>
	',
	
	'journalauthors.authors_head' => '
		<h1 class="dashboard-title withoutBorder">' . getstr('pjs.journal_authors') . '</h1>
		<div style="margin: 10px;">
	',
	'journalauthors.authors_startrs' => '
			{_displayFilterCriteria(0, 0, author_letter, byaffiliation)}
			<p style="font-size: 14px;">' . getstr('pjs.refine_filters_in_the_left_panel') . '</p>
			{nav}
			<div class="P-Clear"></div>
	',
	'journalauthors.authors_row' => '
			<div class="authorInfoHolder">
				{_getProfilePicWithLink(previewpicid, journal_id, id, 1)}
				<div style="float: left; margin-left: 10px; width: 65%;">
					<a href="/browse_journal_articles_by_author.php?journal_id={journal_id}&user_id={id}" class="green" style="line-height: 24px;">{author_names}</a><br/>
					<div class="greenDesc">{affiliation}</div>
				</div>
			</div>
	',
	'journalauthors.authors_endrs' => '
			<div class="P-Clear"></div>
			{nav}
	',
	'journalauthors.authors_foot' => '
		</div>
	',
	'journalauthors.authors_empty' => ''
	
	/*
	
	'journalissue.special_list_head' => '
		<h1 class="dashboard-title">' . getstr('pjs.special_issues') . '</h1>
	',
	
	'journalissue.special_list_startrs' => '
		<table width="100%">
	',
	
	'journalissue.special_list_row' => '
			<tr>
				<td align="left" class="P-Green-Title">
					{title}
				</td>
			</tr>
			<tr>
				<td class="P-Description-Txt" style="padding-bottom: 10px;">
					{description}
					<br/>
					{_getIssueEditors(special_issue_editors)}
				</td>
			</tr>
	',
	
	'journalissue.special_list_endrs' => '
		</table>
	',
	
	'journalissue.special_list_foot' => '',
	
	'journalissue.list_empty' => '<br/>' . getstr('pjs.no_issues') . '<br/>',*/
);
?>
