
var gPreviewCommentFormName = 'commentpost';
var gPreviewCommentAjaxUrl = '/lib/ajax_srv/preview_comment.php';
var gCommentAjaxSrvUrl = '/lib/ajax_srv/comment_srv.php';
var gCommentPositionRecalculateAjaxUrl = '/lib/ajax_srv/comment_pos_recalculate.php';


function SetCommentsDocument(pDocumentId){
	gCommentsDocumentId = pDocumentId;
}

function GetPreviewContent(){
	return $('#' + gPreviewIframeId).contents();
}

function GetCommentSelection(){
	var lIframe = $('#' + gPreviewIframeId)[0];
	if(!lIframe)
		return;
	var lSelection = rangy.getIframeSelection(lIframe);
	return lSelection;
}



/**
 * Изчислява позицията по вертикала на възела на коментара
 * Не трябва да забравим и позицията на iframe-a (offset() на възела е релативен спрямо iframe-a)
 */

function getCommentNodeVerticalPosition(pNode){
//	var lDiv = pNode.parentNode.insertBefore(pNode.ownerDocument.createElement('div'));
//	lResult = $(lDiv).offset().top;
//	lDiv.parentNode.removeChild(lDiv);
//	return lResult;
	return $(pNode).offset().top + $('#' + gPreviewIframeId).offset().top;
}


/**
 * Попълваме позицията на коментара
 * спрямо instance/field-а в който е направен
 */
function fillCommentPos(){
	var lCommentPos = GetSelectedTextPos();	
	if(lCommentPos && !lCommentPos.selection_is_empty){
		
	
		var lStartNodeDetails = lCommentPos['start_pos'];
		var lEndNodeDetails = lCommentPos['end_pos'];
	
		var lStartInstanceId = lStartNodeDetails['instance_id'],
			lStartFieldId = lStartNodeDetails['field_id'],
			lStartOffset = lStartNodeDetails['offset'];
	
		var lEndInstanceId = lEndNodeDetails['instance_id'],
			lEndFieldId = lEndNodeDetails['field_id'],
			lEndOffset = lEndNodeDetails['offset'];
		
		if(lStartInstanceId > 0 && lStartFieldId > 0 && lEndInstanceId > 0 && lEndFieldId > 0){
			//If we know start instance & field and end instance and field -> inline comment
			$('#comments_start_instance_id').val(lStartInstanceId);
			$('#comments_start_field_id').val(lStartFieldId);
			$('#comments_start_offset').val(lStartOffset);
		
			$('#comments_end_instance_id').val(lEndInstanceId);
			$('#comments_end_field_id').val(lEndFieldId);
			$('#comments_end_offset').val(lEndOffset);
			MarkCurrentCommentAsInline();
		}else{
			//Unable to comment
			MarkCurrentCommentAsUnavailable();
			clearCommentPos();
			return false;
		}
	}else{
		//General comment
		MarkCurrentCommentAsGeneral();
		clearCommentPos();
	}
	return true;
}

function clearCommentPos(){
	$('#comments_start_instance_id').val('');
	$('#comments_start_field_id').val('');
	$('#comments_start_offset').val('');

	$('#comments_end_instance_id').val('');
	$('#comments_end_field_id').val('');
	$('#comments_end_offset').val('');
}


/**
 * Слага event-a за показване на попъпа за нов коментар при селектиране на текст в превю режим
 */
function initPreviewSelectCommentEvent(){
	//Слагаме event-a на mouseup понеже onselect не е crossbrowser
	$('#' + gPreviewIframeId).contents().on('mouseup', function(pEvent){
		CheckSelectedTextForActiveComment();
		gPreviousPreviewSelectionStartNode = false;
		fillCommentPos();	
	});

	$('#' + gPreviewIframeId).contents().bind('keyup', function(pEvent) {
		gPreviousPreviewSelectionStartNode = false
		CheckSelectedTextForActiveComment();
	});
	$('#' + gPreviewIframeId).contents().bind('selectionchange', function(pEvent) {
		cancelPreviewNewComment();
		fillCommentPos();
	});
}

function displayNewCommentPopup(pEvent){
	//Тук координатите са релативни спрямо iframe-a, но попъпа е релативен спрямо holder-а на iframe-a
	var lPositionX = pEvent.pageX + $('#' + gPreviewIframeId).offset().left;
	var lPositionY = pEvent.pageY + $('#' + gPreviewIframeId).offset().top - 90;


//	var lPositionX = pEvent.pageX;
//	var lPositionY = pEvent.pageY;
	fillCommentPos();
	var lNewCommentPopup = $('#popupNewComment');
	lNewCommentPopup.css('left', lPositionX);
	lNewCommentPopup.css('top', lPositionY);
	lNewCommentPopup.show();

}


function cancelPreviewNewComment(){
	$('#P-Comment-Form_').hide();
	$('form[name="' + gPreviewCommentFormName + '"]').resetForm();
}

