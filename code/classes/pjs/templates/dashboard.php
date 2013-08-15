<?php
// @formatter->off
$gTemplArr = array(

'dashboard.authors.all' => '<span class="authors"><a href="mailto:{submitter_email}" title="'.getstr('pjs.dashboards.EmailSubmittingAuthor').'">{submitter_name}</a>{_comma_if(authors)}</span>',
'pjs.doi' => '<a href="http://dx.doi.org/{doi}" target="_blank">{doi}</a>',
'pjs.submission'  => '<a href="view_document.php?id={id}&amp;view_role={role_id}" class="submission" title="'.getstr('pjs.dashboards.ViewSubmissionDetails').'" target="_blank">{title}</a>',
'pjs.review-type' => '<img src="/i/review_type{review_num}.png" alt="{review_type}" title="{review_type}" />',
'pjs.dashboard.journal' => '<abbr title="{journal_full}">{journal_short}</abbr>',

'dashboard.HEADER' => '<h1 class="dashboard-title">{_getstr(viewmode_title)}</h1>',
'dashboard.endtable' => '								</table>',
'dashboard.NODATA' => '<p>{_getstr(viewmode_no_data)}</p>',


//All current tasks
'dashboard.YourTasks.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.YourRole') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th></tr>',
'dashboard.YourTasks.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{yourrole}</td>
  <td>{_getstr(action)}</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td></tr>',

//Author Pending
'dashboard.AuthorPending.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Status') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th></tr>',
'dashboard.AuthorPending.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{_action(action, submitter_email, submitter_name, late)}</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td></tr>',

//Author Published
'dashboard.AuthorPublished.HEADER' => '<h1 class="dashboard-title">' . getstr('pjs.dashboards.a.PublishedArticles').'</h1>',
'dashboard.AuthorPublished.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.ArticleType') . '</th>
  <th>' . getstr('pjs.dashboards.PublicationDate') . '</th>
  <th>' . getstr('pjs.dashboards.IssueType') . '</th>
  <th>' . getstr('pjs.dashboards.IssueNumber') . '</th>
  <th>' . getstr('pjs.dashboards.DOI') . '</th></tr>',
'dashboard.AuthorPublished.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{articletype}</td>
  <td>{publicationdate}</td>
  <td>{_issue(issuetype)}</td>
  <td>{issuenumber}</td>
  <td>{*pjs.doi}</td></tr>',
'dashboard.AuthorPublished.ENDRS' => '</table>',
'dashboard.AuthorPublished.NODATA' => '<p>' .getstr('pjs.dashboard.nodata.AuthorPublished'). '</p>',

//Author Rejected
'dashboard.AuthorRejected.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.EditorialDecision') . '</th>
  <th>' . getstr('pjs.dashboards.Date') . '</th></tr>',
'dashboard.AuthorRejected.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{editorialdecision}</td>
  <td>{date}</td></tr>',

//Author Incomplete
'dashboard.AuthorIncomplete.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Date') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th></tr>',
'dashboard.AuthorIncomplete.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{date}</td>
  <td><a href="view_document.php?id={id}&amp;view_role={role_id}"  target="_blank">'.getstr('pjs.dashboards.CompleteSubmission') . '</a></td></tr>',

//Subject Editor In Review
'dashboard.SubjectEditorInReview.STARTRS' => '
<script type="text/javascript">
  var style = document.createElement("style");
  document.getElementsByTagName("head")[0].appendChild(style);
  var s = document.styleSheets[document.styleSheets.length - 1];
</script>
<table class="dashboard"><tbody><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewType') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewRound') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th>
  <th><img src="/i/reminders.png" alt="' . getstr('pjs.dashboards.Remind') . '" title="' . getstr('pjs.dashboards.Remind') . '" /></th>
  </tr><tbody>',
'dashboard.SubjectEditorInReview.ROWTEMPL' => 
'<tbody>
<tr>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}">{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="center">{*pjs.review-type}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="center">{reviewround}</td>
  
  {_merge_cells(action, who, schedule, days, late, remind)}
  
  </tr>
  </tbody>',

//Subject Editor In Production
'dashboard.SubjectEditorInProduction.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Status') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th></tr>',
'dashboard.SubjectEditorInProduction.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{status}</td>
  <td class="{late}">{_action(action, submitter_email, submitter_name, late)}</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td></tr>',

