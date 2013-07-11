var gPreviewCommentFormName = 'newCommentForm';
var gReplyCommentFormPrefix = 'comment_reply_form';
var gCommentStartPos;
var gCommentEndPos;
var gSelectionIsEmpty = true;
var gPreviewHolderId = 'previewHolder';
var gPreviewCommentAjaxUrl = '/lib/ajax_srv/comment_srv.php';
var gCommentAjaxSrvUrl = '/lib/ajax_srv/comment_srv.php';
var gRecalculateCommentPositionAction = 'recalculate_position';



function SetCommentsVersion(pVersionId) {
	gCommentsVersionId = pVersionId;
}

function GetPreviewContent() {
	return $('#' + gPreviewHolderId).contents();
}

function GetCommentSelection() {
	return rangy.getSelection();
}

function showCommentForm2(pId) {
	if(!pId){
		pId = '';
	}

	var ShowHidePopupForm = $('#popupNewComment').is(":visible");

	if( gCommentFormHide || !ShowHidePopupForm){ //show form
		gCommentFormHide = 0;
		$('#P-Comment-Form_' + pId).show();
		$('#displayNewCommentPopupBtn').hide();
		$('#popupNewComment').show();
		$('#P-Comment-Btn-' + pId).removeClass('comment_btn');
		$('#P-Comment-Btn-' + pId).addClass('comment_btn_inactive');
	}else{				//hide form
		gCommentFormHide = 1;
		$('#P-Comment-Form_' + pId).hide();
		$('#displayNewCommentPopupBtn').hide();
		$('#popupNewComment').hide();
		$('#P-Comment-Btn-' + pId).removeClass('comment_btn_inactive');
		$('#P-Comment-Btn-' + pId).addClass('comment_btn');
	}
	positionCommentsBase();
}

function displayNewCommentPopup() {
	fillCommentPos();
	var lNewCommentPopup = $('#popupNewComment');
	lNewCommentPopup.show();
	var lMakeCommentHolder = $('#displayNewCommentPopupBtn');
	lMakeCommentHolder.hide();

}

function cancelPreviewNewComment() {
	$('#popupNewComment').hide();
	$('form[name="' + gPreviewCommentFormName + '"]').resetForm();
	gCommentStartPos = null;
	gCommentEndPos = null;
	gSelectionIsEmpty = true;
}

function clearNewReplyCommentForm(pRootId) {
	$('form[name="' + gReplyCommentFormPrefix + pRootId + '"]').resetForm();
	$('form[name="' + gReplyCommentFormPrefix + pRootId + '"] textarea').val('');
}

function cancelNewReplyComment(pRootId) {
	showCommentForm(pRootId);
	clearNewReplyCommentForm(pRootId);
}

/**
 * Изчислява позицията по вертикала на възела на коментара Не трябва да забравим
 * и позицията на iframe-a (offset() на възела е релативен спрямо iframe-a)
 */

function getCommentNodeVerticalPosition(pNode) {
	// var lDiv =
	// pNode.parentNode.insertBefore(pNode.ownerDocument.createElement('div'));
	// lResult = $(lDiv).offset().top;
	// lDiv.parentNode.removeChild(lDiv);
	// return lResult;
	return $(pNode).offset().top;
}

/**
 * Слага event-a за показване на попъпа за нов коментар при селектиране на текст
 * в превю режим
 */
function initPreviewSelectCommentEvent() {
	// Слагаме event-a на mouseup понеже onselect не е crossbrowser
	$('#' + gPreviewHolderId).contents().bind('mouseup', function(pEvent) {
		CheckSelectedTextForActiveComment();
		gPreviousPreviewSelection = GetCommentSelection();
		gPreviousPreviewSelectionStartNode = false;
		lCommentPos = GetSelectedTextPos();
		if(!lCommentPos){
			return;
		}
		gCommentStartPos = lCommentPos['start_pos'];
		gCommentEndPos = lCommentPos['end_pos'];
		gSelectionIsEmpty = lCommentPos['selection_is_empty'];
		fillCommentPos();
	});
	$('#' + gPreviewHolderId).contents().bind('keyup', function(pEvent) {
		gPreviousPreviewSelection = GetCommentSelection();
		gPreviousPreviewSelectionStartNode = false;
		CheckSelectedTextForActiveComment();
	});
	$('#' + gPreviewHolderId).bind('select', function(pEvent) {
		cancelPreviewNewComment();
		fillCommentPos();
	});
}

/**
 * Попълваме позицията на коментара спрямо instance/field-а в който е направен
 */
