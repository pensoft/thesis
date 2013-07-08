<?php
// @formatter->off
$gTemplArr = array(
	'view_version_pwt.version_preview' => '
	{*document_edit.document_header}
		<div class="P-Wrapper-Container-Left" {_fixTopPositionLeftCol(read_only)}>
			<div class="P-Article-Structures">
				{structure}
				<div class="P-Article-Buttons">
					<div class="clear"></div>
					<div class="P-Clear"></div>
				</div>
			</div>
			<div class="P-Container-Toggler-Btn-Left" onclick="toggleLeftContainer();"></div>
		</div>
		<div class="P-Wrapper-Container-Right ">
			<div id="P-Wrapper-Right-Content">
				<div class="content">
					<div class="P-Article-StructureHead" id="CommentsFreeze">
						Comments:
						{*comments.new_form_wrapper}
						{*comments.filter}
						<div class="P-Container-Toggler-Btn-Right" onclick="toggleRightContainer();"></div>
						<hr style="border-bottom: 1px solid #96968A; border-top: 1px solid #96968A; margin: 5px -18px 0px -15px; border-style: solid; padding-top: 1px;" />
					</div>
					{comments}
					<div class="P-Clear"></div>
				</div>
			</div>

			<div class="P-Clear"></div>
		</div>	<!-- P-Wrapper-Right-Content -->

		<div id="P-Ajax-Loading-Image">
			<img src="./i/loading.gif" alt="" />
		</div>
		<div class="P-Article-Content" id="P-Article-Content" style="display:none;{_fixMarginTop(read_only)}">
			<div class="P-Article-Preview-Holder" id="previewHolder">
				<script type="text/javascript">
					getDocumentPreview({version_id}, {read_only}, \'previewHolder\', \'P-Article-Content\');
				</script>
			</div>
			{_SetVersionMode(role)}

			<script type="text/javascript">
				initPreviewSelectCommentEvent();
				SetVersionUser({current_user_id}, {_json_encode(current_user_name)});
				SetDisplayUserChangeEvent();
				InitTrackers({version_id});
				GetVersionUserDisplayNames();
			</script>
			<div id="changeContextMenu">
				<a href="#" id="approveChangeContextLink">Accept</a><a href="#" id="rejectChangeContextLink">Reject</a>
			</div>
			{_addSingleDocumentClass(role)}
			<div class="br"></div>
			<div class="response"></div>
			<div class="h10"></div>
			<div class="h10"></div>
			<div class="brownBorder" {_showHideByRole(role)}></div>
			<div class="headlineContainer" id="pollhead" {_showHideByRole(role)}>
				<div class="headline">
					<h3>' . getstr('admin.article_versions.previewForm') . '</h3>
				</div>
				<div class="date">
					<!-- Editorial decision is due in {round_due_date_main} days -->
				</div>
				<div class="clear"></div>
			</div>
			<div class="brownBorder" {_showHideByRole(role)}></div>
			{reviewerpoll}
			{form}
		</div>
		<script>
			if($(\'#P-Article-Content\').height() < 960) {
				$(\'#P-Article-Content\').css(\'height\',\'960px\');
			}
		</script>
	',
	'view_version_pwt.structure_head' => '<div class="P-Article-StructureHead">Contents:</div>',
	'view_version_pwt.structure_foot' => '',
	'view_version_pwt.structure_start' => '
		<ul id="articleMenu">
	',
	'view_version_pwt.structure_end' => '
		</ul>
	',
	'view_version_pwt.structure_row' => '
		<li>
			<div class="P-Article-Holder">
				<a href="#{_seoUrl(object_name)}">{object_name}</a>
			</div>
		</li>
	',
	'view_version_pwt.poll_head' => '',

	'view_version_pwt.poll_startrs' => '
				<table class="previewform">
				<tr class="bold">
					<td class="no-Border withSmallPadding">' . getstr('admin.article_versions.quest1') . '<span class="txtred">*</span></td>
					<td class="center no-Border withSmallPadding">' . getstr('admin.article_versions.option1') . '</td><td class="center no-Border withSmallPadding">' . getstr('admin.article_versions.option2') . '</td><td class="center no-Border withSmallPadding">' . getstr('admin.article_versions.option3') . '</td><td class="center no-Border withSmallPadding">' . getstr('admin.article_versions.option4') . '</td>
				</tr>',
	'view_version_pwt.poll_row' => '
		<tr>
			<td>{_returnQuestion(rownum)}</td>
			<td class="center">{1}</td>
			<td class="center">{2}</td>
			<td class="center">{3}</td>
			<td class="center">{4}</td>
		</tr>',
	'view_version_pwt.poll_endrs' => '',
	'view_version_pwt.poll_foot' => '</table>',
	'view_version_pwt.pollanswers' => '
			<table class="previewform">
				<tr class="bold">
					<td>' . getstr('admin.article_versions.quest1') . '<span class="txtred">*</span></td>
					<td class="center">' . getstr('admin.article_versions.option1') . '</td><td class="center">' . getstr('admin.article_versions.option2') . '</td><td class="center">' . getstr('admin.article_versions.option3') . '</td><td class="center">' . getstr('admin.article_versions.option4') . '</td>
				</tr>
				<tr><td>' . getstr('admin.article_versions.quest2') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest3') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest4') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest5') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest6') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest7') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest8') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest9') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest10') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest11') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest12') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest13') . '</td></tr>
				<tr><td>' . getstr('admin.article_versions.quest14') . '</td></tr>
		</table>
	',

	'view_version_pwt.form_se' => '
					<div class="P-Article-Editing">
					<div class="bold">
						' . getstr('admin.article_versions.yourpreview') . '
					</div>
					<div class="br"></div>
					{*notes_to_author}<span class="txtred">*</span>:
					<div class="br"></div>
					{!notes_to_author}
					{notes_to_author}
					<div class="br"></div>
					<div class="br"></div>
					<div class="bold">
						' . getstr('admin.article_versions.recomend') . '
						<span class="txtred">*</span>:
					</div>
					<div class="br"></div>
					{!decision_id}
					<div id="decision">
						<table>
							<tr style="font-size: 12px;">{decision_id}</tr>
						</table>
					</div>
					<div class="clear"></div>
					<div class="P-Green-Btn-Holder saveForm FirstBtn">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-80">{save}</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Green-Btn-Holder previewBtn">
						<div class="P-Green-Btn-Left" ></div>
						<div class="P-Green-Btn-Middle P-Green-Btn-Preview">
							{review}
						</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					{_returnGrayCloseBtn()}

					<div class="clear"></div>
				</div>
				{previewmode}
				{_disableFormFields(previewmode)}
				{_closePopUp(close, url_params)}
			<script type="text/javascript">
				// <![CDATA[
				$(document).ready(function(){
					BindDecisionClickEvents(\'#decision\');
					//~ window.setTimeout(function() {
						//~ var lDocumentHeight = $(\'.P-Article-Content\').outerHeight();
						//~ $(\'.P-Wrapper-Container-Left, .P-Wrapper-Container-Right\').height(lDocumentHeight);
					//~ }, 3000);

					$(\'#submit-view-version-form\').click(function(){
						SubmitFormByName(\'document_review_form\');
					});

				});
				CKEDITOR.config.contentsCss = \'editor_iframe1.css\' ;
				CKEDITOR.config.language = \'en\';
				var instance = CKEDITOR.instances[\'.review"\'];
				if(instance){
					instance.destroy(true);
				}
				$(\'.review\' ).ckeditor(function(){
						//~ fixEditorMaximizeBtn(this);
					}, {
					skin : \'office2003\',
					toolbar : \'ModerateToolbar\',
					removePlugins: \'elementspath\',
					height: 200
				});
				// ]]>
			</script>
	',

	'view_version_pwt.form_ce' => '
					{decision_id}
					<div class="P-Article-Editing">
					<div class="bold">
						' . getstr('admin.article_versions.yourpreview') . '
					</div>
					<div class="br"></div>
					{*notes_to_author}<span class="txtred">*</span>:
					<div class="br"></div>
					{notes_to_author}
					<div class="br"></div>
					<div class="clear"></div>
					<div class="P-Green-Btn-Holder saveForm FirstBtn">
						<div class="P-Green-Btn-Left"></div>
						<div class="P-Green-Btn-Middle P-80">{save}</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					<div class="P-Green-Btn-Holder previewBtn">
						<div class="P-Green-Btn-Left" ></div>
						<div class="P-Green-Btn-Middle P-Green-Btn-Preview">
							{review}
						</div>
						<div class="P-Green-Btn-Right"></div>
					</div>
					{_returnGrayCloseBtn()}

					<div class="clear"></div>
				</div>
				{previewmode}
				{_disableFormFields(previewmode)}
				{_closePopUp(close, url_params)}
			<script type="text/javascript">
				$(document).ready(function(){
					BindDecisionClickEvents(\'#decision\');
					//~ window.setTimeout(function() {
						//~ var lDocumentHeight = $(\'.P-Article-Content\').outerHeight();
						//~ $(\'.P-Wrapper-Container-Left, .P-Wrapper-Container-Right\').height(lDocumentHeight);
					//~ }, 3000);

					$(\'#submit-view-version-form\').click(function(){
						SubmitFormByName(\'document_review_form\');
					});

				});
				CKEDITOR.config.contentsCss = \'editor_iframe1.css\' ;
				CKEDITOR.config.language = \'en\';
				var instance = CKEDITOR.instances[\'.review"\'];
				if(instance){
					instance.destroy(true);
				}
				$(\'.review\' ).ckeditor(function(){
						//~ fixEditorMaximizeBtn(this);
					}, {
					skin : \'office2003\',
					toolbar : \'ModerateToolbar\',
					removePlugins: \'elementspath\',
					height: 200
				});
			</script>
	',

	'view_version_pwt.form_reviewer' => '
				<table class="previewform">
					<tr class="bold">
						<td class="no-Border withSmallPadding">' . getstr('admin.article_versions.quest1') . '{_showRequiredStart(user_role)}
						<div style="font-weight: normal">Please consult our
					<a href="http://biodiversitydatajournal.com/about#FocusandScope" target="_blank">Focus and Scope</a>,
					<a href="http://biodiversitydatajournal.com/about#Criteriaforpublication" target="_blank">Criteria for publication</a>,
					<a href="http://biodiversitydatajournal.com/about#Authorguidelines" target="_blank">Author guidelines</a> and
					<a href="http://biodiversitydatajournal.com/about#Datapublication" target="_blank">Data publication</a> pages, if necessary.</div>

						</td>
						<td class="center no-Border withSmallPadding">' . getstr('admin.article_versions.option1') . '</td><td class="center no-Border withSmallPadding">' . getstr('admin.article_versions.option2') . '</td><td class="center no-Border withSmallPadding">' . getstr('admin.article_versions.option3') . '</td><td class="center no-Border withSmallPadding">' . getstr('admin.article_versions.option4') . '</td>
					</tr>
					<tr {_showHideErrRow(err_cnt)}><td colspan="5" class="no-Border">{!question1}</td></tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest2') . '
						</td>
						{question1}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest3') . '</td>
						{question2}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest4') . '</td>
						{question3}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest5') . '</td>
						{question4}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest6') . '</td>
						{question5}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest7') . '</td>
						{question6}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest8') . '</td>
						{question7}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest9') . '</td>
						{question8}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest10') . '</td>
						{question9}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest11') . '</td>
						{question10}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest12') . '</td>
						{question11}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest13') . '</td>
						{question12}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest14') . '</td>
						{question13}
					</tr>
					<tr>
						<td>' . getstr('admin.article_versions.quest15') . '</td>
						{question14}
					</tr>
				</table>
				<div class="P-Article-Editing">
				<div class="bold" {_showHideTextByViewMode(view_mode)}>' . getstr('admin.article_versions.yourpreview') . '</div>
				<div class="br"></div>

				<div {_showHideTextByViewMode(view_mode)}>{*notes_to_author}{_showRequiredStart(user_role)}:</div>
				<div {_showHideTextByViewMode2(view_mode)}>' . getstr('admin.article_versions.viewyourpreview') . ':</div>
				<div class="br"></div>
				{!notes_to_author}
				<div {_showHideTextByViewMode(view_mode)}>{notes_to_author}</div>
				<div {_showHideTextByViewMode2(view_mode)}>{@notes_to_author}</div>

				<div class="br"></div>
				<div class="br"></div>
				<div {_showHideTextByViewMode(view_mode)}>{*notes_to_editor}:</div>
				<div {_showHideTextByViewMode2(view_mode)}>' . getstr('admin.article_versions.view2yourpreview') . ':</div>
				<div class="br"></div>
				{!notes_to_editor}
				<div {_showHideTextByViewMode(view_mode)}>{notes_to_editor}</div>
				<div {_showHideTextByViewMode2(view_mode)}>{@notes_to_editor}</div>

				<div class="br"></div>
				<div class="br"></div>
				<div class="bold">
					' . getstr('admin.article_versions.recomend') . '
					<span class="txtred">*</span>:
				</div>
				<div class="br"></div>
				{!decision_id}
				<div id="decision">
					<table>
						<tr style="font-size: 12px;">{decision_id}</tr>
					</table>
				</div>
				<div class="br"></div>
				<div class="br"></div>
				<div class="br"></div>
				{disclose_name}{*disclose_name}
				{publish_review}{*publish_review}
				<div class="clear"></div>

				<div class="P-Green-Btn-Holder saveForm FirstBtn">
					<div class="P-Green-Btn-Left"></div>
					<div class="P-Green-Btn-Middle P-80">{save}</div>
					<div class="P-Green-Btn-Right"></div>
				</div>
				<div class="P-Green-Btn-Holder previewBtn">
					<div class="P-Green-Btn-Left" ></div>
					<div class="P-Green-Btn-Middle P-Green-Btn-Preview">
						{review}
					</div>
					<div class="P-Green-Btn-Right"></div>
				</div>
				{_returnGrayCloseBtn()}

				<div class="clear"></div>
			</div>
			{previewmode}
			{_disableFormFields(previewmode)}
			{_closePopUp(close, url_params)}
		<script type="text/javascript">
			$(document).ready(function(){
				BindDecisionClickEvents(\'#decision\');
				window.setTimeout(function() {
					var lDocumentHeight = $(\'.P-Article-Content\').outerHeight();
					$(\'.P-Wrapper-Container-Left, .P-Wrapper-Container-Right\').height(lDocumentHeight);
				}, 3000);

				$(\'#submit-view-version-form\').click(function(){
					SubmitFormByName(\'document_review_form\');
				});

			});
			CKEDITOR.config.contentsCss = \'editor_iframe1.css\' ;
			CKEDITOR.config.language = \'en\';
			var instance = CKEDITOR.instances[\'.review"\'];
			if(instance){
				instance.destroy(true);
			}
			$(\'.review\' ).ckeditor(function(){
					//~ fixEditorMaximizeBtn(this);
				}, {
				skin : \'office2003\',
				toolbar : \'ModerateToolbar\',
				removePlugins: \'elementspath\',
				height: 200
			});
		</script>
	',
	'view_version_pwt.error_row' => '<p style="text-align: center;">{err_msg}</p>',

	'view_version_pwt.legend_head' => '',

	'view_version_pwt.legend_row' => '
			<div class="filterInput">
				<input type="checkbox" checked="checked" name="display_user_change" value="{id}"/>
				{_DisplayCommentUserName(is_disclosed, undisclosed_real_usr_id, has_editor_permissions, current_user_id, name, undisclosed_user_fullname)}
				<img src="/i/eye.png" alt="eye" />
			</div>
	',

	'view_version_pwt.legend_foot' => '',

	'view_version_pwt.no_premissions' => '
		<h3 class="limitedPermissions">' . getstr('pjs.no_permissions_for_page') . '</h3>
	',

);

?>