//Subject Editor Published
'dashboard.SubjectEditorPublished.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.ArticleType') . '</th>
  <th>' . getstr('pjs.dashboards.PublicationDate') . '</th>
  <th>' . getstr('pjs.dashboards.IssueType') . '</th>
  <th>' . getstr('pjs.dashboards.IssueNumber') . '</th>
  <th>' . getstr('pjs.dashboards.DOI') . '</th></tr>',
'dashboard.SubjectEditorPublished.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{articletype}</td>
  <td>{publicationdate}</td>
  <td>{_issue(issuetype)}</td>
  <td>{issuenumber}</td>
  <td>{*pjs.doi}</td></tr>',

//Subject Editor Rejected
'dashboard.SubjectEditorRejected.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.EditorialDecision') . '</th>
  <th>' . getstr('pjs.dashboards.Date') . '</th></tr>',
'dashboard.SubjectEditorRejected.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{editorialdecision}</td>
  <td>{date}</td></tr>',

//Reviewer Requests
/*
'dashboard.ReviewerRequests.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.ReviewerType') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewRound') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th></tr>',
'dashboard.ReviewerRequests.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{reviewertype}</td>
  <td class="center">{reviewround}</td>
  <td>'.getstr('pjs.dashboards.actions.respond2request').'</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td></tr>',
*/

//Reviewer Pending
'dashboard.ReviewerPending.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.ReviewerType') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewRound') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th></tr>',
'dashboard.ReviewerPending.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{reviewertype}</td>
  <td class="center 1">{reviewround}</td>
  <td class="{late}">{_getstr(action)}</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td></tr>',

//Reviewer Archived
'dashboard.ReviewerArchived.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.ReviewerType') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewRound') . '</th>
  <th>' . getstr('pjs.dashboards.YouSaid') . '</th>
  <th>' . getstr('pjs.dashboards.EditorialDecision') . '</th>
  <th>' . getstr('pjs.dashboards.FinalDecision') . '</th></tr>',
'dashboard.ReviewerArchived.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{reviewertype}</td>
  <td class="center">{reviewround}</td>
  <td>{yousaid}</td>
  <td>{editorialdecision}</td>
  <td>{finaldecision}</td></tr>',

//Editor Active
'dashboard.EditorPending.STARTRS' => '
<script type="text/javascript">
  var style = document.createElement("style");
  document.getElementsByTagName("head")[0].appendChild(style);
  var s = document.styleSheets[document.styleSheets.length - 1];
</script>
<table class="dashboard"><tbody><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewType') . '</th>
  <th>' . getstr('pjs.dashboards.Status') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th>
  <th><img src="/i/reminders.png" alt="' . getstr('pjs.dashboards.Remind') . '" title="' . getstr('pjs.dashboards.Remind') . '" /></th></tr>
</tbody>  ',
'dashboard.EditorPending.ROWTEMPL' => '
<tbody>
<tr>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}">{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="center">{*pjs.review-type}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="center">{status}</td>
  {_merge_cells(action, who, schedule, days, late, remind)}
  </tr>
</tbody>
  ',

//Editor Unassigned
'dashboard.EditorUnassigned.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewType') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="right">' . getstr('pjs.dashboards.Days') . '</th>
  <th><img src="/i/reminders.png" alt="' . getstr('pjs.dashboards.Remind') . '" title="' . getstr('pjs.dashboards.Remind') . '" /></th></tr>',
'dashboard.EditorUnassigned.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td class="center">{*pjs.review-type}</td>
  <td class="{late}">'.getstr('pjs.dashboards.AssignSE').'</td>
  <td class="{late}">'.getstr('pjs.dashboards.EditorialOffice').'</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td>
  <td>{remind}</td></tr>',

//Editor In Review
'dashboard.EditorInReview.STARTRS' => '
<script type="text/javascript">
  var style = document.createElement("style");
  document.getElementsByTagName("head")[0].appendChild(style);
  var s = document.styleSheets[document.styleSheets.length - 1];
</script>
<table class="dashboard"><tbody><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewType') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ReviewRound') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th>
  <th><img src="/i/reminders.png" alt="' . getstr('pjs.dashboards.Remind') . '" title="' . getstr('pjs.dashboards.Remind') . '" /></th>
  </tr></tbody>',
