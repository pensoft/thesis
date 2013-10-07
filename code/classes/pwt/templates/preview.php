<?php

$gTemplArr = array(

// 	'preview.content' => '
// 		{preview}
// 	',

	'preview.editHeaderIframe' => '
			
			<div id="changeContextMenu">
				<a href="#" id="approveChangeContextLink">Accept</a><a href="#" id="rejectChangeContextLink">Reject</a>
			</div>
			<script>
				$(document).ready(function(){
					DisableChangeTracking();
					{_EnableJSTracksFigures(track_figures)}
					SetDisplayUserChangeEvent();
					InitTrackers({revision_id}, {document_id});
					
					GetVersionUserDisplayNames();
					SetAutosaveTimeout();
					parent.AutoSendDocumentLockSignal();
				});
			</script>
	',

	'preview.editHeaderWithoutChangesIframe' => '
			<script>
				$(document).ready(function(){
					DisableChangeTracking();
					{_EnableJSTracksFigures(track_figures)}
					InitTrackers({revision_id}, {document_id});
					SetAutosaveTimeout();
					parent.AutoSendDocumentLockSignal();
				});
			</script>
	',
		
		/* <div class="box clearBorder">
					<img title="Accept/reject all changes in the manuscript and then use Advance edit button" src="' . PJS_SITE_URL . '/i/lightbulb.png" alt="Help" style="padding-top:16px" style="vertical-align:middle;" /> 
		</div> */
				
	'preview.editHeader' => '
			<div id="docEditHeader">
				<div class="box clearBorder" style="width: 155px;" id="changes_display_holder">
					<h3>View</h3>
					<input type="radio" id="changes" name="changes_display" checked="checked" value="1" style="margin-left: 5px; margin-right: 3px; margin-top: 3px;" /> 
					<label for="changes">Changes</label>
					<input type="radio" id="final" name="changes_display" style="margin-left: 5px; margin-right: 3px; margin-top: 3px;"/> 
					<label for="final">Final</label>
					<script type="text/javascript">
						$("#changes_display_holder :radio").bind("change", function(){
							$(\'#previewIframe\')[0].contentWindow.toggleChangesDisplay();
						});
					</script>
				</div>
				<div class="box" align="center">
					<h3>Changes</h3>
					<div class="optionHolder">
						<a href="#" onclick="AcceptRejectCurrentChange(1);return false;" id="P-Accept-Change-Btn-Id" class="P-Disabled-Btn">
							<img src="' . PJS_SITE_URL . '/i/adddoc-small.png" alt="Accept current change" style="vertical-align:middle;" />
							<span>Accept</span>
						</a>
					</div>
					<div class="optionHolder">
						<a href="#" onclick="AcceptRejectCurrentChange();return false;" id="P-Reject-Change-Btn-Id" class="P-Disabled-Btn">
							<img src="' . PJS_SITE_URL . '/i/removedoc-small.png" alt="Reject current change" style="vertical-align:middle;" />
							<span>Reject</span>
						</a>
					</div>
					<div class="optionHolder" onclick="SelectPreviousNextChange(1);return false;">
						<a href="#">
							<img src="' . PJS_SITE_URL . '/i/docleftarrow.png" alt="Go to previous change" style="vertical-align:middle;" />
							<span>Previous</span>
						</a>
					</div>
					<div class="optionHolder" onclick="SelectPreviousNextChange();return false;" >
						<a href="#">
							<img src="' . PJS_SITE_URL . '/i/docrightarrow.png" alt="Go to next change" style="vertical-align:middle;" />
							<span>Next</span>
						</a>
					</div>
					<script>InitChangeBtns()</script>
				</div>
				{legend}
				<div class="P-Clear"></div>	
			</div>
	',
		
	'preview.editHeaderWithoutChanges' => '',

	'preview.user_legend_start' => '
				<div class="box filter">
					<h3>Show markup</h3>
					<div class="popup">
						<br />
						<a href="#" style="border-bottom: 1px solid #E2E2DC; border-top: none;" onclick="openFilterPopUp();"><img src="' . PJS_SITE_URL . '/i/filter.png" alt="filter" style="vertical-align:middle;" /> Filter by reviewer</a>
	',

	'preview.user_legend_row' => '
						<div class="filterInput">
							<input type="checkbox" checked="checked" name="display_user_change" value="{id}"/>
							<div class="block changeLegend{id}" ></div>
							{_DisplayCommentUserName(is_disclosed, undisclosed_real_usr_id, current_user_id, user_name, undisclosed_user_fullname)}
							{name}
							<img src="' . PJS_SITE_URL . '/i/eye.png" alt="eye" />
						</div>
	',

	'preview.user_legend_end' => '
						<br />
						<a href="#" onclick="$(\'#previewIframe\')[0].contentWindow.ShowAllReviews();return false;">Show all reviews</a>
					</div>
					<a href="#" onclick="openFilterPopUp(); return false;">
						<img src="' . PJS_SITE_URL . '/i/filter.png" alt="filter" style="vertical-align:middle;" /> Filter by reviewer
					</a>
				</div>
	',

	'preview.content' => '
		{*document.documentOnlyForm}
		{preview_header}
		<iframe src="/preview_src.php?document_id={document_id}&template_xsl_path={template_xsl_path}&track_figures=1" id="previewIframe" class="previewIframe {_displayIframePreviewHasUnprocessedChangesClass(document_has_unprocessed_changes)}" frameBorder="0" scrolling="no">
		</iframe>
		<input type="hidden" value="{document_id}" name="document_id">
		<script type="text/javascript">
			InsertLoadingDivMarkup();
			showLoading();
			$("#previewIframe").load(function(){
				resizePreviewIframe("previewIframe");
				initPreviewSelectCommentEvent();
				fillCommentPos();
				positionCommentsBase(true);
				hideLoading();						
			});
			window.onresize = function() {
				resizePreviewIframe("previewIframe");
			}
		</script>
		<div id="popupNewComment" class="P-PopUp-Comment-Selection">
			<div id="popupNewCommentLabel" class="P-PopUp-Comment-Selection-Title">Comment on selection</div>
			<form name="newPreviewCommentForm">
				<input type="hidden" name="documentid" value="{document_id}"/>
				<input type="hidden" name="kfor_name" value="commentpost"/>

				<input type="hidden" name="start_instance_id" id="previewNewCommentStartInstanceId"/>
				<input type="hidden" name="start_field_id" id="previewNewCommentStartFieldId"/>
				<input type="hidden" name="start_offset" id="previewNewCommentStartOffset"/>


				<input type="hidden" name="end_instance_id" id="previewNewCommentEndInstanceId"/>
				<input type="hidden" name="end_field_id" id="previewNewCommentEndFieldId"/>
				<input type="hidden" name="end_offset" id="previewNewCommentEndOffset"/>

				<div class="P-PopUp-Comment-Textarea-Holder">
					<textarea name="msg"></textarea>
				</div>
				<div class="P-Green-Btn-Holder" id="newCommentOkBtn" onmausedown="submitPreviewNewComment(); return false">
					<div class="P-Green-Btn-Left"></div>
					<div class="P-Green-Btn-Middle">Comment</div>
					<div class="P-Green-Btn-Right"></div>
				</div>
				<div class="P-Grey-Btn-Holder" id="newCommentCancelBtn" onclick="cancelPreviewNewComment()">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>Close</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
			</form>
		</div>
		{*figures.figures_popup}
		{*tables.tables_popup}
	',

	'preview.content_revision' => '
		{*document.documentOnlyForm}
		<iframe src="/preview_src.php?document_id={document_id}&template_xsl_path={template_xsl_path}&revision_id={revision_id}&show_revision=1" id="previewIframe" class="previewIframe" frameBorder="0" scrolling="no">
		</iframe>
		<input type="hidden" value="{document_id}" name="document_id">
		<script type="text/javascript">
			$("#previewIframe").load(function(){
				resizePreviewIframe("previewIframe");
				initPreviewSelectCommentEvent();
			});
			window.onresize = function() {
				resizePreviewIframe("previewIframe");
			}
		</script>
	',

	'preview.revisions_head' => '
			<div>',
	'preview.revisions_startrs' => '
				<div class="P-Revisions-Holder">',
	'preview.revisions_row' => '
					<div class="P-Inline-Line"></div>
					<div id="P-Revision-{id}" class="P-Revisions-History" onclick="showRevision(\'previewIframe\', {id}, {document_id}, \'{template_xsl_path}\')">
						<div class="P-Image">{_showCommentPic(photo_id)}</div>
						<div style="float: left;">
							<div class="revisionUsername">{fullname}</div>
							<div class="P-Clear"></div>
							<div class="revisionDate">{_formatCreateDate(createdate)}</div>
						</div>
						<div class="P-Clear"></div>
					</div>',
	'preview.revisions_endrs' => '
					<div class="P-Inline-Line"></div>
					<div style="margin: 20px 0px 10px 100px;">
						<a class="P-Grey-Btn-Holder">
							<span class="P-Grey-Btn-Left"></span>
							<span onclick="window.location.href=\'/preview.php?document_id={document_id}\'" class="P-Grey-Btn-Middle">' . getstr('pwt.go_to_latest_version') . '</span>
							<span class="P-Grey-Btn-Right"></span>
						</a>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Clear"></div>
	',
	'preview.revisions_foot' => '
			</div>
	',
	'preview.revisions_empty' => '
			<div>
				<div class="P-Revisions-Holder">
					<div style="margin: 20px 0px 10px 100px;">
						<a class="P-Grey-Btn-Holder">
							<span class="P-Grey-Btn-Left"></span>
							<span onclick="window.location.href=\'/preview.php?document_id={document_id}\'" class="P-Grey-Btn-Middle">' . getstr('pwt.go_to_latest_version') . '</span>
							<span class="P-Grey-Btn-Right"></span>
						</a>
						<div class="P-Clear"></div>
					</div>
				</div>
				<div class="P-Clear"></div>
			</div>
	',
);
?>