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
							<div class="filterBlockContent">
								<input id="author_first_letter" type="hidden" name="author_letter" value="{author_letter}" />
								<a href="javascript: filterAuthorsLetter(\'affiliation_input\', {journal_id}, \'\')" class="green letter">All</a><span style="color: #b0ada2;">&nbsp;&nbsp;|&nbsp;</span>
								<span class="lettersHolder">
									{_getLetters(journal_id, \'affiliation_input\')}
								</span>
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
						<!--
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
						-->
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
			
			{nav}
			<div class="P-Clear"></div>
	',
	//<p style="font-size: 14px;">' . getstr('pjs.refine_filters_in_the_left_panel') . '</p>
	'journalauthors.authors_row' => '
			{_setAuthorRowOpenDiv(rownum, records)}
			<div class="authorInfoHolder">
				{_getProfilePicWithLink(previewpicid, journal_id, id, 1)}
				<div style="float: left; margin-left: 10px; width: 65%;">
					<a href="/browse_journal_articles_by_author.php?journal_id={journal_id}&user_id={id}" class="green" style="line-height: 24px;">{author_names}</a><br/>
					{_showAdditionalAuthorInfo(affiliation, addr_city, usr_country, website)}
					<div class="greenDesc">
						More articles in:&nbsp;
						<span class="AOF-Author-more-link">
							<a target="_blank" href="http://search.labs.crossref.org/?q={first_name}+{last_name}">
								CrossRef
							</a>
						</span>
						&nbsp;|&nbsp;
						<span class="AOF-Author-more-link">
							<a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">
								PubMed
							</a>
						</span>
						&nbsp;|&nbsp;
						<span class="AOF-Author-more-link">
							<a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">
								Google Scholar
							</a>
						</span>
					</div>
				</div>
			</div>
			{_setAuthorRowCloseDiv(rownum, records)}
	',
	'journalauthors.authors_endrs' => '
			<div class="P-Clear"></div>
			{nav}
	',
	'journalauthors.authors_foot' => '
		</div>
	',
	'journalauthors.authors_empty' => '
			{_displayFilterCriteria(0, 0, author_letter, byaffiliation)}
			{nav}
			<div class="P-Clear"></div>
			<div class="textCenterAlign">No authors matching your criteria</div>
	'
	
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
