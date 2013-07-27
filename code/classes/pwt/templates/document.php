<?php

$gTemplArr = array(

	'document.rightcol' => '
							<div id="P-Wrapper-Right-Content">
								<div class="content">
									<div class="P-Article-StructureHead" id="CommentsFreeze">
										Comments
										{*comments.new_form_wrapper}
										{*comments.filter}
										<hr style="border-bottom: 1px solid rgb(226, 226, 220); border-top: 1px solid rgb(226, 226, 220); margin: 5px -18px 0px -15px; border-style: solid; padding-top: 1px; border-color: rgb(226, 226, 220);" />
									</div>
									{*comments.wrapper}
								</div>
								<div class="P-Clear"></div>
							</div>	<!-- P-Wrapper-Right-Content -->
	',

	'document.revisions_rightcol' => '
							<div id="P-Wrapper-Right-Content">
								<div class="content">
									<div class="page" style="width: 335px;">
										<div class="P-Article-StructureHead">
											Revisions:
											<div class="P-Clear"></div>
										</div>
										{revisions}
									</div>
								</div>
								<div class="P-Clear"></div>
							</div>	<!-- P-Wrapper-Right-Content -->
	',

	'document.path_head' => '',

	'document.path_foot' => '',

	'document.path_start' => ' <a href="/display_document.php?document_id={document_id}">{_CutText(document_name, 40)}</a> ',

	'document.path_end' => '',

	'document.path_nodata' => '',

	'document.path_row' => ' » {_getDocumentPathLink(instance_id, object_name, current_instance_id)}',
		
	'document.wrapper_leftcol' => '
						<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
							{document_structure}							
							<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
						</div><!-- End P-Wrapper-Container-Left -->
	',
		
	'document.wrapper_rightcol' => '
						<div class="P-Wrapper-Container-Right {_getContainerHideClass(2)}">
							{*document.rightcol}
							<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						</div><!-- End P-Wrapper-Container-Right -->
	',
		
	'document.wrapper_container_middle' => '
						<div class="P-Wrapper-Container-Middle {_getContainerHideClass(1)} {_getContainerHideClass(2)}">
							{*document.wrapper_container_middle_content}
						</div><!-- End P-Wrapper-Container-Middle -->
	',
	'document.wrapper_container_middle_nocomments' => '
						<div class="P-Wrapper-Container-Middle P-Wrapper-Container-No-Right {_getContainerHideClass(1)}">
							{*document.wrapper_container_middle_content}
						</div><!-- End P-Wrapper-Container-Middle -->
	',
		
	'document.wrapper_container_middle_content' => '					
						<div class="P-Document-Save-Message">{save_msg}</div>
						<form name="document_form" method="post" id="document_form">
							<input type="hidden" name="document_id" value="{document_id}"/>
							<input type="hidden" name="instance_id" value="{instance_id}"/>
							<input type="hidden" name="perform_save_action" value="1"/>
							{document_object_instance}
							<!-- <div class="P-Data-Resources-Save-Preview">
								<span class="P-SaveBtn"><input class="save_btn" type="submit" value="" /></span>
								<span class="P-PreviewBtn"><input onclick="window.location=\'/preview.php?document_id={document_id}\';return false" class="preview_btn" type="submit" value="" /></span>
							</div>
							-->
						</form>					
	',
		
	'document.wrapper_additional_items' => '
						{*figures.figures_popup}
						{*tables.tables_popup}
						{*references.new_reference_popup}
						<script type="text/javascript">
							window.onbeforeunload = function(){
								unlock_document();
							};
							autoSaveDocument(' . AUTO_SAVE_INTERVAL . ');

							AutoSendDocumentLockSignal();
						</script>
	',

	'document.wrapper' => '
						{*document.wrapper_leftcol}
						{*document.wrapper_rightcol}
						{*document.wrapper_container_middle}
						{*document.wrapper_additional_items}		
	',
		
	'document.wrapper_no_comments' => '
						{*document.wrapper_leftcol}
						{*document.wrapper_container_middle_nocomments}
						{*document.wrapper_additional_items}
	',

	'document.figures_wrapper' => '
						<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
							{document_structure}
							<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
							<script type="text/javascript">
								var articleStructure = new articleMenu(\'articleMenu\', \'P-Right-Arrow\' , \'P-Down-Arrow\');
							</script>
						</div><!-- End P-Wrapper-Container-Left -->
						<div class="P-Wrapper-Container-Right {_getContainerHideClass(2)}">
							{*document.rightcol}
							<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						</div><!-- End P-Wrapper-Container-Right -->
						<div class="P-Wrapper-Container-Middle {_getContainerHideClass(1)} {_getContainerHideClass(2)}">
							{figures_structure}
						</div><!-- End P-Wrapper-Container-Middle -->
						{*figures.figures_popup}
	',

	'document.tables_wrapper' => '
						<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
							{document_structure}
							<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
							<script type="text/javascript">
								var articleStructure = new articleMenu(\'articleMenu\', \'P-Right-Arrow\' , \'P-Down-Arrow\');
							</script>
						</div><!-- End P-Wrapper-Container-Left -->
						<div class="P-Wrapper-Container-Right {_getContainerHideClass(2)}">
							{*document.rightcol}
							<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						</div><!-- End P-Wrapper-Container-Right -->
						<div class="P-Wrapper-Container-Middle {_getContainerHideClass(1)} {_getContainerHideClass(2)}">
							{tables_structure}
						</div><!-- End P-Wrapper-Container-Middle -->

						{*tables.tables_popup}
	',

	'document.xml_validation_wrapper' => '
						<div class="P-Wrapper-Container-Left {_getContainerHideClass(1)}">
							{document_structure}
							<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
							<script type="text/javascript">
								var articleStructure = new articleMenu(\'articleMenu\', \'P-Right-Arrow\' , \'P-Down-Arrow\');
							</script>
						</div><!-- End P-Wrapper-Container-Left -->
						<div class="P-Wrapper-Container-Right {_getContainerHideClass(2)}">
							{*document.rightcol}
							<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						</div><!-- End P-Wrapper-Container-Right -->
						<div class="P-Wrapper-Container-Middle {_getContainerHideClass(1)} {_getContainerHideClass(2)}">
							{xml_validation}
						</div><!-- End P-Wrapper-Container-Middle -->

						{*tables.tables_popup}
	',
		
	'document.tree_ajax_wrapper' => '
						<div id="document_tree_holder">
							<script>LoadDocumentTree(\'document_tree_holder\', {document_id}, {instance_id});</script>
							<div class="ajaxTreeLoadingHolder">					
								<img src="/i/loading.gif" />
							</div>
						</div>
	',

	'document.tree_head' => '
							<div class="P-Article-Structures">
								<div class="P-Article-StructureHead">{document_papertype}</div>
								<ul id="articleMenu">
	',

	'document.tree_foot' => '									
								</ul>
								<div class="P-Article-Buttons">
									<div class="P-Grey-Btn-Holder P-Validation ' . ((int)ENABLE_FEATURES ? '' : 'P-Inactive-Button') .'" onclick="">
										<div class="P-Grey-Btn-Left"></div>
										<div class="P-Grey-Btn-Middle P-Green-Btn-Middle-Big_One" ' . ((int)ENABLE_FEATURES ? 'onclick="showLoading(); SubmitDocumentAction(\'/xml_validate.php?document_id={document_id}\');return false;"' : 'onclick="window.location=\'/under_construction.php\';return false;"') . '><div class="P-Btn-Icon"></div>Validate</div>
										<div class="P-Grey-Btn-Right"></div>
									</div>
									<div class="P-Clear"></div>
									{_showPJSSubmitButton(document_id, documentstate)}
								</div>
								<!-- bottom buttons -->
							</div>
							<script type="text/javascript">
								var articleStructure = new articleMenu(\'articleMenu\', \'P-Right-Arrow\' , \'P-Down-Arrow\');
							</script>
	',
							/* 	<!-- bottom buttons -->
								<div class="P-Article-Buttons P-Bottom">
									{_displayBottomTreeButtons( document_is_locked, preview_mode, document_id)}
									<div class="P-Clear"></div>
								</div>
							*/
	'document.tree_start' => '

	',

	'document.tree_end' => '<script>ScrollToSelectedTreeElement()</script>
	',

	'document.tree_row0' => '
									<li id="{instance_id}" >
										<div class="{_objHasIcon(object_id)} {_displayDocumentTreeDivClass(is_active, has_warning, level, has_children, validation_errors, instance_id, document_id)}">
											{_displayDocumentTreeArrow(is_active, has_children, instance_id)}
											{_displayDocumentTreeAdd(P-Article-Add, object_id, instance_id, num_children, is_locked, lock_usr_id, xml_validation_flag, documentstate)}
											{_displayDocumentTreeDelete(P-Article-Delete, parent_object_id, instance_id, is_locked, lock_usr_id, xml_validation_flag, documentstate)}
											<a href="/display_document.php?instance_id={instance_id}">{object_name}</a>
										</div>
									</li>
	',

	'document.tree_row1' => '
									<li id="{instance_id}">
										<div class="{_displayDocumentTreeDivClass(is_active, has_warning, level, has_children, validation_errors, instance_id, document_id)}">
											{_displayDocumentTreeArrow(is_active, has_children, instance_id)}
											{_displayDocumentTreeAdd(P-Article-Add, object_id, instance_id, num_children, is_locked, lock_usr_id, xml_validation_flag, documentstate)}
											{_displayDocumentTreeDelete(P-Article-Delete, parent_object_id, instance_id, is_locked, lock_usr_id, xml_validation_flag, documentstate)}
											<a href="/display_document.php?instance_id={instance_id}">{object_name}</a>
										</div>
										<ul {_returnSortableMenuId(object_id, instance_id)} class="{_displayShowHideClass(instance_id)} {_returnSortableMenuClass(object_id)}">
											{&}
										</ul>
										{_returnSortableMenuDef(object_id, instance_id)}
									</li>
	',

	'document.tree_nodata' => '',

	'document.custom_html_fields' => '{content}',

	'document.instance_label1' => '
									<div style="" class="P-Data-Resources-Head {_getInstanceWrapperCssClass(top_pos_actions_cnt)}">
										{_displayInstanceName(instance_name, display_label, instance_id)}
										<div class="P-Inline-Line-Middle floatl"></div>
										<div class="unfloat"></div>
									</div>
										<div class="P-Data-Resources-Head-Actions">
											<div class="instance_top_actions" id="instance_top_actions_{instance_id}">{top_actions}<div class="unfloat"></div></div>
										</div>
										{_displayClearDiv(mode)}

	',

	'document.instance_label2' => '
									<div style=\'\' class="P-Data-Resources-Head">
										<div class="P-Inline-Line-Middle floatl"></div>
										<div class="unfloat"></div>
									</div>
									<div class="P-Data-Resources-Head-Actions">
										<div class="instance_top_actions" id="instance_top_actions_{instance_id}">{top_actions}<div class="unfloat"></div></div>

									</div>
									{_displayClearDiv(mode)}

	',


	//Instance
	'document.instance_head' => '
							<div class="instance_wrapper {css_class} {_displayMarkForDeleteClass(allow_right_actions)}" id="instance_wrapper_{instance_id}" level="{level}" mode="{mode}">
								<div class="{_getInstanceWrapperClass(level, display_nesting_indicator)} {_displayMarkForDeleteBackgroundClass(allow_right_actions)}">
									{instance_label}
									{_displayInstanceHiddenInput(instance_id, mode)}
									<div id="instance_containers_{instance_id}">
										{_displayTableHeadByMode(mode)}
	',

	'document.instance_foot' => '
										{_displayTableFootByMode(mode)}
									</div>
									<div id="instance_bottom_actions_{instance_id}" class="P-Instance-Bottom-Actions">
										{bottom_actions}
										<div class="unfloat"></div>
									</div>
									<div class="unfloat"></div>

									<div id="instance_right_actions_{instance_id}" class="P-Instance-Right-Actions" is_inited="0">
										{right_actions}
										<div class="unfloat"></div>
										{_pasteInstanceRightActionsCoverJS(instance_id, allow_right_actions)}
									</div>
								</div>
							</div>
	',

	'document.instance_start' => '


	',

	'document.instance_end' => '

	',

	'document.instance_nodata' => '

	',

	//Container
	'document.container_head' => '
									<div class="container {container_id} {css_class} {_getContainerClass(container_type)}" id="container_{instance_id}_{container_id}">

	',

	'document.container_foot' => '
										<div id="container_actions_{instance_id}_{container_id}">
											{actions}
										</div>
										<div class="unfloat"></div>
									</div>
	',

	'document.container_start' => '

	',

	'document.container_row' => '<div id="container_item_wrapper_{instance_id}_{container_id}_{container_item_type}_{container_item_id}" class="container_item_wrapper {item_css_class} {_getContainerItemWrapperClass(items_count, rownum)}" style="{container_item_style}">
									<div class="container_item_inner_wrapper">{container_item}</div>
								</div>',

	'document.container_end' => '
								<div class="P-Clear"></div>
	',

	'document.container_nodata' => '',

	'document.tabbedElement_head' => '<div class="tabbed_element P-Tabbed-Element-Holder" id="tabbed_element_{instance_id}_{tabbed_element_id}" active_item_id="{active_element_id}">
		<input type="hidden" id="tabbed_element_{instance_id}_{tabbed_element_id}_active_item" name="tabbed_element_{instance_id}_{tabbed_element_id}_active_item" value="{active_element_id}">
	',

	'document.tabbedElement_foot' => '</div>',

	'document.tabbedElement_tabRow' => '
		<li class="tabbedElementTab {_getTabbedElementActiveClass(active_element_id, current_tab_element_id)}" id="tabbed_element_tab_holder_{instance_id}_{tabbed_element_id}_{current_tab_element_id}">
			<div class="P-PopUp-Menu-Elem-Left"></div>
			<div class="P-PopUp-Menu-Elem-Middle" onclick="changeTabbedElementActiveTab({instance_id},{tabbed_element_id}, {current_tab_element_id})">{current_tab_element_title}</div>
			<div class="P-PopUp-Menu-Elem-Right"></div>
		</li>
	',

	'document.tabbedElement_start' => '
		<ul id="popUp_nav">
			{tabs}
			<div class="P-Clear"></div>
		</ul>',

	'document.tabbedElement_row' => '
							<div class="{_getTabbedElementDisplayClass(active_element_id, item_id)} tabbedElementItem" id="tabbed_element_item_wrapper_{instance_id}_{tabbed_element_id}_{item_id}">{item}</div>
	',

	'document.tabbedElement_end' => '',

	'document.tabbedElement_nodata' => '',


	'document.actions_row' => '{action}',

	'document.error_while_saving_msg' => '
		' . getstr('pwt.save.couldNotSaveDocumentError') . '
		{err_msg}
		<br/>
		<a href="/display_document.php?instance_id={instance_id}">' . getstr('pwt.save.goBack') . '</a>
	',

	'document.successful_saving_msg' => '
		' . getstr('pwt.save.successfulSaveMsg') . '
		<br/><div class="input-left"></div>
													<div class="input-right"></div>
													<div class="input-middle">
														{field}
													</div>
		<a href="/display_document.php?instance_id={instance_id}">' . getstr('pwt.save.goBack') . '</a>
	',

	'document.create_form' => '
		<h1 style="background: none; padding-left: 10px; margin-top: 0; margin-bottom: 5px">' . getstr('pwt.create.manuscript') . '</h1>
		<p style="margin-left: 1em; color: #005500">
			<img width="24" height="24" style="float: left; margin: 2px 7px 0 0" alt="Note:" src="/i/lightbulb.png">
			Please read our <a href="http://biodiversitydatajournal.com/about#whatType" target="_blank">How to select an article type</a> <br>concise guidelines before you start a new manuscript!</p>
		<form id="form1" name="form1" method="post" action="index.php?show=new_doc2" onsubmit="return(validateForm())">
		<div class="manuscriptBtns">
		<div id="papertypes">
		<fieldset><legend>' . getstr('pwt.create.manuscript.article') . '</legend>
		{papertype_id}
		</div>
		<div id="journals">
			<fieldset><legend>' . getstr('pwt.create.manuscript.journal') . '</legend>
		<p>
		{journal_id}
		</p></fieldset>
		</div>
		<div class="clear"></div>
		</div>
		<div style="clear: both"></div>
		<table style="display: block;">
		<tr>
		<td style=" width: 290px"><p id="docDescr"></p></td>
		<td style=" width: 290px"><p id="journalDescr"></p></td>
		</tr>
		<tr>
		<td style="height:  50px; width: 290px; padding-left: 11px;">
			<div class="P-Green-Btn-Holder P-Save" onclick="showLoading()">
				<div class="P-Green-Btn-Left"></div>
				<div class="P-Green-Btn-Middle" style="width: auto; padding: 0 20px;">{save}</div>
				<div class="P-Green-Btn-Right"></div>
			</div>
		</td>
		<td style="height:  50px; width: 290px" align="right">
		</td>
		</tr>
		</table>
		<div class="P-Data-Resources-Subsection-Title">
			<!--
			<div style="width: 300px; float: left;">
				<div class="P-Label-Holder">' . getstr('pwt.create_document_papertype') . ' <span class="txtred">*</span></div>
				<div class="P-Input-Full-Width P-Select P-Input-With-Help">
					<div class="P-Input-Inner-Wrapper">
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								<span class="P-Select-Value"></span>
								{papertype_id}
								<div class="P-Select-Arrow"></div>
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					' . displayFieldHelpLabel(1, 'Each article type has a template of fields, part of which are mandatory. Article type cannot be changed once associated with a manuscript.') . '
				</div>
			</div>
			<script type="text/javascript">
				var selectPaper = new designSelect( \'paper_type\', 0 );
			</script>
			<div class="P-HSpace-20"></div>
			<div style="width: 300px; float: left;">
				<div class="P-Label-Holder">' . getstr('pwt.create_document_journal') . ' <span class="txtred">*</span></div>
				<div class="P-Input-Full-Width P-Select P-Input-With-Help">
					<div class="P-Input-Inner-Wrapper">
						<div class="P-Input-Holder">
							<div class="P-Input-Left"></div>
							<div class="P-Input-Middle">
								<span class="P-Select-Value"></span>
								{journal_id}
								<div class="P-Select-Arrow"></div>
							</div>
							<div class="P-Input-Right"></div>
							<div class="P-Clear"></div>
						</div>
					</div>
					' . displayFieldHelpLabel(1, 'Each journal permits a different set of article types. In case you do not see the journal you want to submit to, this means the article type you selected is not accepted by this journal.') . '
				</div>
			</div>
			<script type="text/javascript">
				var selectJournal = new designSelect( \'journal\', 0 );
			</script>
		</div>
		<div class="P-Clear"></div>
		<div class="P-VSpace-15"></div>
		<div class="P-Green-Btn-Holder P-Save" onclick="showLoading()">
			<div class="P-Green-Btn-Left"></div>
			<div class="P-Green-Btn-Middle" onclick="showLoading();">{save}</div>
			<div class="P-Green-Btn-Right"></div>
		</div>
		-->
		{document_id}
		<div class="P-Clear"></div>
		<script type="text/javascript">
			leftColFullHeight();
			$(\'#journals label\').hover(function(){
				showActiveDataPapersForCurrentJournal($(this).prev(\'input\'));
			});
			$(\'#papertypes label\').click(function(){
				selectPrevInput(this);
			});
			$(\'#journals label\').click(function(){
				selectPrevInput(this);
			});
			$(\'#journals label\').addClass(\'disabled\');

		</script>
		</div>
	',

	'document.mail_document_add_author_edit' => '
		<html>
		<body>
			Dear {user_fullname}: <br /><br />
			You are invited by <a href="mailto:{usrfrom_mail}">{usrfrom}</a> to co-author a manuscript entitled "<a href="{siteurl}/display_document.php?document_id={document_id}&u_autolog_hash={autolog_hash}">{document_title}</a>".<br /><br />

			The manuscript is being created in the Pensoft Writing Tool (PWT). Within the PWT, you can participate in the writing process by commenting, correcting or adding text in the manuscript.<br /><br />

			We wish you a pleasant and successful collaboration with the authors team within PWT! Please do not hesitate to share your impressions or send your inquiries to <a href="mailto:helpdesk@pensoft.net">helpdesk@pensoft.net</a>.<br /><br />

			<a title="Pensoft Writing Tool" href="{siteurl}">The Pensoft PWT team</a>
		</body>
		</html>
	',

	'document.mail_document_add_author_comment' => '
		<html>
		<body>
			Dear {user_fullname}: <br /><br />
			You are invited by <a href="mailto:{usrfrom_mail}">{usrfrom}</a> to co-author a manuscript entitled "<a href="{siteurl}/display_document.php?document_id={document_id}&u_autolog_hash={autolog_hash}">{document_title}</a>".<br /><br />

			The manuscript is being created in the Pensoft Writing Tool (PWT). Within the PWT, you can participate in the writing process by commenting in this manuscript.<br /><br />

			We wish you a pleasant and successful collaboration with the authors team within PWT! Please do not hesitate to share your impressions or send your inquiries to <a href="mailto:helpdesk@pensoft.net">helpdesk@pensoft.net</a>.<br /><br />

			<a title="Pensoft Writing Tool" href="{siteurl}">The Pensoft PWT team</a>
		</body>
		</html>
	',

	'document.mail_document_add_contributor' => '
		<html>
		<body>
			Dear {user_fullname}: <br /><br />

			You are invited to become a "contributor" during the process of writing of a manuscript entitled "<a href="{siteurl}/display_document.php?document_id={document_id}&u_autolog_hash={autolog_hash}">{document_title}</a>". The invitation is sent by the coordinating author <a href="mailto:{usrfrom_mail}">{usrfrom}</a>.<br /><br />

			Please note that this invitation does not necessarily assume a co-authorship. Contributors can be mentors, potential reviewers, linguistic and copy editors, or just colleagues or friends of the authors.<br /><br />

			You have been invited to contribute as: {colaborate_role}. Please send your questions, if any, to the coordinating author <a href="mailto:{usrfrom_mail}">{usrfrom}</a>.<br /><br />

			The manuscript is being created in the Pensoft Writing Tool (PWT). Within the PWT, you can participate in the writing process by commenting, correcting or adding text in the manuscript.<br /><br />

			We wish you a pleasant and successful collaboration with the author\'s team within PWT! Please do not hesitate share your impressions  or send your inquiries to <a href="mailto:helpdesk@pensoft.net">helpdesk@pensoft.net</a>.<br /><br />

			<a title="Pensoft Writing Tool" href="{siteurl}">The Pensoft PWT team</a>
		</body>
		</html>
	',

	'document.mail_document_add_newauthor_register' => '
		<html>
		<body>

			Dear {user_fullname},<br /><br />

			We kindly inform you that you have just been registered with the Pensoft Writing Tool (PWT)  (<a href="{siteurl}">' . PENSOFT_SITE_URL . '</a>). <br /><br />
			Username: {new_user_mail}<br />
			Password: {new_user_pass}<br /><br />

			The reason for this registration is one of the following:<br /><br />

			 1. You are invited to be a co-author of a manuscript created in the Pensoft Writing Tool<br />
			 2. You are invited to be a contributor to a manuscript created in the Pensoft Writing Tool. Contributors can be mentors, potential reviewers, colleagues/friends of the authors, linguistic/copy editors and are not supposed to become co-authors of the manuscript in question. <br /><br />

			The title of the manuscript is: "{document_title}" and the coordinating author of it is {usrfrom}.

			The registration does not entail any form of commitment for you. Please contact <a href="mailto:{usrfrom_mail}">{usrfrom}</a> for any question regarding this manuscript. <br /><br />

			Please use the link (<a href="{siteurl}">' . PENSOFT_SITE_URL . '</a>) to log in the Pensoft Writing Tool website. After logging in, you can change your password and/or edit your profile.<br /><br />

			<a title="Pensoft Writing Tool" href="{siteurl}">The Pensoft PWT team</a>
		</body>
		</html>
	',

	'document.mail_document_add_newauthor_register_api' => '
	<html>
	<body>

		Dear {user_fullname},<br /><br />

		We kindly inform you that you have just been registered with the Pensoft Writing Tool (PWT)  (<a href="{siteurl}">' . PENSOFT_SITE_URL . '</a>). <br /><br />
		Username: {new_user_mail}<br />
		Password: {new_user_pass}<br /><br />

		The reason for this registration is one of the following:<br /><br />

		1. You are invited to be an author of a manuscript created in the Pensoft Writing Tool<br />


		The title of the manuscript is: "{document_title}".

		The registration does not entail any form of commitment for you.

		Please use the link (<a href="{siteurl}">' . PENSOFT_SITE_URL . '</a>) to log in the Pensoft Writing Tool website. After logging in, you can change your password and/or edit your profile.<br /><br />

		<a title="Pensoft Writing Tool" href="{siteurl}">The Pensoft PWT team</a>
	</body>
	</html>
	',


	'document.documentOnlyForm' => '
		<form name="document_form" method="post" id="document_form">
			<input type="hidden" name="document_id" value="{document_id}"/>
		</form>
	',


	'document.email_message_ready_to_submit' => '
		Document {document_id} is ready for review. Please review it <a href="' . SITE_URL . '/preview.php?document_id={document_id}">here</a>
	',

	'document.email_message_submit' => '
		Document {document_id} is ready to be submitted. You can submit it <a href="' . SITE_URL . '/preview.php?document_id={document_id}">here</a>
	',


);

?>