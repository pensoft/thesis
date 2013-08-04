var gPreviewCommentFormName = 'newCommentForm';
var gReplyCommentFormPrefix = 'comment_reply_form';
var gCommentStartPos;
var gCommentEndPos;
var gSelectionIsEmpty = true;
var gPreviewHolderId = 'previewHolder';
var gPreviewCommentAjaxUrl = '/lib/ajax_srv/comment_srv.php';
var gCommentPositionRecalculateAjaxUrl = gPreviewCommentAjaxUrl;
var gCommentAjaxSrvUrl = '/lib/ajax_srv/comment_srv.php';
var gRecalculateCommentPositionAction = 'recalculate_position';
var gPreviewIframeId = 'previewIframe';

var gCommentEndInstanceIdInputId = 'previewNewCommentEndInstanceId';
var gCommentEndFieldIdInputId = 'previewNewCommentEndFieldId';
var gCommentEndOffsetInputId = 'previewNewCommentEndOffset';

var gCommentStartInstanceIdInputId = 'previewNewCommentStartInstanceId';
var gCommentStartFieldIdInputId = 'previewNewCommentStartFieldId';
var gCommentStartOffsetInputId = 'previewNewCommentStartOffset';

var gPreviewNewCommentFormActionName = 'new_comment';


function SetCommentsVersion(pVersionId) {
	gCommentsVersionId = pVersionId;
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

function clearNewReplyCommentForm(pRootId) {
	$('form[name="' + gReplyCommentFormPrefix + pRootId + '"]').resetForm();
	$('form[name="' + gReplyCommentFormPrefix + pRootId + '"] textarea').val('');
}

function cancelNewReplyComment(pRootId) {
	showCommentForm(pRootId);
	clearNewReplyCommentForm(pRootId);
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
			CleanupAfterCommentDelete(pCommentId);
		}
	});
}

function scrollToComment(pCommentId) {
	var lComment = $('#P-Root-Comment-' + pCommentId);
	if(!lComment.length){
		return false;
	}
	
	// the header of the html has fixed position
	$('html, body').scrollTop(lComment.offset().top - $('.documentHeader').outerHeight() - $('#CommentsFreeze').outerHeight());// Because
	return false;
}


function InitFreezeResizeEvent(){
	$(document).ready(function(){
		$("#CommentsFreeze").css('top', ($('.documentHeader').outerHeight() -2) + 'px');
	});
	$("#CommentsFreeze").bind("resize", function(){
		var lHeight = $(this).outerHeight();
		$(this).parent().css('padding-top', (lHeight) + 'px');
		positionCommentsBase();
	});
}