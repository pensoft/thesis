<?php

$gTemplArr = array(	
	'dashboard.htmlstartcontent' => '
			{*global.htmlonlyheader}
				<div class="P-Wrapper P-Without-Bread-Crumbs">
					{*global.header}
					<div class="P-Wrapper-Container">
	',

	'dashboard.content' => '
				<div class="P-Wrapper-Container-Middle Dashboar-Container-Middle">
					<div class="P-Add-New-Manuscript-Btn-Holder">
						<span>
							<div class="P-Green-Btn-Holder" onclick="window.location=\'create_document.php\'">
								<div class="P-Green-Btn-Left"></div>
								<div class="P-Green-Btn-Middle">' . getstr('dashboard.write_new_manuscript') . '</div>
								<div class="P-Green-Btn-Right"></div>
							</div>
						</span>
						<div class="P-Clear"></div>
					</div>
					<div class="P-Content-Dashboard-Holder">
						
						
						{content}
					</div>
					<div class="P-Clear"></div>
				</div>
					
					{activity}
					
				<div class="P-Clear"></div>
	',
	
	'dashboard.content_head' => '
						<div class="P-Section-Title-Holder">
							{_showDashboardAdminFilter(showall)}
						</div>
	',
	
	'dashboard.activity_head' => '
				<div class="P-Wrapper-Container-Right P-Dashboard-Container-Right">
					<div class="P-Activity-Fieed-Wrapper">
						<div class="P-Activity-Fieed-Title">
							<table class="P-Data-Resources-Head" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<td class="P-Data-Resources-Head-Text">' . getstr('dashboard.activityFeed') . ':</td>
										<td class="P-Inline-Line"></td>
									</tr>
								</tbody>
							</table>
						</div>
	',

	'dashboard.activity_row' => '
						<div class="P-Activity-Fieed-Item">
							{_showUserPic(photo_id)}
							<div class="P-Activity-Fieed-Item-Details">
								<div class="P-Username">{editedbyuser}</div>
								<div class="P-Date">{_showFormatedPubDate(editdate, ,1)}</div>
								<div class="P-Clear"></div>
								<div class="P-Activity-Fieed-Content"><span>{activity_type}&nbsp;</span><a href="/display_document.php?document_id={document_id}">{_strim(name)}</a></div>
								<div class="P-Clear"></div>
							</div>
							<div class="P-Clear"></div>
						</div>
	',
	
	'dashboard.activity_foot' => '
						{_getMoreActivity(records)}	
						</div>
				</div><!-- End P-Wrapper-Container-Right -->
	',
	
	'dashboard.content_row' => '
						<div class="P-Content-Dashboard-Row {_displayClassByDocumentStatus(documentstatus, is_locked)}">
							<div class="P-Content-Dashboard-Row-Left">
								<div class="P-Content-Dashboard-Row-Names">{fullname}</div>
								<div class="P-Content-Dashboard-Row-Title">
									<a href="/preview.php?document_id={document_id}" target="_blank">{_strim(name)}</a>
									<div class="P-Clear"></div>
									{_displayEditedByRow(is_locked, editedbyuser, editedbyuserid)}
									<div class="papertype">{papertype}. Started: {createdate}. Last revision: {lastdate}.</div>
									{_displayDeleteDocumentBtn(document_creator, document_id, is_locked, documentstatus)}
									<div class="P-Clear"></div>
								</div>
								<div class="P-Clear"></div>
							</div>
							<div class="P-Content-Dashboard-Row-Right">
								<div class="P-Content-Dashboard-Row-Status">{_getCurrentDocumentStatus(documentstatus, is_locked, editedbyuserid)}</div>
								<div class="P-Content-Dashboard-Row-History">
									<div class="P-Content-Dashboard-Row-History-Icon-Holder">
										<div class="P-Icon-Clock"></div>
									</div>
									<div class="P-Content-Dashboard-Row-History-Link"><a href="/preview_revisions.php?document_id={document_id}">' . getstr('dashboard.revision_history') . '</a></div>
								</div>
								<div class="P-Clear"></div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
	',
	
	'dashboard.content_foot' => '
						<!-- <div class="P-See-Inactive-Btn-Holder">
							<div class="P-Grey-Btn-Holder" onclick="">
								<div class="P-Grey-Btn-Left"></div>
								<div class="P-Grey-Btn-Middle">' . getstr('dashboard.see_inactive_manuscripts') . ' (4)</div>
								<div class="P-Grey-Btn-Right"></div>
							</div>
							<div class="P-Clear"></div>
						</div>						
						<div class="P-Clear"></div> -->
						<div class="P-VSpace-20"></div>
						<div class="P-Paging">
						<ul>
							{nav}
							</ul>
						</div>
	',
	
	'dashboard.no_manuscripts' => '
		<div class="P-Empty-Content">' . getstr('pwt.dashboard.thereHasNoActiveManuscripts') . '</div>
	',
	
	'dashboard.activity_empty' => '
		<div class="P-Empty-Content">' . getstr('pwt.dashboard.thereHasNoActivity') . '</div>
	',
);
?>