function fillCommentPos() {
	var lStartNodeDetails = gCommentStartPos;
	var lEndNodeDetails = gCommentEndPos;
	var lSelectionIsEmpty = gSelectionIsEmpty;
	
	if(!lSelectionIsEmpty){
		var lStartInstanceId , lStartFieldId, lStartOffset;
		var lEndInstanceId, lEndFieldId, lEndOffset;
		if(lStartNodeDetails){
			lStartInstanceId = lStartNodeDetails['instance_id'];
			lStartFieldId = lStartNodeDetails['field_id'];
			lStartOffset = lStartNodeDetails['offset'];
	
			
		}
	
		if(lEndNodeDetails){
			lEndInstanceId = lEndNodeDetails['instance_id'];
			lEndFieldId = lEndNodeDetails['field_id'];
			lEndOffset = lEndNodeDetails['offset'];			
		}
		if(lStartInstanceId && lStartFieldId && lEndInstanceId && lEndFieldId){
			$('#previewNewCommentEndInstanceId').val(lEndInstanceId);
			$('#previewNewCommentEndFieldId').val(lEndFieldId);
			$('#previewNewCommentEndOffset').val(lEndOffset);
			$('#previewNewCommentStartInstanceId').val(lStartInstanceId);
			$('#previewNewCommentStartFieldId').val(lStartFieldId);
			$('#previewNewCommentStartOffset').val(lStartOffset);
			MarkCurrentCommentAsInline();
		}else{
			MarkCurrentCommentAsUnavailable();
			clearCommentPos();
			return false;
		}
	}else{
		clearCommentPos();
		MarkCurrentCommentAsGeneral();		
	}
	return true;
}

function clearCommentPos(){
	$('#previewNewCommentEndInstanceId').val('');
	$('#previewNewCommentEndFieldId').val('');
	$('#previewNewCommentEndOffset').val('');

	$('#previewNewCommentStartInstanceId').val('');
	$('#previewNewCommentStartFieldId').val('');
	$('#previewNewCommentStartOffset').val('');
}

var gCommentN = 0;
function submitPreviewNewComment() {
	if(!gCommentN){
		gCommentN = 1;
		var lCommentPosIsFound = fillCommentPos();
		if(!lCommentPosIsFound){
			gCommentN = 0;
			return;
		}
		var lFormData = $('form[name="' + gPreviewCommentFormName + '"]').formSerialize();
		lFormData += '&tAction=save&action=new_comment';
		$.ajax({
			url : gPreviewCommentAjaxUrl,
			dataType : 'json',
			data : lFormData,
			success : function(pAjaxResult) {
				if(pAjaxResult['err_cnt']){
					alert(pAjaxResult['err_msgs'][0]['err_msg']);
					gCommentN = 0;
					return;
				}
				var lStartInstanceId = pAjaxResult['start_instance_id'];
				var lStartFieldId = pAjaxResult['start_field_id'];
				var lStartOffset = pAjaxResult['start_offset'];

				var lEndInstanceId = pAjaxResult['end_instance_id'];
				var lEndFieldId = pAjaxResult['end_field_id'];
				var lEndOffset = pAjaxResult['end_offset'];

				var lCommentId = pAjaxResult['comment_id'];

				// Insert the comment start and end nodes
				var lStartPositionDetails = calculateCommentPositionAccordingToInternalPosition(lStartInstanceId, lStartFieldId, lStartOffset, true);
				InsertCommentStartEndTag(lStartPositionDetails.node, lStartPositionDetails.offset, lCommentId, true);

				var lEndPositionDetails = calculateCommentPositionAccordingToInternalPosition(lEndInstanceId, lEndFieldId, lEndOffset, true);
				InsertCommentStartEndTag(lEndPositionDetails.node, lEndPositionDetails.offset, lCommentId, false);

				var lPreviewContent = GetPreviewContent();
				if(lStartInstanceId && lStartFieldId){
					var lStartTrackerNode = null;
					if(GetInstanceFieldTrackerNode){
						lStartTrackerNode = GetInstanceFieldTrackerNode(lStartInstanceId, lStartFieldId);
					}
					if(lStartTrackerNode){// Edit mode - save the field
						SaveNodeTrackerContents(lStartTrackerNode);
					}else{// Readonly
						var lInstanceNode = lPreviewContent.find('*[instance_id="' + lStartInstanceId + '"]');
						lInstanceNode = lInstanceNode.first();
						if(lInstanceNode){
							var lFieldNodes = lPreviewContent.find('*[field_id="' + lStartFieldId + '"]');
							var lFieldNode = null;
							for( var i = 0; i < lFieldNodes.length; ++i){
								var lField = $(lFieldNodes.get(i));
								if(lField.closest('*[instance_id]')[0] === lInstanceNode[0]){// Това
																								// е
																								// търсения
																								// field
									lFieldNode = lField[0];
									break;
								}
							}
							if(lFieldNode){
								$.ajax({
									url : gPreviewCommentAjaxUrl,
									dataType : 'json',
									data : {
										'action' : gRecalculateCommentPositionAction,
										'instance_id' : lStartInstanceId,
										'field_id' : lStartFieldId,
										'comment_id' : lCommentId,
										'position_fix_type' : gCommentPositionStartType,
										'field_html_value' : $(lFieldNode).html()
									}
								});
							}

						}
					}
				}
				if(lEndInstanceId && lEndFieldId){
					var lEndTrackerNode = null;
					if(GetInstanceFieldTrackerNode){
						lEndTrackerNode = GetInstanceFieldTrackerNode(lEndInstanceId, lEndFieldId);
					}
					if(lEndTrackerNode){// Edit mode - save the field
						SaveNodeTrackerContents(lEndTrackerNode);
					}else{// Readonly
						var lInstanceNode = lPreviewContent.find('*[instance_id="' + lEndInstanceId + '"]');
						lInstanceNode = lInstanceNode.first();
						if(lInstanceNode){
							var lFieldNodes = lPreviewContent.find('*[field_id="' + lEndFieldId + '"]');
							var lFieldNode = null;
							for( var i = 0; i < lFieldNodes.length; ++i){
								var lField = $(lFieldNodes.get(i));
								if(lField.closest('*[instance_id]')[0] === lInstanceNode[0]){// Това
																								// е
																								// търсения
																								// field
									lFieldNode = lField[0];
									break;
								}
							}
							if(lFieldNode){
								$.ajax({
									url : gPreviewCommentAjaxUrl,
									dataType : 'json',
									data : {
										'action' : gRecalculateCommentPositionAction,
										'instance_id' : lEndInstanceId,
										'field_id' : lEndFieldId,
										'comment_id' : lCommentId,
										'position_fix_type' : gCommentPositionEndType,
										'field_html_value' : $(lFieldNode).html()
									}
								});
							}

						}
					}
				}

				// Крием формата
				cancelPreviewNewComment();
				// Aко няма коментари ползваме темплейт с expand и collapse и
				// добавяме след хедър секцията
				if(pAjaxResult['is_first']){
					$('#P-Wrapper-Right-Content').find('.P-Article-StructureHead').after(pAjaxResult['comment_preview']);
				}else{ // Иначе Добавяме коментара след последния коментар
					$('#P-Root-Comments-Holder').children('.P-Root-Comment').last().after(pAjaxResult['comment_preview']);
				}
				setCommentsWrapEvents();
				positionCommentsBase();
				MakeCommentActive(lCommentId);	
				ExpandSingleComment(lCommentId);
				displayCommentEditForm(lCommentId);
				scrollToComment(lCommentId);
				gCommentN = 0;
			}
		});
	}
}