'dashboard.EditorInReview.ROWTEMPL' => '
<tbody>
<tr>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}">{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="center">{*pjs.review-type}</td>
  <td onmouseover="javascript:s.insertRule(rule, 0)" onmouseout="javascript:s.deleteRule(0)" rowspan="{_count(who)}" class="center">{reviewround}</td>
  
  {_merge_cells(action, who, schedule, days, late, remind)}
  
  </tr>
  </tbody>
  ',

//Editor In Copy Edit
'dashboard.EditorInCopyEdit.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th>
  <th><img src="/i/reminders.png" alt="' . getstr('pjs.dashboards.Remind') . '" title="' . getstr('pjs.dashboards.Remind') . '" /></th></tr>',
'dashboard.EditorInCopyEdit.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td class="{late}">{_action(action, submitter_email, submitter_name, late)}</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td>
  <td>{remind}</td></tr>',

//Editor In Layout
'dashboard.EditorInLayout.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.ForPublicationIn') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th>
  <th><img src="/i/reminders.png" alt="' . getstr('pjs.dashboards.Remind') . '" title="' . getstr('pjs.dashboards.Remind') . '" /></th></tr>',
'dashboard.EditorInLayout.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{_issue(forpublicationin)}</td>
  <td class="{late}">{_action(action, submitter_email, submitter_name, late)}</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td>
  <td>{remind}</td></tr>',

//Editor Ready For Publishing
'dashboard.EditorReadyForPublishing.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.ArticleType') . '</th>
  <th>' . getstr('pjs.dashboards.DateApproved') . '</th>
  <th>' . getstr('pjs.dashboards.Pages') . '</th>
  <th>' . getstr('pjs.dashboards.ForPublicationIn') . '</th>
  <th>' . getstr('pjs.dashboards.IssueNumber') . '</th></tr>',
'dashboard.EditorReadyForPublishing.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{articletype}</td>
  <td>{dateapproved}</td>
  <td>{pages}</td>
  <td>{_issue(issuetype)}</td>
  <td>{issuenumber}</td></tr>',

//Editor Published
'dashboard.EditorPublished.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.ArticleType') . '</th>
  <th>' . getstr('pjs.dashboards.PublicationDate') . '</th>
  <th>' . getstr('pjs.dashboards.IssueType') . '</th>
  <th>' . getstr('pjs.dashboards.IssueNumber') . '</th>
  <th>' . getstr('pjs.dashboards.DOI') . '</th></tr>',
'dashboard.EditorPublished.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{articletype}</td>
  <td>{publicationdate}</td>
  <td>{_issue(issuetype)}</td>
  <td>{issuenumber}</td>
  <td>{*pjs.doi}</td></tr>',

//Editor Rejected
'dashboard.EditorRejected.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.EditorialDecision') . '</th>
  <th>' . getstr('pjs.dashboards.Date') . '</th></tr>',
'dashboard.EditorRejected.ROWTEMPL' => '<tr>
  <td class="id right">{id}<br />{_editor_notes(editor_notes)}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{editorialdecision}</td>
  <td>{date}</td></tr>',

//Layout Editor Pending
'dashboard.LayoutEditorPending.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Journal') . '</th>
  <th>' . getstr('pjs.dashboards.ForPublicationIn') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th>
  <th><img src="/i/reminders.png" alt="' . getstr('pjs.dashboards.Remind') . '" title="' . getstr('pjs.dashboards.Remind') . '" /></th></tr>',
'dashboard.LayoutEditorPending.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{*pjs.dashboard.journal}</td>
  <td>{_issue(issuetype)}</td>
  <td class="{late}">{_action(action, submitter_email, submitter_name, late)}</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td>
  <td>{remind}</td></tr>',

//Layout Editor Ready For Publishing
'dashboard.LayoutEditorReadyForPublishing.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Journal') . '</th>
  <th>' . getstr('pjs.dashboards.ArticleType') . '</th>
  <th>' . getstr('pjs.dashboards.DateApproved') . '</th>
  <th>' . getstr('pjs.dashboards.Pages') . '</th>
  <th>' . getstr('pjs.dashboards.ForPublicationIn') . '</th>
  <th>' . getstr('pjs.dashboards.IssueNumber') . '</th></tr>',
'dashboard.LayoutEditorReadyForPublishing.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{*pjs.dashboard.journal}</td>
  <td>{articletype}</td>
  <td>{dateapproved}</td>
  <td>{pages}</td>
  <td>{_issue(issuetype)}</td>
  <td>{issuenumber}</td></tr>',

