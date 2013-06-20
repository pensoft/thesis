<?php

$gTemplArr = array(
	'journalissue.sidebar_left_browse_issues' => '
				<div id="leftSider">
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.browse') . '</h3>
						<div class="P-Clear"></div>
						<div class="siderBlockLinksHolder">
							<a class="link" href="/browse_journal_articles.php?journal_id={journal_id}">
								<span></span>
								<span class="content">' . getstr('pjs.articles') . '</span>
							</a>
							<a class="link active" href="/browse_journal_issues.php?journal_id={journal_id}">
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
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.filter') . '</h3>
						<div class="P-Clear"></div>
						<div class="filterBlock">
							<div class="filterBlockTitle">' . getstr('pjs.byyear') . '</div>
							<div class="filterBlockContent">
								<input id="issue_year_input" type="hidden" name="issue_year" value="{_intThis(issue_year)}" />
								<a href="/browse_journal_issues.php?journal_id={journal_id}&special_issues={_intThis(special_issues)}" class="green">All</a> {_getLastFiveYears(journal_id, special_issues, 5)}
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="filterBlock">
							<div class="filterBlockTitle">' . getstr('pjs.byspecialissues') . '</div>
							<div class="filterBlockContent">
								<input onclick="filterIssues(this, {journal_id}, \'issue_year_input\');" type="checkbox" name="special_issues" value="1" {_isChecked(special_issues)} /> <span>' . getstr('pjs.show_only_special_issues') . '</span>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Grey-Btn-Holder P-Clear-Filter">
							<div class="P-Grey-Btn-Left"></div>
							<a class="P-Grey-Btn-Middle" href="/browse_journal_issues.php?journal_id={journal_id}">' . getstr('pjs.clear_filters') . '</a>
							<div class="P-Grey-Btn-Right"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.view_issue') . '</h3>
						<div class="P-Clear"></div>
						
						<div class="issueNoHolder">
							<div class="issueNoLabel">' . getstr('pjs.issueno') . '</div>
							<form name="filter_by_issue_no" method="post" action="/browse_journal_issues.php" enctype="multipart/form-data">
								<input type="hidden" id="go_to_issue_input" name="journal_id" value="{journal_id}" />
								<div class="floatLeft fieldHolder">
									<input style="width: 40px;" type="text" id="go_to_issue_input" name="issue_volume" />
								</div>
								<div class="P-Grey-Btn-Holder P-Go">
									<div class="P-Grey-Btn-Left"></div>
									<div class="P-Grey-Btn-Middle"><input type="submit" name="submit" value="GO" /></div>
									<div class="P-Grey-Btn-Right"></div>
								</div>
								<div class="issueFromTo">' . getstr('pjs.from') . ' {_intThis(min_volume)} ' . getstr('pjs.to') . ' {_intThis(max_volume)}</div>
							</form>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
				</div>
	',
	
	'journalissue.sidebar_left_issues' => '
				<div id="leftSider">
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.issue_summary') . '</h3>
						<div class="P-Clear"></div>
						<div id="book">
							<div class="floatLeft">
								<img src="i/book.png" alt="book"></img>
								<div class="P-Clear"></div>
							</div>
							<div id="bookInfo">
								<p>
									' . getstr('pjs.papers_published') . ': <span>{_intThis(count_documents)}</span>
								</p>
								<p>
									' . getstr('pjs.total_pages') . ': <span>{_intThis(count_pages)}</span>
								</p>
								<p>
									' . getstr('pjs.color_pages') . ': <span>{_intThis(count_color_pages)}</span>
								</p>
								<p>
									' . getstr('pjs.printed_version') . ': <span>Paperback</span>
								</p>
								<img src="i/openAccess.png" alt="open access" style="margin-left: 10px; margin-top: 25px;"></img>
								<div id="price">
									<div class="floatLeft">&euro; {issue_price}</div><img class="floatLeft" src="i/cart.png" alt="cart"></img>
									<span class="floatLeft">' . getstr('pjs.order_reprint') . '</span>
									<div class="P-Clear"></div>
								</div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Clear"></div>
					<div class="leftSiderBlock bigBlock">
						<h3>' . getstr('pjs.view_issue') . '</h3>
						<div class="P-Clear"></div>
						
						<div id="issue">
							<div class="pagesWrapper">
								<div class="floatLeft">
									{_getPrevIssueBtn(journal_id, prev_issue_id)}
								</div>
								<div class="floatLeft">
									<div class="issueNo">Issue {issue_num}</div>
								</div>
								<div class="floatLeft">
									{_getNextIssueBtn(journal_id, next_issue_id)}
								</div>
							</div>
							<div class="P-Clear"></div>
							<div class="issueNoHolder">
								<div class="issueNoLabel">Issue No</div>
								<input type="hidden" id="go_to_issue_input" name="journal_id" value="1">
								<div class="floatLeft fieldHolder">
									<input style="width: 40px;" type="text" name="goToIssue" id="go_to_issue_input"></input>
								</div>
								<div class="P-Grey-Btn-Holder P-Go">
									<div class="P-Grey-Btn-Left"></div>
									<a class="P-Grey-Btn-Middle" href="javascript: goToIssue({journal_id}, \'go_to_issue_input\');">GO</a>
									<div class="P-Grey-Btn-Right"></div>
								</div>
								<div class="issueFromTo">from {_intThis(min_issue_num)} to {_intThis(max_issue_num)}</div>
							</div>
						</div>
						<div class="P-Clear"></div>
					</div>
				</div>
	',

	'journalissue.edit_journal_issue_form' => '
	<h1 class="dashboard-title">' . getstr('pjs.manage_journal_issues') . '</h1>
	<div class="leftMar10">
		<div class="P-Left-Col-Fields">
			{~}{~~}{journal_id}{issue_id}{previewpicid}
			<div class="input-reg-title">{*volume}</div>
			<div class="fieldHolder">
				{volume}
			</div>
			<div class="input-reg-title">{*number}</div>
			<div class="fieldHolder">
				{number}
			</div>
			<div class="input-reg-title">{*year}</div>
			<div class="fieldHolder">
				{year}
			</div>
			<div class="input-reg-title">{*is_regular_issue}</div>
			<div class="P-User-Type-Radios">{is_regular_issue}</div><br /><br />
			<div class="P-User-Type-Radios">{is_active}</div>
			<div class="clear"></div>
			<div class="input-reg-title">{*special_issue_editors}</div>
			<div class="fieldHolder">
				{special_issue_editors}
			</div>
			<div class="input-reg-title">{*title}</div>
			<div class="fieldHolder">
				{title}
			</div>
			<div class="input-reg-title">{*description}</div>
			<div class="fieldHolder">
				{description}
			</div>
			<div class="input-reg-title">{*previewpic}</div>
			<div class="fieldHolder">
			<input type="text" class="fileInput" />
			{previewpic}
			<div class="P-Grey-Btn-Holder P-Reg-Btn fileUpload">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle fileUpload">Browse</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
			<div class="clear"></div>
			<script type="text/javascript">
				$(function(){
					var lBrowseBtn = $(\'.fileUpload\')
					lBrowseBtn.click(function(){
						$lFileInput = $(\'#fileInput\');
						$(this).parent().find(\'input[type="file"]\').trigger(\'click\');
						// $(\'#fileInput\').trigger(\'click\');
						$(\'#fileInput\').change(function(){
							var fieldVal = $(\'#fileInput\').val();
							$(\'.fileInput\').attr("value" , fieldVal);
						});
					});
				})
			</script>
			</div>
			{_displayOriginalPic(previewpicid)}
			
			<div class="input-reg-title">{*cover_caption}</div>
			<div class="fieldHolder">
				{cover_caption}
			</div>
			<div class="input-reg-title">{*price}</div>
			<div class="fieldHolder">
				{price}
			</div>
			<div class="P-Grey-Btn-Holder P-Reg-Btn">
				<div class="P-Grey-Btn-Left"></div>
				<div class="P-Grey-Btn-Middle">{save}</div>
				<div class="P-Grey-Btn-Right"></div>
			</div>
		</div>
	</div>
	<div class="clear"></div>
	',
	
	'journalissue.list_head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_future_issues') . '</h1>
	',
	
	'journalissue.list_back_head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_back_issues') . '</h1>
	',
	'journalissue.list_startrs' => '
		<table class="dashboard">
			<tr>
				<th class="left">' . getstr('pjs.title') . '</th>
				<th class="left">' . getstr('pjs.items') . '</th>
				<th class="left">' . getstr('pjs.contents') . '</th>
				<th class="left">' . getstr('pjs.delete') . '</th>
				<th class="left">' . getstr('pjs.edit') . '</th>
				<th class="left"></th>
			</tr>',
	'journalissue.list_row'=>'
			<tr>
				<td class="left">{journal_name} {number} {_getIssueYear(year)} {name}</td>
				<td class="left">{_intThis(count_documents)}</td>
				<td class="left"><a href="edit_issue_documents?issue_id={id}">Contents</a></td>
				<td class="left"><a onclick="confirmDelete(\'' . getstr('pjs.are_you_sure') . '\', \'/edit_journal_issue.php?journal_id={journal_id}&amp;issue_id={id}&amp;back_issue={back_issue}&amp;tAction=delete\')" href="javascript: void(0);">' . getstr('pjs.delete') . '</a></td>
				<td class="left"><a href="/edit_journal_issue?issue_id={id}&amp;back_issue={back_issue}&amp;tAction=showedit">' . getstr('pjs.edit') . '</a></td>
				<td class="left">{_getChangeStateBtn(ispublished, journal_id, id, back_issue, iscurrent)}</td>
			</tr>
	',
	'journalissue.list_endrs'=>'
		</table>
		<div class="leftPad10">
			<div class="submitLink">
				<a href="/edit_journal_issue.php?journal_id={journal_id}&amp;back_issue={back_issue}&amp;tAction=showedit">' . getstr('pjs.create_issue') . '</a>
			</div>
		</div>
	',
	
	'journalissue.list_foot'=>'',
	'journalissue.list_empty' =>'' . getstr('pjs.no_issues') . '.',
	
	
	'journalissue.edit_document_head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_issue_documents') . '</h1>
	',
	'journalissue.edit_document_startrs' => '
				<table class="dashboard">
					<tr>
						<th class="left" colspan="2">' . getstr('pjs.order') . '</th>
						<th class="left">' . getstr('pjs.section') . '</th>
						<th class="left">' . getstr('pjs.author') . '</th>
						<th class="left">' . getstr('pjs.title') . '</th>
						<th class="left">' . getstr('pjs.Remove') . '</th>
						<th class="left">' . getstr('pjs.pages') . '</th>
					</tr>
	',
	'journalissue.edit_document_row' => '
					<tr>
						<td class="left">
							<a href="/edit_issue_documents.php?journal_id={journal_id}&amp;issue_id={issue_id}&amp;document_id={id}&amp;direction=1&amp;tAction=move" class="journalArticleAction {_hideIfFirst(rownum)}">&uarr;</a>
						</td>
						<td class="left">
							<a href="/edit_issue_documents.php?journal_id={journal_id}&amp;issue_id={issue_id}&amp;document_id={id}&amp;direction=2&amp;tAction=move" class="journalArticleAction {_hideIfLast(rownum, records)}">&darr;</a>
						</td>
						<td class="left">{journal_section_abbreviation}</td>
						<td class="left">{submitting_author}</td>
						<td class="left">{name}</td>
						<td class="left"><a onclick="confirmDelete(\'' . getstr('pjs.deleteSectionConfirm') . '\', \'/edit_issue_documents.php?journal_id={journal_id}&amp;issue_id={issue_id}&amp;document_id={id}&amp;tAction=remove\');" href="javascript:void(0);">' . getstr('pjs.remove') . '</a></td>
						<td class="left">
							<form name="document_form" action="edit_issue_documents.php" method="post" enctype="multipart/form-data">
								<div class="editDocument">
									<input type="hidden" name="issue_id" value="{issue_id}"></input>
									<input type="hidden" name="journal_id" value="{journal_id}"></input>
									<input type="hidden" name="document_id" value="{id}"></input>
									<input type="text" name="range_start" value="{start_page}"></input>
									<input type="text" name="range_end" value="{end_page}"></input>
									<input type="text" name="color_pages" value="{number_of_color_pages}"></input>
									<input type="submit" name="tAction" value="save"></input>
								</div>
							</form>
						</td>
					</tr>
	',
	'journalissue.edit_document_endrs' => '
				</table>
	',
	'journalissue.edit_document_foot' => '',
	
	
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
	
	'journalissue.list_empty' => '<br/>' . getstr('pjs.no_issues') . '<br/>',
);
?>
