<?php

$gTemplArr = array(
	'journalgroups.edit_journal_groups_form' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_groups') . '</h1>
		<div class="leftMar10">
			<div class="P-Left-Col-Fields">
				{~}{~~}{journal_id}{id}
				<div class="input-reg-title">
					{*title}
				</div>
				<div class="fieldHolder">
					{title}
				</div>
				<div class="input-reg-title">
					{*description}
				</div>
				<div class="fieldHolder">
					{description}
				</div>
				<div class="input-reg-title">
					{*parentnode}
				</div>
				<div class="fieldHolder">
					{parentnode}
				</div>
				<div class="br"></div>
				<div class="br"></div>
				<div class="buttonsHolder clearMargin">
					<div class="P-Green-Btn-Holder clearMargin">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-80">{save}</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="br"></div>
		<div class="br"></div>
	',
	'journalgroups.browse_head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_groups') . '</h1>
	',
	
	'journalgroups.browse_startrs' => '
		<p>{_displayErrorIfExist(error)}</p>
		<table width="100%" class="dashboard">
			<tr>
				<th class="left">' . getstr('pjs.title') . '</th>
				<th class="left">' . getstr('pjs.description') . '</th>
				<th colspan="3">' . getstr('pjs.action') . '</th>
				
			</tr>
	',
	'journalgroups.browse_row' => '
		<tr>
			<td width="20%">{title}</td>
			<td class="left">{description}</td>
			<td  style="vertical-align: middle;">
				<a href="/edit_journal_group.php?journal_id={journal_id}&tAction=showedit&id={id}">' . getstr('pjs.edit') . '</a>
			</td>
			<td style="vertical-align: middle;">
				<a href="javascript: void(0);" onclick="confirmDelete(\'' . getstr('pjs.are_you_sure') . '\', \'/edit_journal_group.php?journal_id={journal_id}&tAction=delete&id={id}\'); return false;">' . getstr('pjs.delete') . '</a>
			</td>
			<td style="vertical-align: middle;">
				<a href="javascript: void(0);" class="journalArticleAction" onclick="moveGroupOrUserAjax(this, 1, {journal_id}, {id}, 1);">
					<div class="goup"><img src="/i/toparrow.png" alt="go up" /></div>
				</a>
				<a href="javascript: void(0);" class="journalArticleAction" onclick="moveGroupOrUserAjax(this, 1, {journal_id}, {id}, 0);">
					<div class="godown"><img src="/i/bottomarrow.png" alt="go down" class="vote" /></div>&nbsp;
				</a>
			</td>
		</tr>
	',
	'journalgroups.browse_endrs' => '
				</table>
	',
	'journalgroups.browse_foot' => '
				<div class="submitLink">
					<a class="leftMar10" href="/edit_journal_group.php?journal_id={journal_id}&amp;tAction=showedit"><img src="/i/addpage.png" alt="add new" />&nbsp;' . getstr('pjs.addNewGroup') . '</a>
				</div>
				<div class="clear"></div>
	',
	'journalgroups.browse_empty' => '<p style="color: #1f1f1f;">No groups in this journal.</p>',
	
	'journalgroups.browse_foot_users_list' => '
			<div class="reviewers_footer clearBorder leftMar10" style="padding: 0;">
			<div class="reviewers_footer_txt" style="padding: 25px 0 10px 0">{_getstr(pjs.addusertogroup)}</div>
			<div class="reviewers_footer_content" style="padding-left: 0px;">
				<div class="reviewers_footer_content_left">
					<div class="reviewers_footer_content_left_label">{_getstr(pjs.searchusers)}</div>
					<div class="reviewers_footer_content_left_inp_holder">
							<div class="fieldHolder" style="padding-right: 40px;">
								<input type="text" value="" name="reviewer_search" id="reviewer_search" />
								<div class="input-reg-title" style="float: right; margin: -35px -40px 0 0;">OR</div>
								<script type="text/javascript">
								//<![CDATA[
								$.ui.autocomplete.prototype._renderMenu = function(ul, items) {
								  var self = this;
								  ul.append("<table width=\"100%\"><tbody></tbody></table>");
								  $.each( items, function( index, item ) {
									self._renderItem( ul.find("table tbody"), item );
								  });
								};
								$.ui.autocomplete.prototype._renderItem = function ( table, row ) {															
									var TR =  $( "<tr></tr>" )
										.data( "item.autocomplete", row )
										.append( 
												"<td class=\"name\">&nbsp;&nbsp;&nbsp;" + row.name + "</td>" + 
												"<td class=\"affiliation\">" + row.affiliation + "</td>" +
												"<td class=\"affiliation\">" + row.email + "</td>" +
												"<td class=\'right\'><a href=\"javascript:void(0)\" onclick=\"ExecuteUserInvitation(1, \'/lib/ajax_srv/invite_users_to_group_ajax_srv.php' . '\', " + row.id + ");\">add to list&nbsp;</a></td>"
												) // ExecuteUserInvitation(pOper, pUrl, pUserId)
										.appendTo( table );
									return TR;
								};
								
								$(document).ready(function () {
									$("#reviewer_search").autocomplete({
										source: "' . SITE_URL . 'lib/ajax_srv/usr_autocomplete_srv.php?action=get_users",
										autoFocus: true,
										minLength: 3,
										select: function(){
											$("#subject_editor_search").val("");
											return false;
										}
										
									});
								});
								var lGrpId = $(\'#groupid\').val();
								if (lGrpId){
									$(\'.reviewers_footer\').css(\'display\', \'block\');
								} else {
									$(\'.reviewers_footer\').css(\'display\', \'none\');
								}
								//]]>
								</script>
							</div>
						<!--<div class="reviewers_footer_content_left_icon"></div>-->
						<div class="P-Clear"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="P-Grey-Btn-Holder P-Reg-Btn" style="margin-left: 10px; margin-top: 5px;">
			<div class="P-Grey-Btn-Left"></div>
			<div class="P-Grey-Btn-Middle"><a href="/create_user.php?journal_id={journal_id}">' . getstr('pjs.create_user') . '</a></div>
			<div class="P-Grey-Btn-Right"></div>
		</div>
		<div class="clear"></div>
	</div>
	',
	
	'journalgroups.browse_startrs_users_list' => '
		<table width="98%" class="reviewer_tbl leftMar10 groupusers" >
			<tr>
				<th class="left textLeftAlign">' . getstr('pjs.name') . '</th>
				<th class="left textLeftAlign">' . getstr('pjs.email') . '</th>
				<th class="left textLeftAlign">' . getstr('pjs.affiliation') . '</th>
				<th class="left textLeftAlign" colspan="2">&nbsp;&nbsp;' . getstr('pjs.role') . '</th>
				<th class="left textLeftAlign" colspan="3">' . getstr('pjs.action') . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
			</tr>
	',
	
	'journalgroups.browse_row_users_list' => '
		<tr>
			<td class="left">{fullname}</td>
			<td class="left">{email}</td>
			<td class="left">{affiliation}</td>
			<td class="left">
				<form action="">
					<div class="fieldHolder role" style="margin-top: -12px;">
						<input type="text" name="subtitle" id="{id}" value="{subtitle}"  />
					</div>
				</form>
			</td>
			<td>	
				<a href="javascript: void(0)" onclick="updateUserRole({journal_id}, {group_id}, {id}); return false;">
				' . getstr('pjs.update') . '</a>
			</td>
			<td class="right">
				
				<a href="javascript: void(0);" onclick="confirmDelete(\'' . getstr('pjs.are_you_sure') . '\', \'/lib/ajax_srv/invite_users_to_group_ajax_srv.php?group_id={group_id}&amp;user_id={id}&amp;oper=2\'); return false;">
				<img src="i/remove_reviewer.png" alt="' . getstr('pjs.removefromgroup') . '" title="' . getstr('pjs.removefromgroup') . '" /></a>
				

			</td>
			<td width="30"><a href="javascript: void(0);" class="journalArticleAction" onclick="moveGroupOrUserAjax(this, 2, {journal_id}, {id}, 1, {group_id});">
							<img src="/i/toparrow.png" alt="vote up" width="20" height="10" />
						</a></td>
			<td width="30"><a href="javascript: void(0);" class="journalArticleAction" onclick="moveGroupOrUserAjax(this, 2, {journal_id}, {id}, 0, {group_id});">
							<img src="/i/bottomarrow.png" alt="vote down"  width="20" height="10" class="vote" />
						</a></td>
		</tr>		
	',
	'journalgroups.no_users_data' => '
		<p class="noUsersInGroup"></p>
			<!-- No users in this journals group -->
	',
	
	'journalgroups.head' => '',
	
	'journalgroups.startrs' => '
		{_displayGroupName(grptitle, grpsubtitle)}
		<div class="leftMar10">
			{nav}
		<div class="P-Clear"></div>
	',
	'journalgroups.row' => '
			<div class="authorInfoHolder">
				{_getUserPictureIfExist(previewpicid, 1)}
				<div class="authorDesc">
					<h3>{fullname}</h3>
					{_render_if(subtitle, <p><em>, </em></p>)}
					<p>
					{_render_if(affiliation, , <br />)}
					{_comma_ifs(addr_city, country, ,<br />)}
					<a href="mailto:{email}">{email}</a>
					</p>
				</div>
			</div>
			{_getClearDiv(rownum)}
	',
	'journalgroups.SE_row' => '
			<div class="authorInfoHolder">
				{_getUserPictureIfExist(previewpicid, 1)}
				<div class="authorDesc">
					<h3>{fullname}</h3>
					<p>
						{_render_if(affiliation, , <br />)}
						{_comma_ifs(addr_city, country, ,<br />)}
						<a href="mailto:{email}">{email}</a>
					</p>
					<p>
						{_render_if(taxon, <strong>Taxa:</strong>&nbsp;, <br />)}
						{_render_if(subject, <strong>Subjects:</strong>&nbsp;, <br />)}
						{_render_if(geographical, <strong>Regions:</strong>&nbsp;, <br />)}
					</p>
				</div>
			</div>
			{_getClearDiv(rownum)}
	',	
	
	'journalgroups.endrs' => '
		</div>
		<div class="P-Clear"></div>
		{_editorial_office(group_id)}
		<div class="leftMar10">
			{nav}
		</div>
	',
	'journalgroups.foot' => '
		<script type="text/javascript">
			$(\'#contentSmallHolder\').css(\'margin-left\', \'391px\');
		</script>
		</div>
		<div class="P-Clear">
	',
	'journalgroups.nodata' => '<div class="br"></div><p class="dashboard-title withoutBorder textCenterAlign">No users in this group!</p>',
	'journalgroups.authors_empty' => '',
	
	'journalgroups.search_form' =>'
		<div class="leftSiderBlock bigBlock">
			<h3>' . getstr('pjs.filter') . '</h3>
				<div class="P-Clear"></div>
				<div class="filterBlock category">
					<div class="filterBlockTitle">
						' . getstr('pjs.bytaxon') . ' 
						<a id="taxon_arrow" class="blockUpArrow tree" href="javascript:void(0);" onclick="toggleBlock(\'taxon_arrow\', \'taxon_tree\')"></a>
					</div>
					<div class="filterBlockContent" id="taxon_tree">
						<div class="P-Input-Full-Width P-W390 spacer10t">
								{alerts_taxon_cats}
						</div>
						<!-- Tree alerts_taxon_cats -->
						<div id="treealerts_taxon_cats" class="filterBy">
							{^taxon_tree}
						</div>
						{^taxon_tree_script}
						<script type="text/javascript">//<![CDATA[
							// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
							var initComplete = false;
							var lSelectedCats =  new Array();
							lSelectedCats = {_json_encode(taxon_selected_vals)};
							if(!lSelectedCats.length)
								toggleBlock(\'taxon_arrow\', \'taxon_tree\');
							var InputVal = new Array();
							for ( var i = 0; i < lSelectedCats.length; i++) {
								$("#alerts_taxon_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
							}
							//]]>
						</script>
						<!-- Tree #3 END -->
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="filterBlock category">
					<div class="filterBlockTitle">
						' . getstr('pjs.bysubject') . '
						<a id="subject_arrow" class="blockUpArrow tree" href="javascript:void(0);" onclick="toggleBlock(\'subject_arrow\', \'subject_tree\')"></a>
					</div>
					<div class="filterBlockContent" id="subject_tree">
						<div class="P-Input-Full-Width P-W390">
							{alerts_subject_cats}						
						</div>
						<!-- Tree alerts_subject_cats -->
						<div id="treealerts_subject_cats" class="filterBy">
							{^subjects_tree}
						</div>
						<!-- Tree #1 END -->
						{^subjects_tree_script}
						<script type="text/javascript">//<![CDATA[
							// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
							var lSelectedCats =  new Array();
							lSelectedCats = {_json_encode(subject_selected_vals)};
							var InputVal = new Array();
							if(!lSelectedCats.length)
								toggleBlock(\'subject_arrow\', \'subject_tree\');
							for ( var i = 0; i < lSelectedCats.length; i++) {
								$("#alerts_subject_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
							}
							//]]>
						</script>
					</div>
				</div>
				
				<div class="filterBlock category">
					<div class="filterBlockTitle">
						' . getstr('pjs.bygeographical') . '
						<a id="geographical_arrow" class="blockUpArrow tree" href="javascript:void(0);" onclick="toggleBlock(\'geographical_arrow\', \'geographical_tree\')"></a>
					</div>
					<div class="filterBlockContent" id="geographical_tree">
						<div class="P-Input-Full-Width P-W390">
							{alerts_geographical_cats}
						</div>
						<!-- Tree alerts_geographical_cats -->
						<div id="treealerts_geographical_cats" class="filterBy">
							{^geographical_tree}
						</div>
						{^geographical_tree_script}
						<script type="text/javascript">//<![CDATA[
							// Тук популираме първоначално инпута със селектнатите стойности или ако има грешка при сейв със селектнатите + новоселектнатите преди сейв
							var lSelectedCats =  new Array();
							lSelectedCats = {_json_encode(geographical_selected_vals)};
							var InputVal = new Array();
							if(!lSelectedCats.length)
								toggleBlock(\'geographical_arrow\', \'geographical_tree\');
							for ( var i = 0; i < lSelectedCats.length; i++) {
								$("#alerts_geographical_cats_autocomplete").tokenInput("add", lSelectedCats[i]);
							}
							initComplete = true;
							gFormToSubmit = \'filter_groups\';
							//]]>
						</script>
						<!-- Tree #4 END -->
					</div>
				</div>
				<div class="filterBlock">
					<div class="filterBlockContent">
						<input id="author_first_letter" type="hidden" name="user_letter" value="{@user_letter}"></input>
						<a href="javascript: filterUsersLetter({@journal_id}, \'\', {@grp_id}, {@role_id})" class="green letter">
							All
						</a>
						<span style="color: #b0ada2;">&nbsp;&nbsp;|&nbsp;</span>
						<span class="lettersHolder">
							{_getUsersLetters(journal_id, grp_id, role_id)}
						</span>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="buttonsHolder">
					<div class="P-Grey-Btn-Holder" style="margin-top: 32px;">
						<div class="P-Grey-Btn-Left"></div>
						<a class="P-Grey-Btn-Middle" href="/browse_journal_groups.php?journal_id={@journal_id}&amp;role_id=3">' . getstr('pjs.clear_filters') . '</a>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Clear"></div>
				</div>
				<div class="P-Clear"></div>
				<script type="text/javascript">
					$(function(){
						$(\'#taxon_tree\').show();
						$(\'#subject_tree\').show();
						$(\'#geographical_tree\').show();
					});
				</script>
			</div>',

			'journalgroups.sidebar_left_browse_groups_header' => '
			<div id="leftSider">
				<div class="leftSiderBlock bigBlock">
					<h3>' . getstr('pjs.editorial_team') . '</h3>
					<div class="P-Clear"></div>
					<div class="siderBlockLinksHolder">
			',
			'journalgroups.sidebar_left_browse_groups_row' => '
					{_displayGroupNames(id, journal_id, title)}
					<div class="clear"></div>
			',
			'journalgroups.sidebar_left_browse_groups_foot' => '
				<a class="link" id="subj_editors_link" href="/browse_journal_groups.php?journal_id={journal_id}&amp;role_id=3">
					<span></span>
					<span class="content">' . getstr('pjs.subject_editors') . '</span>
				</a>
				<a class="link" href="/contacts" st="106">
					<span></span>
					<span class="content">' . getstr('pjs.contacts_page_link_text') . '</span>
				</a>
				{_changeMenuStyleToBold(grp_id, role_id, taction)}
				<div class="clear"></div>
				</div>
			</div>
			<div id="filters_form_holder">
					{search_form}
				</div>
		</div>
			',
			'journalgroups.browse_startrs_subgroupss_list' => '
			<table width="98%" class="reviewer_tbl leftMar10" >
				<tr>
					<th class="left textLeftAlign">' . getstr('Sub groups') . '</th>
				</tr>
		',
			'journalgroups.browse_row_subgroups_list' => '
			<tr>
				<td class="left"><a style="color: #1F1F1F;" href="/edit_journal_group.php?journal_id={journal_id}&tAction=showedit&id={id}">{name}</a></td>
			</tr>
			',
);
?>