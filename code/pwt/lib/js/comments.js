
var gPreviewCommentFormName = 'commentpost';
var gPreviewCommentAjaxUrl = '/lib/ajax_srv/preview_comment.php';
var gCommentAjaxSrvUrl = '/lib/ajax_srv/comment_srv.php';
var gCommentPositionRecalculateAjaxUrl = '/lib/ajax_srv/comment_pos_recalculate.php';

var gCommentEndInstanceIdInputId = 'comments_end_instance_id';
var gCommentEndFieldIdInputId = 'comments_end_field_id';
var gCommentEndOffsetInputId = 'comments_end_offset';

var gCommentStartInstanceIdInputId = 'comments_start_instance_id';
var gCommentStartFieldIdInputId = 'comments_start_field_id';
var gCommentStartOffsetInputId = 'comments_start_offset';
var gPreviewNewCommentFormActionName = '';
var gRecalculateCommentPositionAction = '';


function SetCommentsDocument(pDocumentId){
	gCommentsDocumentId = pDocumentId;
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

function deleteComment( pCommentId ) {
	if (confirm("Are you sure you want to delete this comment?")) {
		$.ajax({
			url: gDeleteCommentSrv,
			dataType : 'json',
			type : 'POST',
			async : false,
			data : {
				comment_id: pCommentId
			},
			success: function(pAjaxResult){
				if(pAjaxResult["result"] == 1){				
					CleanupAfterCommentDelete(pCommentId);
				}else{

				}
			}
		});
	}
}