//Layout Editor Published
'dashboard.LayoutEditorPublished.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Journal') . '</th>
  <th>' . getstr('pjs.dashboards.ArticleType') . '</th>
  <th>' . getstr('pjs.dashboards.PublicationDate') . '</th>
  <th>' . getstr('pjs.dashboards.IssueType') . '</th>
  <th>' . getstr('pjs.dashboards.IssueNumber') . '</th>
  <th>' . getstr('pjs.dashboards.DOI') . '</th></tr>',
'dashboard.LayoutEditorPublished.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{*pjs.dashboard.journal}</td>
  <td>{articletype}</td>
  <td>{publicationdate}</td>
  <td>{_issue(issuetype)}</td>
  <td>{issuenumber}</td>
  <td>{*pjs.doi}</td></tr>',

//Layout Editor Statistics
'dashboard.LayoutEditorStatistics.STARTRS' => '<table class="dashboard"><tr>
  <th class="right">' . getstr('pjs.dashboards.Journal') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ArticlesLayout') . '</th>
  <th class="center">' . getstr('pjs.dashboards.PagesLayout') . '</th>
  <th class="center">' . getstr('pjs.dashboards.ArticlesPublished') . '</th>
  <th class="center">' . getstr('pjs.dashboards.PagesPublished') . '</th></tr>',
'dashboard.LayoutEditorStatistics.ROWTEMPL' => '<tr>
  <td class="right">{*pjs.dashboard.journal}</td>
  <td class="center">{articles_laidout}</td>
  <td class="center">{pages_laidout}</td>
  <td class="center">{articles_published}</td>
  <td class="center">{pages_published}</td></tr>',

//Copy Editor Pending
'dashboard.CopyEditorPending.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Action') . '</th>
  <th>' . getstr('pjs.dashboards.Who') . '</th>
  <th>' . getstr('pjs.dashboards.Schedule') . '</th>
  <th class="days">' . getstr('pjs.dashboards.Days') . '</th></tr>',
'dashboard.CopyEditorPending.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td class="{late}">{_action(action, submitter_email, submitter_name, late)}</td>
  <td class="{late}">{_getstr(schedule)}</td>
  <td class="days {late}">{_getstr(days)}</td></tr>',

//Copy Editor Archived
'dashboard.CopyEditorArchived.STARTRS' => '<table class="dashboard"><tr>
  <th class="center">' . getstr('pjs.dashboards.ID') . '</th>
  <th>' . getstr('pjs.dashboards.TitleAuthors') . '</th>
  <th>' . getstr('pjs.dashboards.Status') . '</th>
  <th>' . getstr('pjs.dashboards.DOI') . '</th></tr>',
'dashboard.CopyEditorArchived.ROWTEMPL' => '<tr>
  <td class="id right">{id}</td>
  <td>{*pjs.submission}<br />{*dashboard.authors.all}</td>
  <td>{_getstr(status)}</td>
  <td>{*pjs.doi}</td></tr>',



'dashboard.storyListRow' => '<div><a href="/view_document.php?id={id}&amp;view_role={view_role}">{name}</a></div>',

'dashboard.storyListIncompleteRow' => '<div><a href="/document_pwt_permissions.php?document_id={id}">{name}</a></div>',

'dashboard.leftcol' => '
				<div id="dashboard-menu">
					{your_tasks}
					{journal_manager}
					{journal_editor}
					{se}
					{author}
					{dedicated_reviewer}
					{le}
					{ce}					
				</div>
',
'dashboard.your_task_link' => '/dashboard.php?journal_id=".{journal_id}."&amp;view_mode=' . DASHBOARD_YOUR_TASKS_VIEWMODE . '',

'dashboard.your_tasks_leftcol' => '
	<h3 id="mytasks">'.getstr('pjs.dashboards.MyTasks').'</h3>
	<table class="crazy">
	<tr><td><a href="/dashboard.php?journal_id={journal_id}&amp;view_mode=' . DASHBOARD_YOUR_TASKS_VIEWMODE . '">Pending</a></td>
		<td class="right">---</td></tr>
	</table>
',
'dashboard.left.STARTRS' => '<h3>{_getstr(header)}</h3>
								<table class="crazy">
',
'dashboard.left.ROWTEMPL' => '									<tr><td><a href="{href}">{text}</a></td><td class="right"><a href="{href2}">{text2}</a></td></tr>
',
);

?>