var gCommentN = 0;
function submitPreviewNewComment(){
	if(!gCommentN){
		gCommentN = 1;
		var lCommentPosIsFound = fillCommentPos();
		if(!lCommentPosIsFound){
			gCommentN = 0;
			return;
		}
		var lFormData = $('form[name="' + gPreviewCommentFormName + '"]').formSerialize();
		lFormData += '&tAction=save';
		$.ajax({
			url : gPreviewCommentAjaxUrl,
			dataType : 'json',
			data : lFormData,
			success : function(pAjaxResult){
				if(pAjaxResult['err_cnt']){
					alert(pAjaxResult['err_msg']);
					gCommentN = 0;
					return;
					return;
				}
				var lStartInstanceId = pAjaxResult['start_instance_id'];
				var lStartFieldId =  pAjaxResult['start_field_id'];
				var lStartOffset =  pAjaxResult['start_offset'];

				var lEndInstanceId = pAjaxResult['end_instance_id'];
				var lEndFieldId =  pAjaxResult['end_field_id'];
				var lEndOffset =  pAjaxResult['end_offset'];

				var lCommentId = pAjaxResult['comment_id'];
				//Insert the comment start and end nodes

				if(lStartInstanceId > 0 && lStartFieldId > 0 && lEndInstanceId > 0 && lEndFieldId > 0 ){

					var lStartPositionDetails = calculateCommentPositionAccordingToInternalPosition(lStartInstanceId, lStartFieldId, lStartOffset, true);
					InsertCommentStartEndTag(lStartPositionDetails.node, lStartPositionDetails.offset, lCommentId, true);

					var lEndPositionDetails = calculateCommentPositionAccordingToInternalPosition(lEndInstanceId, lEndFieldId, lEndOffset);
					InsertCommentStartEndTag(lEndPositionDetails.node, lEndPositionDetails.offset, lCommentId, false);




					var lIframeContent = $('#' + gPreviewIframeId).contents();
					var lIframeWindow = document.getElementById(gPreviewIframeId).contentWindow;
					if(lStartInstanceId && lStartFieldId){
						var lStartTrackerNode = null;
						if(lIframeWindow.GetInstanceFieldTrackerNode){
							lStartTrackerNode = lIframeWindow.GetInstanceFieldTrackerNode(lStartInstanceId, lStartFieldId);
						}
						if(lStartTrackerNode){//Edit mode - save the field
							lIframeWindow.SaveNodeTrackerContents(lStartTrackerNode);
						}else{//Readonly
							var lInstanceNode = lIframeContent.find('*[instance_id="' + lStartInstanceId + '"]');
							lInstanceNode = lInstanceNode.first();
							if(lInstanceNode){
								var lFieldNodes = lIframeContent.find('*[field_id="' + lStartFieldId + '"]');
								var lFieldNode = null;
								for(var i = 0; i < lFieldNodes.length; ++ i){
									var lField = $(lFieldNodes.get(i));
									if(lField.closest('*[instance_id]')[0] === lInstanceNode[0]){//Това е търсения field
										lFieldNode = lField[0];
										break;
									}
								}
								if(lFieldNode){
									$.ajax({
										url : gCommentPositionRecalculateAjaxUrl,
										dataType : 'json',
										data : {
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
						if(lIframeWindow.GetInstanceFieldTrackerNode){
							lEndTrackerNode = lIframeWindow.GetInstanceFieldTrackerNode(lEndInstanceId, lEndFieldId);
						}
						if(lEndTrackerNode){//Edit mode - save the field
							lIframeWindow.SaveNodeTrackerContents(lEndTrackerNode);
						}else{//Readonly
							var lInstanceNode = lIframeContent.find('*[instance_id="' + lEndInstanceId + '"]');
							lInstanceNode = lInstanceNode.first();
							if(lInstanceNode){
								var lFieldNodes = lIframeContent.find('*[field_id="' + lEndFieldId + '"]');
								var lFieldNode = null;
								for(var i = 0; i < lFieldNodes.length; ++ i){
									var lField = $(lFieldNodes.get(i));
									if(lField.closest('*[instance_id]')[0] === lInstanceNode[0]){//Това е търсения field
										lFieldNode = lField[0];
										break;
									}
								}
								if(lFieldNode){
									$.ajax({
										url : gCommentPositionRecalculateAjaxUrl,
										dataType : 'json',
										data : {
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
				}
				//Крием формата
				cancelPreviewNewComment();
				// Aко няма коментари ползваме темплейт с expand и collapse и добавяме след хедър секцията
				if(pAjaxResult['is_first']){
					$('#P-Wrapper-Right-Content').find('.P-Article-StructureHead').after(pAjaxResult['result']);
				}else{ // Иначе Добавяме коментара след последния коментар
					$('#P-Root-Comments-Holder').children('.P-Root-Comment').last().after(pAjaxResult['result']);
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

function scrollToComment(pCommentId){
	var lComment = $('#P-Root-Comment-' + pCommentId);
	if(!lComment.length){
		return false;
	}

//	$('html, body').scrollTop(lComment.offset().top);
	$('html, body').scrollTop(lComment.offset().top - $('.P-Header').outerHeight() - $('.P-Bread-Crumbs').outerHeight() - $('#CommentsFreeze').outerHeight());// Because
	return false;
}

function GetPreviewPreviousSelection(){
	return getIframeSelection(gPreviewIframeId);
}

function GetPreviewFirstNode(){
	var lIframeContents = GetPreviewContent();
	return lIframeContents.find('#previewHolder');
}