var gCommentNow = 0;
function SubmitCommentReplyForm(pRootId) {
	var lFormData = $('form[name="' + gReplyCommentFormPrefix + pRootId + '"]').formSerialize();
	lFormData += '&tAction=save&action=new_reply';
	gCommentNow = 1;
	$.ajax({
		url : gPreviewCommentAjaxUrl,
		dataType : 'json',
		data : lFormData,
		success : function(pAjaxResult) {
			gCommentNow = 0;
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msgs'][0]['err_msg']);
				return;
			}

			// Крием формата
			cancelNewReplyComment(pRootId);
			$('#P-Root-Comment-' + pRootId).next('.P-Comments-Revisions-Item-Content').find('.P-Comments-Container').append(pAjaxResult['comment_preview']);
			gCommentNow = 0;
		}
	});
}

function DeleteComment(pCommentId) {
	$.ajax({
		url : gPreviewCommentAjaxUrl,
		dataType : 'json',
		data : {
			comment_id : pCommentId,
			action : 'delete_comment'
		},
		success : function(pAjaxResult) {
			if(pAjaxResult['err_cnt']){
				alert(pAjaxResult['err_msg']);
				return;
			}

			// Remove the tree with the comments
			$('#P-Root-Comment-Holder-' + pCommentId).remove();
			
			var lPreviewContents = GetPreviewContent();			
			
			lPreviewContents.find('.P-Preview-Comment[comment_id~="' + pCommentId + '"]').each(function(pIdx, pElement){
				if($(pElement).attr('comment_id') == pCommentId){
					if(pElement.nodeName.toLowerCase() == 'span'){
						$(pElement).replaceWith($(pElement).contents());
					}else{
						$(pElement).removeClass('P-Preview-Comment');
					}
				}else{
					removeAttributeValue($(pElement), 'comment_id', pCommentId, ' ')
				}						
			});
		}
	});
}

function scrollToComment(pCommentId) {
	var lComment = $('#P-Root-Comment-' + pCommentId);

	// the header of the html has fixed position
	$('html, body').scrollTop(lComment.offset().top - $('.documentHeader').outerHeight() - $('#CommentsFreeze').outerHeight());// Because
	return false;
}

function GetPreviewPreviousSelection(){
	return gPreviousPreviewSelection;
}

function GetPreviewFirstNode(){
	$('#' + gPreviewHolderId);
}

function InitFreezeResizeEvent(){
	$(document).ready(function(){
		$("#CommentsFreeze").css('top', ($('.documentHeader').outerHeight() - 7) + 'px');
	});
	$("#CommentsFreeze").bind("resize", function(){
		var lHeight = $(this).outerHeight();
		$(this).parent().css('padding-top', lHeight + 'px');
		positionCommentsBase();
	});
}