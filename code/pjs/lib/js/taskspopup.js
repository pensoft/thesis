/* Показване на формата за нотификация на потребителите */
var gReviwersFlagFieldId = '#reviewers_email_flag_id';
var gDocumentIdFieldId = '#email_document_id';
var gRoleRedirectFieldId = '#role_redirect';
function LayerEventTasksFrm(pElem, pEventIds, pUrl, pReviewersFlag, pDocumentId, pRoleRedirect) {
	$.ajax({
		url : '/lib/ajax_srv/taskspopup.php',
		async : false,
		data : {
			event_ids : pEventIds,
			reviewers_email_flag: pReviewersFlag,
			document_id : pDocumentId,
			role_redirect : pRoleRedirect
		},
		success : function(pAjaxResult) {
			if(pAjaxResult == 'close') {
				$.modal.close();
			} else {
				$('#' + pElem).html(pAjaxResult);
		
				$('#' + pElem).modal({
					autoResize : true,
					position : ["10%", ],
					minHeight : 430,
					maxHeight : 430,
					overlayClose : true,
					close : false,
					onShow : function(dialog) {
						var doch = $(window).height();
						if(doch <= 430){
							var calh = doch - 2 * 80;
							$('#simplemodal-container').height(calh);
							$('#simplemodal-container .taskspopup-rightcol').height((calh - 20));
						} else {
							var docw = $('#simplemodal-container').width();
							var modalh = $('#P-Registration-Content').height();
							//$('#simplemodal-container .taskspopup-rightcol').height(430);	
							if(modalh > 430) {
								$('#simplemodal-container').width(docw + 15);
							}
						}
						$(".simplemodal-wrap").css('overflowX', 'hidden');
					},
					onClose : function(dialog) {
						if(pUrl)
							window.location = pUrl;
						
						$.modal.close();
					}
				});
			}
		}
	});
}

function ChangeTaskRecipient(pElem, pEventIds, pTaskId) {
	var lReviewersFlagValue = $(gReviwersFlagFieldId).val();
	var lDocumentId = $(gDocumentIdFieldId).val();
	var lRoleRedirect = $(gRoleRedirectFieldId).val();
	
	$.ajax({
		url : '/lib/ajax_srv/taskspopup.php',
		async : false,
		data : {
			event_ids : pEventIds,
			task_detail_id: pTaskId,
			action : 'getall',
			reviewers_email_flag: lReviewersFlagValue,
			document_id : lDocumentId,
			role_redirect : lRoleRedirect
		},
		success : function(pAjaxResult) {
			if(pAjaxResult == 'close') {
				$.modal.close();
			} else {
				$('#' + pElem).html(pAjaxResult);
				//$('#simplemodal-container .taskspopup-rightcol').height(430);
			}
		}
	});
}

/**
 * The feature is removed, but the function is here if pensoft want to enabled it again...
 * 
 * @param {Object} pCheckBox
 * @param {Object} pElem
 * @param {Object} pEventIds
 * @param {Object} pTaskId
 * @param {Object} pCurTask
 * 
 * 
 */
function SkipTaskRecipient(pCheckBox, pElem, pEventIds, pTaskId, pCurTask) {
	var lReviewersFlagValue = $(gReviwersFlagFieldId).val();
	var lDocumentId = $(gDocumentIdFieldId).val();
	var lRoleRedirect = $(gRoleRedirectFieldId).val();
	var lAction = 'skip';
	var lOper = 1;
	if(pTaskId == pCurTask) {
		lAction = 'skip_refresh_form';
	}
	
	if($(pCheckBox).is(':checked')) {
		lOper = 2;
	}
	
	$.ajax({
		url : '/lib/ajax_srv/taskspopup.php',
		async : false,
		data : {
			event_ids : pEventIds,
			task_detail_id: pCurTask,
			skip_task_detail_id: pTaskId,
			action : lAction,
			skip_oper : lOper,
			reviewers_email_flag: lReviewersFlagValue,
			document_id : lDocumentId,
			role_redirect : lRoleRedirect
		},
		success : function(pAjaxResult) {
			if(pAjaxResult == 'close') {
				$.modal.close();
			} else {
				if(lAction == 'skip_refresh_form') {
					$('#' + pElem).html(pAjaxResult);
				}
			}
		}
	});
}

function PerformTaskAction(ptAction, pContainer) {
		var lSerializedFormValues = $('form[name="tasksfrm"]').serialize();
		lSerializedFormValues = lSerializedFormValues + '&action=' + ptAction;
		
		$.ajax({
			url : '/lib/ajax_srv/taskspopup.php',
			async : false,
			data : lSerializedFormValues,
			success : function(pAjaxResult) {
				if(pAjaxResult == 'close') {
					$.modal.close();
				} else {
					$('#' + pContainer).html(pAjaxResult);
					//$('#simplemodal-container .taskspopup-rightcol').height(430);
				}
			}
		});
}

function saveFld(pTaskDetailId, pFldElem) {
	var lFldValue = $(pFldElem).val();
	var lFldName = $(pFldElem).attr("name");
	var lTaskDetailId = $(pTaskDetailId).val();
	 
	$.ajax({
			url : '/lib/ajax_srv/taskspopup_saver.php',
			async : false,
			data : {
				email_task_detail_id : lTaskDetailId,
				fld_name : lFldName,
				fld_value : lFldValue
			},
			success : function(pAjaxResult) {
				
			}
		});
}

function ShowHideReviewersCancelInvitationLink() {
	var lReviewersFlagValue = $(gReviwersFlagFieldId).val();
	if(!lReviewersFlagValue || typeof(lReviewersFlagValue) == 'indefined' || lReviewersFlagValue != 1) {
		$('.taskpopup-list-row-content-right-main-role_right').remove();
	}
}

function CancelReviewerInvitationInEmailForm(event, pEventIds, pUID, pTaskDetailID, ptAction, pContainer) {
	event.stopPropagation();
	event.preventDefault(); 
	var lReviewersFlagValue = $(gReviwersFlagFieldId).val();
	var lDocumentId = $(gDocumentIdFieldId).val();
	var lRoleRedirect = $(gRoleRedirectFieldId).val();
	
	$.ajax({
		url : '/lib/ajax_srv/taskspopup.php',
		async : false,
		data : {
			event_ids : pEventIds,
			reviewer_task_id: pTaskDetailID,
			action : ptAction,
			reviewers_email_flag: lReviewersFlagValue,
			document_id : lDocumentId,
			uid : pUID,
			role_redirect : lRoleRedirect
		},
		success : function(pAjaxResult) {
			if(pAjaxResult == 'close') {
				$.modal.close();
				if(!lRoleRedirect || typeof(lRoleRedirect) == 'undefined' || lRoleRedirect == 0) {
					window.location.reload();	
				}
			} else {
				$('#' + pContainer).html(pAjaxResult);
			}
		}
	});
}
